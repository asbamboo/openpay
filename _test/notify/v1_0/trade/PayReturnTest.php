<?php
namespace asbamboo\openpay\_test\notify\v1_0\trade;

use PHPUnit\Framework\TestCase;
use asbamboo\database\FactoryInterface;
use asbamboo\database\Factory;
use asbamboo\database\Connection;
use asbamboo\openpay\_test\fixtures\channel\ChannelManager;
use asbamboo\openpay\model\tradePay\TradePayManager;
use asbamboo\openpay\model\tradePay\TradePayRespository;
use asbamboo\http\ServerRequest;
use asbamboo\openpay\Constant;
use asbamboo\openpay\notify\v1_0\trade\PayReturn;
use asbamboo\api\apiStore\ApiResponseRedirectParamsInterface;
use asbamboo\http\RedirectResponse;

/**
 * - 基础功能测试
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年11月15日
 */
class PayReturnTest extends TestCase
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
            'path'      => dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'db.sqlite'
        ], dirname(dirname(dirname(dirname(__DIR__)))) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database', Connection::MATADATA_YAML));
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
        $channel            = 'channel' . mt_rand(0,999);
        $title              = 'title' . mt_rand(0,999);
        $total_fee          = mt_rand(0,999);
        $out_trade_no       = 'out_trade_no' . mt_rand(0,999);
        $client_ip          = mt_rand(0,255) . '.' . mt_rand(0,255) . '.' . mt_rand(0,255) . '.' . mt_rand(0,255);

        $ChannelManager             = new ChannelManager();
        $TradePayManager            = new TradePayManager(static::$Db);
        $TradePayRespository        = new TradePayRespository(static::$Db);
        $Request                    = new ServerRequest();
        $PayReturn                  = new PayReturn($ChannelManager, $Request, $TradePayManager, $TradePayRespository, static::$Db);

        $TradePayEntity             = $TradePayManager->insert('TEST_PAY_PC', $title, $total_fee, $out_trade_no, $client_ip, 'notify_url', 'return_url');
        static::$Db->getManager()->flush($TradePayEntity);

        $_REQUEST['in_trade_no']        = $TradePayEntity->getInTradeNo();
        $_REQUEST['test_pay_status']    = Constant::TRADE_PAY_TRADE_STATUS_PAYOK;

        $Response                       = $PayReturn->exec($TradePayEntity->getChannel());
        $this->assertInstanceOf(RedirectResponse::class, $Response);

        $this->assertEquals(Constant::TRADE_PAY_TRADE_STATUS_PAYOK, $TradePayEntity->getTradeStatus());
        $this->assertEquals('third_trade_no', $TradePayEntity->getThirdTradeNo());
        $this->assertNotEmpty($TradePayEntity->getPayokTime());
    }
}