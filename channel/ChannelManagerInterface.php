<?php
namespace asbamboo\openpay\channel;

/**
 * 渠道管理
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月22日
 */
interface ChannelManagerInterface
{
    /**
     * 返回接口 handler 可用的渠道
     *
     * @param string $handler_class
     * @return array
     */
    public function getChannels(string $handler_class) : array;

    /**
     * 返回一个名称为$name的$handler_class可用的ChannelInterface实例
     *
     * @param string $handler_class
     * @param string $name
     * @return ChannelInterface|null
     */
    public function getChannel(string $handler_class, string $name) : ?ChannelInterface;
}