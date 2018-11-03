<?php
namespace asbamboo\openpay\model\tradePayThirdPart;

use asbamboo\database\Factory;

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
     * @var Factory
     */
    private $Db;

    /**
     *
     * @param Factory $Db
     */
    public function __construct(Factory $Db)
    {
        $this->Db   = $Db;
    }

    /**
     * 添加一条新数据
     *
     * @param TradePayThirdPartEntity $TradePayThirdPartEntity
     */
    public function insert(TradePayThirdPartEntity $TradePayThirdPartEntity) : void
    {
        if(is_null($TradePayThirdPartEntity->getSendData())){
            $TradePayThirdPartEntity->setSendData('[]');
        }
        $this->validateInsert($TradePayThirdPartEntity);
        $this->Db->getManager()->persist($TradePayThirdPartEntity);
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