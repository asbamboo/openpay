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
     * @var TradeRefundThirdPartEntity
     */
    protected $TradeRefundThirdPartEntity;

    /**
     *
     * @param FactoryInterface $Db
     */
    public function __construct(FactoryInterface $Db)
    {
        $this->Db   = $Db;
    }

    /**
     *
     * @param TradeRefundThirdPartEntity $TradeRefundThirdPartEntity
     * @return self
     */
    public function load(TradeRefundThirdPartEntity $TradeRefundThirdPartEntity) : self
    {
        $this->TradeRefundThirdPartEntity = $TradeRefundThirdPartEntity;
        return $this;
    }

    /**
     * 添加一条新数据
     */
    public function insert(TradeRefundEntity $TradeRefundEntity, $send_data) : self
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
    private function validateInsert() : void
    {
        $this->validateSendData($this->TradeRefundThirdPartEntity->getSendData());
    }
}