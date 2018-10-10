<?php
namespace asbamboo\openpay\payment\wxpay\builder;

use asbamboo\http\Request;
use asbamboo\http\StreamInterface;
use asbamboo\http\RequestInterface;
use asbamboo\http\Constant;
use asbamboo\openpay\wxpay\traits\GatewayUriTrait;
use asbamboo\openpay\BuilderInterface;
use asbamboo\http\UriInterface;
use asbamboo\openpay\AssignDataInterface;
use asbamboo\openpay\payment\wxpay\requestParams\unit\TradeCreateParams;
use asbamboo\openpay\common\traits\MakeRequstBodyTrait;

class TradeCreate implements BuilderInterface
{
    use GatewayUriTrait;
    use MakeRequstBodyTrait;

    private $assign_data;

    public function uri() : UriInterface
    {
        $Uri    = $this->getGateway();
        $path   = rtrim($Uri->getPath(), '/') . '/pay/unifiedorder';
        $Uri    = $Uri->withPath($path);
        return $Uri;
    }

    public function body() : StreamInterface
    {
        return $this->makeStream($this->assign_data);
    }

    public function assignData(AssignDataInterface $AssignData) : BuilderInterface
    {
        $TradeCreateParams          = new TradeCreateParams();
        $TradeCreateParams          = $TradeCreateParams->mappingData($AssignData);
        $TradeCreateParams->sign    = $TradeCreateParams->makeSign();
        $this->assign_data          = get_object_vars($CommonParams);
        return $this;
    }

    public function create() : RequestInterface
    {
        return new Request(
            $this->uri(),
            $this->body(),
            Constant::METHOD_POST
        );
    }
}
