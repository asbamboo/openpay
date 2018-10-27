<?php
namespace asbamboo\openpay\apiStore\handler\v1_0\trade;

use asbamboo\api\apiStore\ApiClassAbstract;
use asbamboo\openpay\apiStore\parameter\v1_0\trade\query\QueryRequestValidateTrait;
use asbamboo\openpay\channel\ChannelManagerInterface;
use asbamboo\openpay\apiStore\parameter\v1_0\trade\pay\PayRequest;
use asbamboo\api\apiStore\ApiRequestParamsInterface;
use asbamboo\api\apiStore\ApiResponseParamsInterface;

/**
 * @name 交易查询
 * @desc 交易查询
 * @request asbamboo\openpay\apiStore\parameter\v1_0\trade\query\QueryRequest
 * @response asbamboo\openpay\apiStore\parameter\v1_0\trade\query\QueryResponse
 * @author 李春寅<licy2013@aliyun.com>
 * @since 2018年10月27日
 */
class Query extends ApiClassAbstract
{
    use QueryRequestValidateTrait;
    
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
        $Channel        = $this->ChannelManager->getChannel(__CLASS__, $channel_name);
        $Response       = $Channel->execute($Params);
        return  $Response;
    }
}