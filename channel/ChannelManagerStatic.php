<?php
namespace asbamboo\openpay\channel;

/**
 * 静态的渠道管理器
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月22日
 */
class ChannelManagerStatic
{
    /**
     * 返回默认的渠道管理器
     *
     * @return \asbamboo\openpay\channel\ChannelManager
     */
    public static function getInstance()
    {
        static $Manager;
        if(empty($Manager)){
            $ChannelMapping = new ChannelMapping();
            $Manager        = new ChannelManager($ChannelMapping);
        }
        return $Manager;
    }
}