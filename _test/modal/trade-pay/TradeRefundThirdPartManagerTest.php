<?php
namespace asbamboo\openpay\_test\apiStore\modal\tradePay;

use PHPUnit\Framework\TestCase;
use asbamboo\database\FactoryInterface;
use asbamboo\database\Factory;
use asbamboo\database\Connection;
use asbamboo\openpay\model\tradePay\TradePayManager;
use asbamboo\openpay\model\tradePayThirdPart\TradePayThirdPartManager;
use asbamboo\openpay\model\tradeRefund\TradeRefundEntity;
use asbamboo\openpay\model\tradeRefundThirdPart\TradeRefundThirdPartManager;
use asbamboo\openpay\model\tradePay\TradePayEntity;
use asbamboo\openpay\model\tradePayThirdPart\TradePayThirdPartEntity;
use asbamboo\openpay\model\tradeRefundThirdPart\TradeRefundThirdPartEntity;
use asbamboo\openpay\model\tradePay\TradePayRepository;
use asbamboo\openpay\model\tradePayThirdPart\TradePayThirdPartRepository;
use asbamboo\openpay\model\tradeRefundThirdPart\TradeRefundThirdPartRepository;

/**
 * - 测试insert方法
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年11月14日
 */
class TradeRefundThirdPartManagerTest extends TestCase
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
        $TradePayRepository = new TradePayRepository(static::$Db);
        $TradePayManager    = new TradePayManager(static::$Db, $TradePayRepository);
        $TradePayEntity     = $TradePayManager->load();
        $TradePayManager->insert($channel, $title, $total_fee, $out_trade_no, $client_ip, $notify_url, $return_url);

        $send_data                      = json_encode('send_data' . mt_rand(0, 999));
        $TradePayThirdPartRepository    = new TradePayThirdPartRepository(static::$Db);
        $TradePayThirdPartManager       = new TradePayThirdPartManager(static::$Db, $TradePayThirdPartRepository);
        $TradePayThirdPartEntity        = $TradePayThirdPartManager->load();
        $TradePayThirdPartManager->insert($TradePayEntity, $send_data);

        $in_refund_no       = date('ymdhis') . mt_rand(0, 999);
        $out_refund_no      = 'out_refund_no' . mt_rand(0, 999);
        $refund_fee         = $total_fee;
        $TradeRefundEntity   = new TradeRefundEntity();
        $TradeRefundEntity->setInRefundNo($in_refund_no);
        $TradeRefundEntity->setOutRefundNo($out_refund_no);
        $TradeRefundEntity->setInTradeNo($TradePayEntity->getInTradeNo());
        $TradeRefundEntity->setRefundFee($refund_fee);

        static::$Db->getManager()->persist($TradePayEntity);
        static::$Db->getManager()->persist($TradePayThirdPartEntity);
        static::$Db->getManager()->persist($TradeRefundEntity);
        static::$Db->getManager()->flush();

        $send_data                      = json_encode('send_data' . mt_rand(0, 9999));
        $TradeRefundThirdPartRepository = new TradeRefundThirdPartRepository(static::$Db);
        $TradeRefundThirdPartManager    = new TradeRefundThirdPartManager(static::$Db, $TradeRefundThirdPartRepository);
        $TradeRefundThirdPartEntity     = $TradeRefundThirdPartManager->load();
        $TradeRefundThirdPartManager->insert($TradeRefundEntity, $send_data);

        $this->assertEquals($in_refund_no, $TradeRefundThirdPartEntity->getInRefundNo());
        $this->assertEquals($send_data, $TradeRefundThirdPartEntity->getSendData());
    }
}