<?php
namespace asbamboo\openpay\_test\notify\v1_0\trade;

use PHPUnit\Framework\TestCase;
use asbamboo\openpay\notify\v1_0\trade\RefundNotify;
use asbamboo\database\Factory;
use asbamboo\database\FactoryInterface;
use asbamboo\database\Connection;
use asbamboo\openpay\_test\fixtures\channel\ChannelManager;
use asbamboo\openpay\model\tradePay\TradePayManager;
use asbamboo\openpay\model\tradePay\TradePayRepository;
use asbamboo\http\ServerRequest;
use asbamboo\openpay\Constant;
use asbamboo\openpay\model\tradePay\TradePayEntity;
use asbamboo\openpay\model\tradeRefund\TradeRefundManager;
use asbamboo\openpay\model\tradeRefund\TradeRefundRepository;

/**
 * - 通知支付成功（不可退款）
 * - 通知支付成功（可退款）
 * - 通知支付失败（不可退款）
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年11月14日
 */
class RefundNotifyTest extends TestCase
{
    /**
     *
     * @var FactoryInterface
     */
    public static $Db;

    /**
     * $_REQUEST
     *
     * @var array
     */
    public $_request;

    /**
     *
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    public function setUp()
    {
        $this->_request = $_REQUEST;
    }

    /**
     *
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::tearDown()
     */
    public function tearDown()
    {
        $_REQUEST   = $this->_request;
    }

    /**
     *
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::setUpBeforeClass()
     */
    public static function setUpBeforeClass()
    {
        static::$Db     = new Factory();
        static::$Db->addConnection(Connection::create([
            'driver'    => 'pdo_sqlite',
            'path'      => dirname(dirname(dirname(dirname(__DIR__)))) . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'db.sqlite'
        ], dirname(dirname(dirname(dirname(__DIR__)))) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'entity', Connection::MATADATA_YAML));
        static::$Db->getManager()->beginTransaction();
    }

    /**
     *
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::tearDownAfterClass()
     */
    public static function tearDownAfterClass()
    {
        static::$Db->getManager()->rollback();
    }

     /**
     *
     */
    public function testExecRefundSuccess()
    {
        $channel            = 'TEST-REFUNDING';
        $title              = 'title' . mt_rand(0,999);
        $total_fee          = mt_rand(1,999);
        $out_trade_no       = 'out_trade_no' . mt_rand(0,999);
        $out_refund_no      = 'out_refund_no' . mt_rand(0,999);
        $refund_fee         = $total_fee;
        $notify_url         = '';
        $client_ip          = mt_rand(0,255) . '.' . mt_rand(0,255) . '.' . mt_rand(0,255) . '.' . mt_rand(0,255);

        $ChannelManager             = new ChannelManager();
        $TradePayRepository         = new TradePayRepository(static::$Db);
        $TradePayManager            = new TradePayManager(static::$Db, $TradePayRepository);
        $TradeRefundRepository      = new TradeRefundRepository(static::$Db);
        $TradeRefundManager         = new TradeRefundManager(static::$Db, $TradeRefundRepository);
        $Request                    = new ServerRequest();
        $RefundNotify               = new RefundNotify($ChannelManager, $Request, $TradeRefundManager, static::$Db);

        $TradePayEntity             = $TradePayManager->load();
        $TradePayManager->insert($channel, $title, $total_fee, $out_trade_no, $client_ip, '', '');
        $TradePayManager->updateTradeStatusToPayok('third_trade_no');
        static::$Db->getManager()->flush($TradePayEntity);

        $TradeRefundEntity          = $TradeRefundManager->load();
        $TradeRefundManager->insert($TradePayEntity, $out_refund_no, $refund_fee, $notify_url);
        static::$Db->getManager()->flush($TradeRefundEntity);
        
        
        $_REQUEST['in_refund_no']       = $TradeRefundEntity->getInRefundNo();
        $_REQUEST['test_refund_status'] = Constant::TRADE_REFUND_STATUS_SUCCESS;

        $Response                       = $RefundNotify->exec($TradePayEntity->getChannel());
        $response_body                  = $Response->getBody()->getContents();
        $this->assertEquals('SUCCESS', $response_body);
        $this->assertEquals(Constant::TRADE_REFUND_STATUS_SUCCESS, $TradeRefundEntity->getStatus());
        $this->assertNotEmpty($TradeRefundEntity->getPayTime());
    }

    public function testExecRefundFailed()
    {
        $channel            = 'TEST-REFUNDING';
        $title              = 'title' . mt_rand(0,999);
        $total_fee          = mt_rand(1,999);
        $out_trade_no       = 'out_trade_no' . mt_rand(0,999);
        $out_refund_no      = 'out_refund_no' . mt_rand(0,999);
        $refund_fee         = $total_fee;
        $notify_url         = '';
        $client_ip          = mt_rand(0,255) . '.' . mt_rand(0,255) . '.' . mt_rand(0,255) . '.' . mt_rand(0,255);
        
        $ChannelManager             = new ChannelManager();
        $TradePayRepository         = new TradePayRepository(static::$Db);
        $TradePayManager            = new TradePayManager(static::$Db, $TradePayRepository);
        $TradeRefundRepository      = new TradeRefundRepository(static::$Db);
        $TradeRefundManager         = new TradeRefundManager(static::$Db, $TradeRefundRepository);
        $Request                    = new ServerRequest();
        $RefundNotify                  = new RefundNotify($ChannelManager, $Request, $TradeRefundManager, static::$Db);
        
        $TradePayEntity             = $TradePayManager->load();
        $TradePayManager->insert($channel, $title, $total_fee, $out_trade_no, $client_ip, '', '');
        $TradePayManager->updateTradeStatusToPayok('third_trade_no');
        static::$Db->getManager()->flush($TradePayEntity);
        
        $TradeRefundEntity          = $TradeRefundManager->load();
        $TradeRefundManager->insert($TradePayEntity, $out_refund_no, $refund_fee, $notify_url);
        static::$Db->getManager()->flush($TradeRefundEntity);
        
        
        $_REQUEST['in_refund_no']       = $TradeRefundEntity->getInRefundNo();
        $_REQUEST['test_refund_status'] = Constant::TRADE_REFUND_STATUS_FAILED;
        
        $Response                       = $RefundNotify->exec($TradePayEntity->getChannel());
        $response_body                  = $Response->getBody()->getContents();
        $this->assertEquals('SUCCESS', $response_body);
        $this->assertEquals(Constant::TRADE_REFUND_STATUS_FAILED, $TradeRefundEntity->getStatus());
    }
}
