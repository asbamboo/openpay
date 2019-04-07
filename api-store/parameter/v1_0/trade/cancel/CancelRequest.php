<?php
namespace asbamboo\openpay\apiStore\parameter\v1_0\trade\cancel;

use asbamboo\api\apiStore\ApiRequestParamsAbstract;
use asbamboo\api\apiStore\traits\CommonApiRequestParamsTrait;
use asbamboo\openpay\apiStore\parameter\common\RequestThirdPartTrait;

/**
 * 交易取消接口请求参数
 *
 * @author 李春寅<licy2013@aliyun.com>
 * @since 2018年11月6日
 */
class CancelRequest extends ApiRequestParamsAbstract
{
    use CommonApiRequestParamsTrait;
    use RequestThirdPartTrait;

    /**
     *
     * @desc 交易编号(聚合系统内的) 如果out_trade_no同时存在, 优先使用in_trade_no
     * @required 当out_trade_no为空时必填
     * @var string length(32)
     */
    protected $in_trade_no;


    /**
     * @desc 交易编号(对接应用的) 如果in_trade_no同时存在, 优先使用in_trade_no
     * @required 当in_trade_no为空时必填
     * @example 2018101310270023
     * @var string length(45)
     */
    protected $out_trade_no;
}
