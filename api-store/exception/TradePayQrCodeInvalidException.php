<?php
namespace asbamboo\openpay\apiStore\exception;

use asbamboo\api\exception\InvalidArgumentException;

/**
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2019年2月27日
 */
class TradePayQrCodeInvalidException extends InvalidArgumentException
{
    public function __construct(string $message="参数 qr_code 无效.", \Exception $previous = null)
    {
        parent::__construct($message, Code::TRADE_PAY_QR_CODE_INVALID, $previous);
    }
}
