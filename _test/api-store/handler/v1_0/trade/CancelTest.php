<?php
namespace asbamboo\openpay\_test\apiStore\handler\v1_0\trade;

use PHPUnit\Framework\TestCase;
use asbamboo\openpay\apiStore\handler\v1_0\trade\Cancel;
use asbamboo\openpay\_test\fixtures\channel\ChannelManager;
use asbamboo\database\Factory;
use asbamboo\openpay\model\tradePay\TradePayRepository;
use asbamboo\openpay\model\tradePay\TradePayManager;
use asbamboo\openpay\model\tradePayThirdPart\TradePayThirdPartRepository;
use asbamboo\database\Connection;
use asbamboo\openpay\apiStore\parameter\v1_0\trade\cancel\CancelRequest;
use asbamboo\http\ServerRequest;
use asbamboo\openpay\apiStore\exception\TradeCancelNotFoundInvalidException;
use asbamboo\openpay\model\tradePay\TradePayEntity;
use asbamboo\openpay\Constant;
use asbamboo\openpay\apiStore\exception\TradeCancelNotAllowedException;
use asbamboo\openpay\model\tradePayThirdPart\TradePayThirdPartEntity;

/**
 * - 参数没有时抛出异常。
 * - out_trade_no无效，并且没有传入in_trade_no抛出异常
 * - in_trade_no无效时抛出异常
 * - out_trade_no 和 in_trade_no同时传入时优先使用in_trade_no
 * - 使用out_trade_no取消订单
 * - 使用in_trade_no取消订单
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年11月12日
 */
class CancelTest extends TestCase
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
        $this->expectException(TradeCancelNotFoundInvalidException::class);
        $Request            = $this->getRequest([]);
        $Handler            = $this->getHandler();
        $Handler->exec($Request);
    }

    public function testExecNotFindInTradeNo()
    {
        $this->expectException(TradeCancelNotFoundInvalidException::class);
        $Request            = $this->getRequest([
            'in_trade_no'   => 'not_found_trade',
        ]);
        $Handler            = $this->getHandler();
        $Handler->exec($Request);
    }

    public function testExecNotFindOutTradeNo()
    {
        $this->expectException(TradeCancelNotFoundInvalidException::class);
        $Request            = $this->getRequest([
            'out_trade_no'   => 'not_found_trade',
        ]);
        $Handler            = $this->getHandler();
        $Handler->exec($Request);
    }

    public function testExecNotAllowed()
    {
        $this->expectException(TradeCancelNotAllowedException::class);
        $Handler    = $this->getHandler();
        $this->Db->getManager()->transactional(function()use($Handler){
            $ip                 = mt_rand(0,255) . '.0.0.1';
            $in_trade_no        = date('ymdhis') . mt_rand(0, 999);
            $TradePayEntity     = new TradePayEntity();
            $TradePayEntity->setClientIp($ip);
            $TradePayEntity->setChannel('TEST');
            $TradePayEntity->setTradeStatus(Constant::TRADE_PAY_TRADE_STATUS_PAYOK);
            $TradePayEntity->setInTradeNo($in_trade_no);
            $this->Db->getManager()->persist($TradePayEntity);
            $this->Db->getManager()->flush();
            $Request            = $this->getRequest([
                'in_trade_no'   => $in_trade_no,
            ]);
            $Handler->exec($Request);
            throw new RollbackException('rollback');
        });
    }

    public function testExecOutTradeNo()
    {
        try{
            $Handler    = $this->getHandler();
            $this->Db->getManager()->transactional(function()use($Handler){
                $title              = 'title' . mt_rand(0,999);
                $ip                 = mt_rand(0,255) . '.0.0.1';
                $in_trade_no        = date('ymdhis') . mt_rand(0, 999);
                $out_trade_no       = 'o' . date('ymdhis') . mt_rand(0, 999);
                $total_fee          = mt_rand(0,9999);

                $TradePayEntity     = new TradePayEntity();
                $TradePayEntity->setClientIp($ip);
                $TradePayEntity->setChannel('TEST');
                $TradePayEntity->setTitle($title);
                $TradePayEntity->setOutTradeNo($out_trade_no);
                $TradePayEntity->setTradeStatus(Constant::TRADE_PAY_TRADE_STATUS_NOPAY);
                $TradePayEntity->setInTradeNo($in_trade_no);
                $TradePayEntity->setTotalFee($total_fee);

                $TradePayThirdPartEntity     = new TradePayThirdPartEntity();
                $TradePayThirdPartEntity->setInTradeNo($in_trade_no);
                $TradePayThirdPartEntity->setSendData('');

                $this->Db->getManager()->persist($TradePayEntity);
                $this->Db->getManager()->persist($TradePayThirdPartEntity);
                $this->Db->getManager()->flush();

                $Request            = $this->getRequest([
                    'out_trade_no'   => $out_trade_no,
                ]);
                $CancelResponse = $Handler->exec($Request);
                $response_array = $CancelResponse->getObjectVars();
                $this->assertEquals('TEST', $response_array['channel']);
                $this->assertEquals($in_trade_no, $response_array['in_trade_no']);
                $this->assertEquals($title, $response_array['title']);
                $this->assertEquals($out_trade_no, $response_array['out_trade_no']);
                $this->assertEquals($total_fee, $response_array['total_fee']);
                $this->assertEquals($ip, $response_array['client_ip']);
                $this->assertEquals(Constant::getTradePayTradeStatusNames()[Constant::TRADE_PAY_TRADE_STATUS_CANCEL], $response_array['trade_status']);
                $this->assertNotEmpty($response_array['cancel_ymdhis']);
                throw new RollbackException('rollback');
            });
        }catch(RollbackException $e){
            //
        }
    }

    public function testExecInTradeNo()
    {
        try{
            $Handler    = $this->getHandler();
            $this->Db->getManager()->transactional(function()use($Handler){
                $title              = 'title' . mt_rand(0,999);
                $ip                 = mt_rand(0,255) . '.0.0.1';
                $in_trade_no        = date('ymdhis') . mt_rand(0, 999);
                $out_trade_no       = 'o' . date('ymdhis') . mt_rand(0, 999);
                $total_fee          = mt_rand(0,9999);

                $TradePayEntity     = new TradePayEntity();
                $TradePayEntity->setClientIp($ip);
                $TradePayEntity->setChannel('TEST');
                $TradePayEntity->setTitle($title);
                $TradePayEntity->setOutTradeNo($out_trade_no);
                $TradePayEntity->setTradeStatus(Constant::TRADE_PAY_TRADE_STATUS_NOPAY);
                $TradePayEntity->setInTradeNo($in_trade_no);
                $TradePayEntity->setTotalFee($total_fee);

                $TradePayThirdPartEntity     = new TradePayThirdPartEntity();
                $TradePayThirdPartEntity->setInTradeNo($in_trade_no);
                $TradePayThirdPartEntity->setSendData('');

                $this->Db->getManager()->persist($TradePayEntity);
                $this->Db->getManager()->persist($TradePayThirdPartEntity);
                $this->Db->getManager()->flush();

                $Request            = $this->getRequest([
                    'in_trade_no'   => $in_trade_no,
                    'out_trade_no'  => $out_trade_no . 'xxx',
                ]);
                $CancelResponse = $Handler->exec($Request);
                $response_array = $CancelResponse->getObjectVars();
                $this->assertEquals('TEST', $response_array['channel']);
                $this->assertEquals($in_trade_no, $response_array['in_trade_no']);
                $this->assertEquals($title, $response_array['title']);
                $this->assertEquals($out_trade_no, $response_array['out_trade_no']);
                $this->assertEquals($total_fee, $response_array['total_fee']);
                $this->assertEquals($ip, $response_array['client_ip']);
                $this->assertEquals(Constant::getTradePayTradeStatusNames()[Constant::TRADE_PAY_TRADE_STATUS_CANCEL], $response_array['trade_status']);
                $this->assertNotEmpty($response_array['cancel_ymdhis']);
                throw new RollbackException('rollback');
            });
        }catch(RollbackException $e){
            //
        }
    }

    private function getRequest(array $request)
    {
        $_REQUEST                       = $request;
        $Request                        = new ServerRequest();
        return new CancelRequest($Request);
    }

    private function getHandler()
    {
        $Db                             = new Factory();
        $Db->addConnection(Connection::create([
            'driver'    => 'pdo_sqlite',
            'path'      => dirname(dirname(dirname(dirname(__DIR__)))) . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'db.sqlite'
        ], dirname(dirname(dirname(dirname(dirname(__DIR__))))) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database', Connection::MATADATA_YAML));
        $this->Db                       = $Db;

        $ChannelManager                 = new ChannelManager();
        $TradePayRepository             = new TradePayRepository($Db);
        $TradePayManager                = new TradePayManager($Db, $TradePayRepository);
        $TradePayThirdPartRepository    = new TradePayThirdPartRepository($Db);
        $Cancel                         = new Cancel($ChannelManager, $Db, $TradePayRepository, $TradePayManager, $TradePayThirdPartRepository);
        return $Cancel;
    }
}

if(!class_exists(RollbackException::class)){
    class RollbackException extends \RuntimeException{}
}