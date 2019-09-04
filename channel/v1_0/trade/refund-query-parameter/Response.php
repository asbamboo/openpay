<?php
namespace asbamboo\openpay\channel\v1_0\trade\refundQueryParameter;

use asbamboo\openpay\Constant;
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
     * 交易状态
     * 需要吧第三方平台的退款处理状态转换成聚合平台中的状态值
     *
     * @see Constant 取值范围参考常量中的 TRADE_REFUND_STATUS_XXXX
     * @var integer
     */
    private $refund_status;

    /**
     * 聚合平台中的退款编号
     *
     * @var string
     */
    private $in_refund_no;
    
    /**
     * 退款支付时间
     * 
     * @var string
     */
    private $refund_pay_ymdhis = '';

    /**
     *
     * @param string|int $refund_status
     */
    public function setRefundStatus($refund_status) : self
    {
        if(!in_array($refund_status, [
            Constant::TRADE_REFUND_STATUS_FAILED,
            Constant::TRADE_REFUND_STATUS_REQUEST,
            Constant::TRADE_REFUND_STATUS_SUCCESS,
        ])){
            throw new OpenpayException('退款状态超出聚合平台支持的范围。');
        }
        $this->refund_status = $refund_status;
        return $this;
    }

    /**
     *
     * @return string|int
     */
    public function getRefundStatus()
    {
        return $this->refund_status;
    }

    /**
     * 聚合平台中的退款编号
     *
     * @param string $in_refund_no
     * @return self
     */
    public function setInRefundNo($in_refund_no) : self
    {
        if(ctype_digit((string) $in_refund_no) == false){
            throw new OpenpayException('聚合平台退款编号必须时数字。');
        }
        if(strlen((string) $in_refund_no) > 32){
            throw new OpenpayException('聚合平台退款编号不能超过32个字。');
        }
        $this->in_refund_no    = $in_refund_no;
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
     * 第三方平台的退款编号
     *
     * @param string $refund_pay_ymdhis
     * @return self
     */
    public function setRefundPayYmdhis($refund_pay_ymdhis) : self
    {
        if(date('Y-m-d H:i:s', strtotime($refund_pay_ymdhis)) != $refund_pay_ymdhis){
            throw new OpenpayException('退款支付时间格式错误。');
        }
        $this->refund_pay_ymdhis   = $refund_pay_ymdhis;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getRefundPayYmdhis()
    {
        return $this->refund_pay_ymdhis;
    }
}
