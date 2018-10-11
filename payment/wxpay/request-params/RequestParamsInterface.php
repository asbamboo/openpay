<?php
namespace asbamboo\openpay\payment\wxpay\requestParams;

use asbamboo\openpay\AssignDataInterface;

/**
 * 公共参数接口
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月9日
 */
interface RequestParamsInterface
{
    /**
     * 使用指定的数据字段集合，映射到公共请求参数实例的属性
     *
     * @param AssignDataInterface $AssignData
     */
    public function mappingData(AssignDataInterface $AssignData) : void;

    /**
     * 生成签名
     *
     * @return string
     */
    public function makeSign() : string;
}
