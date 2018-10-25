<?php
namespace asbamboo\openpay\apiStore\handler\v1_0\trade;

use asbamboo\api\apiStore\ApiClassAbstract;
use asbamboo\api\apiStore\ApiRequestParamsInterface;
use asbamboo\openpay\apiStore\parameter\v1_0\trade\PayRequest;
use asbamboo\api\apiStore\ApiResponseParamsInterface;
use asbamboo\openpay\apiStore\parameter\v1_0\trade\PayRequestValidateTrait;
use asbamboo\openpay\channel\ChannelManagerInterface;

/**
 * @name 交易支付
 * @desc 发起交易支付
 * @request asbamboo\openpay\apiStore\parameter\v1_0\trade\PayRequest
 * @response asbamboo\openpay\apiStore\parameter\v1_0\trade\PayResponse
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月13日
 */
class Pay extends ApiClassAbstract
{
    use PayRequestValidateTrait;

    /**
     * 渠道管理器
     *
     * @var ChannelManagerInterface
     */
    private $ChannelManager;

    /**
     *
     * @param ChannelManagerInterface $Client
     */
    public function __construct(ChannelManagerInterface $ChannelManager)
    {
        $this->ChannelManager   = $ChannelManager;
    }

    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\api\apiStore\ApiClassAbstract::successApiResponseParams()
     * @var PayRequest $Params
     */
    public function successApiResponseParams(ApiRequestParamsInterface $Params) : ?ApiResponseParamsInterface
    {
        $channel_name   = $Params->getChannel();
        $PayChannel     = $this->ChannelManager->getChannel(__CLASS__, $channel_name);
        return  $PayChannel->execute($Params);
    }
}
