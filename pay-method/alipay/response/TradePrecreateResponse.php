<?php
namespace asbamboo\openpay\payMethod\alipay\response;

use asbamboo\openpay\payMethod\alipay\response\tool\ResponseAbstract;

/**
 * alipay.trade.precreate(统一收单线下交易预创建)响应结果
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月12日
 */
class TradePrecreateResponse extends ResponseAbstract
{
    /**
     * 必填 最大长度 64
     * 商户的订单号
     *
     * @var string
     */
    protected $out_trade_no;

    /**
     * 当前预下单请求生成的二维码码串，可以用二维码生成工具根据该码串值生成对应的二维码
     *
     * @var string
     */
    protected $qr_code;

    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\openpay\payMethod\alipay\response\tool\ResponseAbstract::getResponseRootNode()
     */
    final protected function getResponseRootNode() : string
    {
        return 'alipay_trade_precreate_response';
    }
}
