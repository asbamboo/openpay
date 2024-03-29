<?php
namespace asbamboo\openpay\channel\v1_0\trade;

use asbamboo\openpay\channel\ChannelInterface;
use asbamboo\openpay\channel\v1_0\trade\RefundQueryParameter\Response;
use asbamboo\openpay\channel\v1_0\trade\RefundQueryParameter\Request;

/**
 * 交易查询接口 trade.refund 1.0版本 渠道处理
 * 
 * @author 李春寅<licy2013@aliyun.com>
 * @since 2018年11月5日
 */
interface RefundQueryInterface extends ChannelInterface
{
    /**
     *
     * @param Request $Request
     * @return Response
     */
    public function execute(Request $Request) : Response;
}