<?php
namespace asbamboo\openpay\payMethod\alipay\gateway;

use asbamboo\http\Uri;
use asbamboo\http\UriInterface;
use asbamboo\openpay\BuilderInterface;

/**
 * 接口请求网关uri
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月9日
 */
trait GatewayUriTrait
{
    /**
     * 网关uri
     *  - 官方 sandbox uri 等于 https://openapi.alipaydev.com/gateway.do
     *
     * @var string
     */
    private $gateway_uri    = 'https://openapi.alipay.com/gateway.do';

    /**
     * 设置请求网关
     *
     * @param string $uri
     * @return BuilderInterface
     */
    public function setGateway(string $uri) : BuilderInterface
    {
        $this->gateway_uri  = $uri;
        return $this;
    }

    /**
     * 返回请求网关
     *
     * @return string|NULL
     */
    public function getGateway() : ?UriInterface
    {
        return new Uri($this->gateway_uri);
    }
}