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
    /*********************************************************************************************
     * 请求第三方平台接口发生的异常
     *********************************************************************************************/
    const API3_NOT_SUCCESS_RESPONSE = '1000'; // Get3NotSuccessResponseException
    /*********************************************************************************************/

    /*********************************************************************************************
     * 交易支付家口trade.pay接口的异常
     *********************************************************************************************/
    const TRADE_PAY_PAYMENT_INVALID = '2001'; // TradePayPaymentInvalidException
    const TRADE_PAY_TITLE_INVALID = '2002'; // TradePayTitleInvalidException
    const TRADE_PAY_OUT_TRADE_NO_INVALID = '2003'; // TradePayOutTradeNoInvalidException
    const TRADE_PAY_TOTAL_FEE_INVALID = '2004'; // TradePayTotalFeeInvalidException
    const TRADE_PAY_CLIENT_IP_INVALID = '2005'; // TradePayClientIpInvalidException
    const TRADE_PAY_THIRD_PART_INVALID = '2006'; // TradePayThirdPartInvalidException
    /*********************************************************************************************/
}