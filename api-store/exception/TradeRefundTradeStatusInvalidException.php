<?php
namespace asbamboo\openpay\apiStore\exception;

use asbamboo\api\exception\ApiException;

/**
 * trade.refund 接口 对接应用订单状态不能退款
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月14日
 */
class TradeRefundTradeStatusInvalidException extends ApiException
{
    public function __construct(string $message="该订单状态不允许退款.", \Exception $previous = null)
    {
        parent::__construct($message, Code::TRADE_REFUND_TRADE_STATUS_INVALID, $previous);
    }
}
