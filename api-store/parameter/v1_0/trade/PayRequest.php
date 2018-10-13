<?php
namespace asbamboo\openpay\apiStore\parameter\v1_0\trade;

use asbamboo\api\apiStore\ApiRequestParamsAbstract;
use asbamboo\api\apiStore\traits\CommonApiRequestParamsTrait;

/**
 * 交易支付接口请求传递的参数
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月13日
 */
class PayRequest extends ApiRequestParamsAbstract
{
    use CommonApiRequestParamsTrait;

    /**
     * @desc 支付方式
     * @example WXPAY_QRCD
     * @range WXPAY_QRCD[微信扫码],ALIPAY_QRCD[支付宝扫码]
     * @required 必须
     * @var string(45)
     */
    protected $payment;

    /**
     * @desc 交易标题
     * @example 支付测试
     * @required 必须
     * @var string(45)
     */
    protected $title;

    /**
     * @desc 交易编号只能是数字
     * @example 2018101310270023
     * @required 必须
     * @var number(32)
     */
    protected $out_trade_no;

    /**
     * @desc 交易金额 单位为分
     * @example 100
     * @required 必须
     * @var price(11)
     */
    protected $total_fee;

    /**
     * @desc 客户ip
     * @example 123.123.123.123
     * @required 必须
     * @var string(20)
     */
    protected $client_ip;

    /**
     * @desc 异步支付结果通知的回调地址, 如果为空的话只能通过主动查询接口获取订单是否交易成功
     * @example https://pay.notify.asbamboo.com
     * @required 可选
     * @var string(256)
     */
    protected $notify_url;

    /**
     * @desc 第三方支付平台的参数，请自行查阅相关支付平台相关文档中的参数列表
     * @example {limit_pay:no_credit}
     * @required 可选
     * @var json()
     */
    protected $third_part;
}