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
     * @var $TradePayThirdPartRepository
     */
    protected $TradePayThirdPartRepository;

    /**
     *
     * @var TradePayThirdPartEntity
     */
    protected $TradePayThirdPartEntity;

    /**
     *
     * @param FactoryInterface $Db
     */
    public function __construct(FactoryInterface $Db, TradePayThirdPartRepository $TradePayThirdPartRepository)
    {
        $this->Db                               = $Db;
        $this->TradePayThirdPartRepository      = $TradePayThirdPartRepository;
    }

    /**
     *
     * @param string $in_trade_no
     * @return TradePayThirdPartEntity
     */
    public function load(string $in_trade_no = null) : TradePayThirdPartEntity
    {
        if(is_null($in_trade_no)){
            $TradePayThirdPartEntity    = new TradePayThirdPartEntity();
        }else{
            $TradePayThirdPartEntity    = $this->TradePayThirdPartRepository->findOneByInTradeNo($in_trade_no);
            if(empty($TradePayThirdPartEntity)){
                $TradePayThirdPartEntity    = new TradePayThirdPartEntity();
            }
        }
        $this->TradePayThirdPartEntity = $TradePayThirdPartEntity;
        return $this->TradePayThirdPartEntity;
    }

    /**
     * 添加一条新数据
     *
     *
     * @param TradePayEntity $TradePayEntity
     * @param string $send_data
     */
    public function insert(TradePayEntity $TradePayEntity, $send_data) : TradePayThirdPartManager
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