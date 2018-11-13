<?php
namespace asbamboo\openpay\_test\apiStore\handler\v1_0\trade;

use PHPUnit\Framework\TestCase;
use asbamboo\openpay\apiStore\parameter\v1_0\trade\query\QueryRequest;
use asbamboo\database\Factory;
use asbamboo\database\Connection;
use asbamboo\openpay\_test\fixtures\channel\ChannelManager;
use asbamboo\openpay\model\tradePay\TradePayRespository;
use asbamboo\openpay\apiStore\handler\v1_0\trade\Query;
use asbamboo\openpay\model\tradePay\TradePayManager;
use asbamboo\http\ServerRequest;
use asbamboo\openpay\apiStore\exception\TradeQueryNotFoundInvalidException;
use asbamboo\openpay\model\tradePay\TradePayEntity;
use asbamboo\openpay\model\tradePayThirdPart\TradePayThirdPartEntity;
use asbamboo\openpay\Constant;

/**
 * - 参数没有时抛出异常。
 * - out_trade_no无效，并且没有传入in_trade_no抛出异常
 * - in_trade_no无效时抛出异常
 * - out_trade_no 和 in_trade_no同时传入时优先使用in_trade_no
 * - 当订单状态时未支付，渠道返回支付成功（不可退款时），订单需要做相应的状态修改
 * - 当订单状态时未支付，渠道返回支付成功（可退款时），订单需要做相应的状态修改
 * - 当订单状态时未支付，渠道返回支付取消，订单需要做相应的状态修改
 * - 渠道未返回支付状态时，订单状态不变。
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年11月13日
 */
class QueryTest extends TestCase
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
        $this->expectException(TradeQueryNotFoundInvalidException::class);
        $Request            = $this->getRequest([]);
        $Handler            = $this->getHandler();
        $Handler->exec($Request);
    }

    public function testExecNotFindInTradeNo()
    {
        $this->expectException(TradeQueryNotFoundInvalidException::class);
        $Request            = $this->getRequest([
            'in_trade_no'   => 'not_found_trade',
        ]);
        $Handler            = $this->getHandler();
        $Handler->exec($Request);
    }

    public function testExecNotFindOutTradeNo()
    {
        $this->expectException(TradeQueryNotFoundInvalidException::class);
        $Request            = $this->getRequest([
            'out_trade_no'   => 'not_found_trade',
        ]);
        $Handler            = $this->getHandler();
        $Handler->exec($Request);
    }

    public function testExecOutTradeNoCancel()
    {
        try{
            $Handler    = $this->getHandler();
            $this->Db->getManager()->transactional(function()use($Handler){
                $title              = 'title' . mt_rand(0,999);
                $client_ip          = mt_rand(0,255) . '.0.0.1';
                $in_trade_no        = date('ymdhis') . mt_rand(0, 999);
                $out_trade_no       = 'o' . date('ymdhis') . mt_rand(0, 999);
                $total_fee          = mt_rand(0,9999);

                $TradePayEntity     = new TradePayEntity();
                $TradePayEntity->setClientIp($client_ip);
                $TradePayEntity->setChannel('TEST_QUERY_CANCEL');
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
                $ChannelResponse    = $Handler->exec($Request);
                $response_array     = $ChannelResponse->getObjectVars();

                $this->assertEquals('TEST_QUERY_CANCEL', $response_array['channel']);
                $this->assertEquals($in_trade_no, $response_array['in_trade_no']);
                $this->assertEquals($title, $response_array['title']);
                $this->assertEquals($out_trade_no, $response_array['out_trade_no']);
                $this->assertEquals($total_fee, $response_array['total_fee']);
                $this->assertEquals($client_ip, $response_array['client_ip']);
                $this->assertEquals(Constant::getTradePayTradeStatusNames()[Constant::TRADE_PAY_TRADE_STATUS_CANCEL], $response_array['trade_status']);
                $this->assertEquals('', $response_array['payok_ymdhis']);
                $this->assertEquals('', $response_array['payed_ymdhis']);
                $this->assertNotEmpty($response_array['cancel_ymdhis']);

                throw new RollbackException('rollback');
            });
        }catch(RollbackException $e){
            //
        }
    }

    public function testExecInTradeNoPayed()
    {
        try{
            $Handler    = $this->getHandler();
            $this->Db->getManager()->transactional(function()use($Handler){
                $title              = 'title' . mt_rand(0,999);
                $client_ip          = mt_rand(0,255) . '.0.0.1';
                $in_trade_no        = date('ymdhis') . mt_rand(0, 999);
                $out_trade_no       = 'o' . date('ymdhis') . mt_rand(0, 999);
                $total_fee          = mt_rand(0,9999);

                $TradePayEntity     = new TradePayEntity();
                $TradePayEntity->setClientIp($client_ip);
                $TradePayEntity->setChannel('TEST_QUERY_PAYED');
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
                ]);
                $ChannelResponse    = $Handler->exec($Request);
                $response_array     = $ChannelResponse->getObjectVars();

                $this->assertEquals('TEST_QUERY_PAYED', $response_array['channel']);
                $this->assertEquals($in_trade_no, $response_array['in_trade_no']);
                $this->assertEquals($title, $response_array['title']);
                $this->assertEquals($out_trade_no, $response_array['out_trade_no']);
                $this->assertEquals($total_fee, $response_array['total_fee']);
                $this->assertEquals($client_ip, $response_array['client_ip']);
                $this->assertEquals(Constant::getTradePayTradeStatusNames()[Constant::TRADE_PAY_TRADE_STATUS_PAYED], $response_array['trade_status']);
                $this->assertNotEmpty($response_array['payok_ymdhis']);
                $this->assertNotEmpty($response_array['payed_ymdhis']);
                $this->assertEquals('', $response_array['cancel_ymdhis']);

                throw new RollbackException('rollback');
            });
        }catch(RollbackException $e){
            //
        }
    }

    public function testExecInTradeNoPayok()
    {
        try{
            $Handler    = $this->getHandler();
            $this->Db->getManager()->transactional(function()use($Handler){
                $title              = 'title' . mt_rand(0,999);
                $client_ip          = mt_rand(0,255) . '.0.0.1';
                $in_trade_no        = date('ymdhis') . mt_rand(0, 999);
                $out_trade_no       = 'o' . date('ymdhis') . mt_rand(0, 999);
                $total_fee          = mt_rand(0,9999);

                $TradePayEntity     = new TradePayEntity();
                $TradePayEntity->setClientIp($client_ip);
                $TradePayEntity->setChannel('TEST_QUERY_PAYOK');
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
                ]);
                $ChannelResponse    = $Handler->exec($Request);
                $response_array     = $ChannelResponse->getObjectVars();

                $this->assertEquals('TEST_QUERY_PAYOK', $response_array['channel']);
                $this->assertEquals($in_trade_no, $response_array['in_trade_no']);
                $this->assertEquals($title, $response_array['title']);
                $this->assertEquals($out_trade_no, $response_array['out_trade_no']);
                $this->assertEquals($total_fee, $response_array['total_fee']);
                $this->assertEquals($client_ip, $response_array['client_ip']);
                $this->assertEquals(Constant::getTradePayTradeStatusNames()[Constant::TRADE_PAY_TRADE_STATUS_PAYOK], $response_array['trade_status']);
                $this->assertNotEmpty($response_array['payok_ymdhis']);
                $this->assertEquals('', $response_array['payed_ymdhis']);
                $this->assertEquals('', $response_array['cancel_ymdhis']);

                throw new RollbackException('rollback');
            });
        }catch(RollbackException $e){
            //
        }
    }

    public function testExecInTradeNoNopay()
    {
        try{
            $Handler    = $this->getHandler();
            $this->Db->getManager()->transactional(function()use($Handler){
                $title              = 'title' . mt_rand(0,999);
                $client_ip          = mt_rand(0,255) . '.0.0.1';
                $in_trade_no        = date('ymdhis') . mt_rand(0, 999);
                $out_trade_no       = 'o' . date('ymdhis') . mt_rand(0, 999);
                $total_fee          = mt_rand(0,9999);

                $TradePayEntity     = new TradePayEntity();
                $TradePayEntity->setClientIp($client_ip);
                $TradePayEntity->setChannel('TEST_QUERY');
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
                    'out_trade_no'  => $out_trade_no,
                ]);
                $ChannelResponse    = $Handler->exec($Request);
                $response_array     = $ChannelResponse->getObjectVars();

                $this->assertEquals('TEST_QUERY', $response_array['channel']);
                $this->assertEquals($in_trade_no, $response_array['in_trade_no']);
                $this->assertEquals($title, $response_array['title']);
                $this->assertEquals($out_trade_no, $response_array['out_trade_no']);
                $this->assertEquals($total_fee, $response_array['total_fee']);
                $this->assertEquals($client_ip, $response_array['client_ip']);
                $this->assertEquals(Constant::getTradePayTradeStatusNames()[Constant::TRADE_PAY_TRADE_STATUS_NOPAY], $response_array['trade_status']);
                $this->assertEquals('', $response_array['payok_ymdhis']);
                $this->assertEquals('', $response_array['payed_ymdhis']);
                $this->assertEquals('', $response_array['cancel_ymdhis']);

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
        return new QueryRequest($Request);
    }

    private function getHandler()
    {
        $Db                             = new Factory();
        $Db->addConnection(Connection::create([
            'driver'    => 'pdo_sqlite',
            'path'      => dirname(dirname(dirname(dirname(__DIR__)))) . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'db.sqlite'
        ], dirname(dirname(dirname(dirname(dirname(__DIR__))))) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database', Connection::MATADATA_YAML));
        $this->Db                       = $Db;

        $ChannelManager         = new ChannelManager();
        $TradePayRespository    = new TradePayRespository($Db);
        $TradePayManager        = new TradePayManager($Db);
        $Query                  = new Query($ChannelManager, $Db, $TradePayRespository, $TradePayManager);
        return $Query;
    }
}

if(!class_exists(RollbackException::class)){
    class RollbackException extends \RuntimeException{}
}
