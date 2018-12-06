<?php
namespace asbamboo\openpay\model\tradePayThirdPart;

use asbamboo\openpay\model\tradePay\TradePayEntity;
use asbamboo\database\FactoryInterface;

/**
 * 管理TradePayThirdPart的数据变更
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月30日
 */
class TradePayThirdPartManager
{

    use TradePayThirdPartValidator;

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
     *
     * @param TradePayEntity $TradePayEntity
     * @param string $send_data
     */
    public function insert(TradePayEntity $TradePayEntity, $send_data) : TradePayThirdPartEntity
    {
        $TradePayThirdPartEntity    = new TradePayThirdPartEntity();
        $TradePayThirdPartEntity->setInTradeNo($TradePayEntity->getInTradeNo());
        $TradePayThirdPartEntity->setSendData($send_data);

        $this->validateInsert($TradePayThirdPartEntity);
        $this->Db->getManager()->persist($TradePayThirdPartEntity);

        return $TradePayThirdPartEntity;
    }

    /**
     * 验证
     *
     * @param TradePayThirdPartEntity $TradePayThirdPartEntity
     */
    private function validateInsert(TradePayThirdPartEntity $TradePayThirdPartEntity) : void
    {
        $this->validateSendData($TradePayThirdPartEntity->getSendData());
    }
}