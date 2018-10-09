<?php
namespace asbamboo\openpay\payment\alipay\request;

use asbamboo\http\Request;
use asbamboo\http\StreamInterface;
use asbamboo\http\RequestInterface;
use asbamboo\http\Constant;
use asbamboo\openpay\payment\alipay\traits\GatewayUriTrait;
use asbamboo\openpay\BuilderInterface;
use asbamboo\openpay\common\traits\MakeRequstBodyTrait;
use asbamboo\openpay\AssignDataInterface;
use asbamboo\openpay\payment\alipay\requestParams\bizContent\TradeCreateParams;
use asbamboo\openpay\payment\alipay\requestParams\CommonParams;

/**
 * alipay.trade.create(统一收单交易创建接口)
 *
 * @see https://docs.open.alipay.com/api_1/alipay.trade.create/
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月9日
 */
class TradeCreate implements BuilderInterface
{
    use GatewayUriTrait;
    use MakeRequstBodyTrait;

    private $assign_data;

    public function body() : StreamInterface
    {
        return $this->makeStream($this->assign_data);
    }

    public function assignData(AssignDataInterface $AssignData) : BuilderInterface
    {
        $BizContent         = new TradeCreateParams();
        $BizContent         = $BizContent->mappingData($AssignData);
        $CommonParams       = new CommonParams();
        $CommonParams       = $CommonParams->mappingData($AssignData);
        $CommonParams       = $CommonParams->setBizContent($BizContent);
        $CommonParams->sign = $CommonParams->makeSign();
        $this->assign_data  = get_object_vars($CommonParams);
        return $this;
    }

    public function create() : RequestInterface
    {
        return new Request(
            $this->getGateway(),
            $this->body(),
            Constant::METHOD_POST
        );
    }
}
