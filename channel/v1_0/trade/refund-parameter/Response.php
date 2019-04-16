<?php
namespace asbamboo\openpay\channel\v1_0\trade\RefundParameter;

use asbamboo\openpay\exception\OpenpayException;

/**
 * 渠道处理方法处理请求后应该返回的结果
 *
 * @author 李春寅<licy2013@aliyun.com>
 * @since 2018年10月30日
 */
final class Response
{
    /**
     * 聚合平台生成的退款编号, 全局唯一
     *
     * @var string length(32)
     */
    protected $in_refund_no;

    /**
     * 退款金额
     *
     * @var int
     */
    protected $refund_fee;

    /**
     * 是否成功
     *
     * @var bool
     */
    protected $is_success;

    /**
     * 退款支付时间
     *
     * @var string date('YYYY-mm-dd HH:ii:ss')
     */
    protected $pay_ymdhis;

    /**
     *
     * @param string $in_refund_no
     * @return self
     */
    public function setInRefundNo($in_refund_no) : self
    {
        if(ctype_digit((string) $in_refund_no) == false && $in_refund_no != ''){
            throw new OpenpayException('聚合平台交易编号必须时数字。');
        }
        if(strlen((string) $in_refund_no) > 32){
            throw new OpenpayException('聚合平台交易编号不能超过32个字。');
        }
        $this->in_refund_no;
        return $this;
    }

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
     * @param int $refund_fee
     * @return self
     */
    public function setRefundFee($refund_fee) : self
    {
        if(ctype_digit((string) $refund_fee) == false){
            throw new OpenpayException('退款金额只能是数字。');
        }
        $this->refund_fee;
        return $this;
    }

    /**
     *
     * @return int
     */
    public function getRefundFee()
    {
        return $this->refund_fee;
    }

    /**
     *
     * @param bool $is_success
     * @return self
     */
    public function setIsSuccess(bool $is_success) : self
    {
        $this->is_success = $is_success;
        return $this;
    }

    /**
     *
     * @return boolean
     */
    public function getIsSuccess()
    {
        return $this->is_success;
    }

    /**
     *
     * @param string date $pay_ymdhis
     * @return self
     */
    public function setPayYmdhis($pay_ymdhis) : self
    {
        if(date('Y-m-d H:i:s', strtotime($pay_ymdhis)) != $pay_ymdhis){
            throw new OpenpayException('退款支付日期无效。');
        }
        $this->pay_ymdhis = $pay_ymdhis;
        return $this;
    }

    /**
     *
     * @return string date('YYYY-mm-dd HH:ii:ss')
     */
    public function getPayYmdhis()
    {
        return $this->pay_ymdhis;
    }
}
