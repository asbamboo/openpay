<?php
namespace asbamboo\openpay\apiStore\exception;

use asbamboo\api\exception\ApiException;

/**
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月1日
 */
class AppKeyInvalidException extends ApiException
{
    public function __construct(string $message="App key 无效.", \Exception $previous = null)
    {
        parent::__construct($message, Code::INVALID_APP_KEY, $previous);
    }
}
