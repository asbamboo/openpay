<?php
namespace asbamboo\openpay\channel\v1_0\trade;

use asbamboo\openpay\channel\ChannelInterface;
use asbamboo\openpay\apiStore\parameter\v1_0\trade\PayRequest;
use asbamboo\openpay\apiStore\parameter\v1_0\trade\PayResponse;

/**
 * 交易支付接口 trade.pay 1.0版本 渠道处理
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月19日
 */
interface PayInterface extends ChannelInterface
{
    /**
     *
     * @param PayRequest $PayRequest
     * @return PayResponse
     */
    public function execute(PayRequest $PayRequest) : PayResponse;
}