<?php
namespace asbamboo\openpay;

/**
 * 常量配置
 * 环境变量的key
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月10日
 */
class Env
{
    // 用于设置支付宝私银文件地址
    const ALIPAY_RSA_PRIVATE_KEY    = 'OPENPAY_ALIPAY_RSA_PRIVATE';

    // 用于设置支付宝应用公银文件地址
    const ALIPAY_RSA_PUBLIC_KEY    = 'OPENPAY_ALIPAY_RSA_PUBLIC';

    // 用于设置支付宝公银文件地址
    const ALIPAY_RSA_ALIPAY_KEY    = 'OPENPAY_ALIPAY_RSA_ALIPAY';

    // 生成微信签名的key
    const WXPAY_SIGN_KEY            = 'OPENPAY_WXPAY_SIGN_KEY';
}