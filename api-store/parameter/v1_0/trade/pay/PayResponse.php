<?php
namespace asbamboo\openpay\apiStore\parameter\v1_0\trade\pay;

use asbamboo\api\apiStore\ApiResponseParams;

/**
 * 交易支付接口请求响应值
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月13日
 */
class PayResponse extends ApiResponseParams
{
    /**
     * @desc 交易编号 与支付请求的编号一致
     * @example 2018101310270023
     * @var number(32)
     */
    protected $out_trade_no;

    /**
     * @desc 交易编号 与支付请求的编号对应的聚合平台生成的交易编号 是一个全局唯一的编号
     * @example 201810131027242582
     * @var number(32)
     */
    protected $in_trade_no;

    /**
     * @desc 交易状态
     * @example PAYDONE
     * @range eval:asbamboo\openpay\apiStore\parameter\v1_0\trade\pay\Doc::tradeStatusRange();
     * @var string(45)
     */
    protected $trade_status;

    /**
     * @desc 交易支付事件
     * @example 20181013102750
     * @var date(YYYY-mm-dd HH:ii:ss)
     */
    protected $payed_time;
}