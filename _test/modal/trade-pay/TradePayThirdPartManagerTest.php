<?php
namespace asbamboo\openpay\_test\apiStore\modal\tradePay;

use PHPUnit\Framework\TestCase;
use asbamboo\database\Factory;
use asbamboo\database\Connection;
use asbamboo\database\FactoryInterface;
use asbamboo\openpay\model\tradePayThirdPart\TradePayThirdPartManager;
use asbamboo\openpay\model\tradePay\TradePayManager;
use asbamboo\openpay\model\tradePay\TradePayEntity;
use asbamboo\openpay\model\tradePayThirdPart\TradePayThirdPartEntity;

/**
 * - 测试insert
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年11月14日
 */
class TradePayThirdPartManagerTest extends TestCase
{
    /**
     *
     * @var FactoryInterface
     */
    public static $Db;

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
            'path'      => dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'db.sqlite'
        ], dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database', Connection::MATADATA_YAML));
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
     * @return \asbamboo\openpay\model\tradePay\TradePayEntity
     */
    public function testInsert()
    {
        $channel            = 'channel' . mt_rand(0,999);
        $title              = 'title' . mt_rand(0,999);
        $total_fee          = mt_rand(0,999);
        $out_trade_no       = 'out_trade_no' . mt_rand(0,999);
        $client_ip          = mt_rand(0,255) . '.' . mt_rand(0,255) . '.' . mt_rand(0,255) . '.' . mt_rand(0,255);
        $notify_url         = 'notify_url' . mt_rand(0, 999);
        $return_url         = 'return_url' . mt_rand(0, 999);
        $TradePayManager    = new TradePayManager(static::$Db);
        $TradePayEntity     = new TradePayEntity();
        $TradePayManager->load($TradePayEntity)->insert($channel, $title, $total_fee, $out_trade_no, $client_ip, $notify_url, $return_url);

        $send_data                  = json_encode('send_data' . mt_rand(0, 999));
        $TradePayThirdPartManager   = new TradePayThirdPartManager(static::$Db);
        $TradePayThirdPartEntity    = new TradePayThirdPartEntity();
        $TradePayThirdPartManager->load($TradePayThirdPartEntity)->insert($TradePayEntity, $send_data);

        $this->assertEquals($TradePayEntity->getInTradeNo(), $TradePayThirdPartEntity->getInTradeNo());
        $this->assertEquals($send_data, $TradePayThirdPartEntity->getSendData());
    }
}