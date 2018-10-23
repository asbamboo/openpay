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
     * @param array $channels 渠道处理的类名
     * @return ChannelMappingInterface
     */
    public function addMappingChannels(array $channels) : ChannelMappingInterface;

    /**
     * 将渠道映射关系重置为最初的状态
     *
     * @return ChannelMappingInterface
     */
    public function resetMappingContent() : ChannelMappingInterface;

    /**
     * 返回映射关系
     *
     * @return array
     */
    public function getMappingContent() : array;
}