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
     * @desc 交易编号只能是数字
     * @example 2018101310270023
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
     *
     * @return \asbamboo\openpay\apiStore\parameter\v1_0\trade\string(45)
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     *
     * @return \asbamboo\openpay\apiStore\parameter\v1_0\trade\number(32)
     */
    public function getOutTradeNo()
    {
        return $this->out_trade_no;
    }

    /**
     *
     * @return \asbamboo\openpay\apiStore\parameter\v1_0\trade\price(11)
     */
    public function getTotalFee()
    {
        return $this->total_fee;
    }

    /**
     *
     * @return \asbamboo\openpay\apiStore\parameter\v1_0\trade\string(20)
     */
    public function getClientIp()
    {
        return $this->client_ip;
    }
}