<?php
namespace asbamboo\openpay\payMethod\wxpay\sign;

use asbamboo\helper\env\Env AS EnvHelper;
use asbamboo\openpay\Env;

/**
 * 签名生成器
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月12日
 */
trait SignTrait
{

    /**
     * 生成签名
     * 应该在其他参数都设置完毕以后调用这个方法生成签名字段的值
     *
     * @param array $data
     * @param string $sign_type
     * @return string
     */
    public function makeSign(array $data, string $sign_type) : string
    {
        $sign_str       = $this->getSignString($data);

        if($sign_type == SignType::MD5){
            return strtoupper(md5($sign_str));
        }else if($sign_type = SignType::HMAC_SHA256){
            $sign_key   = EnvHelper::get(Env::WXPAY_SIGN_KEY);
            return strtoupper(hash_hmac("sha256", $sign_str, $sign_key));
        }
    }

    /**
     * 返回签名使用的字符串
     *
     * @return string
     */
    private function getSignString(array $data) : string
    {
        $sign_data  = [];
        ksort($data);
        foreach($data AS $key => $value){
            if($this->checkIsSignKey($key, $value)){
                $sign_data[]    = "{$key}={$value}";
            }
        }

        $sign_key       = EnvHelper::get(Env::WXPAY_SIGN_KEY);
        $sign_data[]    = "key={$sign_key}";

        return implode('&', $sign_data);
    }

    /**
     * 判断一个本实例的一个属性，是不是应该当做签名字符串的一部分。
     * 参考自微信sdk
     *
     * @param string $key 本实例的键名
     * @param mixed $value
     * @return bool
     */
    private function checkIsSignKey(string $key, $value) : bool
    {
        if($key != 'sign' && $value != '' && !is_array($value)){
            return true;
        }
        return false;
    }
}