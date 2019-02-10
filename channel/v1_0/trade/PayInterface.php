<?php
namespace asbamboo\openpay\channel\v1_0\trade;

use asbamboo\openpay\channel\ChannelInterface;
use asbamboo\openpay\channel\v1_0\trade\payParameter\Request;
use asbamboo\openpay\channel\v1_0\trade\payParameter\Response;
use asbamboo\http\ServerRequestInterface;
use asbamboo\openpay\channel\v1_0\trade\payParameter\NotifyResult;

/**
 * 交易支付接口 trade.pay 1.0版本 渠道处理
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月19日
 */
interface PayInterface extends ChannelInterface
{
    /**
     * 一个应用发起支付请求时通过这个方法向第三方支付平台发起请求
     *
     * @param Request $Request
     * @return Response
     */
    public function execute(Request $Request) : Response;

    /**
     * 第三方支付平台消息推送过来的时候需要通过这个方法处理推送的消息
     *  - 与return方法的区别是，notify是异步通知。
     *
     * @param ServerRequestInterface $Request
     * @return NotifyResult
     */
    public function notify(ServerRequestInterface $Request) : NotifyResult;

    /**
     * 第三方支付平台消息推送过来的时候需要通过这个方法处理推送的消息
     *  - 与notify方法的区别是，return是同步通知。
     *
     * @param ServerRequestInterface $Request
     * @return NotifyResult
     */
    public function return(ServerRequestInterface $Request) : NotifyResult;

    /**
     * 返回asbamboo系统的订单编号对应第三方平台推送结果的字段名称。
     *
     * @return string
     */
    public function getTradeNoKeyName(): string;
}