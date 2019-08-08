<?php
namespace asbamboo\openpay\model\tradePayClob;

use asbamboo\openpay\model\tradePay\TradePayEntity;
use asbamboo\database\FactoryInterface;

/**
 * 管理TradePayClob的数据变更
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月30日
 */
class TradePayClobManager
{

    use TradePayClobValidator;

    /**
     *
     * @var FactoryInterface
     */
    protected $Db;

    /**
     *
     * @var $TradePayClobRepository
     */
    protected $TradePayClobRepository;

    /**
     *
     * @var TradePayClobEntity
     */
    protected $TradePayClobEntity;

    /**
     *
     * @param FactoryInterface $Db
     */
    public function __construct(FactoryInterface $Db, TradePayClobRepository $TradePayClobRepository)
    {
        $this->Db                          = $Db;
        $this->TradePayClobRepository      = $TradePayClobRepository;
    }

    /**
     *
     * @param string $in_trade_no
     * @return TradePayClobEntity
     */
    public function load(string $in_trade_no = null) : TradePayClobEntity
    {
        if(is_null($in_trade_no)){
            $TradePayClobEntity    = new TradePayClobEntity();
        }else{
            $TradePayClobEntity    = $this->TradePayClobRepository->findOneByInTradeNo($in_trade_no);
            if(empty($TradePayClobEntity)){
                $TradePayClobEntity    = new TradePayClobEntity();
            }
        }
        $this->TradePayClobEntity = $TradePayClobEntity;
        return $this->TradePayClobEntity;
    }

    /**
     * 添加一条新数据
     *
     *
     * @param TradePayEntity $TradePayEntity
     * @param string $send_data
     */
    public function insert(TradePayEntity $TradePayEntity, $third_part) : TradePayClobManager
    {
        $this->TradePayClobEntity->setInTradeNo($TradePayEntity->getInTradeNo());
        $this->TradePayClobEntity->setThirdPart($third_part);

        $this->validateInsert();
        $this->Db->getManager()->persist($this->TradePayClobEntity);

        return $this;
    }

    /**
     * 修改app_pay_json字段
     *
     * @param string $app_pay_json json 格式
     * @return TradePayClobManager
     */
    public function updateAppPayJson($app_pay_json) : TradePayClobManager
    {
        $this->TradePayClobEntity->setAppPayJson($app_pay_json);
        $this->validateUpdateAppPayJson();

        return $this;
    }

    /**
     * 修改app_pay_json字段
     * 
     * @param string $onecd_pay_json
     * @return TradePayClobManager
     */
    public function updateOnecdPayJson($onecd_pay_json) : TradePayClobManager
    {
        $this->TradePayClobEntity->setOnecdPayJson($onecd_pay_json);
        $this->validateUpdateOnecdPayJson();
        
        return $this;
    }
    
    /**
     * 验证
     */
    protected function validateInsert() : void
    {
        $this->validateThirdPart($this->TradePayClobEntity->getThirdPart());
    }

    /**
     * 验证
     */
    protected function validateUpdateAppPayJson() : void
    {
        $this->validateAppPayJson($this->TradePayClobEntity->getAppPayJson());
    }

    /**
     * 验证
     */
    protected function validateUpdateOnecdPayJson() : void
    {
        $this->validateOnecdPayJson($this->TradePayClobEntity->getAppPayJson());
    }
}