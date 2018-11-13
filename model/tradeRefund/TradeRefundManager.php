<?php
namespace asbamboo\openpay\model\tradeRefund;

use asbamboo\database\Factory;
use asbamboo\openpay\Constant;
use asbamboo\openpay\model\tradePay\TradePayEntity;
use asbamboo\openpay\apiStore\exception\TradeRefundRefundFeeInvalidException;
use asbamboo\openpay\apiStore\exception\TradeRefundStatusInvalidException;
use Doctrine\DBAL\LockMode;
use asbamboo\openpay\apiStore\exception\TradeRefundTradeStatusInvalidException;

/**
 * 管理TradePayEntity的数据变更
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月30日
 */
class TradeRefundManager
{
    use TradeRefundValidator;

    /**
     *
     * @var Factory
     */
    private $Db;

    /**
     *
     * @var TradeRefundRespository
     */
    private $TradeRefundRespository;

    /**
     *
     * @param Factory $Db
     */
    public function __construct(Factory $Db, TradeRefundRespository $TradeRefundRespository)
    {
        $this->Db                       = $Db;
        $this->TradeRefundRespository   = $TradeRefundRespository;
    }

    /**
     *  生成聚合支付系统内不的交易编号
     *  为了规范交易编号，向第三方平台发起请求时，传递聚合系统内的交易编号。
     */
    public function makeInRefundNo() : string
    {
        return date('y') . str_pad(date('z'), 3, '0', STR_PAD_LEFT) . str_pad(date('B'), 3, '0', STR_PAD_LEFT) . str_pad(mt_rand(0, 999999999), 9, '0', STR_PAD_LEFT);
    }

    /**
     * 插入一条数据用
     *
     * @param TradeRefundEntity $TradeRefundEntity
     */
    public function insert(TradePayEntity $TradePayEntity, $out_refund_no, $refund_fee) : TradeRefundEntity
    {
        $TradeRefundEntity  = new TradeRefundEntity();
        $TradeRefundEntity->setOutTradeNo($TradePayEntity->getOutTradeNo());
        $TradeRefundEntity->setInTradeNo($TradePayEntity->getInTradeNo());
        $TradeRefundEntity->setOutRefundNo($out_refund_no);
        $TradeRefundEntity->setRefundFee($refund_fee);

        $this->validateInsert($TradeRefundEntity, $TradePayEntity);
        $TradeRefundEntity->setInRefundNo($this->makeInRefundNo());
        $this->Db->getManager()->persist($TradeRefundEntity);

        return $TradeRefundEntity;
    }

    /**
     * 更新状态后通过渠道发送退款请求
     *
     * @param TradeRefundEntity $TradeRefundEntity
     * @return TradeRefundEntity
     */
    public function updateRequest(TradeRefundEntity $TradeRefundEntity) : TradeRefundEntity
    {
        $this->validateUpdateRequest($TradeRefundEntity);
        $TradeRefundEntity->setStatus(Constant::TRADE_REFUND_STATUS_REQUEST);
        $TradeRefundEntity->setRequestTime(time());
        $this->Db->getManager()->lock($TradeRefundEntity, LockMode::OPTIMISTIC);
        return $TradeRefundEntity;
    }

    /**
     *
     * @param TradeRefundEntity $TradeRefundEntity
     * @param int $pay_time
     * @return TradeRefundEntity
     */
    public function updateRefundSuccess(TradeRefundEntity $TradeRefundEntity, $pay_time) : TradeRefundEntity
    {
        $TradeRefundEntity->setPayTime($pay_time);

        $this->validateUpdateRefundSuccess($TradeRefundEntity);
        $TradeRefundEntity->setStatus(Constant::TRADE_REFUND_STATUS_SUCCESS);
        $TradeRefundEntity->getResponseTime(time());

        return $TradeRefundEntity;
    }

    /**
     *
     * @param TradeRefundEntity $TradeRefundEntity
     * @return TradeRefundEntity
     */
    public function updateRefundFailed(TradeRefundEntity $TradeRefundEntity) : TradeRefundEntity
    {
        $this->validateUpdateRefundFailed($TradeRefundEntity);
        $TradeRefundEntity->setStatus(Constant::TRADE_REFUND_STATUS_FAILED);
        $TradeRefundEntity->getResponseTime(time());

        return $TradeRefundEntity;
    }


    /**
     *
     * @param TradeRefundEntity $TradeRefundEntity
     * @param TradePayEntity $TradePayEntity
     */
    private function validateInsert(TradeRefundEntity $TradeRefundEntity, TradePayEntity $TradePayEntity) : void
    {
        if($TradePayEntity->getTradeStatus() != Constant::TRADE_PAY_TRADE_STATUS_PAYOK){
            throw new TradeRefundTradeStatusInvalidException('当前订单状态不允许退款.');
        }

        $this->validateOutRefundNo($TradeRefundEntity->getOutRefundNo());
        $this->validateRefundFee($TradeRefundEntity->getRefundFee());

        $total_refund_fee   = $this->TradeRefundRespository->getTotalRefundFeeByInTradeNo($TradeRefundEntity->getInTradeNo());
        if(bccomp($TradeRefundEntity->getRefundFee(), bcsub($TradePayEntity->getTotalFee(), $total_refund_fee)) > 0){
            throw new TradeRefundRefundFeeInvalidException('退款金额不能大于交易总金额减去已退款金额.');
        }
    }

    /**
     *
     * @param TradeRefundEntity $TradeRefundEntity
     */
    private function validateUpdateRequest(TradeRefundEntity $TradeRefundEntity) : void
    {
        if(!in_array($TradeRefundEntity->getStatus(), [Constant::TRADE_REFUND_STATUS_FAILED, Constant::TRADE_REFUND_STATUS_REQUEST])){
            throw new TradeRefundStatusInvalidException('只有上一次请求失败, 或者还没有发起退款请求的退款信息能发起退款。');
        }
    }

    /**
     *
     * @param TradeRefundEntity $TradeRefundEntity
     * @throws TradeRefundStatusInvalidException
     */
    private function validateUpdateRefundSuccess(TradeRefundEntity $TradeRefundEntity) : void
    {
        if(!in_array($TradeRefundEntity->getStatus(), [Constant::TRADE_REFUND_STATUS_REQUEST])){
            throw new TradeRefundStatusInvalidException('只有请求中的退款，状态才能修改为成功。');
        }
    }

    /**
     *
     * @param TradeRefundEntity $TradeRefundEntity
     * @throws TradeRefundStatusInvalidException
     */
    private function validateUpdateRefundFailed(TradeRefundEntity $TradeRefundEntity) : void
    {
        if(!in_array($TradeRefundEntity->getStatus(), [Constant::TRADE_REFUND_STATUS_REQUEST])){
            throw new TradeRefundStatusInvalidException('只有请求中的退款，状态才能修改为失败。');
        }
    }
}