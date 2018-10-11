<?php
namespace asbamboo\openpay\payment\alipay\requestParams;

use asbamboo\openpay\common\traits\MappingDataTrait;
use asbamboo\helper\env\Env AS EnvHelper;
use asbamboo\openpay\env;

/**
 * 公共请求参数
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月9日
 */
class CommonParams implements CommonParamsInterface
{
    use MappingDataTrait;

    public $app_id;
    public $method; // 这个参数每个接口有固定值
    public $format;
    public $charset;
    public $sign_type;
    public $sign;
    public $timestamp;
    public $version;
    public $notify_url;
    public $app_auth_token;
    public $biz_content;

    /**
     * - 初始化一些接口固定值的参数
     */
    public function __construct()
    {
        $this->format       = 'JSON';
        $this->charset      = 'UTF-8';
        $this->sign_type    = 'RSA2'; // sign_type不允许改变，如果改变会导致签名错误
        $this->timestamp    = date('Y-m-d H:i:s');
        $this->version      = '1.0';
    }

    /**
     * 数据映射配置
     *  - 返回的数组时请求参数的key与接受参数的key的映射关系
     *
     * @return array
     */
    private function mappingConfig() : array
    {
        return [
            'app_id'            => 'app_id',
            'notify_url'        => 'notify_url',
            'app_auth_token'    => 'app_auth_token',
        ];
    }

    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\openpay\payment\alipay\requestParams\CommonParamsInterface::setBizContent()
     */
    public function setBizContent(BizContentInterface $BizContent) : CommonParamsInterface
    {
        $biz_content        = get_object_vars($BizContent);
        $this->biz_content  = json_encode($biz_content, JSON_UNESCAPED_UNICODE);
        return $this;
    }

    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\openpay\payment\alipay\requestParams\CommonParamsInterface::makeSign()
     */
    public function makeSign() : string
    {
        $sign           = '';
        $sign_str       = $this->getSignString();
        $private_pem    = EnvHelper::get(env::ALIPAY_RSA_PRIVATE_KEY);
        if(is_file($private_pem)){
            $private_pem    = 'file://' . $private_pem;
        }
        $parse_key      = openssl_get_privatekey($private_pem);
        openssl_sign($sign_str, $sign, $parse_key, OPENSSL_ALGO_SHA256);
        openssl_free_key($parse_key);
        return base64_encode($sign);
    }

    /**
     * 返回签名使用的字符串
     *
     * @return string
     */
    private function getSignString() : string
    {
        $sign_data  = [];
        $data       = get_object_vars($this);
        ksort($data);
        foreach($data AS $key => $value){
            if($this->checkIsSignKey($key)){
                $sign_data[]    = "{$key}={$value}";
            }
        }
        return implode('&', $sign_data);
    }

    /**
     * 判断一个本实例的一个属性，是不是应该当做签名字符串的一部分。
     *
     *  - sign字段不是签名字符串
     *  - self::checkIsEmpty 不是签名字符串
     *  - 上传文件字段 不是签名字符串
     *
     * @param string $key 本实例的键名
     * @return bool
     */
    private function checkIsSignKey($key) : bool
    {
        if($key != 'sign' && $this->checkIsEmpty($this->{$key}) == false && "@" != substr($this->{$key}, 0, 1)){
            return true;
        }
        return false;
    }

    /**
     * 判断一个参数的值是否是空
     *
     * 下列情况返回true
     *  - 空字符串|trim($value) === ''
     *  - null值|$value === null
     *  - 未定义|!isset($value)
     *
     * @param mixed $value
     * @return bool
     */
    private function checkIsEmpty($value) : bool
    {
        if(!isset($value)){
            return true;
        }

        if($value === null){
            return true;
        }

        if(trim($value) === ""){
            return true;
        }

        return false;
    }
}