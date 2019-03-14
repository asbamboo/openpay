<?php
namespace asbamboo\openpay\_test\notify\v1_0\trade;

use PHPUnit\Framework\TestCase;
use asbamboo\openpay\notify\v1_0\trade\PayNotify;
use asbamboo\database\Factory;
use asbamboo\database\FactoryInterface;
use asbamboo\database\Connection;
use asbamboo\openpay\_test\fixtures\channel\ChannelManager;
use asbamboo\openpay\model\tradePay\TradePayManager;
use asbamboo\openpay\model\tradePay\TradePayRepository;
use asbamboo\http\ServerRequest;
use asbamboo\openpay\Constant;
use asbamboo\openpay\model\tradePay\TradePayEntity;

/**
 * - 通知支付成功（不可退款）
 * - 通知支付成功（可退款）
 * - 通知支付失败（不可退款）
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年11月14日
 */
class PayNotifyTest extends TestCase
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
    public function testExecPayok()
    {
        $channel            = 'TEST_PAY_PC';
        $title              = 'title' . mt_rand(0,999);
        $total_fee          = mt_rand(1,999);
        $out_trade_no       = 'out_trade_no' . mt_rand(0,999);
        $client_ip          = mt_rand(0,255) . '.' . mt_rand(0,255) . '.' . mt_rand(0,255) . '.' . mt_rand(0,255);

        $ChannelManager             = new ChannelManager();
        $TradePayRepository         = new TradePayRepository(static::$Db);
        $TradePayManager            = new TradePayManager(static::$Db, $TradePayRepository);
        $Request                    = new ServerRequest();
        $PayNotify                  = new PayNotify($ChannelManager, $Request, $TradePayManager, $TradePayRepository, static::$Db);

        $TradePayEntity             = $TradePayManager->load();
        $TradePayManager->insert('TEST_PAY_PC', $title, $total_fee, $out_trade_no, $client_ip, '', '');
        static::$Db->getManager()->flush($TradePayEntity);
        $_REQUEST['in_trade_no']        = $TradePayEntity->getInTradeNo();
        $_REQUEST['test_pay_status']    = Constant::TRADE_PAY_TRADE_STATUS_PAYOK;

        $Response                       = $PayNotify->exec($TradePayEntity->getChannel());
        $response_body                  = $Response->getBody()->getContents();
        $this->assertEquals('SUCCESS', $response_body);
        $this->assertEquals(Constant::TRADE_PAY_TRADE_STATUS_PAYOK, $TradePayEntity->getTradeStatus());
        $this->assertEquals('third_trade_no', $TradePayEntity->getThirdTradeNo());
        $this->assertNotEmpty($TradePayEntity->getPayokTime());
    }

    public function testExecPayed()
    {
        $channel            = 'channel' . mt_rand(0,999);
        $title              = 'title' . mt_rand(0,999);
        $total_fee          = mt_rand(0,999);
        $out_trade_no       = 'out_trade_no' . mt_rand(0,999);
        $client_ip          = mt_rand(0,255) . '.' . mt_rand(0,255) . '.' . mt_rand(0,255) . '.' . mt_rand(0,255);

        $ChannelManager             = new ChannelManager();
        $TradePayRepository         = new TradePayRepository(static::$Db);
        $TradePayManager            = new TradePayManager(static::$Db, $TradePayRepository);
        $Request                    = new ServerRequest();
        $PayNotify                  = new PayNotify($ChannelManager, $Request, $TradePayManager, $TradePayRepository, static::$Db);

        $TradePayEntity             = new TradePayEntity();
        $TradePayEntity             = $TradePayManager->load();
        $TradePayManager->insert('TEST_PAY_PC', $title, $total_fee, $out_trade_no, $client_ip, '', '');
        static::$Db->getManager()->flush($TradePayEntity);
        $_REQUEST['in_trade_no']        = $TradePayEntity->getInTradeNo();
        $_REQUEST['test_pay_status']    = Constant::TRADE_PAY_TRADE_STATUS_PAYED;

        $Response                       = $PayNotify->exec($TradePayEntity->getChannel());
        $response_body                  = $Response->getBody()->getContents();
        $this->assertEquals('SUCCESS', $response_body);
        $this->assertEquals(Constant::TRADE_PAY_TRADE_STATUS_PAYED, $TradePayEntity->getTradeStatus());
        $this->assertEquals('third_trade_no', $TradePayEntity->getThirdTradeNo());
        $this->assertNotEmpty($TradePayEntity->getPayokTime());
        $this->assertNotEmpty($TradePayEntity->getPayedTime());
    }

    public function testExecCancel()
    {
        $channel            = 'channel' . mt_rand(0,999);
        $title              = 'title' . mt_rand(0,999);
        $total_fee          = mt_rand(0,999);
        $out_trade_no       = 'out_trade_no' . mt_rand(0,999);
        $client_ip          = mt_rand(0,255) . '.' . mt_rand(0,255) . '.' . mt_rand(0,255) . '.' . mt_rand(0,255);

        $ChannelManager             = new ChannelManager();
        $TradePayRepository         = new TradePayRepository(static::$Db);
        $TradePayManager            = new TradePayManager(static::$Db, $TradePayRepository);
        $Request                    = new ServerRequest();
        $PayNotify                  = new PayNotify($ChannelManager, $Request, $TradePayManager, $TradePayRepository, static::$Db);

        $TradePayEntity             = $TradePayManager->load();
        $TradePayManager->insert('TEST_PAY_PC', $title, $total_fee, $out_trade_no, $client_ip, '', '');
        static::$Db->getManager()->flush($TradePayEntity);
        $_REQUEST['in_trade_no']        = $TradePayEntity->getInTradeNo();
        $_REQUEST['test_pay_status']    = Constant::TRADE_PAY_TRADE_STATUS_CANCEL;

        $Response                       = $PayNotify->exec($TradePayEntity->getChannel());
        $response_body                  = $Response->getBody()->getContents();
        $this->assertEquals('SUCCESS', $response_body);
        $this->assertEquals(Constant::TRADE_PAY_TRADE_STATUS_CANCEL, $TradePayEntity->getTradeStatus());
        $this->assertEquals('third_trade_no', $TradePayEntity->getThirdTradeNo());
        $this->assertNotEmpty($TradePayEntity->getCancelTime());
    }
}
