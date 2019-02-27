<?php
namespace asbamboo\openpay\apiStore\exception;

use asbamboo\api\exception\InvalidArgumentException;

/**
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月14日
 */
class TradePayAppPayJsonInvalidException extends InvalidArgumentException
{
    public function __construct(string $message="参数 app_pay_json 无效.", \Exception $previous = null)
    {
        parent::__construct($message, Code::TRADE_PAY_APP_PAY_JSON_INVALID, $previous);
    }
}
