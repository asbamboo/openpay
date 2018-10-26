<?php
namespace asbamboo\openpay\channel;

/**
 * 渠道接口
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月19日
 */
interface ChannelInterface
{
    /**
     * 渠道名称
     * api 请求时传递的名称
     *
     * @return string
     */
    public function getName() : string;

    /**
     * 渠道名称
     * 用于帮助文档中显示的字面意思
     *
     * @return string
     */
    public function getLabel() : string;
}