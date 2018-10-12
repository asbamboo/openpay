<?php
namespace asbamboo\openpay\payMethod\wxpay\requestParams;

use asbamboo\openpay\payMethod\wxpay\sign\SignType;
use asbamboo\openpay\payMethod\wxpay\sign\SignTrait;

/**
 * 公共请求参数
 * @see https://pay.weixin.qq.com/wiki/doc/api/index.html
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月9日
 */
abstract class RequestParams implements RequestParamsInterface
{
    use SignTrait;

    /**
     * 必填
     * 公众账号ID
     *
     * @var string(32)
     */
    public $appid;

    /**
     * 必填
     * 商户号
     *
     * @var string(32)
     */
    public $mch_id;

    public $sub_appid;
    public $sub_mch_id;
    public $device_info;

    /**
     * 必填
     * 随机字符串
     *
     * @var string(32)
     */
    public $nonce_str;

    /**
     * 必填
     * 签名
     *
     * @var string(32)
     */
    public $sign;

    /**
     * 签名类型
     * 签名类型，默认为MD5，支持HMAC-SHA256和MD5。
     *
     * @var string(32)
     */
    public $sign_type;

    public function __construct()
    {
        $this->nonce_str    = md5(uniqid());
        $this->sign_type    = SignType::MD5;
    }

    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\openpay\payMethod\wxpay\requestParams\RequestParamsInterface::mappingData()
     */
    public function mappingData(array $assign_data) : void
    {
        foreach($assign_data AS $key => $value){
            if(property_exists($this, $key)){
                $this->{$key}   = $value;
            }
        }

        $this->sign = $this->makeSign(get_object_vars($this), $this->sign_type);
    }
}