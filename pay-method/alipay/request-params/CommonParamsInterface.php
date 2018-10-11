<?php
namespace asbamboo\openpay\payMethod\alipay\requestParams;

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
     * @param array $assign_data
     */
    public function mappingData(array $assign_data) : void;

    /**
     * 设置$biz_content字段
     *
     * @param BizContentInterface $BizContent
     * @return CommonParamsInterface
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
