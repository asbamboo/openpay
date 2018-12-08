<?php
namespace asbamboo\openpay\apiStore\exception;

use asbamboo\api\exception\ApiException;

/**
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月1日
 */
class NotFoundTradePayException extends ApiException
{
    public function __construct(string $message="交易不存在.", \Exception $previous = null)
    {
        parent::__construct($message, Code::NOT_FOUND_TRADE_PAY, $previous);
    }
}
