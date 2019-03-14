<?php
namespace asbamboo\openpay\apiStore\parameter\v1_0\trade\pay;

use asbamboo\api\apiStore\ApiRequestParamsAbstract;
use asbamboo\api\apiStore\traits\CommonApiRequestParamsTrait;
use asbamboo\openpay\apiStore\parameter\common\RequestThirdPartTrait;

/**
 * 交易支付接口请求传递的参数
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月13日
 */
class PayRequest extends ApiRequestParamsAbstract
{
    use CommonApiRequestParamsTrait;
    use RequestThirdPartTrait;

    /**
     * @desc 支付渠道
     * @example eval:asbamboo\openpay\apiStore\parameter\v1_0\trade\pay\Doc::channelExample();
     * @range eval:asbamboo\openpay\apiStore\parameter\v1_0\trade\pay\Doc::channelRange()
     * @required 必须
     * @var string(45)
     */
    protected $channel;

    /**
     * @desc 交易标题
     * @example 支付测试
     * @required 必须
     * @var string(45)
     */
    protected $title;

    /**
     * @desc 交易编号(一笔支付对应一个out_trade_no)
     * @example eval:date('YmdHis') . mt_rand(0,999)
     * @required 必须
     * @var string(45)
     */
    protected $out_trade_no;

    /**
     * @desc 交易金额 单位为分
     * @example 100
     * @required 必须
     * @var price(10)
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
     * @desc 聚合平台服务器主动通知接入应用指定的http url
     * @example http://api.test.asbamboo.com/notify/trade/pay
     * @var string(200)
     */
    protected $notify_url = '';

    /**
     * @desc 聚合平台跳转到接入应用的指定url，返回参数通过get传递
     * @example http://api.test.asbamboo.com/return/trade/pay
     * @var string(200)
     */
    protected $return_url = '';
}