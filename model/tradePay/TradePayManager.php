<?php
namespace asbamboo\openpay\model\tradePay;

use asbamboo\openpay\Constant;
use Doctrine\DBAL\LockMode;
use asbamboo\openpay\apiStore\exception\TradePayTradeStatusInvalidException;
use asbamboo\database\FactoryInterface;

/**
 * 管理TradePayEntity的数据变更
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月30日
 */
class TradePayManager
{
    use TradePayValidator;

    /**
     *
     * @var FactoryInterface
     */
    private $Db;

    /**
     *
     * @param FactoryInterface $Db
     */
    public function __construct(FactoryInterface $Db)
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
     * @param string $channel
     * @param string $title
     * @param string $total_fee
     * @param string $out_trade_no
     * @param string $client_ip
     * @param string $notify_url
     * @param string $return_url
     * @return TradePayEntity
     */
    public function insert($channel, $title, $total_fee, $out_trade_no, $client_ip, $notify_url, $return_url) : TradePayEntity
    {
        $TradePayEntity     = new TradePayEntity();
        $TradePayEntity->setChannel($channel);
        $TradePayEntity->setTitle($title);
        $TradePayEntity->setTotalFee($total_fee);
        $TradePayEntity->setOutTradeNo($out_trade_no);
        $TradePayEntity->setClientIp($client_ip);
        $TradePayEntity->setNotifyUrl($notify_url);
        $TradePayEntity->setReturnUrl($return_url);

        $this->validateInsert($TradePayEntity);
        $TradePayEntity->setInTradeNo($this->makeInTradeNo());
        $TradePayEntity->setPayokTime('0');
        $TradePayEntity->setPayedTime('0');
        $TradePayEntity->setTradeStatus(Constant::TRADE_PAY_TRADE_STATUS_NOPAY);

        $this->Db->getManager()->persist($TradePayEntity);

        return $TradePayEntity;
    }

    /**
     * 交易状态变更为支付成功(可退款)
     *
     * @param TradePayEntity $TradePayEntity
     * @param string $thrid_trade_no
     */
    public function updateTradeStatusToPayok(TradePayEntity $TradePayEntity, string $third_trade_no) : TradePayEntity
    {
        $TradePayEntity->setThirdTradeNo($third_trade_no);

        $time   = time();
        $this->validateUpdateTradeStatusToPayok($TradePayEntity);
        $TradePayEntity->setTradeStatus(Constant::TRADE_PAY_TRADE_STATUS_PAYOK);
        $TradePayEntity->setPayokTime($time);
        $this->Db->getManager()->lock($TradePayEntity, LockMode::OPTIMISTIC);

        return $TradePayEntity;
    }

    /**
     * 交易状态变更为支付成功(不可退款)
     *
     * @param TradePayEntity $TradePayEntity
     * @param string $third_trade_no
     */
    public function updateTradeStatusToPayed(TradePayEntity $TradePayEntity, string $third_trade_no = null) : TradePayEntity
    {
        if(!is_null($third_trade_no)){
            $TradePayEntity->setThirdTradeNo($third_trade_no);
        }

        $this->validateUpdateTradeStatusToPayed($TradePayEntity);
        $TradePayEntity->setTradeStatus(Constant::TRADE_PAY_TRADE_STATUS_PAYED);
        if(empty($TradePayEntity->getPayokTime())){
            $TradePayEntity->setPayokTime(time());
        }
        $TradePayEntity->setPayedTime(time());
        $this->Db->getManager()->lock($TradePayEntity, LockMode::OPTIMISTIC);

        return $TradePayEntity;
    }

    /**
     * 交易状态变更为取消支付
     *
     * @param TradePayEntity $TradePayEntity
     * @param string $third_trade_no
     */
    public function updateTradeStatusToCancel(TradePayEntity $TradePayEntity, string $third_trade_no = null) : TradePayEntity
    {
        if(!is_null($third_trade_no)){
            $TradePayEntity->setThirdTradeNo($third_trade_no);
        }

        $this->validateUpdateTradeStatusToCancel($TradePayEntity);
        $TradePayEntity->setTradeStatus(Constant::TRADE_PAY_TRADE_STATUS_CANCEL);
        $TradePayEntity->setCancelTime(time());
        $this->Db->getManager()->lock($TradePayEntity, LockMode::OPTIMISTIC);

        return $TradePayEntity;
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

    /**
     *
     * @param TradePayEntity $TradePayEntity
     * @throws TradePayTradeStatusInvalidException
     */
    private function validateUpdateTradeStatusToPayok(TradePayEntity $TradePayEntity) : void
    {
        $this->validateThirdTradeNo($TradePayEntity->getThirdTradeNo());
        if(!in_array($TradePayEntity->getTradeStatus(), [Constant::TRADE_PAY_TRADE_STATUS_NOPAY, Constant::TRADE_PAY_TRADE_STATUS_PAYFAILED, Constant::TRADE_PAY_TRADE_STATUS_PAYING ])){
            throw new TradePayTradeStatusInvalidException('当前交易状态不允许被修改成支付成功[可退款].');
        }
    }

    /**
     *
     * @param TradePayEntity $TradePayEntity
     * @throws TradePayTradeStatusInvalidException
     */
    private function validateUpdateTradeStatusToPayed(TradePayEntity $TradePayEntity) : void
    {
        $this->validateThirdTradeNo($TradePayEntity->getThirdTradeNo());
        if(!in_array($TradePayEntity->getTradeStatus(), [Constant::TRADE_PAY_TRADE_STATUS_NOPAY, Constant::TRADE_PAY_TRADE_STATUS_PAYFAILED, Constant::TRADE_PAY_TRADE_STATUS_PAYING ])){
            throw new TradePayTradeStatusInvalidException('当前交易状态不允许被修改成支付成功[不可退款].');
        }
    }

    /**
     *
     * @param TradePayEntity $TradePayEntity
     * @throws TradePayTradeStatusInvalidException
     */
    private function validateUpdateTradeStatusToCancel(TradePayEntity $TradePayEntity) : void
    {
        $this->validateThirdTradeNo($TradePayEntity->getThirdTradeNo());
        if(!in_array($TradePayEntity->getTradeStatus(), [Constant::TRADE_PAY_TRADE_STATUS_NOPAY, Constant::TRADE_PAY_TRADE_STATUS_PAYFAILED, Constant::TRADE_PAY_TRADE_STATUS_PAYING ])){
            throw new TradePayTradeStatusInvalidException('当前交易状态不允许被修改成取消支付.');
        }
    }
}