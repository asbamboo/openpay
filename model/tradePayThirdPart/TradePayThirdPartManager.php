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
    protected $Db;

    /**
     *
     * @var TradePayThirdPartEntity
     */
    protected $TradePayThirdPartEntity;

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
     * @param TradePayEntity $TradePayEntity
     * @return self
     */
    public function load(TradePayThirdPartEntity $TradePayThirdPartEntity) : self
    {
        $this->TradePayThirdPartEntity = $TradePayThirdPartEntity;
        return $this;
    }

    /**
     * 添加一条新数据
     *
     *
     * @param TradePayEntity $TradePayEntity
     * @param string $send_data
     */
    public function insert(TradePayEntity $TradePayEntity, $send_data) : self
    {
        $this->TradePayThirdPartEntity->setInTradeNo($TradePayEntity->getInTradeNo());
        $this->TradePayThirdPartEntity->setSendData($send_data);

        $this->validateInsert();
        $this->Db->getManager()->persist($this->TradePayThirdPartEntity);

        return $this;
    }

    /**
     * 验证
     */
    protected function validateInsert() : void
    {
        $this->validateSendData($this->TradePayThirdPartEntity->getSendData());
    }
}