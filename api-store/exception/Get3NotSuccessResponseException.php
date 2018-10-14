<?php
namespace asbamboo\openpay\apiStore\exception;

use asbamboo\api\exception\ApiException;

/**
 * 向支付平台发起一个请求得到的响应结果表示接口请求没有成功
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月14日
 */
class Get3NotSuccessResponseException extends ApiException
{
    public function __construct(string $message="第三方支付平台返回的响应结果表示业务没有成功.", \Exception $previous = null)
    {
        parent::__construct($message, Code::API3_NOT_SUCCESS_RESPONSE, $previous);
    }
}
