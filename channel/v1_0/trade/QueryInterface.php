<?php
namespace asbamboo\openpay\channel\v1_0\trade;

use asbamboo\openpay\channel\ChannelInterface;
use asbamboo\openpay\channel\v1_0\trade\queryParameter\Request;
use asbamboo\openpay\channel\v1_0\trade\queryParameter\Response;

/**
 * 交易查询接口 trade.query 1.0版本 渠道处理
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月19日
 */
interface QueryInterface extends ChannelInterface
{
    /**
     * 
     * @param Request $Request
     * @return Response
     */
    public function execute(Request $Request) : Response;
}