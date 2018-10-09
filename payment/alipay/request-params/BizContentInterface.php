<?php
namespace asbamboo\openpay\payment\alipay\requestParams;

use asbamboo\openpay\AssignDataInterface;

/**
 * 公共请求参数中的biz_content
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月9日
 */
interface BizContentInterface
{
    /**
     * 使用指定的数据字段集合，映射到biz content实例的属性
     *
     * @param AssignDataInterface $AssignData
     * @return BizContentInterface
     */
    public function mappingData(AssignDataInterface $AssignData) : BizContentInterface;
}