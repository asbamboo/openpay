<?php
namespace asbamboo\openpay;

/**
 * 内部常量
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月13日
 */
final class Constant
{
    // 展示二维码的url
    const QRCODE_URL                = "/qrcode";

    // 微信扫码支付的notify url
    const WXPAY_QRCD_NOTIFY_URL     = "/wxpay-qrcd-notify";

    // 微信扫码支付的notify url
    const ALIPAY_QRCD_NOTIFY_URL    = "/alipay-qrcd-notify";
}