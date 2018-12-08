<?php
namespace asbamboo\openpay\model\tradePayThirdPart;

use asbamboo\openpay\apiStore\exception\TradePayThirdPartInvalidException;

/**
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月30日
 */
trait TradePayThirdPartValidator
{
    /**
     *
     * @param string $third_part json
     * @throws TradePayThirdPartInvalidException
     */
    public function validateSendData($third_part)
    {
        if(trim($third_part) === ''){
            return;
        }
        json_decode($third_part);
        if(json_last_error()){
            throw new TradePayThirdPartInvalidException('third_part 的值不是有效的json格式');
        }
    }
}