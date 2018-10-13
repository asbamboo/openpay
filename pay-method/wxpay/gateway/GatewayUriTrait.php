<?php
namespace asbamboo\openpay\payMethod\wxpay\gateway;

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
     *  - 官方 sandbox uri 等于 https://api.mch.weixin.qq.com/sandboxnew/
     *  - 官方 生产 uri 等于 https://api.mch.weixin.qq.com/
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
            $this->gateway_uri  = EnvHelper::get(Env::WXPAY_GATEWAY_URI) ?? 'https://api.mch.weixin.qq.com/';
        }
        return new Uri($this->gateway_uri);
    }
}