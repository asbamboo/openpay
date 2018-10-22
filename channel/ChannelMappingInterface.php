<?php
namespace asbamboo\openpay\channel;

/**
 * 该接口处理openpay模块 openpay的渠道扩展模块之间的环境映射管理
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月19日
 */
interface ChannelMappingInterface
{
    /**
     * 设置映射关系
     *
     * @param array $content
     * @return ChannelMappingInterface
     */
    public function setMappingContent(array $content) : ChannelMappingInterface;

    /**
     * 返回映射关系
     *
     * @return array
     */
    public function getMappingContent() : array;
}