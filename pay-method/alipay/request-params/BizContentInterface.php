<?php
namespace asbamboo\openpay\payMethod\alipay\requestParams;

/**
 * 公共请求参数中的biz_content
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月9日
 */
interface BizContentInterface
{
    /**
     *
     * @param array $assign_data
     */
    public function mappingData(array $assign_data) : void;
}