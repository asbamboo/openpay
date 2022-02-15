<?php
namespace asbamboo\openpay\channel\v1_0\trade\refundParameter;

use asbamboo\openpay\Constant;
use asbamboo\openpay\exception\OpenpayException;

/**
 * notify消息处理后返回的结果
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月31日
 */
final class NotifyResult
{
    /**
     * 交易状态
     * 需要吧第三方平台的支付状态转换成聚合平台中的状态值
     *
     * @see Constant 取值范围参考常量中的 TRADE_REFUND_STATUS_XXXXXX
     * @var integer
     */
    private $refund_status = '0';

    /**
     * 聚合平台中的退款编号
     *
     * @var string
     */
    private $in_refund_no = '';

    /**
     * 退款支付时间，第三方的响应结果
     *
     * @var string
     */
    private $refund_pay_ymdhis = '';

    /**
     * 整个第三方平台的notify数据
     * 传递json格式
     *
     * @var string
     */
    private $third_part = '';

    /**
     * 成功时，返回给第三方平台的响应内容
     *
     * @var string
     */
    private $response_success = 'SUCCESS';

    /**
    * 失败时，返回给第三方平台的响应内容
    *
    * @var string
    */
    private $response_failed = 'FAILED';

    /**
     *
     * @param string|int $trade_status
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
     * 聚合平台中的交易编号
     *
     * @param string $in_trade_no
     * @return NotifyResult
     */
    public function setInRefundNo($in_refund_no) : self
    {
        if(ctype_digit((string) $in_refund_no) == false){
            throw new OpenpayException('聚合平台交易编号必须时数字。');
        }
        if(strlen((string) $in_refund_no) > 32){
            throw new OpenpayException('聚合平台交易编号不能超过32个字。');
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
     * 退款时间
     *  - 如:2019-12-30 12:00:31
     * 
     * @param string $refund_pay_ymdhis
     * @return self
     */
    public function setRefundPayYmdhis(string $refund_pay_ymdhis) : self
    {
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

    /**
     * 整个第三方平台的notify数据
     * 传递json格式
     *
     * @param string $third_part
     * @return NotifyResult
     */
    public function setThirdPart(string $third_part) : self
    {
        $this->third_part   = $third_part;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getThirdPart()
    {
        return $this->third_part;
    }

    /**
     *
     * @param string $response_success
     * @return NotifyResult
     */
    public function setResponseSuccess(string $response_success) : self
    {
        $this->response_success    = $response_success;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getResponseSuccess()
    {
        return $this->response_success;
    }

    /**
     *
     * @param string $response_failed
     * @return NotifyResult
     */
    public function setResponseFailed(string $response_failed) : self
    {
        $this->response_failed    = $response_failed;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getResponseFailed()
    {
        return $this->response_failed;
    }
}