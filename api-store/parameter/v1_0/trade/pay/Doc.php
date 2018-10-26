<?php
namespace asbamboo\openpay\apiStore\parameter\v1_0\trade\pay;

use asbamboo\openpay\channel\ChannelManagerStatic;
use asbamboo\openpay\apiStore\handler\v1_0\trade\Pay;

/**
 * 帮助文档中 动态生成的帮助信息
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月25日
 */
class Doc
{
    /**
     * 渠道示例值
     *
     * @return string|NULL
     */
    public static function channelExample() : ?string
    {
        /**
         *
         * @var asbamboo\openpay\channel\ChannelInterface $Channel
         */
        static $result;
        if(empty($result)){
            $channels   = ChannelManagerStatic::getInstance()->getChannels(Pay::class);
            $Channel    = unserialize(current($channels));
            $result     = $Channel->getName();
        }
        return $result;
    }

    /**
     * 渠道取值范围
     *
     * @return array
     */
    public static function channelRange()
    {
        /**
         *
         * @var asbamboo\openpay\channel\ChannelInterface $Channel
         */
        static $result;
        if(empty($result)){
            $result     = [];
            $channels   = ChannelManagerStatic::getInstance()->getChannels(Pay::class);
            foreach($channels AS $Channel){
                $Channel    = unserialize($Channel);
                $result[]   = $Channel->getName() . '[' . $Channel->getLabel() . ']';
            }
            $result = implode(' ', $result);
        }
        return $result;
    }
}