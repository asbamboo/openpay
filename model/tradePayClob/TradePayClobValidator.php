<?php
namespace asbamboo\openpay\model\tradePayClob;

use asbamboo\openpay\apiStore\exception\TradePayThirdPartInvalidException;
use asbamboo\openpay\apiStore\exception\TradePayAppPayJsonInvalidException;

/**
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月30日
 */
trait TradePayClobValidator
{
    /**
     *
     * @param string $third_part json
     * @throws TradePayThirdPartInvalidException
     */
    public function validateThirdPart($third_part)
    {
        if(trim($third_part) === ''){
            return;
        }
        json_decode($third_part);
        if(json_last_error()){
            throw new TradePayThirdPartInvalidException('third_part 的值不是有效的json格式');
        }
    }

    /**
     * 
     * @param string $app_pay_json
     * @throws TradePayAppPayJsonInvalidException
     */
    public function validateAppPayJson($app_pay_json)
    {
        if(trim($app_pay_json) === ''){
            return;
        }
        json_decode($app_pay_json);
        if(json_last_error()){
            throw new TradePayAppPayJsonInvalidException('app_pay_json 的值不是有效的json格式');
        }
    }


    /**
     * 
     * @param string $onecd_pay_json
     * @throws TradePayAppPayJsonInvalidException
     */
    public function validateOnecdPayJson($onecd_pay_json)
    {
        if(trim($onecd_pay_json) === ''){
            return;
        }
        json_decode($onecd_pay_json);
        if(json_last_error()){
            throw new TradePayAppPayJsonInvalidException('onecd_pay_json 的值不是有效的json格式');
        }
    }
}