<?php
namespace asbamboo\openpay\apiStore\exception;

/**
 * 异常编号
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月14日
 */
final class Code
{
    const API3_NOT_SUCCESS_RESPONSE = '1000'; // Get3NotSuccessResponseException

    const TRADE_PAY_PAYMENT_INVALID = '2001'; // TradePayPaymentInvalidException
    const TRADE_PAY_TITLE_INVALID = '2002'; // TradePayTitleInvalidException
    const TRADE_PAY_OUT_TRADE_NO_INVALID = '2003'; // TradePayOutTradeNoInvalidException
    const TRADE_PAY_TOTAL_FEE_INVALID = '2004'; // TradePayTotalFeeInvalidException
    const TRADE_PAY_CLIENT_IP_INVALID = '2005'; // TradePayClientIpInvalidException
    const TRADE_PAY_NOTIFY_URL_INVALID = '2006'; // TradePayNotifyUrlInvalidException
    const TRADE_PAY_THIRD_PART_INVALID = '2007'; // TradePayThirdPartInvalidException





}