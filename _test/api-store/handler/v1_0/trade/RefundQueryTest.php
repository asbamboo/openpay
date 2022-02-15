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
use asbamboo\openpay\apiStore\handler\v1_0\trade\RefundQuery;
use asbamboo\openpay\apiStore\parameter\v1_0\trade\refundQuery\RefundQueryRequest;

/**
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年11月13日
 */
class RefundQueryTest extends TestCase
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
            'in_refund_no'   => 'not_found',
        ]);
        $Handler            = $this->getHandler();
        $Handler->exec($Request);
    }

    public function testExecNotFindOutTradeNo()
    {
        $this->expectException(TradeRefundNotFoundInvalidException::class);
        $Request            = $this->getRequest([
            'in_refund_no'   => 'not_found',
        ]);
        $Handler            = $this->getHandler();
        $Handler->exec($Request);
    }

    public function testExecRefundProcessing()
    {
        try{
            $Handler    = $this->getHandler();
            $this->Db->getManager()->transactional(function()use($Handler){
                
                $ip                 = mt_rand(0,255) . '.0.0.1';                
                $notify_url         = '';
                $in_trade_no        = date('ymdhis') . mt_rand(0, 999);
                $out_trade_no       = 'OT' . date('ymdhis') . mt_rand(0, 999);
                $in_refund_no       = date('ymdhis') . mt_rand(0, 999);
                $out_refund_no      = 'OF' . date('ymdhis') . mt_rand(0, 999);
                $pay_time           = 0;
                $request_time       = time();
                $response_time      = time();
                $refund_fee         = mt_rand(0, 999);

                $TradePayEntity     = new TradePayEntity();
                $TradePayEntity->setClientIp($ip);
                $TradePayEntity->setChannel('TEST-REFUND-QUERY-REQUEST');
                $TradePayEntity->setTradeStatus(Constant::TRADE_PAY_TRADE_STATUS_PAYOK);
                $TradePayEntity->setInTradeNo($in_trade_no);
                $TradePayEntity->setTotalFee($refund_fee);
                $this->Db->getManager()->persist($TradePayEntity);
                
                $TradeRefundEntity  = new TradeRefundEntity();
                $TradeRefundEntity->setInRefundNo($in_refund_no);
                $TradeRefundEntity->setInTradeNo($in_trade_no);
                $TradeRefundEntity->setNotifyUrl($notify_url);
                $TradeRefundEntity->setOutRefundNo($out_refund_no);
                $TradeRefundEntity->setOutTradeNo($out_trade_no);
                $TradeRefundEntity->setPayTime($pay_time);
                $TradeRefundEntity->setRefundFee($refund_fee);
                $TradeRefundEntity->setStatus(Constant::TRADE_REFUND_STATUS_REQUEST);
                $TradeRefundEntity->setRequestTime($request_time);
                $TradeRefundEntity->setResponseTime($response_time);
                
                $this->Db->getManager()->persist($TradeRefundEntity);
                $this->Db->getManager()->flush();
                
                $Request            = $this->getRequest([
                    'out_refund_no' => $out_refund_no,
                ]);
                
                $ChannelResponse    = $Handler->exec($Request);
                $response_array     = $ChannelResponse->getObjectVars();
                
                $this->assertEquals($in_trade_no, $response_array['in_trade_no']);
                $this->assertEquals($TradeRefundEntity->getOutTradeNo(), $response_array['out_trade_no']);
                $this->assertNotEmpty($response_array['in_refund_no']);
                $this->assertEquals($out_refund_no, $response_array['out_refund_no']);
                $this->assertEquals($refund_fee, $response_array['refund_fee']);
                $this->assertEquals(Constant::getTradeRefundStatusNames()[Constant::TRADE_REFUND_STATUS_REQUEST], $response_array['refund_status']);
                $this->assertEmpty($response_array['refund_pay_ymdhis']);
                
                throw new RollbackException('Rollback');
            });
        }catch(RollbackException $e){
            //
        }
    }
    
    public function testExecRefundFailed()
    {
        try{
            $Handler    = $this->getHandler();
            $this->Db->getManager()->transactional(function()use($Handler){
                
                $ip                 = mt_rand(0,255) . '.0.0.1';
                $notify_url         = '';
                $in_trade_no        = date('ymdhis') . mt_rand(0, 999);
                $out_trade_no       = 'OT' . date('ymdhis') . mt_rand(0, 999);
                $in_refund_no       = date('ymdhis') . mt_rand(0, 999);
                $out_refund_no      = 'OF' . date('ymdhis') . mt_rand(0, 999);
                $pay_time           = 0;
                $request_time       = time();
                $response_time      = time();
                $refund_fee         = mt_rand(0, 999);
                
                $TradePayEntity     = new TradePayEntity();
                $TradePayEntity->setClientIp($ip);
                $TradePayEntity->setChannel('TEST-REFUND-QUERY-FAILED');
                $TradePayEntity->setTradeStatus(Constant::TRADE_PAY_TRADE_STATUS_PAYOK);
                $TradePayEntity->setInTradeNo($in_trade_no);
                $TradePayEntity->setTotalFee($refund_fee);
                $this->Db->getManager()->persist($TradePayEntity);
                
                $TradeRefundEntity  = new TradeRefundEntity();
                $TradeRefundEntity->setInRefundNo($in_refund_no);
                $TradeRefundEntity->setInTradeNo($in_trade_no);
                $TradeRefundEntity->setNotifyUrl($notify_url);
                $TradeRefundEntity->setOutRefundNo($out_refund_no);
                $TradeRefundEntity->setOutTradeNo($out_trade_no);
                $TradeRefundEntity->setPayTime($pay_time);
                $TradeRefundEntity->setRefundFee($refund_fee);
                $TradeRefundEntity->setStatus(Constant::TRADE_REFUND_STATUS_REQUEST);
                $TradeRefundEntity->setRequestTime($request_time);
                $TradeRefundEntity->setResponseTime($response_time);
                
                $this->Db->getManager()->persist($TradeRefundEntity);
                $this->Db->getManager()->flush();
                
                $Request            = $this->getRequest([
                    'out_refund_no' => $out_refund_no,
                ]);
                
                $ChannelResponse    = $Handler->exec($Request);
                $response_array     = $ChannelResponse->getObjectVars();
                
                $this->assertEquals($in_trade_no, $response_array['in_trade_no']);
                $this->assertEquals($TradeRefundEntity->getOutTradeNo(), $response_array['out_trade_no']);
                $this->assertNotEmpty($response_array['in_refund_no']);
                $this->assertEquals($out_refund_no, $response_array['out_refund_no']);
                $this->assertEquals($refund_fee, $response_array['refund_fee']);
                $this->assertEquals(Constant::getTradeRefundStatusNames()[Constant::TRADE_REFUND_STATUS_FAILED], $response_array['refund_status']);
                $this->assertEmpty($response_array['refund_pay_ymdhis']);
                
                throw new RollbackException('Rollback');
            });
        }catch(RollbackException $e){
            //
        }
    }
    
    public function testExecRefundSuccess()
    {
        try{
            $Handler    = $this->getHandler();
            $this->Db->getManager()->transactional(function()use($Handler){
                
                $ip                 = mt_rand(0,255) . '.0.0.1';
                $notify_url         = '';
                $in_trade_no        = date('ymdhis') . mt_rand(0, 999);
                $out_trade_no       = 'OT' . date('ymdhis') . mt_rand(0, 999);
                $in_refund_no       = date('ymdhis') . mt_rand(0, 999);
                $out_refund_no      = 'OF' . date('ymdhis') . mt_rand(0, 999);
                $pay_time           = 0;
                $request_time       = time();
                $response_time      = time();
                $refund_fee         = mt_rand(0, 999);
                
                $TradePayEntity     = new TradePayEntity();
                $TradePayEntity->setClientIp($ip);
                $TradePayEntity->setChannel('TEST-REFUND-QUERY-SUCCESS');
                $TradePayEntity->setTradeStatus(Constant::TRADE_PAY_TRADE_STATUS_PAYOK);
                $TradePayEntity->setInTradeNo($in_trade_no);
                $TradePayEntity->setTotalFee($refund_fee);
                $this->Db->getManager()->persist($TradePayEntity);
                
                $TradeRefundEntity  = new TradeRefundEntity();
                $TradeRefundEntity->setInRefundNo($in_refund_no);
                $TradeRefundEntity->setInTradeNo($in_trade_no);
                $TradeRefundEntity->setNotifyUrl($notify_url);
                $TradeRefundEntity->setOutRefundNo($out_refund_no);
                $TradeRefundEntity->setOutTradeNo($out_trade_no);
                $TradeRefundEntity->setPayTime($pay_time);
                $TradeRefundEntity->setRefundFee($refund_fee);
                $TradeRefundEntity->setStatus(Constant::TRADE_REFUND_STATUS_REQUEST);
                $TradeRefundEntity->setRequestTime($request_time);
                $TradeRefundEntity->setResponseTime($response_time);
                
                $this->Db->getManager()->persist($TradeRefundEntity);
                $this->Db->getManager()->flush();
                
                $Request            = $this->getRequest([
                    'in_refund_no'  => $in_refund_no,
                ]);
                
                $ChannelResponse    = $Handler->exec($Request);
                $response_array     = $ChannelResponse->getObjectVars();
                
                $this->assertEquals($in_trade_no, $response_array['in_trade_no']);
                $this->assertEquals($TradeRefundEntity->getOutTradeNo(), $response_array['out_trade_no']);
                $this->assertNotEmpty($response_array['in_refund_no']);
                $this->assertEquals($out_refund_no, $response_array['out_refund_no']);
                $this->assertEquals($refund_fee, $response_array['refund_fee']);
                $this->assertEquals(Constant::getTradeRefundStatusNames()[Constant::TRADE_REFUND_STATUS_SUCCESS], $response_array['refund_status']);
                $this->assertNotEmpty($response_array['refund_pay_ymdhis']);
                
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
        return new RefundQueryRequest($Request);
    }

    private function getHandler()
    {
        $Db                             = new Factory();
        $Db->addConnection(Connection::create([
            'driver'    => 'pdo_sqlite',
            'path'      => dirname(dirname(dirname(dirname(dirname(__DIR__))))) . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'db.sqlite'
        ], dirname(dirname(dirname(dirname(dirname(__DIR__))))) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'entity', Connection::MATADATA_YAML));
        $this->Db                       = $Db;

        $ChannelManager                 = new ChannelManager();
        $TradePayRepository            = new TradePayRepository($Db);
        $TradeRefundRepository         = new TradeRefundRepository($Db);
        $TradeRefundManager            = new TradeRefundManager($Db, $TradeRefundRepository);
        $ChannelHandler                = new RefundQuery(
            $Db, 
            $TradeRefundRepository, 
            $TradeRefundManager, 
            $TradePayRepository,
            $ChannelManager
        );
        return $ChannelHandler;
    }
}

if(!class_exists(RollbackException::class)){
    class RollbackException extends \RuntimeException{}
}