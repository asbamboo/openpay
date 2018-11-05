<?php
namespace asbamboo\openpay\apiStore\exception;

use asbamboo\api\exception\ApiException;

/**
 * trade.refund 接口 对接应用传入的退款单号不合法
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月14日
 */
class TradeRefundOutRefundNoInvalidException extends ApiException
{
    public function __construct(string $message="退款单号不符合规则.", \Exception $previous = null)
    {
        parent::__construct($message, Code::TRADE_REFUND_OUT_REFUND_NO_INVALID, $previous);
    }
}
