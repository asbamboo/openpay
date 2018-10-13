<?php
namespace asbamboo\openpay\payMethod\alipay\gateway;

use asbamboo\http\Uri;
use asbamboo\http\UriInterface;
use asbamboo\openpay\BuilderInterface;
use asbamboo\helper\env\Env AS EnvHelper;
use asbamboo\openpay\Env;

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
     *  - 官方 生产 uri 等于 https://openapi.alipay.com/gateway.do
     *
     * @var string
     */
    private $gateway_uri;

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
        if($this->gateway_uri == null){
            $this->gateway_uri    = EnvHelper::get(Env::ALIPAY_GATEWAY_URI) ?? 'https://openapi.alipay.com/gateway.do';
        }
        return new Uri($this->gateway_uri);
    }
}