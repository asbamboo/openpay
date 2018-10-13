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
     * 支付宝环境变量
     *********************************************************************************************/
    // 支付宝接口网关uri
    const ALIPAY_GATEWAY_URI        = 'OPENPAY_ALIPAY_GATEWAY_URI';
    // 用于设置支付宝私银文件地址
    const ALIPAY_RSA_PRIVATE_KEY    = 'OPENPAY_ALIPAY_RSA_PRIVATE';
    // 用于设置支付宝应用公银文件地址
    const ALIPAY_RSA_PUBLIC_KEY     = 'OPENPAY_ALIPAY_RSA_PUBLIC';
    // 用于设置支付宝公银文件地址
    const ALIPAY_RSA_ALIPAY_KEY     = 'OPENPAY_ALIPAY_RSA_ALIPAY';
    // 支付宝 app id
    const ALIPAY_APP_ID             = 'OPENPAY_ALIPAY_APP_ID';
    /*********************************************************************************************/

    /*********************************************************************************************
     * 微信环境变量
     *********************************************************************************************/
    // 微信支付接口网关uri
    const WXPAY_GATEWAY_URI         = 'OPENPAY_WXPAY_GATEWAY_URI';
    // 生成微信签名的key
    const WXPAY_SIGN_KEY            = 'OPENPAY_WXPAY_SIGN_KEY';
    // 微信 appid
    const WXPAY_APP_ID              = 'OPENPAY_WXPAY_APP_ID';
    // 微信商户号
    const WXPAY_MCH_ID              = 'OPENPAY_WXPAY_MCH_ID';
    /*********************************************************************************************/
}