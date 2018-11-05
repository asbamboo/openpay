<?php
namespace asbamboo\openpay\apiStore\exception;

use asbamboo\api\exception\ApiException;

/**
 * trade.refund 接口 对接应用传入的退款金额不符合规则
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月14日
 */
class TradeRefundRefundFeeInvalidException extends ApiException
{
    public function __construct(string $message="退款金额不符合规则.", \Exception $previous = null)
    {
        parent::__construct($message, Code::TRADE_REFUND_REFUND_FEE_INVALID, $previous);
    }
}
