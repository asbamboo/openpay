<?php
namespace asbamboo\openpay\payMethod\Alipay\builder;

use asbamboo\http\RequestInterface;
use asbamboo\openpay\BuilderInterface;
use asbamboo\openpay\payMethod\alipay\gateway\GatewayUriTrait;
use asbamboo\openpay\payMethod\alipay\requestParams\bizContent\TradePrecreateParams;
use asbamboo\openpay\payMethod\alipay\requestParams\CommonHasNotifyParams;
use asbamboo\openpay\payMethod\Alipay\builder\tool\BodyTrait;
use asbamboo\openpay\payMethod\Alipay\builder\tool\UriTrait;
use asbamboo\http\Request;
use asbamboo\http\Constant;

/**
 * alipay.trade.precreate(统一收单线下交易预创建)
 * 收银员通过收银台或商户后台调用支付宝接口，生成二维码后，展示给用户，由用户扫描二维码完成订单支付。
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月11日
 */
class TradePreCreate implements BuilderInterface
{
    use GatewayUriTrait;
    use BodyTrait;
    use UriTrait;

    /**
     * 接口请求的method参数的固定值
     *
     * @var string
     */
    const METHOD    = 'alipay.trade.precreate';

    /**
     * 指派参数的数据集合
     *
     * @var array
     */
    private $assign_data;

    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\openpay\BuilderInterface::assignData()
     */
    public function assignData(array $assign_data): BuilderInterface
    {
        $BizContent     = new TradePrecreateParams();
        $CommonParams   = new CommonHasNotifyParams();

        $BizContent->mappingData($assign_data);
        $CommonParams->mappingData($assign_data);
        $CommonParams->setBizContent($BizContent);

        $CommonParams->method   = self::METHOD;
        $CommonParams->sign     = $CommonParams->makeSign();
        $this->assign_data      = get_object_vars($CommonParams);

        return $this;
    }

    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\openpay\BuilderInterface::create()
     */
    public function create(): RequestInterface
    {
        return new Request($this->uri(), $this->body(), Constant::METHOD_POST, ['content-type' => ['application/x-www-form-urlencoded;charset=UTF-8']]);
    }
}