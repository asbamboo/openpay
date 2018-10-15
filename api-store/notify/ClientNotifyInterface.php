<?php
namespace asbamboo\openpay\apiStore\notify;

/**
 * notify url是一个聚合平台像客户端推送消息的url
 * 管理客户端请求时传递的notify url字段
 * 有可能将客户请求的notify url存入数据库中,等聚合平台接收到支付宝、微信等第三方平台的响应结果时，给客户端发出响应结果。
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月15日
 */
interface ClientNotifyInterface
{
    /**
     * 设置客户端请求的notify url
     *  - 例如可以将notify url存入数据库中，后面像客户端发出响应结果的时候取出
     *
     * 聚合平台会提供主动查询接口，所以当客户端请求是不带 notify url 的话，部分接口客户只能通过主动查询的方式获取请求结果。
     *  - 如网页端支付请求、条码支付等需要等待用户端行为的接口
     *
     * @param string $notify_url
     * @return ClientNotifyInterface
     */
    public function set(?string $notify_url) : ClientNotifyInterface;

    /**
     * 返回像客户端推送消息的url
     *
     * @return string|NULL
     */
    public function get() : ?string;
}
