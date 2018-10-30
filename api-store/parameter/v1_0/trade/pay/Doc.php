<?php
namespace asbamboo\openpay\apiStore\parameter\v1_0\trade\pay;

use asbamboo\openpay\channel\ChannelManagerStatic;
use asbamboo\openpay\apiStore\handler\v1_0\trade\Pay;
use asbamboo\openpay\apiStore\parameter\v1_0\trade\Constant;

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
            foreach($channels AS $channel_name => $channel_info){
                $result     = $channel_name;
                break;
            }
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
            foreach($channels AS $channel_name => $channel_info){
                $channel_label  = $channel_info[0];
                $Channel        = unserialize($channel_info[1]);
                $result[]       = $channel_name . '[' . $channel_label . ']';
            }
            $result = implode(' ', $result);
        }
        return $result;
    }
    
    /**
     * 交易状态取值范围
     * 
     * @return string
     */
    public static function tradeStatusRange()
    {
        return implode(' ', [
            Constant::TRADE_STATUS_NOPAY . '[未支付]',
            Constant::TRADE_STATUS_PAYFAILED . '[支付失败]',
            Constant::TRADE_STATUS_PAYING . '[正在支付]',
            Constant::TRADE_STATUS_PAYOK . '[支付成功]',
        ]);
    }
}