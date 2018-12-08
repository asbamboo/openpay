<?php
namespace asbamboo\openpay\_test\apiStore\modal\tradePay;

use PHPUnit\Framework\TestCase;
use asbamboo\database\Connection;
use asbamboo\database\Factory;
use asbamboo\database\FactoryInterface;
use asbamboo\openpay\model\tradePay\TradePayManager;
use asbamboo\openpay\Constant;
use asbamboo\openpay\model\tradePay\TradePayEntity;
use asbamboo\openpay\apiStore\exception\TradePayTradeStatusInvalidException;

/**
 * - 测试创建支付交易
 * - 测试取消支付
 * - 支付成功（可退款）
 * - 支付成功（不可退款）
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年11月14日
 */
class TradePayManagerTest extends TestCase
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

        $this->assertEquals('0', $TradePayEntity->getCancelTime());
        $this->assertEquals($channel, $TradePayEntity->getChannel());
        $this->assertEquals($client_ip, $TradePayEntity->getClientIp());
        $this->assertNotEmpty($TradePayEntity->getInTradeNo());
        $this->assertEquals($notify_url, $TradePayEntity->getNotifyUrl());
        $this->assertEquals($out_trade_no, $TradePayEntity->getOutTradeNo());
        $this->assertEquals('0', $TradePayEntity->getPayedTime());
        $this->assertEquals('0', $TradePayEntity->getPayokTime());
        $this->assertEquals($return_url, $TradePayEntity->getReturnUrl());
        $this->assertEquals('', $TradePayEntity->getThirdTradeNo());
        $this->assertEquals($title, $TradePayEntity->getTitle());
        $this->assertEquals($total_fee, $TradePayEntity->getTotalFee());
        $this->assertEquals(Constant::TRADE_PAY_TRADE_STATUS_NOPAY, $TradePayEntity->getTradeStatus());

        return $TradePayEntity;
    }

    /**
     * @depends testInsert
     */
    public function testUpdateTradeStatusToPayok(TradePayEntity $TradePayEntity)
    {
        $third_trade_no     = 'third_trade_no' . mt_rand(0, 999);

        $TradePayManager    = new TradePayManager(static::$Db);
        $TradePayManager->load($TradePayEntity)->updateTradeStatusToPayok($third_trade_no);

        $this->assertEquals($third_trade_no, $TradePayEntity->getThirdTradeNo());
        $this->assertEquals(Constant::TRADE_PAY_TRADE_STATUS_PAYOK, $TradePayEntity->getTradeStatus());
        $this->assertNotEmpty($TradePayEntity->getPayokTime());

        return $TradePayEntity;
    }

    /**
     * @depends testUpdateTradeStatusToPayok
     */
    public function testUpdateTradeStatusToPayokTradePayTradeStatusInvalidException(TradePayEntity $TradePayEntity)
    {
        $this->expectException(TradePayTradeStatusInvalidException::class);
        $third_trade_no     = 'third_trade_no' . mt_rand(0, 999);
        $TradePayManager    = new TradePayManager(static::$Db);
        $TradePayManager->load($TradePayEntity)->updateTradeStatusToPayok($third_trade_no);
    }

    /**
     *
     * @return \asbamboo\openpay\model\tradePay\TradePayEntity
     */
    public function testUpdateTradeStatusToPayed()
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

        $third_trade_no     = 'third_trade_no' . mt_rand(0, 999);

        $TradePayManager    = new TradePayManager(static::$Db);
        $TradePayManager->load($TradePayEntity)->updateTradeStatusToPayed($third_trade_no);

        $this->assertEquals($third_trade_no, $TradePayEntity->getThirdTradeNo());
        $this->assertEquals(Constant::TRADE_PAY_TRADE_STATUS_PAYED, $TradePayEntity->getTradeStatus());
        $this->assertNotEmpty($TradePayEntity->getPayokTime());
        $this->assertNotEmpty($TradePayEntity->getPayedTime());

        return $TradePayEntity;
    }

    /**
     * @depends testUpdateTradeStatusToPayed
     * @param TradePayEntity $TradePayEntity
     */
    public function testUpdateTradeStatusToPayedTradePayTradeStatusInvalidException(TradePayEntity $TradePayEntity)
    {
        $this->expectException(TradePayTradeStatusInvalidException::class);
        $third_trade_no     = 'third_trade_no' . mt_rand(0, 999);
        $TradePayManager    = new TradePayManager(static::$Db);
        $TradePayManager->load($TradePayEntity)->updateTradeStatusToPayed($third_trade_no);
    }

    /**
     *
     * @return \asbamboo\openpay\model\tradePay\TradePayEntity
     */
    public function testUpdateTradeStatusToCancel()
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


        $TradePayManager    = new TradePayManager(static::$Db);
        $TradePayManager->load($TradePayEntity)->updateTradeStatusToCancel();

        $this->assertEquals(Constant::TRADE_PAY_TRADE_STATUS_CANCEL, $TradePayEntity->getTradeStatus());
        $this->assertNotEmpty($TradePayEntity->getCancelTime());

        return $TradePayEntity;
    }

    /**
     * @depends testUpdateTradeStatusToCancel
     * @param TradePayEntity $TradePayEntity
     */
    public function testUpdateTradeStatusToCancelTradePayTradeStatusInvalidException(TradePayEntity $TradePayEntity)
    {
        $this->expectException(TradePayTradeStatusInvalidException::class);
        $third_trade_no     = 'third_trade_no' . mt_rand(0, 999);
        $TradePayManager    = new TradePayManager(static::$Db);
        $TradePayManager->load($TradePayEntity)->updateTradeStatusToCancel($third_trade_no);
    }
}
