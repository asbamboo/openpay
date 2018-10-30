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
     * 支持的渠道
     *  - [渠道参数名=>渠道标签名][]
     *  - 如['ALIPAY_QRCD'=>'支付宝扫码', 'ALIPAY_PC'=>'支付宝PC支付']
     *  
     * @return array
     */
    public function supports() : array;
}