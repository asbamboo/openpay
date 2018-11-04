<?php
namespace asbamboo\openpay\apiStore\parameter\v1_0\trade\query;

use asbamboo\api\apiStore\ApiRequestParamsAbstract;
use asbamboo\api\apiStore\traits\CommonApiRequestParamsTrait;

/**
 * 交易查询接口请求参数
 * 
 * @author 李春寅<licy2013@aliyun.com>
 * @since 2018年10月27日
 */
class QueryRequest extends ApiRequestParamsAbstract
{
    use CommonApiRequestParamsTrait;
    
    /**
     * @desc 交易编号(商户端的)
     * @required 当in_trade_no为空时必填
     * @example 2018101310270023
     * @var string(45)
     */
    protected $out_trade_no;

    /**
     * 
     * @desc 交易编号(聚合系统内的)
     * @required 当out_trade_no为空时必填
     * @var number(32)
     */
    protected $in_trade_no;
}
