<?php
namespace asbamboo\openpay\channel\v1_0\trade\refundQueryParameter;

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
     * 聚合平台生成的退款编号, 全局唯一
     * @desc 交易编号只能是数字
     * @example 2018101310270023
     * @var string length(32)
     */
    protected $in_refund_no;

    /**
     * @desc 第三方支付平台的参数，请自行查阅相关支付平台相关文档中的参数列表
     * @example {"limit_pay":"no_credit"}
     * @required 可选
     * @var string json
     */
    protected $third_part;

    /**
     *
     * @return string
     */
    public function getInRefundNo()
    {
        return $this->in_refund_no;
    }

    /**
     *
     * @return string json
     */
    public function getThirdPart()
    {
        return $this->third_part;
    }
}
