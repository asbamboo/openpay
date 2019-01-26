<?php
namespace asbamboo\openpay;

/**
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年9月30日
 */
final class Event
{
    /**
     * 接收支付通知的程序在收到通知，程序处理前先触发这个事件。 PayNotify对象作为参数。
     *
     * @see \asbamboo\openpay\notify\v1_0\trade\PayNotify
     * @var string
     */
    const PAY_NOTIFY_PRE_EXEC   = 'openpay.pay.notity.pre.exec';

    /**
     * 接收支付通知的程序在收到通知，程序处理完成后触发这个事件。 PayNotify $NotifyResult 对象作为参数。
     *
     * @var string
     */
    const PAY_NOTIFY_AFTER_EXEC = 'openpay.pay.notity.after.exec';

    /**
     * 接收支付通知的程序（页面跳转回来）在收到通知，程序处理前先触发这个事件。 PayReturn对象作为参数。
     *
     * @see \asbamboo\openpay\notify\v1_0\trade\PayReturn
     * @var string
     */
    const PAY_RETURN_PRE_EXEC   = 'openpay.pay.return.pre.exec';

    /**
     * 接收支付通知的程序（页面跳转回来）在收到通知，程序处理完成后触发这个事件。 PayReturn $RerurnResult 对象作为参数。
     *
     * @var string
     */
    const PAY_RETURN_AFTER_EXEC = 'openpay.pay.return.after.exec';
}