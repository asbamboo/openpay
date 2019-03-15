<?php
namespace asbamboo\openpay\_test\apiStore\handler\v1_0\trade;

use PHPUnit\Framework\TestCase;
use asbamboo\openpay\apiStore\parameter\v1_0\trade\refund\RefundRequest;
use asbamboo\http\ServerRequest;
use asbamboo\database\Factory;
use asbamboo\database\Connection;
use asbamboo\openpay\apiStore\handler\v1_0\trade\Refund;
use asbamboo\openpay\model\tradePay\TradePayRepository;
use asbamboo\openpay\model\tradeRefund\TradeRefundRepository;
use asbamboo\openpay\model\tradeRefund\TradeRefundManager;
use asbamboo\openpay\model\tradeRefundClob\TradeRefundClobRepository;
use asbamboo\openpay\model\tradeRefundClob\TradeRefundClobManager;
use asbamboo\openpay\apiStore\exception\TradeRefundNotFoundInvalidException;
use asbamboo\openpay\model\tradeRefund\TradeRefundEntity;
use asbamboo\openpay\model\tradePay\TradePayEntity;
use asbamboo\openpay\Constant;
use asbamboo\openpay\_test\fixtures\channel\ChannelManager;
use asbamboo\openpay\apiStore\exception\TradeRefundOutRefundNoInvalidException;

/**
 * - 参数没有时抛出异常。
 * - out_trade_no无效，并且没有传入in_trade_no抛出异常
 * - in_trade_no无效时抛出异常
 * - out_trade_no 和 in_trade_no同时传入时优先使用in_trade_no
 * - 只有在订单状态是TRADE_PAY_TRADE_STATUS_PAYOK的状态下允许退款 (在model测试中测试)
 * - 一次性退款退款金额不能超过订单金额 (在model测试中测试)
 * - 多次退款退款总金额不能超过订单金额 (在model测试中测试)
 * - 对接应用的退款单号如果多次请求，请求的参数必须和上一次请求保持一致（退款单号必须是唯一的）
 * - 对接应用的退款单号如果多次请求.
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年11月13日
 */
class RefundTest extends TestCase
{
    public $request;

    public $Db;

    public function setUp()
    {
        $this->request  = $_REQUEST;
    }

    public function tearDown()
    {
        $_REQUEST       = $this->request;
    }

    public function testExecMissingParameter()
    {
        $this->expectException(TradeRefundNotFoundInvalidException::class);
        $Request            = $this->getRequest([]);
        $Handler            = $this->getHandler();
        $Handler->exec($Request);
    }

    public function testExecNotFindInTradeNo()
    {
        $this->expectException(TradeRefundNotFoundInvalidException::class);
        $Request            = $this->getRequest([
            'in_trade_no'   => 'not_found_trade',
        ]);
        $Handler            = $this->getHandler();
        $Handler->exec($Request);
    }

    public function testExecNotFindOutTradeNo()
    {
        $this->expectException(TradeRefundNotFoundInvalidException::class);
        $Request            = $this->getRequest([
            'out_trade_no'   => 'not_found_trade',
        ]);
        $Handler            = $this->getHandler();
        $Handler->exec($Request);
    }

    public function testExecRepeatRequest()
    {
        $this->expectException(TradeRefundOutRefundNoInvalidException::class);
        $Handler    = $this->getHandler();
        $this->Db->getManager()->transactional(function()use($Handler){

            $ip                 = mt_rand(0,255) . '.0.0.1';
            $in_trade_no        = date('ymdhis') . mt_rand(0, 999);
            $TradePayEntity     = new TradePayEntity();
            $TradePayEntity->setClientIp($ip);
            $TradePayEntity->setChannel('TEST');
            $TradePayEntity->setTradeStatus(Constant::TRADE_PAY_TRADE_STATUS_PAYOK);
            $TradePayEntity->setInTradeNo($in_trade_no);
            $TradePayEntity->setTotalFee(99999999);
            $this->Db->getManager()->persist($TradePayEntity);


            $refund_fee         = mt_rand(0, 99999);
            $in_refund_no       = date('ymdhis') . mt_rand(0, 999);
            $out_refund_no      = date('ymdhis') . mt_rand(0, 999);
            $TradeRefundEntity  = new TradeRefundEntity();
            $TradeRefundEntity->setInRefundNo($in_refund_no);
            $TradeRefundEntity->setOutRefundNo($out_refund_no);
            $TradeRefundEntity->setRefundFee($refund_fee);
            $this->Db->getManager()->persist($TradeRefundEntity);

            $this->Db->getManager()->flush();

            $Request            = $this->getRequest([
                'in_trade_no'   => $in_trade_no,
                'out_refund_no' => $out_refund_no,
                'refund_fee'    => 100000,
            ]);

            $Handler->exec($Request);

            throw new RollbackException('Rollback');
        });
    }

    public function testExecRefund1()
    {
        try{
            $Handler    = $this->getHandler();
            $this->Db->getManager()->transactional(function()use($Handler){

                $ip                 = mt_rand(0,255) . '.0.0.1';
                $in_trade_no        = date('ymdhis') . mt_rand(0, 999);
                $refund_fee         = mt_rand(0, 99999);
                $out_refund_no      = 'OF' . date('ymdhis') . mt_rand(0, 999);
                $TradePayEntity     = new TradePayEntity();
                $TradePayEntity->setClientIp($ip);
                $TradePayEntity->setChannel('TEST');
                $TradePayEntity->setTradeStatus(Constant::TRADE_PAY_TRADE_STATUS_PAYOK);
                $TradePayEntity->setInTradeNo($in_trade_no);
                $TradePayEntity->setTotalFee(99999999);
                $this->Db->getManager()->persist($TradePayEntity);
                $this->Db->getManager()->flush();

                $Request            = $this->getRequest([
                    'in_trade_no'   => $in_trade_no,
                    'out_refund_no' => $out_refund_no,
                    'refund_fee'    => $refund_fee,
                ]);

                $ChannelResponse    = $Handler->exec($Request);
                $response_array     = $ChannelResponse->getObjectVars();

                $this->assertEquals($in_trade_no, $response_array['in_trade_no']);
                $this->assertEquals($TradePayEntity->getOutTradeNo(), $response_array['out_trade_no']);
                $this->assertNotEmpty($response_array['in_refund_no']);
                $this->assertEquals($out_refund_no, $response_array['out_refund_no']);
                $this->assertEquals($refund_fee, $response_array['refund_fee']);
                $this->assertEquals(Constant::getTradeRefundStatusNames()[Constant::TRADE_REFUND_STATUS_SUCCESS], $response_array['refund_status']);
                $this->assertEquals('2018-11-13 20:07:50', $response_array['refund_pay_ymdhis']);

                throw new RollbackException('Rollback');
            });
        }catch(RollbackException $e){
            //
        }
    }

    public function testExecRefund2()
    {
        try{
            $Handler    = $this->getHandler();
            $this->Db->getManager()->transactional(function()use($Handler){

                $ip                 = mt_rand(0,255) . '.0.0.1';
                $in_trade_no        = date('ymdhis') . mt_rand(0, 999);
                $refund_fee         = mt_rand(0, 99999);
                $out_refund_no      = 'OF' . date('ymdhis') . mt_rand(0, 999);
                $TradePayEntity     = new TradePayEntity();
                $TradePayEntity->setClientIp($ip);
                $TradePayEntity->setChannel('TEST');
                $TradePayEntity->setTradeStatus(Constant::TRADE_PAY_TRADE_STATUS_PAYOK);
                $TradePayEntity->setInTradeNo($in_trade_no);
                $TradePayEntity->setTotalFee(99999999);
                $this->Db->getManager()->persist($TradePayEntity);

                $in_refund_no       = date('ymdhis') . mt_rand(0, 999);
                $TradeRefundEntity  = new TradeRefundEntity();
                $TradeRefundEntity->setInTradeNo($in_trade_no);
                $TradeRefundEntity->setInRefundNo($in_refund_no);
                $TradeRefundEntity->setOutRefundNo($out_refund_no);
                $TradeRefundEntity->setRefundFee($refund_fee);
                $TradeRefundEntity->setStatus(Constant::TRADE_REFUND_STATUS_FAILED);
                $this->Db->getManager()->persist($TradeRefundEntity);
                $this->Db->getManager()->flush();

                $Request            = $this->getRequest([
                    'in_trade_no'   => $in_trade_no,
                    'out_refund_no' => $out_refund_no,
                    'refund_fee'    => $refund_fee,
                ]);
                $ChannelResponse    = $Handler->exec($Request);
                $response_array     = $ChannelResponse->getObjectVars();
                $this->assertEquals($in_trade_no, $response_array['in_trade_no']);
                $this->assertEquals($TradePayEntity->getOutTradeNo(), $response_array['out_trade_no']);
                $this->assertNotEmpty($response_array['in_refund_no']);
                $this->assertEquals($out_refund_no, $response_array['out_refund_no']);
                $this->assertEquals($refund_fee, $response_array['refund_fee']);
                $this->assertEquals(Constant::getTradeRefundStatusNames()[Constant::TRADE_REFUND_STATUS_SUCCESS], $response_array['refund_status']);
                $this->assertEquals('2018-11-13 20:07:50', $response_array['refund_pay_ymdhis']);

                throw new RollbackException('Rollback');
            });
        }catch(RollbackException $e){
            //
        }
    }

    private function getRequest(array $request)
    {
        $_REQUEST                       = $request;
        $Request                        = new ServerRequest();
        return new RefundRequest($Request);
    }

    private function getHandler()
    {
        $Db                             = new Factory();
        $Db->addConnection(Connection::create([
            'driver'    => 'pdo_sqlite',
            'path'      => dirname(dirname(dirname(dirname(dirname(__DIR__))))) . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'db.sqlite'
        ], dirname(dirname(dirname(dirname(dirname(__DIR__))))) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'entity', Connection::MATADATA_YAML));
        $this->Db                       = $Db;

        $ChannelManager                = new ChannelManager();
        $TradePayRepository            = new TradePayRepository($Db);
        $TradeRefundRepository         = new TradeRefundRepository($Db);
        $TradeRefundManager            = new TradeRefundManager($Db, $TradeRefundRepository);
        $TradeRefundClobRepository     = new TradeRefundClobRepository($Db);
        $TradeRefundClobManager        = new TradeRefundClobManager($Db, $TradeRefundClobRepository);
        $ChannelHandler                = new Refund(
            $ChannelManager,
            $Db,
            $TradePayRepository,
            $TradeRefundRepository,
            $TradeRefundManager,
            $TradeRefundClobRepository,
            $TradeRefundClobManager
        );
        return $ChannelHandler;
    }
}

if(!class_exists(RollbackException::class)){
    class RollbackException extends \RuntimeException{}
}