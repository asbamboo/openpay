<?php
namespace asbamboo\openpay\model\tradeRefundThirdPart;

use asbamboo\openpay\model\tradeRefund\TradeRefundEntity;
use asbamboo\database\FactoryInterface;

/**
 * 管理TradeRefundThirdPart的数据变更
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月30日
 */
class TradeRefundThirdPartManager
{

    use TradeRefundThirdPartValidator;

    /**
     *
     * @var FactoryInterface
     */
    protected $Db;

    /**
     *
     * @var TradeRefundThirdPartRepository
     */
    protected $TradeRefundThirdPartRepository;

    /**
     *
     * @var TradeRefundThirdPartEntity
     */
    protected $TradeRefundThirdPartEntity;

    /**
     *
     * @param FactoryInterface $Db
     */
    public function __construct(FactoryInterface $Db, TradeRefundThirdPartRepository $TradeRefundThirdPartRepository)
    {
        $this->Db                               = $Db;
        $this->TradeRefundThirdPartRepository   = $TradeRefundThirdPartRepository;
    }

    /**
     *
     * @param string $in_refund_no
     * @return TradeRefundThirdPartEntity
     */
    public function load(string $in_refund_no = null) : TradeRefundThirdPartEntity
    {
        if(is_null($in_refund_no)){
            $TradeRefundThirdPartEntity = new TradeRefundThirdPartEntity();
        }else{
            $TradeRefundThirdPartEntity = $this->TradeRefundThirdPartRepository->findOneByInRefundNo($in_refund_no);
            if(empty($TradeRefundThirdPartEntity)){
                $TradeRefundThirdPartEntity = new TradeRefundThirdPartEntity();
            }
        }
        $this->TradeRefundThirdPartEntity = $TradeRefundThirdPartEntity;
        return $this->TradeRefundThirdPartEntity;
    }

    /**
     * 添加一条新数据
     */
    public function insert(TradeRefundEntity $TradeRefundEntity, $send_data) : TradeRefundThirdPartManager
    {
        $this->TradeRefundThirdPartEntity->setInRefundNo($TradeRefundEntity->getInRefundNo());
        $this->TradeRefundThirdPartEntity->setSendData($send_data);
        $this->validateInsert();
        $this->Db->getManager()->persist($this->TradeRefundThirdPartEntity);
        return $this;
    }

    /**
     * 验证
     */
    protected function validateInsert() : void
    {
        $this->validateSendData($this->TradeRefundThirdPartEntity->getSendData());
    }
}