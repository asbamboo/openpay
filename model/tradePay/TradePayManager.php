<?php
namespace asbamboo\openpay\model\tradePay;

use asbamboo\database\Factory;

/**
 * 管理TradePayEntity的数据变更
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月30日
 */
class TradePayManager
{
    use TradePayValidator;

    const TRADE_STATUS_NOPAY  = '0';

    /**
     *
     * @var Factory
     */
    private $Db;

    /**
     *
     * @param Factory $Db
     */
    public function __construct(Factory $Db)
    {
        $this->Db   = $Db;
    }

    /**
     *  生成聚合支付系统内不的交易编号
     *  为了规范交易编号，向第三方平台发起请求时，传递聚合系统内的交易编号。
     */
    public function makeInTradeNo() : string
    {
        return date('y') . str_pad(date('z'), 3, '0', STR_PAD_LEFT) . str_pad(date('B'), 3, '0', STR_PAD_LEFT) . str_pad(mt_rand(0, 999999999), 9, '0', STR_PAD_LEFT);
    }

    /**
     * 插入一条数据用
     *
     * @param TradePayEntity $TradePayEntity
     */
    public function insert(TradePayEntity $TradePayEntity) : void
    {
        $this->validateInsert($TradePayEntity);
        $TradePayEntity->setInTradeNo($this->makeInTradeNo());
        $TradePayEntity->setPayedTime('0');
        $TradePayEntity->setTradeStatus(self::TRADE_STATUS_NOPAY);
        $this->Db->getManager()->persist($TradePayEntity);
    }

    /**
     *
     * @param TradePayEntity $TradePayEntity
     */
    private function validateInsert(TradePayEntity $TradePayEntity) : void
    {
        $this->validateChannel($TradePayEntity->getChannel());
        $this->validateTitle($TradePayEntity->getTitle());
        $this->validateOutTradeNo($TradePayEntity->getOutTradeNo());
        $this->validateTotalFee($TradePayEntity->getTotalFee());
        $this->validateClientIp($TradePayEntity->getClientIp());
    }
}