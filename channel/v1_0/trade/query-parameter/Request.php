<?php
namespace asbamboo\openpay\channel\v1_0\trade\queryParameter;

use asbamboo\openpay\channel\common\ParameterTrait;

/**
 * 传递给渠道处理方法的请求参数
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月30日
 */
final class Request
{
    use ParameterTrait;

    /**
     * @desc 支付渠道
     * @required 必须
     * @var string(45)
     */
    protected $channel;

    /**
     * 聚合平台生成的交易编号, 全局唯一
     * @desc 交易编号只能是数字
     * @example 2018101310270023
     * @var number(32)
     */
    protected $in_trade_no;

    /**
     * @desc 第三方支付平台的参数，请自行查阅相关支付平台相关文档中的参数列表
     * @example {"limit_pay":"no_credit"}
     * @required 可选
     * @var json()
     */
    protected $third_part;

    /**
     *
     * @return \asbamboo\openpay\channel\v1_0\trade\queryParameter\string(45)
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     *
     * @return \asbamboo\openpay\channel\v1_0\trade\queryParameter\number(32)
     */
    public function getInTradeNo()
    {
        return $this->in_trade_no;
    }

    /**
     *
     * @return \asbamboo\openpay\channel\v1_0\trade\payParameter\json()
     */
    public function getThirdPart()
    {
        return $this->third_part;
    }
}
