<?php
namespace asbamboo\openpay\payment\alipay\requestParams;

use asbamboo\openpay\AssignDataInterface;

/**
 * 公共参数接口
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月9日
 */
interface CommonParamsInterface
{
    /**
     * 使用指定的数据字段集合，映射到公共请求参数实例的属性
     *
     * @param AssignDataInterface $AssignData
     * @return CommonParamsInterface
     */
    public function mappingData(AssignDataInterface $AssignData) : void;

    /**
     * 设置$biz_content字段
     *
     * @param string $BizContent
     */
    public function setBizContent(BizContentInterface $BizContent) : CommonParamsInterface;

    /**
     * 生成签名
     * 在其他参数值确定后调用这个方法生成sign
     *
     * @return string
     */
    public function makeSign() : string;
}
