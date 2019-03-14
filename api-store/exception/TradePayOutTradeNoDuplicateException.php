<?php
namespace asbamboo\openpay\apiStore\exception;

use asbamboo\api\exception\InvalidArgumentException;

/**
 * trade.pay 接口 out_trade_no 重复请求
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月14日
 */
class TradePayOutTradeNoDuplicateException extends InvalidArgumentException
{
    public function __construct(string $message="重复请求的out_trade_no.", \Exception $previous = null)
    {
        parent::__construct($message, Code::TRADE_PAY_OUT_TRADE_NO_DUPLICATE, $previous);
    }
}
