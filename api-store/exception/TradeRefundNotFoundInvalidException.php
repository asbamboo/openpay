<?php
namespace asbamboo\openpay\apiStore\exception;

use asbamboo\api\exception\ApiException;

/**
 * trade.refund 接口 找不到相应的支付记录
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月14日
 */
class TradeRefundNotFoundInvalidException extends ApiException
{
    public function __construct(string $message="对应的订单记录找不到.", \Exception $previous = null)
    {
        parent::__construct($message, Code::TRADE_REFUND_NOT_FOUND_INVALID, $previous);
    }
}
