<?php
namespace asbamboo\openpay;

/**
 * 常量配置
 * 环境变量的key
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月10日
 */
final class Env
{
    /*********************************************************************************************
     * 接收第三方平台消息推送的url
     *********************************************************************************************/
    const TRADE_PAY_NOTIFY_URL      = 'OPENPAY_TRADE_PAY_NOTIFY_URL';   // 服务端后台处理的url
    const TRADE_PAY_RETURN_URL      = 'OPENPAY_TRADE_PAY_RETURN_URL';   // 从第三方平台调转回来的url
    /*********************************************************************************************/
}