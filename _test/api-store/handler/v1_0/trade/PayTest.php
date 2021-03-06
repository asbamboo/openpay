<?php
namespace asbamboo\openpay\_test\apiStore\handler\v1_0\trade;

use PHPUnit\Framework\TestCase;
use asbamboo\http\ServerRequest;
use asbamboo\openpay\apiStore\parameter\v1_0\trade\pay\PayRequest;
use asbamboo\database\Connection;
use asbamboo\openpay\_test\fixtures\channel\ChannelManager;
use asbamboo\openpay\apiStore\handler\v1_0\trade\Pay;
use asbamboo\openpay\model\tradePay\TradePayManager;
use asbamboo\openpay\model\tradePayClob\TradePayClobManager;
use asbamboo\router\RouteCollection;
use asbamboo\router\Router;
use asbamboo\router\Route;
use asbamboo\database\Factory;
use asbamboo\openpay\apiStore\exception\TradePayChannelInvalidException;
use asbamboo\api\apiStore\ApiResponseRedirectParamsInterface;
use asbamboo\openpay\Constant;
use asbamboo\openpay\model\tradePay\TradePayRepository;
use asbamboo\openpay\model\tradePayClob\TradePayClobRepository;

/**
 * - 参数没有时抛出异常。
 * - out_trade_no无效，并且没有传入in_trade_no抛出异常
 * - in_trade_no无效时抛出异常
 * - out_trade_no 和 in_trade_no同时传入时优先使用in_trade_no
 * - 测试扫码支付
 * - 测试PC支付
 * - 测试不跳转页面的支付
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年11月14日
 */
class PayTest extends TestCase
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

    public function testExecTradePayChannelInvalidException()
    {
        $this->expectException(TradePayChannelInvalidException::class);
        try{
            $Handler        = $this->getHandler();
            $this->Db->getManager()->transactional(function()use($Handler){
                $Request            = $this->getRequest([
                    'channel'       => 'ChannelInvalidException',
                    'title'         => 'test',
                    'out_trade_no'  => 'test',
                    'total_fee'     => '100',
                    'client_ip'     => '192.168.3.2',
                ]);
                $Handler->exec($Request);
                throw new RollbackException('rollback exception');
            });
        }catch(RollbackException $e){
            //
        }
    }

    public function testExecPayPc()
    {
        try{
            $Handler        = $this->getHandler();
            $this->Db->getManager()->transactional(function()use($Handler){
                $title              = 'testTitle' . mt_rand(0, 9999);
                $out_trade_no       = 'no' . date('ymdhis') . mt_rand(0, 9999);
                $total_fee          = mt_rand(0, 9999);
                $client_ip          = '192.168.3.' . mt_rand(0,255);
                $Request            = $this->getRequest([
                    'channel'       => 'TEST_PAY_PC',
                    'title'         => $title,
                    'out_trade_no'  => $out_trade_no,
                    'total_fee'     => $total_fee,
                    'client_ip'     => $client_ip,
                    'notify_url'    => 'notify_url',
                    'return_url'    => 'return_url',
                ]);
                $PayResponse    = $Handler->exec($Request);

                $this->assertInstanceOf(ApiResponseRedirectParamsInterface::class, $PayResponse);
                $this->assertEquals(['data'=>'test'], $PayResponse->getRedirectResponseData());
                throw new RollbackException('rollback exception');
            });
        }catch(RollbackException $e){
            //
        }
    }

    public function testExecPayH5()
    {
        try{
            $Handler        = $this->getHandler();
            $this->Db->getManager()->transactional(function()use($Handler){
                $title              = 'testTitle' . mt_rand(0, 9999);
                $out_trade_no       = 'no' . date('ymdhis') . mt_rand(0, 9999);
                $total_fee          = mt_rand(0, 9999);
                $client_ip          = '192.168.3.' . mt_rand(0,255);
                $Request            = $this->getRequest([
                    'channel'       => 'TEST_PAY_H5',
                    'title'         => $title,
                    'out_trade_no'  => $out_trade_no,
                    'total_fee'     => $total_fee,
                    'client_ip'     => $client_ip,
                    'notify_url'    => 'notify_url',
                    'return_url'    => 'return_url',
                ]);
                $PayResponse    = $Handler->exec($Request);

                $this->assertInstanceOf(ApiResponseRedirectParamsInterface::class, $PayResponse);
                $this->assertEquals(['data'=>'test'], $PayResponse->getRedirectResponseData());
                throw new RollbackException('rollback exception');
            });
        }catch(RollbackException $e){
            //
        }
    }

    public function testExecPayAPP()
    {
        try{
            $Handler        = $this->getHandler();
            $this->Db->getManager()->transactional(function()use($Handler){
                $title              = 'testTitle' . mt_rand(0, 9999);
                $out_trade_no       = 'no' . date('ymdhis') . mt_rand(0, 9999);
                $total_fee          = mt_rand(0, 9999);
                $client_ip          = '192.168.3.' . mt_rand(0,255);
                $Request            = $this->getRequest([
                    'channel'       => 'TEST_PAY_APP',
                    'title'         => $title,
                    'out_trade_no'  => $out_trade_no,
                    'total_fee'     => $total_fee,
                    'client_ip'     => $client_ip,
                    'notify_url'    => 'notify_url',
                    'return_url'    => 'return_url',
                ]);
                $PayResponse    = $Handler->exec($Request);
                $response_array = $PayResponse->getObjectVars();

                $this->assertEquals('TEST_PAY_APP', $response_array['channel']);
                $this->assertNotEmpty($response_array['in_trade_no']);
                $this->assertEquals($title, $response_array['title']);
                $this->assertEquals($out_trade_no, $response_array['out_trade_no']);
                $this->assertEquals($total_fee, $response_array['total_fee']);
                $this->assertEquals($client_ip, $response_array['client_ip']);
                $this->assertEquals(Constant::getTradePayTradeStatusNames()[Constant::TRADE_PAY_TRADE_STATUS_NOPAY], $response_array['trade_status']);
                $this->assertEquals('', $response_array['payok_ymdhis']);
                $this->assertEquals('', $response_array['payed_ymdhis']);
                $this->assertEquals('', $response_array['cancel_ymdhis']);
                $this->assertEquals('{"key":"test_pay_app_json"}', $response_array['app_pay_json']);

                throw new RollbackException('rollback exception');
            });
        }catch(RollbackException $e){
            //
        }
    }

    public function testExecPayOnecd()
    {
        try{
            $Handler        = $this->getHandler();
            $this->Db->getManager()->transactional(function()use($Handler){
                $title              = 'testTitle' . mt_rand(0, 9999);
                $out_trade_no       = 'no' . date('ymdhis') . mt_rand(0, 9999);
                $total_fee          = mt_rand(0, 9999);
                $client_ip          = '192.168.3.' . mt_rand(0,255);
                $Request            = $this->getRequest([
                    'channel'       => 'TEST_PAY_ONECD',
                    'title'         => $title,
                    'out_trade_no'  => $out_trade_no,
                    'total_fee'     => $total_fee,
                    'client_ip'     => $client_ip,
                    'notify_url'    => 'notify_url',
                    'return_url'    => 'return_url',
                ]);
                $PayResponse    = $Handler->exec($Request);
                $response_array = $PayResponse->getObjectVars();
//                 var_dump($response_array);exit;
                $this->assertEquals('TEST_PAY_ONECD', $response_array['channel']);
                $this->assertNotEmpty($response_array['in_trade_no']);
                $this->assertEquals($title, $response_array['title']);
                $this->assertEquals($out_trade_no, $response_array['out_trade_no']);
                $this->assertEquals($total_fee, $response_array['total_fee']);
                $this->assertEquals($client_ip, $response_array['client_ip']);
                $this->assertEquals(Constant::getTradePayTradeStatusNames()[Constant::TRADE_PAY_TRADE_STATUS_NOPAY], $response_array['trade_status']);
                $this->assertEquals('', $response_array['payok_ymdhis']);
                $this->assertEquals('', $response_array['payed_ymdhis']);
                $this->assertEquals('', $response_array['cancel_ymdhis']);
                $this->assertEquals('{"key":"test_pay_onecd_json"}', $response_array['onecd_pay_json']);
                
                throw new RollbackException('rollback exception');
            });
        }catch(RollbackException $e){
            //
        }
    }
    
    public function testExecPayQRCD()
    {
        try{
            $Handler        = $this->getHandler();
            $this->Db->getManager()->transactional(function()use($Handler){
                $title              = 'testTitle' . mt_rand(0, 9999);
                $out_trade_no       = 'no' . date('ymdhis') . mt_rand(0, 9999);
                $total_fee          = mt_rand(0, 9999);
                $client_ip          = '192.168.3.' . mt_rand(0,255);
                $Request            = $this->getRequest([
                    'channel'       => 'TEST_PAY_QRCD',
                    'title'         => $title,
                    'out_trade_no'  => $out_trade_no,
                    'total_fee'     => $total_fee,
                    'client_ip'     => $client_ip,
                    'notify_url'    => 'notify_url',
                    'return_url'    => 'return_url',
                ]);
                $PayResponse    = $Handler->exec($Request);
                $response_array = $PayResponse->getObjectVars();

                $this->assertEquals('TEST_PAY_QRCD', $response_array['channel']);
                $this->assertNotEmpty($response_array['in_trade_no']);
                $this->assertEquals($title, $response_array['title']);
                $this->assertEquals($out_trade_no, $response_array['out_trade_no']);
                $this->assertEquals($total_fee, $response_array['total_fee']);
                $this->assertEquals($client_ip, $response_array['client_ip']);
                $this->assertEquals(Constant::getTradePayTradeStatusNames()[Constant::TRADE_PAY_TRADE_STATUS_NOPAY], $response_array['trade_status']);
                $this->assertEquals('', $response_array['payok_ymdhis']);
                $this->assertEquals('', $response_array['payed_ymdhis']);
                $this->assertEquals('', $response_array['cancel_ymdhis']);
                $this->assertEquals('qrcode', $response_array['qr_code']);

                throw new RollbackException('rollback exception');
            });
        }catch(RollbackException $e){
            //
        }
    }

    public function testExecPayGeneral()
    {
        try{
            $Handler        = $this->getHandler();
            $this->Db->getManager()->transactional(function()use($Handler){
                $title              = 'testTitle' . mt_rand(0, 9999);
                $out_trade_no       = 'no' . date('ymdhis') . mt_rand(0, 9999);
                $total_fee          = mt_rand(0, 9999);
                $client_ip          = '192.168.3.' . mt_rand(0,255);
                $Request            = $this->getRequest([
                    'channel'       => 'TEST_PAY_GENERAL',
                    'title'         => $title,
                    'out_trade_no'  => $out_trade_no,
                    'total_fee'     => $total_fee,
                    'client_ip'     => $client_ip,
                    'notify_url'    => 'notify_url',
                    'return_url'    => 'return_url',
                ]);
                $PayResponse    = $Handler->exec($Request);
                $response_array = $PayResponse->getObjectVars();

                $this->assertEquals('TEST_PAY_GENERAL', $response_array['channel']);
                $this->assertNotEmpty($response_array['in_trade_no']);
                $this->assertEquals($title, $response_array['title']);
                $this->assertEquals($out_trade_no, $response_array['out_trade_no']);
                $this->assertEquals($total_fee, $response_array['total_fee']);
                $this->assertEquals($client_ip, $response_array['client_ip']);
                $this->assertEquals(Constant::getTradePayTradeStatusNames()[Constant::TRADE_PAY_TRADE_STATUS_NOPAY], $response_array['trade_status']);
                $this->assertEquals('', $response_array['payok_ymdhis']);
                $this->assertEquals('', $response_array['payed_ymdhis']);
                $this->assertEquals('', $response_array['cancel_ymdhis']);

                throw new RollbackException('rollback exception');
            });
        }catch(RollbackException $e){
            //
        }
    }

    private function getRequest(array $request)
    {
        $_REQUEST                       = $request;
        $Request                        = new ServerRequest();
        return new PayRequest($Request);
    }

    private function getHandler()
    {
        $Db                             = new Factory();
        $Db->addConnection(Connection::create([
            'driver'    => 'pdo_sqlite',
            'path'      => dirname(dirname(dirname(dirname(dirname(__DIR__))))) . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'db.sqlite'
        ], dirname(dirname(dirname(dirname(dirname(__DIR__))))) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'entity', Connection::MATADATA_YAML));
        $this->Db                       = $Db;

        $RouteCollection        = new RouteCollection();
        $Router                 = new Router($RouteCollection, new ServerRequest());
        $RouteCollection
        // notify 这个 id在 trade.pay接口中生成url时需要使用到
        ->add(new Route('notify', 'test-notify', function(){}))
        // return 这个 id在 trade.pay接口中生成url时需要使用到
        ->add(new Route('return', 'test-return', function(){}));

        $ChannelManager            = new ChannelManager();
        $TradePayRepository        = new TradePayRepository($Db);
        $TradePayManager           = new TradePayManager($Db, $TradePayRepository);
        $TradePayClobRepository    = new TradePayClobRepository($Db);
        $TradePayClobManager       = new TradePayClobManager($Db, $TradePayClobRepository);
        $Cancel                    = new Pay($ChannelManager, $Db, $TradePayManager, $TradePayClobManager, $Router);
        return $Cancel;
    }
}

if(!class_exists(RollbackException::class)){
    class RollbackException extends \RuntimeException{}
}