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
     * 核心请求异常
     *********************************************************************************************/
    const API3_NOT_SUCCESS_RESPONSE = '1000'; // Get3NotSuccessResponseException
    const APP_KEY_INVALID           = '1001'; // app key 无效  抛出这个异常的接口应该能接收app key参数
    const NOT_FOUND_TRADE_PAY       = '1002'; // 找不到交易
    const NOT_FOUND_TRADE_REFUND    = '1003'; // 找不到退款
    /*********************************************************************************************/

    /*********************************************************************************************
     * 交易支付家口trade.pay接口的异常
     *********************************************************************************************/
    const TRADE_PAY_CHANNEL_INVALID = '2001'; // TradePayPaymentInvalidException
    const TRADE_PAY_TITLE_INVALID = '2002'; // TradePayTitleInvalidException
    const TRADE_PAY_OUT_TRADE_NO_INVALID = '2003'; // TradePayOutTradeNoInvalidException
    const TRADE_PAY_TOTAL_FEE_INVALID = '2004'; // TradePayTotalFeeInvalidException
    const TRADE_PAY_CLIENT_IP_INVALID = '2005'; // TradePayClientIpInvalidException
    const TRADE_PAY_THIRD_PART_INVALID = '2006'; // TradePayThirdPartInvalidException
    const TRADE_PAY_THIRD_TRADE_NO_INVALID = '2007'; // TradePayThirdTradeNoInvalidException
    const TRADE_PAY_TRADE_STATUS_INVALID = '2008'; // TradePayTradeStatusInvalidException
    const TRADE_PAY_NOTIFY_URL_INVALID = '2009'; // TradePayNotifyUrlInvalidException
    const TRADE_PAY_RETURN_URL_INVALID = '2010'; // TradePayReturnUrlInvalidException
    const TRADE_PAY_APP_PAY_JSON_INVALID = '2011'; // TradePayAppPayJsonInvalidException
    const TRADE_PAY_QR_CODE_INVALID = '2012'; // TradePayQrCodeInvalidException
    const TRADE_PAY_OUT_TRADE_NO_DUPLICATE = '2013'; // TradePayOutTradeNoDuplicateException
    /*********************************************************************************************/

    /*********************************************************************************************
     * 交易支付家口trade.query接口的异常
     *********************************************************************************************/
    const TRADE_QUERY_NOT_FOUND_INVALID = '3001'; // TradeQueryNotFoundInvalidException
    /*********************************************************************************************/

    /*********************************************************************************************
     * 交易支付家口trade.refund接口的异常
     *********************************************************************************************/
    const TRADE_REFUND_NOT_FOUND_INVALID = '4001'; // TradeRefundNotFoundInvalidException
    const TRADE_REFUND_OUT_REFUND_NO_INVALID = '4002'; // TradeRefundOutRefundNoInvalidException
    const TRADE_REFUND_REFUND_FEE_INVALID = '4003'; // TradeRefundRefundFeeInvalidException
    const TRADE_REFUND_STATUS_INVALID = '4004'; // TradeRefundStatusInvalidException
    const TRADE_REFUND_THIRD_PART_INVALID = '4005'; // TradeRefundThirdPartInvalidException
    const TRADE_REFUND_TRADE_STATUS_INVALID = '4006'; // TradeRefundTradeStatusInvalidException
    const TRADE_REFUND_TRADE_NOTIFY_URL_INVALID = '4007'; // TradeRefundNorifyUrlInvalidException
    const TRADE_REFUND_STATUS_REQUESTED = '4008'; // TradeRefundStatusRequestedException
    /*********************************************************************************************/

    /*********************************************************************************************
     * 交易支付家口trade.cancel接口的异常
     *********************************************************************************************/
    const TRADE_CANCEL_NOT_FOUND_INVALID = '5001'; // TradeCancelNotFoundInvalidException
    const TRADE_CANCEL_NOT_ALLOWED = '5002'; // TradeCancelNotAllowedException
    /*********************************************************************************************/
}