<?php
namespace asbamboo\openpay\model\tradeRefund;

use asbamboo\openpay\Constant;
use asbamboo\openpay\model\tradePay\TradePayEntity;
use asbamboo\openpay\apiStore\exception\TradeRefundRefundFeeInvalidException;
use asbamboo\openpay\apiStore\exception\TradeRefundStatusInvalidException;
use Doctrine\DBAL\LockMode;
use asbamboo\openpay\apiStore\exception\TradeRefundTradeStatusInvalidException;
use asbamboo\database\FactoryInterface;

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
     * @var FactoryInterface
     */
    private $Db;

    /**
     *
     * @var TradeRefundRepository
     */
    private $TradeRefundRepository;

    /**
     *
     * @var TradeRefundEntity
     */
    private $TradeRefundEntity;

    /**
     *
     * @param FactoryInterface $Db
     */
    public function __construct(FactoryInterface $Db, TradeRefundRepository $TradeRefundRepository)
    {
        $this->Db                       = $Db;
        $this->TradeRefundRepository   = $TradeRefundRepository;
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
     *
     * @param TradeRefundEntity $TradeRefundEntity
     * @return self
     */
    public function load(TradeRefundEntity $TradeRefundEntity) : self
    {
        $this->TradeRefundEntity = $TradeRefundEntity;
        return $this;
    }

    /**
     * 插入一条数据用
     */
    public function insert(TradePayEntity $TradePayEntity, $out_refund_no, $refund_fee) : self
    {
        $this->TradeRefundEntity->setOutTradeNo($TradePayEntity->getOutTradeNo());
        $this->TradeRefundEntity->setInTradeNo($TradePayEntity->getInTradeNo());
        $this->TradeRefundEntity->setOutRefundNo($out_refund_no);
        $this->TradeRefundEntity->setRefundFee($refund_fee);

        $this->validateInsert($TradePayEntity);
        $this->TradeRefundEntity->setInRefundNo($this->makeInRefundNo());
        $this->Db->getManager()->persist($this->TradeRefundEntity);

        return $this;
    }

    /**
     * 更新状态后通过渠道发送退款请求
     *
     * @return TradeRefundEntity
     */
    public function updateRequest() : self
    {
        $this->validateUpdateRequest();
        $this->TradeRefundEntity->setStatus(Constant::TRADE_REFUND_STATUS_REQUEST);
        $this->TradeRefundEntity->setRequestTime(time());
        $this->Db->getManager()->lock($this->TradeRefundEntity, LockMode::OPTIMISTIC);
        return $this;
    }

    /**
     *
     * @param int $pay_time
     * @return TradeRefundEntity
     */
    public function updateRefundSuccess($pay_time) : self
    {
        $this->TradeRefundEntity->setPayTime($pay_time);

        $this->validateUpdateRefundSuccess();
        $this->TradeRefundEntity->setStatus(Constant::TRADE_REFUND_STATUS_SUCCESS);
        $this->TradeRefundEntity->setResponseTime(time());

        return $this;
    }

    /**
     *
     * @return TradeRefundEntity
     */
    public function updateRefundFailed() : self
    {
        $this->validateUpdateRefundFailed();
        $this->TradeRefundEntity->setStatus(Constant::TRADE_REFUND_STATUS_FAILED);
        $this->TradeRefundEntity->setResponseTime(time());

        return $this;
    }


    /**
     *
     * @param TradePayEntity $TradePayEntity
     */
    private function validateInsert(TradePayEntity $TradePayEntity) : void
    {
        if($TradePayEntity->getTradeStatus() != Constant::TRADE_PAY_TRADE_STATUS_PAYOK){
            throw new TradeRefundTradeStatusInvalidException('当前订单状态不允许退款.');
        }

        $this->validateOutRefundNo($this->TradeRefundEntity->getOutRefundNo());
        $this->validateRefundFee($this->TradeRefundEntity->getRefundFee());

        $total_refund_fee   = $this->TradeRefundRepository->getTotalRefundFeeByInTradeNo($this->TradeRefundEntity->getInTradeNo());
        if(bccomp($this->TradeRefundEntity->getRefundFee(), bcsub($TradePayEntity->getTotalFee(), $total_refund_fee)) > 0){
            throw new TradeRefundRefundFeeInvalidException('退款金额不能大于交易总金额减去已退款金额.');
        }
    }

    /**
     *
     */
    private function validateUpdateRequest() : void
    {
        if(!in_array($this->TradeRefundEntity->getStatus(), [Constant::TRADE_REFUND_STATUS_FAILED, Constant::TRADE_REFUND_STATUS_REQUEST])){
            throw new TradeRefundStatusInvalidException('只有上一次请求失败, 或者还没有发起退款请求的退款信息能发起退款。');
        }
    }

    /**
     *
     * @throws TradeRefundStatusInvalidException
     */
    private function validateUpdateRefundSuccess() : void
    {
        if(!in_array($this->TradeRefundEntity->getStatus(), [Constant::TRADE_REFUND_STATUS_REQUEST])){
            throw new TradeRefundStatusInvalidException('只有请求中的退款，状态才能修改为成功。');
        }
    }

    /**
     *
     * @throws TradeRefundStatusInvalidException
     */
    private function validateUpdateRefundFailed() : void
    {
        if(!in_array($this->TradeRefundEntity->getStatus(), [Constant::TRADE_REFUND_STATUS_REQUEST])){
            throw new TradeRefundStatusInvalidException('只有请求中的退款，状态才能修改为失败。');
        }
    }
}