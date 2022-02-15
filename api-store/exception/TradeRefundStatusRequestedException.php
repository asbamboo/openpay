<?php
namespace asbamboo\openpay\apiStore\exception;

use asbamboo\api\exception\ApiException;

/**
 * trade.refund 接口 对接应用订单状态不能退款
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月14日
 */
class TradeRefundStatusRequestedException extends ApiException
{
    public function __construct(string $message="该退款单状态已经是请求中，请使用退款查询接口.", \Exception $previous = null)
    {
        parent::__construct($message, Code::TRADE_REFUND_STATUS_REQUESTED, $previous);
    }
}
