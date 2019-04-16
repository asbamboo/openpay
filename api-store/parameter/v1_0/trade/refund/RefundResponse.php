<?php
namespace asbamboo\openpay\apiStore\parameter\v1_0\trade\refund;

use asbamboo\api\apiStore\ApiResponseParams;

/**
 * 交易查询接口响应值
 *
 * @author 李春寅<licy2013@aliyun.com>
 * @since 2018年10月27日
 */
class RefundResponse extends ApiResponseParams
{
    /**
     * @desc 交易编号 与支付请求的编号对应的聚合平台生成的交易编号 是一个全局唯一的编号
     * @example 201810131027242582
     * @required 必须
     * @var string length(32)
     */
    protected $in_trade_no;

    /**
     * @desc 对接应用的交易编号
     * @example 2018101310270023
     * @required 必须
     * @var string length(45)
     */
    protected $out_trade_no;

    /**
     * @desc 退款编号 与退款请求的编号对应的聚合平台生成的退款编号 是一个全局唯一的编号
     * @example 201810131027242582
     * @required 必须
     * @var string length(32)
     */
    protected $in_refund_no;

    /**
     * @desc 对接应用的退款编号
     * @example 201810131027242582
     * @required 必须
     * @var string length(45)
     */
    protected $out_refund_no;

    /**
     * @desc 退款金额
     * @example 1
     * @required 必须
     * @var int
     */
    protected $refund_fee;

    /**
     * @desc 退款申请状态
     * @range eval:asbamboo\openpay\apiStore\parameter\v1_0\trade\refund\Doc::tradeStatusRange();
     * @example SUCCESS
     * @required 必须
     * @var string
     */
    protected $refund_status;

    /**
     * @desc 退款的付款时间
     * @example 2018-10-13 10:27:50
     * @required 必须
     * @var string date('YYYY-mm-dd HH:ii:ss')
     */
    protected $refund_pay_ymdhis;
}
