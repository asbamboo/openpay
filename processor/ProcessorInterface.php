<?php
namespace asbamboo\openpay\processor;

/**
 * api接口处理
 * 每个接口都需要一个与接口对应的ProcessorInterface
 * 主要时execute规定请求参数与响应参数列表
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月18日
 */
interface ChannelInterface
{
    /**
     * api接口处理渠道的名称
     *
     * @return string
     */
    public function getName() : string;
}