<?php
namespace asbamboo\openpay\payment\wxpay\requestParams;

use asbamboo\openpay\common\traits\MappingDataTrait;

/**
 * 公共请求参数
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月9日
 */
abstract class RequestParams implements RequestParamsInterface
{
    use MappingDataTrait;

    public $appid;
    public $mch_id;
    public $sub_appid;
    public $sub_mch_id;
    public $device_info;
    public $nonce_str = uniqid();
    public $sign;
    public $sign_type = 'MD5';


    /**
     * 数据映射配置
     *  - 返回的数组时请求参数的key与接受参数的key的映射关系
     *
     * @return array
     */
    protected function mappingConfig() : array
    {
        return [
            'appid'         => 'app_id',
            'mch_id'        => 'seller_id',
            'sub_appid'     => 'sub_appid',
            'sub_mch_id'    => 'sub_mch_id',
            'device_info'   => 'device_info',
        ];
    }


    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\openpay\payment\wxpay\requestParams\RequestParamsInterface::makeSign()
     */
    public function makeSign() : string
    {

    }
}