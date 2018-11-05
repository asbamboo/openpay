<?php
namespace asbamboo\openpay\apiStore\exception;

use asbamboo\api\exception\InvalidArgumentException;

/**
 * trade.refund 接口 third_part 参数无效
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月14日
 */
class TradeRefundThirdPartInvalidException extends InvalidArgumentException
{
    public function __construct(string $message="参数 third_part 无效.", \Exception $previous = null)
    {
        parent::__construct($message, Code::TRADE_REFUND_THIRD_PART_INVALID, $previous);
    }
}
