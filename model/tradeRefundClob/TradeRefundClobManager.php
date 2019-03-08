<?php
namespace asbamboo\openpay\model\tradeRefundClob;

use asbamboo\openpay\model\tradeRefund\TradeRefundEntity;
use asbamboo\database\FactoryInterface;

/**
 * 管理TradeRefundClob的数据变更
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月30日
 */
class TradeRefundClobManager
{

    use TradeRefundClobValidator;

    /**
     *
     * @var FactoryInterface
     */
    protected $Db;

    /**
     *
     * @var TradeRefundClobRepository
     */
    protected $TradeRefundClobRepository;

    /**
     *
     * @var TradeRefundClobEntity
     */
    protected $TradeRefundClobEntity;

    /**
     *
     * @param FactoryInterface $Db
     */
    public function __construct(FactoryInterface $Db, TradeRefundClobRepository $TradeRefundClobRepository)
    {
        $this->Db                          = $Db;
        $this->TradeRefundClobRepository   = $TradeRefundClobRepository;
    }

    /**
     *
     * @param string $in_refund_no
     * @return TradeRefundClobEntity
     */
    public function load(string $in_refund_no = null) : TradeRefundClobEntity
    {
        if(is_null($in_refund_no)){
            $TradeRefundClobEntity = new TradeRefundClobEntity();
        }else{
            $TradeRefundClobEntity = $this->TradeRefundClobRepository->findOneByInRefundNo($in_refund_no);
            if(empty($TradeRefundClobEntity)){
                $TradeRefundClobEntity = new TradeRefundClobEntity();
            }
        }
        $this->TradeRefundClobEntity = $TradeRefundClobEntity;
        return $this->TradeRefundClobEntity;
    }

    /**
     * 添加一条新数据
     */
    public function insert(TradeRefundEntity $TradeRefundEntity, $third_part) : TradeRefundClobManager
    {
        $this->TradeRefundClobEntity->setInRefundNo($TradeRefundEntity->getInRefundNo());
        $this->TradeRefundClobEntity->setThirdPart($third_part);
        $this->validateInsert();
        $this->Db->getManager()->persist($this->TradeRefundClobEntity);
        return $this;
    }

    /**
     * 验证
     */
    protected function validateInsert() : void
    {
        $this->validateThirdPart($this->TradeRefundClobEntity->getThirdPart());
    }
}