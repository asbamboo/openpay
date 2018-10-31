<?php
namespace asbamboo\openpay\notify\v1_0\trade;

use asbamboo\openpay\channel\ChannelManagerInterface;
use asbamboo\openpay\apiStore\handler\v1_0\trade\Pay;
use asbamboo\http\ServerRequestInterface;
use asbamboo\openpay\channel\v1_0\trade\payParameter\NotifyResult;

/**
 * 交易支付接口 trade.pay notify处理
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月31日
 */
class PayNotify
{
    /**
     *
     * @var ChannelManagerInterface
     */
    private $ChannelManagr;

    /**
     *
     * @var ServerRequestInterface
     */
    private $Request;

    /**
     *
     * @param ChannelManagerInterface $ChannelManager
     */
    public function __construct(ChannelManagerInterface $ChannelManager, ServerRequestInterface $Request)
    {
        $this->ChannelManager   = $ChannelManager;
        $this->Request          = $Request;
    }

    /**
     * 应该让这个方法透过url访问
     *
     * @param string $channel_name
     */
    public function exec(string $channel)
    {
        /**
         *
         * @var asbamboo\openpay\channel\v1_0\trade\PayInterface $Channel
         * @var NotifyResult $NotifyResult
         */
        $Channel        = $this->ChannelManager->getChannel(Pay::class, $channel);
        $NotifyResult   = $Channel->notify($this->Request);
//         $in_trade_no    = $NotifyResult->getInTradeNo();
//@TODO 数据库修改订单状态 并且返回成功响应
    }
}