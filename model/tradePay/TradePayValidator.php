<?php
namespace asbamboo\openpay\model\tradePay;

use asbamboo\openpay\apiStore\exception\TradePayChannelInvalidException;
use asbamboo\openpay\apiStore\exception\TradePayTitleInvalidException;
use asbamboo\openpay\apiStore\exception\TradePayOutTradeNoInvalidException;
use asbamboo\openpay\apiStore\exception\TradePayTotalFeeInvalidException;
use asbamboo\openpay\apiStore\exception\TradePayClientIpInvalidException;
use asbamboo\openpay\apiStore\exception\TradePayThirdTradeNoInvalidException;

/**
 * 数据表 trade pay 各个字段的验证器
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年11月1日
 */
trait TradePayValidator
{

    /**
     *
     * @param string $payment
     * @throws TradePayChannelInvalidException
     */
    private function validateChannel($channel)
    {
        if(trim($channel) === ''){
            throw new TradePayChannelInvalidException('channel 是必填项。');
        }
        if(mb_strlen($channel) > 45){
            throw new TradePayChannelInvalidException('channel 超长。长度不能超过45字。');
        }
        return true;
    }

    /**
     *
     * @param string $title
     * @throws TradePayTitleInvalidException
     */
    private function validateTitle($title)
    {
        if(trim($title) === ''){
            throw new TradePayTitleInvalidException('title 是必填项。');
        }
        if(mb_strlen($title) > 45){
            throw new TradePayTitleInvalidException('title 超长。长度不能超过45字。');
        }
    }

    /**
     *
     * @param number $out_trade_no
     * @throws TradePayOutTradeNoInvalidException
     */
    private function validateOutTradeNo($out_trade_no)
    {
        if(trim($out_trade_no) === ''){
            throw new TradePayOutTradeNoInvalidException('out_trade_no 是必填项。');
        }

        if(strlen($out_trade_no) > 45){
            throw new TradePayOutTradeNoInvalidException('out_trade_no 长度不能超过45字。');
        }
    }

    /**
     *
     * @param number $total_fee
     * @throws TradePayTotalFeeInvalidException
     */
    private function validateTotalFee($total_fee)
    {
        if(trim($total_fee) === ''){
            throw new TradePayTotalFeeInvalidException('total_fee 是必填项。');
        }

        if(ctype_digit((string) $total_fee) == false){
            throw new TradePayTotalFeeInvalidException('total_fee 只能是数字。');
        }

        if($total_fee > 10000000000 || $total_fee < 1){
            throw new TradePayTotalFeeInvalidException('total_fee 超出范围，1 < total_fee < 10000000000。');
        }
    }

    /**
     *
     * @param string $client_ip
     * @throws TradePayClientIpInvalidException
     */
    private function validateClientIp($client_ip)
    {
        if(trim($client_ip) === ''){
            throw new TradePayClientIpInvalidException('client_ip 是必填项。');
        }
        if(long2ip(ip2long($client_ip)) != $client_ip){
            throw new TradePayClientIpInvalidException('client_ip 的值不是一个有效的ip地址。');
        }
    }

    /**
     *
     * @param string $third_trade_no
     * @throws TradePayOutTradeNoInvalidException
     */
    private function validateThirdTradeNo($third_trade_no)
    {
        if(strlen($third_trade_no) > 45){
            throw new TradePayThirdTradeNoInvalidException('third_trade_no 长度不能超过45字。');
        }
    }
}