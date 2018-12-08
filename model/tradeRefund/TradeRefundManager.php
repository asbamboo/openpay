<?php
namespace asbamboo\openpay\model\tradeRefund;

use asbamboo\openpay\Constant;
use asbamboo\openpay\model\tradePay\TradePayEntity;
use asbamboo\openpay\apiStore\exception\TradeRefundRefundFeeInvalidException;
use asbamboo\openpay\apiStore\exception\TradeRefundStatusInvalidException;
use Doctrine\DBAL\LockMode;
use asbamboo\openpay\apiStore\exception\TradeRefundTradeStatusInvalidException;
use asbamboo\database\FactoryInterface;
use asbamboo\openpay\apiStore\exception\NotFoundTradeRefundException;

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
    protected $Db;

    /**
     *
     * @var TradeRefundRepository
     */
    protected $TradeRefundRepository;

    /**
     *
     * @var TradeRefundEntity
     */
    protected $TradeRefundEntity;

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
     * @param string $in_refund_no
     * @throws NotFoundTradeRefundException
     * @return TradeRefundEntity
     */
    public function load(string $in_refund_no = null) : TradeRefundEntity
    {
        if(is_null($in_refund_no)){
            $TradeRefundEntity  = new TradeRefundEntity();
        }else{
            $TradeRefundEntity  = $this->TradeRefundRepository->load($in_refund_no);
            if(empty($TradeRefundEntity)){
                throw new NotFoundTradeRefundException('退款不存在。');
            }
        }
        $this->TradeRefundEntity = $TradeRefundEntity;
        return $this->TradeRefundEntity;
    }

    /**
     *
     * @param TradePayEntity $TradePayEntity
     * @param string $out_refund_no
     * @param int $refund_fee
     * @return TradeRefundManager
     */
    public function insert(TradePayEntity $TradePayEntity, $out_refund_no, $refund_fee) : TradeRefundManager
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
     *
     * @return TradeRefundManager
     */
    public function updateRequest() : TradeRefundManager
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
     * @return TradeRefundManager
     */
    public function updateRefundSuccess($pay_time) : TradeRefundManager
    {
        $this->TradeRefundEntity->setPayTime($pay_time);

        $this->validateUpdateRefundSuccess();
        $this->TradeRefundEntity->setStatus(Constant::TRADE_REFUND_STATUS_SUCCESS);
        $this->TradeRefundEntity->setResponseTime(time());

        return $this;
    }

    /**
     *
     * @return TradeRefundManager
     */
    public function updateRefundFailed() : TradeRefundManager
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
    protected function validateInsert(TradePayEntity $TradePayEntity) : void
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
    protected function validateUpdateRequest() : void
    {
        if(!in_array($this->TradeRefundEntity->getStatus(), [Constant::TRADE_REFUND_STATUS_FAILED, Constant::TRADE_REFUND_STATUS_REQUEST])){
            throw new TradeRefundStatusInvalidException('只有上一次请求失败, 或者还没有发起退款请求的退款信息能发起退款。');
        }
    }

    /**
     *
     * @throws TradeRefundStatusInvalidException
     */
    protected function validateUpdateRefundSuccess() : void
    {
        if(!in_array($this->TradeRefundEntity->getStatus(), [Constant::TRADE_REFUND_STATUS_REQUEST])){
            throw new TradeRefundStatusInvalidException('只有请求中的退款，状态才能修改为成功。');
        }
    }

    /**
     *
     * @throws TradeRefundStatusInvalidException
     */
    protected function validateUpdateRefundFailed() : void
    {
        if(!in_array($this->TradeRefundEntity->getStatus(), [Constant::TRADE_REFUND_STATUS_REQUEST])){
            throw new TradeRefundStatusInvalidException('只有请求中的退款，状态才能修改为失败。');
        }
    }
}