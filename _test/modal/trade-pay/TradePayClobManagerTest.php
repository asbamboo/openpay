<?php
namespace asbamboo\openpay\_test\apiStore\modal\tradePay;

use PHPUnit\Framework\TestCase;
use asbamboo\database\Factory;
use asbamboo\database\Connection;
use asbamboo\database\FactoryInterface;
use asbamboo\openpay\model\tradePayClob\TradePayClobManager;
use asbamboo\openpay\model\tradePay\TradePayManager;
use asbamboo\openpay\model\tradePay\TradePayRepository;
use asbamboo\openpay\model\tradePayClob\TradePayClobRepository;
use asbamboo\openpay\model\tradePayClob\TradePayClobEntity;

/**
 * - 测试insert
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年11月14日
 */
class TradePayClobManagerTest extends TestCase
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
            'path'      => dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'db.sqlite'
        ], dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'entity', Connection::MATADATA_YAML));
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
        $TradePayRepository = new TradePayRepository(static::$Db);
        $TradePayManager    = new TradePayManager(static::$Db, $TradePayRepository);
        $TradePayEntity     = $TradePayManager->load();
        $TradePayManager->insert($channel, $title, $total_fee, $out_trade_no, $client_ip, $notify_url, $return_url);
        static::$Db->getManager()->flush();

        $third_part                = json_encode('third_part' . mt_rand(0, 999));
        $TradePayClobRepository    = new TradePayClobRepository(static::$Db);
        $TradePayClobManager       = new TradePayClobManager(static::$Db, $TradePayClobRepository);
        $TradePayClobEntity        = $TradePayClobManager->load($TradePayEntity->getInTradeNo());
        $TradePayClobManager->insert($TradePayEntity, $third_part);

        $this->assertEquals($TradePayEntity->getInTradeNo(), $TradePayClobEntity->getInTradeNo());
        $this->assertEquals($third_part, $TradePayClobEntity->getThirdPart());

        return $TradePayClobEntity;
    }

    /**
     * @depends testInsert
     */
    public function testUpdateAppPayJson(TradePayClobEntity $TradePayClobEntity)
    {
        $app_pay_json       = '{"k":"v"}';

        $TradePayClobRepository = new TradePayClobRepository(static::$Db);
        $TradePayClobManager    = new TradePayClobManager(static::$Db, $TradePayClobRepository);
        $TradePayClobEntity     = $TradePayClobManager->load($TradePayClobEntity->getInTradeNo());
        $TradePayClobManager->updateAppPayJson($app_pay_json);

        $this->assertEquals($app_pay_json, $TradePayClobEntity->getAppPayJson());
    }
}