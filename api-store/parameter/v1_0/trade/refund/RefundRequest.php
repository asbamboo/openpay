<?php
namespace asbamboo\openpay\apiStore\parameter\v1_0\trade\refund;

use asbamboo\api\apiStore\ApiRequestParamsAbstract;
use asbamboo\api\apiStore\traits\CommonApiRequestParamsTrait;
use asbamboo\openpay\apiStore\parameter\common\RequestThirdPartTrait;

/**
 * 交易退款接口请求参数
 *
 * @author 李春寅<licy2013@aliyun.com>
 * @since 2018年11月5日
 */
class RefundRequest extends ApiRequestParamsAbstract
{
    use CommonApiRequestParamsTrait;
    use RequestThirdPartTrait;

    /**
     * @desc 交易编号(对接应用的)
     * @required 当in_trade_no为空时必填
     * @example 2018101310270023
     * @var string length(45)
     */
    protected $out_trade_no;

    /**
     *
     * @desc 交易编号(聚合系统内的)
     * @required 当out_trade_no为空时必填
     * @var string length(32)
     */
    protected $in_trade_no;


    /**
     * @desc 退款编号(对接应用的)
     * @required 必须
     * @example eval:date('YmdHis') . mt_rand(0,999)
     * @var string length(45)
     */
    protected $out_refund_no;

    /**
     * @desc 退款金额 1.不能大于交易金额 - 已经退款金额
     * @required 必填
     * @example 1
     * @var int
     */
    protected $refund_fee;
}
