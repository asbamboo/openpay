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
    protected $Db;

    /**
     *
     * @var TradePayEntity
     */
    protected $TradePayEntity;

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
     *
     * @param TradePayEntity $TradePayEntity
     * @return TradePayManager
     */
    public function load(TradePayEntity $TradePayEntity) : TradePayManager
    {
        $this->TradePayEntity = $TradePayEntity;
        return $this;
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
    public function insert($channel, $title, $total_fee, $out_trade_no, $client_ip, $notify_url, $return_url) : TradePayManager
    {
        $this->TradePayEntity->setChannel($channel);
        $this->TradePayEntity->setTitle($title);
        $this->TradePayEntity->setTotalFee($total_fee);
        $this->TradePayEntity->setOutTradeNo($out_trade_no);
        $this->TradePayEntity->setClientIp($client_ip);
        $this->TradePayEntity->setNotifyUrl($notify_url);
        $this->TradePayEntity->setReturnUrl($return_url);

        $this->validateInsert();
        $this->TradePayEntity->setInTradeNo($this->makeInTradeNo());
        $this->TradePayEntity->setPayokTime('0');
        $this->TradePayEntity->setPayedTime('0');
        $this->TradePayEntity->setTradeStatus(Constant::TRADE_PAY_TRADE_STATUS_NOPAY);

        $this->Db->getManager()->persist($this->TradePayEntity);

        return $this;
    }

    /**
     * 交易状态变更为支付成功(可退款)
     *
     * @param string $thrid_trade_no
     */
    public function updateTradeStatusToPayok(string $third_trade_no) : TradePayManager
    {
        $this->TradePayEntity->setThirdTradeNo($third_trade_no);

        $time   = time();
        $this->validateUpdateTradeStatusToPayok();
        $this->TradePayEntity->setTradeStatus(Constant::TRADE_PAY_TRADE_STATUS_PAYOK);
        $this->TradePayEntity->setPayokTime($time);
        $this->Db->getManager()->lock($this->TradePayEntity, LockMode::OPTIMISTIC);

        return $this;
    }

    /**
     * 交易状态变更为支付成功(不可退款)
     *
     * @param string $third_trade_no
     */
    public function updateTradeStatusToPayed(string $third_trade_no = null) : TradePayManager
    {
        if(!is_null($third_trade_no)){
            $this->TradePayEntity->setThirdTradeNo($third_trade_no);
        }

        $this->validateUpdateTradeStatusToPayed();
        $this->TradePayEntity->setTradeStatus(Constant::TRADE_PAY_TRADE_STATUS_PAYED);
        if(empty($this->TradePayEntity->getPayokTime())){
            $this->TradePayEntity->setPayokTime(time());
        }
        $this->TradePayEntity->setPayedTime(time());
        $this->Db->getManager()->lock($this->TradePayEntity, LockMode::OPTIMISTIC);

        return $this;
    }

    /**
     * 交易状态变更为取消支付
     *
     * @param string $third_trade_no
     */
    public function updateTradeStatusToCancel(string $third_trade_no = null) : TradePayManager
    {
        if(!is_null($third_trade_no)){
            $this->TradePayEntity->setThirdTradeNo($third_trade_no);
        }

        $this->validateUpdateTradeStatusToCancel();
        $this->TradePayEntity->setTradeStatus(Constant::TRADE_PAY_TRADE_STATUS_CANCEL);
        $this->TradePayEntity->setCancelTime(time());
        $this->Db->getManager()->lock($this->TradePayEntity, LockMode::OPTIMISTIC);

        return $this;
    }

    /**
     *
     */
    protected function validateInsert() : void
    {
        $this->validateChannel($this->TradePayEntity->getChannel());
        $this->validateTitle($this->TradePayEntity->getTitle());
        $this->validateOutTradeNo($this->TradePayEntity->getOutTradeNo());
        $this->validateTotalFee($this->TradePayEntity->getTotalFee());
        $this->validateClientIp($this->TradePayEntity->getClientIp());
    }

    /**
     *
     * @throws TradePayTradeStatusInvalidException
     */
    protected function validateUpdateTradeStatusToPayok() : void
    {
        $this->validateThirdTradeNo($this->TradePayEntity->getThirdTradeNo());
        if(!in_array($this->TradePayEntity->getTradeStatus(), [Constant::TRADE_PAY_TRADE_STATUS_NOPAY, Constant::TRADE_PAY_TRADE_STATUS_PAYFAILED, Constant::TRADE_PAY_TRADE_STATUS_PAYING ])){
            throw new TradePayTradeStatusInvalidException('当前交易状态不允许被修改成支付成功[可退款].');
        }
    }

    /**
     *
     * @throws TradePayTradeStatusInvalidException
     */
    protected function validateUpdateTradeStatusToPayed() : void
    {
        $this->validateThirdTradeNo($this->TradePayEntity->getThirdTradeNo());
        if(!in_array($this->TradePayEntity->getTradeStatus(), [Constant::TRADE_PAY_TRADE_STATUS_NOPAY, Constant::TRADE_PAY_TRADE_STATUS_PAYFAILED, Constant::TRADE_PAY_TRADE_STATUS_PAYING ])){
            throw new TradePayTradeStatusInvalidException('当前交易状态不允许被修改成支付成功[不可退款].');
        }
    }

    /**
     *
     * @throws TradePayTradeStatusInvalidException
     */
    protected function validateUpdateTradeStatusToCancel() : void
    {
        $this->validateThirdTradeNo($this->TradePayEntity->getThirdTradeNo());
        if(!in_array($this->TradePayEntity->getTradeStatus(), [Constant::TRADE_PAY_TRADE_STATUS_NOPAY, Constant::TRADE_PAY_TRADE_STATUS_PAYFAILED, Constant::TRADE_PAY_TRADE_STATUS_PAYING ])){
            throw new TradePayTradeStatusInvalidException('当前交易状态不允许被修改成取消支付.');
        }
    }
}