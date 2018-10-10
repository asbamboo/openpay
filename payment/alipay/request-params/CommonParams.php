<?php
namespace asbamboo\openpay\payment\alipay\requestParams;

use asbamboo\openpay\common\traits\MappingDataTrait;

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
        $this->charset      = 'utf-8';
        $this->sign_type    = 'RSA2';
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
        $this->biz_content  = get_object_vars($BizContent);
        return $this;
    }

    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\openpay\payment\alipay\requestParams\CommonParamsInterface::makeSign()
     */
    public function makeSign() : string
    {
        return '';
    }

}