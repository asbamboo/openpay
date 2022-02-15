<?php
namespace asbamboo\openpay\channel\v1_0\trade;

use asbamboo\openpay\channel\ChannelInterface;
use asbamboo\openpay\channel\v1_0\trade\RefundParameter\Response;
use asbamboo\openpay\channel\v1_0\trade\RefundParameter\Request;
use asbamboo\http\ServerRequestInterface;
use asbamboo\openpay\channel\v1_0\trade\refundParameter\NotifyResult;

/**
 * 交易查询接口 trade.refund 1.0版本 渠道处理
 * 
 * @author 李春寅<licy2013@aliyun.com>
 * @since 2018年11月5日
 */
interface RefundInterface extends ChannelInterface
{
    /**
     *
     * @param Request $Request
     * @return Response
     */
    public function execute(Request $Request) : Response;
    
    
    /**
     * 第三方支付平台消息推送过来的时候需要通过这个方法处理推送的消息
     *
     * @param ServerRequestInterface $Request
     * @return NotifyResult
     */
    public function notify(ServerRequestInterface $Request) : NotifyResult;
}