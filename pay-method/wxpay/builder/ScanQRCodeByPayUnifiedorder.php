<?php
namespace asbamboo\openpay\payMethod\wxpay\builder;

use asbamboo\openpay\BuilderInterface;
use asbamboo\openpay\payMethod\wxpay\gateway\GatewayUriTrait;
use asbamboo\http\UriInterface;
use asbamboo\openpay\payMethod\wxpay\requestParams\unit\ScanQRCodeByPayUnifiedorderParams;
use asbamboo\openpay\payMethod\wxpay\requestParams\RequestParamsInterface;
use asbamboo\openpay\payMethod\wxpay\builder\tool\BodyTrait;
use asbamboo\openpay\payMethod\wxpay\builder\tool\CreateRequestTrait;

/**
 * 微信扫码支付 统一下单接口
 *
 * https://api.mch.weixin.qq.com/pay/unifiedorder
 *
 * @see https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=9_1
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月12日
 */
class ScanQRCodeByPayUnifiedorder implements BuilderInterface
{
    use GatewayUriTrait;
    use BodyTrait;
    use CreateRequestTrait;

    /**
     *
     * @var RequestParamsInterface
     */
    private $RequestParams;

    /**
     * 返回接口请求uri
     *
     * @return UriInterface
     */
    public function uri() : UriInterface
    {
        $Uri    = $this->getGateway();
        $path   = rtrim($Uri->getPath(), '/') . '/pay/unifiedorder';
        $Uri    = $Uri->withPath($path);
        return $Uri;
    }

    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\openpay\BuilderInterface::assignData()
     */
    public function assignData(array $assign_data) : BuilderInterface
    {
        $this->RequestParams  = new ScanQRCodeByPayUnifiedorderParams();
        $this->RequestParams->mappingData($assign_data);
        return $this;
    }
}