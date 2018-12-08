<?php
namespace asbamboo\openpay\model\tradeRefundThirdPart;

use asbamboo\openpay\apiStore\exception\TradeRefundThirdPartInvalidException;

/**
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月30日
 */
trait TradeRefundThirdPartValidator
{
    /**
     *
     * @param string $third_part json
     * @throws TradeRefundThirdPartInvalidException
     */
    public function validateSendData($third_part)
    {
        if(trim($third_part) === ''){
            return;
        }
        json_decode($third_part);
        if(json_last_error()){
            throw new TradeRefundThirdPartInvalidException('third_part 的值不是有效的json格式');
        }
    }
}