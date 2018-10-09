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
    public $format = 'JSON';
    public $charset = 'utf-8';
    public $sign_type = 'RSA2';
    public $sign;
    public $timestamp = date('Y-m-d H:i:s');
    public $version = '1.0';
    public $notify_url;
    public $app_auth_token;
    public $biz_content;


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

    }

}