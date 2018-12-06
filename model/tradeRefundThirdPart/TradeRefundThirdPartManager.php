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
     * 添加一条新数据
     *
     * @param TradeRefundThirdPartEntity $TradeRefundThirdPartEntity
     */
    public function insert(TradeRefundEntity $TradeRefundEntity, $send_data) : TradeRefundThirdPartEntity
    {
        $TradeRefundThirdPartEntity = new TradeRefundThirdPartEntity();
        $TradeRefundThirdPartEntity->setInRefundNo($TradeRefundEntity->getInRefundNo());
        $TradeRefundThirdPartEntity->setSendData($send_data);
        $this->validateInsert($TradeRefundThirdPartEntity);
        $this->Db->getManager()->persist($TradeRefundThirdPartEntity);
        return $TradeRefundThirdPartEntity;
    }

    /**
     * 验证
     *
     * @param TradeRefundThirdPartEntity $TradeRefundThirdPartEntity
     */
    private function validateInsert(TradeRefundThirdPartEntity $TradeRefundThirdPartEntity) : void
    {
        $this->validateSendData($TradeRefundThirdPartEntity->getSendData());
    }
}