<?php
namespace asbamboo\openpay\channel\v1_0\trade\RefundParameter;

use asbamboo\openpay\Constant;

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
     * @var number(32)
     */
    protected $in_refund_no;
    
    /**
     * 退款金额
     *
     * @var price()
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
     * @var date('YYYY-mm-dd HH:ii:ss')
     */
    protected $pay_ymdhis;
    
    /**
     * 
     * @param string $in_refund_no
     * @return self
     */
    public function setInRefundNo($in_refund_no) : self
    {
        $this->in_refund_no;
        return $this;
    }

    /**
     * 
     * @return \asbamboo\openpay\channel\v1_0\trade\RefundParameter\number(32)
     */
    public function getInRefundNo()
    {
        return $this->in_refund_no;
    }
    
    /**
     * 
     * @param price() $refund_fee
     * @return self
     */
    public function setRefundFee($refund_fee) : self
    {
        $this->refund_fee;
        return $this;
    }

    /**
     * 
     * @return \asbamboo\openpay\channel\v1_0\trade\RefundParameter\price()
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
     * @param date() $pay_ymdhis
     * @return self
     */
    public function setPayYmdhis($pay_ymdhis) : self
    {
        $this->pay_ymdhis = $pay_ymdhis;
        return $this;
    }
    
    /**
     * 
     * @return \asbamboo\openpay\channel\v1_0\trade\RefundParameter\date('YYYY-mm-dd HH:ii:ss')
     */
    public function getPayYmdhis()
    {
        return $this->pay_ymdhis;
    }
}
