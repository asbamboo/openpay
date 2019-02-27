<?php
namespace asbamboo\openpay\_test\apiStore\modal\tradePay;

use PHPUnit\Framework\TestCase;
use asbamboo\database\Factory;
use asbamboo\database\Connection;
use asbamboo\database\FactoryInterface;
use asbamboo\openpay\apiStore\exception\TradeRefundTradeStatusInvalidException;
use asbamboo\openpay\model\tradePay\TradePayEntity;
use asbamboo\openpay\Constant;
use asbamboo\openpay\model\tradePayClob\TradePayClobEntity;
use asbamboo\openpay\model\tradeRefund\TradeRefundManager;
use asbamboo\openpay\model\tradeRefund\TradeRefundRepository;
use asbamboo\openpay\apiStore\exception\TradeRefundRefundFeeInvalidException;
use asbamboo\openpay\model\tradeRefund\TradeRefundEntity;
use asbamboo\openpay\apiStore\exception\TradeRefundStatusInvalidException;

/**
 * - 测试创建退款单, 订单状态非支付成功（可退款时）不允许创建退款单
 * - 测试创建退款单, 一次性退款，退款金额大于订单金额时，不允许创建退款单
 * - 测试创建退款单, 订单分多笔退款，退款金额大于订单金额时，不允许创建退款单
 * - 正常测试创建退款单
 * - 像第三方渠道发起退款请求
 * - 像第三方渠道发起退款请求（状态不允许发起退款请求）
 * - 退款成功
 * - 退款成功（非请求中退款不允许修改为成功）
 * - 退款失败
 * - 退款失败（非请求中退款不允许修改为失败）
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年11月14日
 */
class TradeRefundManagerTest extends TestCase
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

    public function testInsertTradeRefundTradeStatusInvalidException()
    {
        $this->expectException(TradeRefundTradeStatusInvalidException::class);

        $title              = 'title' . mt_rand(0,999);
        $client_ip          = mt_rand(0,255) . '.0.0.1';
        $in_trade_no        = date('ymdhis') . mt_rand(0, 999);
        $out_trade_no       = 'o' . date('ymdhis') . mt_rand(0, 999);
        $total_fee          = mt_rand(0,9999);
        $TradePayEntity     = new TradePayEntity();
        $TradePayEntity->setClientIp($client_ip);
        $TradePayEntity->setChannel('TEST_REFUND_CANCEL');
        $TradePayEntity->setTitle($title);
        $TradePayEntity->setOutTradeNo($out_trade_no);
        $TradePayEntity->setTradeStatus(Constant::TRADE_PAY_TRADE_STATUS_NOPAY);
        $TradePayEntity->setInTradeNo($in_trade_no);
        $TradePayEntity->setTotalFee($total_fee);
        $TradePayClobEntity     = new TradePayClobEntity();
        $TradePayClobEntity->setInTradeNo($in_trade_no);
        $TradePayClobEntity->setThirdPart('');

        $out_refund_no          = 'out_refund_no' . mt_rand(0, 999);
        $refund_fee             = rand(0, $total_fee);
        $TradeRefundRepository = new TradeRefundRepository(static::$Db);
        $TradeRefundManager     = new TradeRefundManager(static::$Db, $TradeRefundRepository);
        $TradeRefundEntity      = $TradeRefundManager->load();
        $TradeRefundManager->insert($TradePayEntity, $out_refund_no, $refund_fee);
    }

    public function testInsertTradeRefundRefundFeeInvalidException1()
    {
        $this->expectException(TradeRefundRefundFeeInvalidException::class);

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
        $TradePayEntity->setTradeStatus(Constant::TRADE_PAY_TRADE_STATUS_PAYOK);
        $TradePayEntity->setInTradeNo($in_trade_no);
        $TradePayEntity->setTotalFee($total_fee);
        $TradePayClobEntity     = new TradePayClobEntity();
        $TradePayClobEntity->setInTradeNo($in_trade_no);
        $TradePayClobEntity->setThirdPart('');

        $out_refund_no          = 'out_refund_no' . mt_rand(0, 999);
        $refund_fee             = rand($total_fee+1, 999999);
        $TradeRefundRepository = new TradeRefundRepository(static::$Db);
        $TradeRefundManager     = new TradeRefundManager(static::$Db, $TradeRefundRepository);
        $TradeRefundEntity      = $TradeRefundManager->load();
        $TradeRefundManager->insert($TradePayEntity, $out_refund_no, $refund_fee);
    }

    public function testInsertTradeRefundRefundFeeInvalidException2()
    {

        $title              = 'title' . mt_rand(0,999);
        $client_ip          = mt_rand(0,255) . '.0.0.1';
        $in_trade_no        = date('ymdhis') . mt_rand(0, 999);
        $out_trade_no       = 'o' . date('ymdhis') . mt_rand(0, 999);
        $total_fee          = mt_rand(3,9999);
        $TradePayEntity     = new TradePayEntity();
        $TradePayEntity->setClientIp($client_ip);
        $TradePayEntity->setChannel('TEST_QUERY_CANCEL');
        $TradePayEntity->setTitle($title);
        $TradePayEntity->setOutTradeNo($out_trade_no);
        $TradePayEntity->setTradeStatus(Constant::TRADE_PAY_TRADE_STATUS_PAYOK);
        $TradePayEntity->setInTradeNo($in_trade_no);
        $TradePayEntity->setTotalFee($total_fee);
        $TradePayClobEntity     = new TradePayClobEntity();
        $TradePayClobEntity->setInTradeNo($in_trade_no);
        $TradePayClobEntity->setThirdPart('');

        $in_refund_no       = date('ymdhis') . mt_rand(0, 999);
        $out_refund_no      = 'out_refund_no' . mt_rand(0, 999);
        $refund_fee         = rand(1, $total_fee - 1);

        $TradeRefundEntity   = new TradeRefundEntity();
        $TradeRefundEntity->setInRefundNo($in_refund_no);
        $TradeRefundEntity->setOutRefundNo($out_refund_no);
        $TradeRefundEntity->setInTradeNo($TradePayEntity->getInTradeNo());
        $TradeRefundEntity->setRefundFee($refund_fee);

        static::$Db->getManager()->persist($TradePayEntity);
        static::$Db->getManager()->persist($TradePayClobEntity);
        static::$Db->getManager()->persist($TradeRefundEntity);
        static::$Db->getManager()->flush();


        $this->expectException(TradeRefundRefundFeeInvalidException::class);
        $out_refund_no          = 'out_refund_no' . mt_rand(0, 999);
        $refund_fee             = $total_fee - $refund_fee + 1;
        $TradeRefundRepository = new TradeRefundRepository(static::$Db);
        $TradeRefundManager     = new TradeRefundManager(static::$Db, $TradeRefundRepository);
        $TradeRefundEntity      = $TradeRefundManager->load();
        $TradeRefundManager->insert($TradePayEntity, $out_refund_no, $refund_fee);
    }

    public function testInsertOk()
    {

        $title              = 'title' . mt_rand(0,999);
        $client_ip          = mt_rand(0,255) . '.0.0.1';
        $in_trade_no        = date('ymdhis') . mt_rand(0, 999);
        $out_trade_no       = 'o' . date('ymdhis') . mt_rand(0, 999);
        $total_fee          = mt_rand(3,9999);
        $TradePayEntity     = new TradePayEntity();
        $TradePayEntity->setClientIp($client_ip);
        $TradePayEntity->setChannel('TEST_QUERY_CANCEL');
        $TradePayEntity->setTitle($title);
        $TradePayEntity->setOutTradeNo($out_trade_no);
        $TradePayEntity->setTradeStatus(Constant::TRADE_PAY_TRADE_STATUS_PAYOK);
        $TradePayEntity->setInTradeNo($in_trade_no);
        $TradePayEntity->setTotalFee($total_fee);
        $TradePayClobEntity     = new TradePayClobEntity();
        $TradePayClobEntity->setInTradeNo($in_trade_no);
        $TradePayClobEntity->setThirdPart('');
        static::$Db->getManager()->persist($TradePayEntity);
        static::$Db->getManager()->persist($TradePayClobEntity);
        static::$Db->getManager()->flush();

        $out_refund_no          = 'out_refund_no' . mt_rand(0, 999);
        $refund_fee             = $total_fee;
        $TradeRefundRepository = new TradeRefundRepository(static::$Db);
        $TradeRefundManager     = new TradeRefundManager(static::$Db, $TradeRefundRepository);
        $TradeRefundEntity      = $TradeRefundManager->load();
        $TradeRefundManager->insert($TradePayEntity, $out_refund_no, $refund_fee);

        $this->assertNotEmpty($TradeRefundEntity->getInRefundNo());
        $this->assertEquals($out_refund_no, $TradeRefundEntity->getOutRefundNo());
        $this->assertEquals($out_trade_no, $TradeRefundEntity->getOutTradeNo());
        $this->assertEquals('0', $TradeRefundEntity->getPayTime());
        $this->assertEquals($refund_fee, $TradeRefundEntity->getRefundFee());
        $this->assertEquals('0', $TradeRefundEntity->getRequestTime());
        $this->assertEquals('0', $TradeRefundEntity->getResponseTime());
        $this->assertEquals(Constant::TRADE_REFUND_STATUS_REQUEST, $TradeRefundEntity->getStatus());
    }

    public function testUpdateRequestTradeRefundStatusInvalidException()
    {
        $title              = 'title' . mt_rand(0,999);
        $client_ip          = mt_rand(0,255) . '.0.0.1';
        $in_trade_no        = date('ymdhis') . mt_rand(0, 999);
        $out_trade_no       = 'o' . date('ymdhis') . mt_rand(0, 999);
        $total_fee          = mt_rand(3,9999);
        $TradePayEntity     = new TradePayEntity();
        $TradePayEntity->setClientIp($client_ip);
        $TradePayEntity->setChannel('TEST_QUERY_CANCEL');
        $TradePayEntity->setTitle($title);
        $TradePayEntity->setOutTradeNo($out_trade_no);
        $TradePayEntity->setTradeStatus(Constant::TRADE_PAY_TRADE_STATUS_PAYOK);
        $TradePayEntity->setInTradeNo($in_trade_no);
        $TradePayEntity->setTotalFee($total_fee);
        $TradePayClobEntity     = new TradePayClobEntity();
        $TradePayClobEntity->setInTradeNo($in_trade_no);
        $TradePayClobEntity->setThirdPart('');

        $in_refund_no       = date('ymdhis') . mt_rand(0, 999);
        $out_refund_no      = 'out_refund_no' . mt_rand(0, 999);
        $refund_fee         = rand(1, $total_fee - 1);

        $TradeRefundEntity   = new TradeRefundEntity();
        $TradeRefundEntity->setInRefundNo($in_refund_no);
        $TradeRefundEntity->setOutRefundNo($out_refund_no);
        $TradeRefundEntity->setInTradeNo($TradePayEntity->getInTradeNo());
        $TradeRefundEntity->setRefundFee($refund_fee);
        $TradeRefundEntity->setStatus(Constant::TRADE_REFUND_STATUS_SUCCESS);

        static::$Db->getManager()->persist($TradePayEntity);
        static::$Db->getManager()->persist($TradePayClobEntity);
        static::$Db->getManager()->persist($TradeRefundEntity);
        static::$Db->getManager()->flush();

        $this->expectException(TradeRefundStatusInvalidException::class);
        $TradeRefundRepository = new TradeRefundRepository(static::$Db);
        $TradeRefundManager     = new TradeRefundManager(static::$Db, $TradeRefundRepository);
        $TradeRefundEntity      = $TradeRefundManager->load($TradeRefundEntity->getInRefundNo());
        $TradeRefundManager->updateRequest($TradeRefundEntity);
    }

    public function testUpdateRequestOk()
    {
        $title              = 'title' . mt_rand(0,999);
        $client_ip          = mt_rand(0,255) . '.0.0.1';
        $in_trade_no        = date('ymdhis') . mt_rand(0, 999);
        $out_trade_no       = 'o' . date('ymdhis') . mt_rand(0, 999);
        $total_fee          = mt_rand(3,9999);
        $TradePayEntity     = new TradePayEntity();
        $TradePayEntity->setClientIp($client_ip);
        $TradePayEntity->setChannel('TEST_QUERY_CANCEL');
        $TradePayEntity->setTitle($title);
        $TradePayEntity->setOutTradeNo($out_trade_no);
        $TradePayEntity->setTradeStatus(Constant::TRADE_PAY_TRADE_STATUS_PAYOK);
        $TradePayEntity->setInTradeNo($in_trade_no);
        $TradePayEntity->setTotalFee($total_fee);
        $TradePayClobEntity     = new TradePayClobEntity();
        $TradePayClobEntity->setInTradeNo($in_trade_no);
        $TradePayClobEntity->setThirdPart('');

        $in_refund_no       = date('ymdhis') . mt_rand(0, 999);
        $out_refund_no      = 'out_refund_no' . mt_rand(0, 999);
        $refund_fee         = rand(1, $total_fee - 1);

        $TradeRefundEntity   = new TradeRefundEntity();
        $TradeRefundEntity->setInRefundNo($in_refund_no);
        $TradeRefundEntity->setOutRefundNo($out_refund_no);
        $TradeRefundEntity->setInTradeNo($TradePayEntity->getInTradeNo());
        $TradeRefundEntity->setRefundFee($refund_fee);
        $TradeRefundEntity->setStatus(Constant::TRADE_REFUND_STATUS_FAILED);

        static::$Db->getManager()->persist($TradePayEntity);
        static::$Db->getManager()->persist($TradePayClobEntity);
        static::$Db->getManager()->persist($TradeRefundEntity);
        static::$Db->getManager()->flush();

        $TradeRefundRepository = new TradeRefundRepository(static::$Db);
        $TradeRefundManager     = new TradeRefundManager(static::$Db, $TradeRefundRepository);
        $TradeRefundEntity      = $TradeRefundManager->load($TradeRefundEntity->getInRefundNo());
        $TradeRefundManager->updateRequest($TradeRefundEntity);

        $this->assertEquals(Constant::TRADE_REFUND_STATUS_REQUEST, $TradeRefundEntity->getStatus());
        $this->assertNotEmpty($TradeRefundEntity->getRequestTime());
    }

    public function testUpdateRefundSuccessTradeRefundStatusInvalidException()
    {
        $title              = 'title' . mt_rand(0,999);
        $client_ip          = mt_rand(0,255) . '.0.0.1';
        $in_trade_no        = date('ymdhis') . mt_rand(0, 999);
        $out_trade_no       = 'o' . date('ymdhis') . mt_rand(0, 999);
        $total_fee          = mt_rand(3,9999);
        $TradePayEntity     = new TradePayEntity();
        $TradePayEntity->setClientIp($client_ip);
        $TradePayEntity->setChannel('TEST_QUERY_CANCEL');
        $TradePayEntity->setTitle($title);
        $TradePayEntity->setOutTradeNo($out_trade_no);
        $TradePayEntity->setTradeStatus(Constant::TRADE_PAY_TRADE_STATUS_PAYOK);
        $TradePayEntity->setInTradeNo($in_trade_no);
        $TradePayEntity->setTotalFee($total_fee);
        $TradePayClobEntity     = new TradePayClobEntity();
        $TradePayClobEntity->setInTradeNo($in_trade_no);
        $TradePayClobEntity->setThirdPart('');

        $in_refund_no       = date('ymdhis') . mt_rand(0, 999);
        $out_refund_no      = 'out_refund_no' . mt_rand(0, 999);
        $refund_fee         = rand(1, $total_fee - 1);

        $TradeRefundEntity   = new TradeRefundEntity();
        $TradeRefundEntity->setInRefundNo($in_refund_no);
        $TradeRefundEntity->setOutRefundNo($out_refund_no);
        $TradeRefundEntity->setInTradeNo($TradePayEntity->getInTradeNo());
        $TradeRefundEntity->setRefundFee($refund_fee);
        $TradeRefundEntity->setStatus(Constant::TRADE_REFUND_STATUS_FAILED);

        static::$Db->getManager()->persist($TradePayEntity);
        static::$Db->getManager()->persist($TradePayClobEntity);
        static::$Db->getManager()->persist($TradeRefundEntity);
        static::$Db->getManager()->flush();

        $this->expectException(TradeRefundStatusInvalidException::class);
        $TradeRefundRepository = new TradeRefundRepository(static::$Db);
        $TradeRefundManager     = new TradeRefundManager(static::$Db, $TradeRefundRepository);

        $pay_time               = date('Y-m-d H:i:s', mt_rand(0, 99999999));
        $TradeRefundEntity      = $TradeRefundManager->load($TradeRefundEntity->getInRefundNo());
        $TradeRefundManager->updateRefundSuccess($pay_time);
    }

    public function testUpdateRefundSuccessOk()
    {
        $title              = 'title' . mt_rand(0,999);
        $client_ip          = mt_rand(0,255) . '.0.0.1';
        $in_trade_no        = date('ymdhis') . mt_rand(0, 999);
        $out_trade_no       = 'o' . date('ymdhis') . mt_rand(0, 999);
        $total_fee          = mt_rand(3,9999);
        $TradePayEntity     = new TradePayEntity();
        $TradePayEntity->setClientIp($client_ip);
        $TradePayEntity->setChannel('TEST_QUERY_CANCEL');
        $TradePayEntity->setTitle($title);
        $TradePayEntity->setOutTradeNo($out_trade_no);
        $TradePayEntity->setTradeStatus(Constant::TRADE_PAY_TRADE_STATUS_PAYOK);
        $TradePayEntity->setInTradeNo($in_trade_no);
        $TradePayEntity->setTotalFee($total_fee);
        $TradePayClobEntity     = new TradePayClobEntity();
        $TradePayClobEntity->setInTradeNo($in_trade_no);
        $TradePayClobEntity->setThirdPart('');

        $in_refund_no       = date('ymdhis') . mt_rand(0, 999);
        $out_refund_no      = 'out_refund_no' . mt_rand(0, 999);
        $refund_fee         = rand(1, $total_fee - 1);

        $TradeRefundEntity   = new TradeRefundEntity();
        $TradeRefundEntity->setInRefundNo($in_refund_no);
        $TradeRefundEntity->setOutRefundNo($out_refund_no);
        $TradeRefundEntity->setInTradeNo($TradePayEntity->getInTradeNo());
        $TradeRefundEntity->setRefundFee($refund_fee);
        $TradeRefundEntity->setStatus(Constant::TRADE_REFUND_STATUS_REQUEST);

        static::$Db->getManager()->persist($TradePayEntity);
        static::$Db->getManager()->persist($TradePayClobEntity);
        static::$Db->getManager()->persist($TradeRefundEntity);
        static::$Db->getManager()->flush();
        $TradeRefundRepository = new TradeRefundRepository(static::$Db);
        $TradeRefundManager     = new TradeRefundManager(static::$Db, $TradeRefundRepository);

        $pay_time               = time();
        $TradeRefundEntity      = $TradeRefundManager->load($TradeRefundEntity->getInRefundNo());
        $TradeRefundManager->updateRefundSuccess($pay_time);

        $this->assertEquals(Constant::TRADE_REFUND_STATUS_SUCCESS, $TradeRefundEntity->getStatus());
        $this->assertEquals($pay_time,$TradeRefundEntity->getPayTime());
        $this->assertNotEmpty($TradeRefundEntity->getResponseTime());
    }

    public function testUpdateRefundFailedTradeRefundStatusInvalidException()
    {
        $title              = 'title' . mt_rand(0,999);
        $client_ip          = mt_rand(0,255) . '.0.0.1';
        $in_trade_no        = date('ymdhis') . mt_rand(0, 999);
        $out_trade_no       = 'o' . date('ymdhis') . mt_rand(0, 999);
        $total_fee          = mt_rand(3,9999);
        $TradePayEntity     = new TradePayEntity();
        $TradePayEntity->setClientIp($client_ip);
        $TradePayEntity->setChannel('TEST_QUERY_CANCEL');
        $TradePayEntity->setTitle($title);
        $TradePayEntity->setOutTradeNo($out_trade_no);
        $TradePayEntity->setTradeStatus(Constant::TRADE_PAY_TRADE_STATUS_PAYOK);
        $TradePayEntity->setInTradeNo($in_trade_no);
        $TradePayEntity->setTotalFee($total_fee);
        $TradePayClobEntity     = new TradePayClobEntity();
        $TradePayClobEntity->setInTradeNo($in_trade_no);
        $TradePayClobEntity->setThirdPart('');

        $in_refund_no       = date('ymdhis') . mt_rand(0, 999);
        $out_refund_no      = 'out_refund_no' . mt_rand(0, 999);
        $refund_fee         = rand(1, $total_fee - 1);

        $TradeRefundEntity   = new TradeRefundEntity();
        $TradeRefundEntity->setInRefundNo($in_refund_no);
        $TradeRefundEntity->setOutRefundNo($out_refund_no);
        $TradeRefundEntity->setInTradeNo($TradePayEntity->getInTradeNo());
        $TradeRefundEntity->setRefundFee($refund_fee);
        $TradeRefundEntity->setStatus(Constant::TRADE_REFUND_STATUS_FAILED);

        static::$Db->getManager()->persist($TradePayEntity);
        static::$Db->getManager()->persist($TradePayClobEntity);
        static::$Db->getManager()->persist($TradeRefundEntity);
        static::$Db->getManager()->flush();

        $this->expectException(TradeRefundStatusInvalidException::class);
        $TradeRefundRepository  = new TradeRefundRepository(static::$Db);
        $TradeRefundManager     = new TradeRefundManager(static::$Db, $TradeRefundRepository);

        $TradeRefundEntity      = $TradeRefundManager->load($TradeRefundEntity->getInRefundNo());
        $TradeRefundManager->updateRefundFailed();
    }

    public function testUpdateRefundFailedOk()
    {
        $title              = 'title' . mt_rand(0,999);
        $client_ip          = mt_rand(0,255) . '.0.0.1';
        $in_trade_no        = date('ymdhis') . mt_rand(0, 999);
        $out_trade_no       = 'o' . date('ymdhis') . mt_rand(0, 999);
        $total_fee          = mt_rand(3,9999);
        $TradePayEntity     = new TradePayEntity();
        $TradePayEntity->setClientIp($client_ip);
        $TradePayEntity->setChannel('TEST_QUERY_CANCEL');
        $TradePayEntity->setTitle($title);
        $TradePayEntity->setOutTradeNo($out_trade_no);
        $TradePayEntity->setTradeStatus(Constant::TRADE_PAY_TRADE_STATUS_PAYOK);
        $TradePayEntity->setInTradeNo($in_trade_no);
        $TradePayEntity->setTotalFee($total_fee);
        $TradePayClobEntity     = new TradePayClobEntity();
        $TradePayClobEntity->setInTradeNo($in_trade_no);
        $TradePayClobEntity->setThirdPart('');

        $in_refund_no       = date('ymdhis') . mt_rand(0, 999);
        $out_refund_no      = 'out_refund_no' . mt_rand(0, 999);
        $refund_fee         = rand(1, $total_fee - 1);

        $TradeRefundEntity   = new TradeRefundEntity();
        $TradeRefundEntity->setInRefundNo($in_refund_no);
        $TradeRefundEntity->setOutRefundNo($out_refund_no);
        $TradeRefundEntity->setInTradeNo($TradePayEntity->getInTradeNo());
        $TradeRefundEntity->setRefundFee($refund_fee);
        $TradeRefundEntity->setStatus(Constant::TRADE_REFUND_STATUS_REQUEST);

        static::$Db->getManager()->persist($TradePayEntity);
        static::$Db->getManager()->persist($TradePayClobEntity);
        static::$Db->getManager()->persist($TradeRefundEntity);
        static::$Db->getManager()->flush();
        $TradeRefundRepository  = new TradeRefundRepository(static::$Db);
        $TradeRefundManager     = new TradeRefundManager(static::$Db, $TradeRefundRepository);

        $TradeRefundEntity      = $TradeRefundManager->load($TradeRefundEntity->getInRefundNo());
        $TradeRefundManager->updateRefundFailed();

        $this->assertEquals(Constant::TRADE_REFUND_STATUS_FAILED, $TradeRefundEntity->getStatus());
        $this->assertNotEmpty($TradeRefundEntity->getResponseTime());
    }
}