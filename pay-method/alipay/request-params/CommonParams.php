<?php
namespace asbamboo\openpay\payMethod\alipay\requestParams;

use asbamboo\helper\env\Env AS EnvHelper;
use asbamboo\openpay\env;

/**
 * 公共请求参数
 *
 * @see https://docs.open.alipay.com/api
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月9日
 */
class CommonParams implements CommonParamsInterface
{
    use MappingDataTrait;

    /**
     * 必填 最大长度32
     * 支付宝分配给开发者的应用ID
     *
     * @var string
     */
    public $app_id;

    /**
     * 必填 最大长度128
     * 接口名称
     *
     * @var string
     */
    public $method; // 这个参数每个接口有固定值

    public $format;

    /**
     * 必填 最大长度10
     * 请求使用的编码格式，如utf-8,gbk,gb2312等
     *
     * @var string
     */
    public $charset;

    /**
     * 必填 最大长度10
     * 商户生成签名字符串所使用的签名算法类型，目前支持RSA2和RSA，推荐使用RSA2
     *
     * 虽然支付宝接收 RSA2和RSA 但是这个类只支持RSA2
     *
     * @var string
     */
    public $sign_type;

    /**
     * 必填 最大长度344
     * 商户请求参数的签名串，详见签名
     *
     * @see https://docs.open.alipay.com/291/105974
     * @var string
     */
    public $sign;

    /**
     * 必填 最大长度19
     * 发送请求的时间，格式"yyyy-MM-dd HH:mm:ss"
     *
     * @var string
     */
    public $timestamp;

    /**
     * 必填 最大长度3
     * 调用的接口版本
     *
     * @var string
     */
    public $version;

    public $app_auth_token;

    /**
     * 必填
     * 请求参数的集合，最大长度不限，除公共参数外所有请求参数都必须放在这个参数中传递，具体参照各产品快速接入文档
     *
     * @var string
     */
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
     *
     * {@inheritDoc}
     * @see \asbamboo\openpay\payMethod\alipay\requestParams\CommonParamsInterface::setBizContent()
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
     * @see \asbamboo\openpay\payMethod\alipay\requestParams\CommonParamsInterface::makeSign()
     */
    public function makeSign() : string
    {
        $sign           = '';
        $sign_str       = $this->getSignString();
        $private_pem    = EnvHelper::get(env::ALIPAY_RSA_PRIVATE_KEY);
        if(is_file($private_pem)){
            $private_pem    = 'file://' . $private_pem;
        }
        $ssl    = openssl_get_privatekey($private_pem);
        openssl_sign($sign_str, $sign, $ssl, OPENSSL_ALGO_SHA256);
        openssl_free_key($ssl);
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