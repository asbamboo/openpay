<?php
namespace asbamboo\openpay\apiStore\exception;

use asbamboo\api\exception\ApiException;

/**
 * trade.cancel 接口 不允许取消
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月14日
 */
class TradeCancelNotAllowedException extends ApiException
{
    public function __construct(string $message="不允许取消.", \Exception $previous = null)
    {
        parent::__construct($message, Code::TRADE_CANCEL_NOT_ALLOWED, $previous);
    }
}
