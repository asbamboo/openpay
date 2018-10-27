<?php
namespace asbamboo\openpay\apiStore\parameter\v1_0\trade;

/**
 * 交易相关的一些常量
 * 
 * @author 李春寅<licy2013@aliyun.com>
 * @since 2018年10月27日
 */
final class Constant
{
    /******************************************************************************
     *  交易状态
     *****************************************************************************/
    const TRADE_STATUS_PAYING       = 'PAYING'; // 正在支付
    const TRADE_STATUS_PAYOK        = 'PAYOK'; // 支付成功
    const TRADE_STATUS_PAYFAILED    = 'PAYFAILED'; // 支付失败
    const TRADE_STATUS_NOPAY        = 'NOPAY'; // 未支付
    /*****************************************************************************/
}