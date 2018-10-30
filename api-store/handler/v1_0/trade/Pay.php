<?php
namespace asbamboo\openpay\apiStore\handler\v1_0\trade;

use asbamboo\api\apiStore\ApiRequestParamsInterface;
use asbamboo\api\apiStore\ApiResponseParamsInterface;
use asbamboo\openpay\channel\ChannelManagerInterface;
use asbamboo\openpay\apiStore\parameter\v1_0\trade\pay\PayRequest;
use asbamboo\openpay\model\tradePay\TradePayManager;
use asbamboo\openpay\model\tradePayThirdPart\TradePayThirdPartManager;
use asbamboo\openpay\model\tradePay\TradePayEntity;
use asbamboo\api\apiStore\ApiClassInterface;
use asbamboo\openpay\model\tradePayThirdPart\TradePayThirdPartEntity;
use asbamboo\database\Factory;

/**
 * @name 交易支付
 * @desc 发起交易支付
 * @request asbamboo\openpay\apiStore\parameter\v1_0\trade\pay\PayRequest
 * @response asbamboo\openpay\apiStore\parameter\v1_0\trade\pay\PayResponse
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月13日
 */
class Pay implements ApiClassInterface
{
    /**
     * 渠道管理器
     *
     * @var ChannelManagerInterface
     */
    private $ChannelManager;

    /**
     *
     * @var Factory
     */
    private $Db;

    /**
     *
     * @var TradePayManager
     */
    private $TradePayManager;

    /**
     *
     * @var TradePayThirdPartManager
     */
    private $TradePayThirdPartManager;

    /**
     *
     * @param ChannelManagerInterface $Client
     */
    public function __construct(ChannelManagerInterface $ChannelManager, Factory $Db, TradePayManager $TradePayManager, TradePayThirdPartManager $TradePayThirdPartManager)
    {
        $this->Db                       = $Db;
        $this->ChannelManager           = $ChannelManager;
        $this->TradePayManager          = $TradePayManager;
        $this->TradePayThirdPartManager = $TradePayThirdPartManager;
    }

    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\api\apiStore\ApiClassAbstract::successApiResponseParams()
     * @var PayRequest $Params
     */
    public function exec(ApiRequestParamsInterface $Params) : ?ApiResponseParamsInterface
    {
        /**
         * 创建交易数据信息
         *
         * @var \asbamboo\openpay\model\tradePay\TradePayEntity $TradePayEntity
         */
        $TradePayEntity = new TradePayEntity();
        $TradePayEntity->setChannel($Params->getChannel());
        $TradePayEntity->setTitle($Params->getTitle());
        $TradePayEntity->setTotalFee($Params->getTotalFee());
        $TradePayEntity->setOutTradeNo($Params->getOutTradeNo());
        $TradePayEntity->setClientIp($Params->getClientIp());
        $this->TradePayManager->insert($TradePayEntity);

        $TradePayThirdPartEntity = new TradePayThirdPartEntity();
        $TradePayThirdPartEntity->setSendData($Params->getThirdPart());
        $this->TradePayThirdPartManager->insert($TradePayThirdPartEntity);

        $Params->setOutTradeNo($TradePayEntity->getInTradeNo());

        /**
         * 发起第三方渠道请求
         *
         * @var PayInterface $Channel
         */
        $channel_name   = $Params->getChannel();
        $Channel        = $this->ChannelManager->getChannel(__CLASS__, $channel_name);
        $Response       = $Channel->execute($Params);

        /**
         * 数据保存
         */
        $this->Db->getManager()->flush();

        /**
         * 返回
         */
        return $Response;
    }
}
