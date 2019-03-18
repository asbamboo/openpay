<?php
namespace asbamboo\openpay\apiStore\exception;

use asbamboo\api\exception\InvalidArgumentException;

/**
 * trade.pay 接口 payment 参数无效
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月14日
 */
class TradePayChannelInvalidException extends InvalidArgumentException
{
    public function __construct(string $message="参数 payment 无效.", \Exception $previous = null)
    {
        parent::__construct($message, Code::TRADE_PAY_CHANNEL_INVALID, $previous);
    }
}
