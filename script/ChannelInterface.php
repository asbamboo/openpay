<?php
namespace asbamboo\openpay;

use Composer\Script\Event;

/**
 * open pay 模块的一些和composer script配置相关的方法
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月19日
 */
interface ChannelInterface
{
    /**
     * 绑定渠道
     *
     * @param Event $Event
     */
    public function bind(Event $Event) : void;
}