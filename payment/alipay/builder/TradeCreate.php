<?php
namespace asbamboo\openpay\payment\alipay\builder;

use asbamboo\http\Request;
use asbamboo\http\StreamInterface;
use asbamboo\http\RequestInterface;
use asbamboo\http\Constant;
use asbamboo\openpay\payment\alipay\traits\GatewayUriTrait;
use asbamboo\openpay\BuilderInterface;
use asbamboo\openpay\common\traits\MakeRequestBodyTrait;
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
    use MakeRequestBodyTrait;

    /**
     * 接口请求的method参数的固定值
     *
     * @var string
     */
    const METHOD    = 'alipay.trade.create';

    /**
     * 指派参数数据集
     *
     * @var array
     */
    private $assign_data;

    public function uri()
    {
        $query_data   = $this->assign_data;
        unset($query_data['biz_content']);
        return $this->getGateway()->withQuery(http_build_query($query_data));
    }

    /**
     * 接口请求Request对象的body
     *
     * @return StreamInterface
     */
    public function body() : StreamInterface
    {
        return $this->makeStream(['biz_content' => $this->assign_data['biz_content']]);
    }

    /**
     * 指派请求数据集
     *
     * {@inheritDoc}
     * @see \asbamboo\openpay\BuilderInterface::assignData()
     */
    public function assignData(AssignDataInterface $AssignData) : BuilderInterface
    {
        $BizContent                 = new TradeCreateParams();
        $BizContent->mappingData($AssignData);

        $BizContent->total_amount           = (string) bcdiv($BizContent->total_amount, 100, 2);    // 原始金额单位是，分支付宝的单位是元
        if($BizContent->discountable_amount > 0){
            $BizContent->discountable_amount    = (string) bcdiv($BizContent->discountable_amount, 100, 2);    // 原始金额单位是，分支付宝的单位是元
        }

        $CommonParams           = new CommonParams();
        $CommonParams->mappingData($AssignData);

        $CommonParams           = $CommonParams->setBizContent($BizContent);
        $CommonParams->method   = self::METHOD;
        $CommonParams->sign     = $CommonParams->makeSign();

        $this->assign_data      = get_object_vars($CommonParams);

        return $this;
    }

    /**
     * 创建request对象
     *
     * {@inheritDoc}
     * @see \asbamboo\openpay\BuilderInterface::create()
     */
    public function create() : RequestInterface
    {
        return new Request(
            $this->uri(),
            $this->body(),
            Constant::METHOD_POST,
            ['content-type' => ['application/x-www-form-urlencoded;charset=UTF-8']]
        );
    }
}
