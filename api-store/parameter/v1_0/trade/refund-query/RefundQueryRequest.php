<?php
namespace asbamboo\openpay\apiStore\parameter\v1_0\trade\refundQuery;

use asbamboo\api\apiStore\ApiRequestParamsAbstract;
use asbamboo\api\apiStore\traits\CommonApiRequestParamsTrait;
use asbamboo\openpay\apiStore\parameter\common\RequestThirdPartTrait;

/**
 * 交易退款接口请求参数
 *
 * @author 李春寅<licy2013@aliyun.com>
 * @since 2018年11月5日
 */
class RefundQueryRequest extends ApiRequestParamsAbstract
{
    use CommonApiRequestParamsTrait;
    use RequestThirdPartTrait;

    /**
     * @desc 退款编号(对接应用的)
     * @required 当in_refund_no为空时必填
     * @example eval:date('YmdHis') . mt_rand(0,999)
     * @var string length(45)
     */
    protected $out_refund_no;

    /**
     *
     * @desc 交易编号(聚合系统内的)
     * @required 当out_refund_no为空时必填
     * @example eval:date('YmdHis') . mt_rand(0,999)
     * @var string length(32)
     */
    protected $in_refund_no;
}
