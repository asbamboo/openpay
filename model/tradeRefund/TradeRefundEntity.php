<?php

namespace asbamboo\openpay\model\tradeRefund;

/**
 * TradeRefundEntity
 */
class TradeRefundEntity
{
    /**
     * @var int
     */
    private $seq;

    /**
     * @var string
     */
    private $in_refund_no = '';

    /**
     * @var string
     */
    private $out_refund_no = '';

    /**
     * @var string
     */
    private $out_trade_no = '';

    /**
     * @var string
     */
    private $in_trade_no = '';

    /**
     * @var int
     */
    private $refund_fee = '0';
    
    /**
     * @var string
     */
    private $notify_url = '';    
    
    /**
     * @var int
     */
    private $status = '0';

    /**
     * @var int
     */
    private $request_time = '0';

    /**
     * @var int
     */
    private $response_time = '0';

    /**
     * @var int
     */
    private $pay_time = '0';

    /**
     * @var int
     */
    private $version = '0';


    /**
     * Get seq.
     *
     * @return int
     */
    public function getSeq()
    {
        return $this->seq;
    }

    /**
     * Set inRefundNo.
     *
     * @param string $inRefundNo
     *
     * @return TradeRefundEntity
     */
    public function setInRefundNo($inRefundNo)
    {
        $this->in_refund_no = $inRefundNo;

        return $this;
    }

    /**
     * Get inRefundNo.
     *
     * @return string
     */
    public function getInRefundNo()
    {
        return $this->in_refund_no;
    }

    /**
     * Set outRefundNo.
     *
     * @param string $outRefundNo
     *
     * @return TradeRefundEntity
     */
    public function setOutRefundNo($outRefundNo)
    {
        $this->out_refund_no = $outRefundNo;

        return $this;
    }

    /**
     * Get outRefundNo.
     *
     * @return string
     */
    public function getOutRefundNo()
    {
        return $this->out_refund_no;
    }

    /**
     * Set outTradeNo.
     *
     * @param string $outTradeNo
     *
     * @return TradeRefundEntity
     */
    public function setOutTradeNo($outTradeNo)
    {
        $this->out_trade_no = $outTradeNo;

        return $this;
    }

    /**
     * Get outTradeNo.
     *
     * @return string
     */
    public function getOutTradeNo()
    {
        return $this->out_trade_no;
    }

    /**
     * Set inTradeNo.
     *
     * @param string $inTradeNo
     *
     * @return TradeRefundEntity
     */
    public function setInTradeNo($inTradeNo)
    {
        $this->in_trade_no = $inTradeNo;

        return $this;
    }

    /**
     * Get inTradeNo.
     *
     * @return string
     */
    public function getInTradeNo()
    {
        return $this->in_trade_no;
    }

    /**
     * Set refundFee.
     *
     * @param int $refundFee
     *
     * @return TradeRefundEntity
     */
    public function setRefundFee($refundFee)
    {
        $this->refund_fee = $refundFee;

        return $this;
    }

    /**
     * Get refundFee.
     *
     * @return int
     */
    public function getRefundFee()
    {
        return $this->refund_fee;
    }

    
    /**
     * Set notifyUrl.
     *
     * @param string $notifyUrl
     *
     * @return TradeRefundEntity
     */
    public function setNotifyUrl($notifyUrl)
    {
        $this->notify_url = $notifyUrl;
        
        return $this;
    }
    
    /**
     * Get notifyUrl.
     *
     * @return string
     */
    public function getNotifyUrl()
    {
        return $this->notify_url;
    }
    
    /**
     * Set status.
     *
     * @param int $status
     *
     * @return TradeRefundEntity
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set requestTime.
     *
     * @param int $requestTime
     *
     * @return TradeRefundEntity
     */
    public function setRequestTime($requestTime)
    {
        $this->request_time = $requestTime;

        return $this;
    }

    /**
     * Get requestTime.
     *
     * @return int
     */
    public function getRequestTime()
    {
        return $this->request_time;
    }

    /**
     * Set responseTime.
     *
     * @param int $responseTime
     *
     * @return TradeRefundEntity
     */
    public function setResponseTime($responseTime)
    {
        $this->response_time = $responseTime;

        return $this;
    }

    /**
     * Get responseTime.
     *
     * @return int
     */
    public function getResponseTime()
    {
        return $this->response_time;
    }

    /**
     * Set payTime.
     *
     * @param int $payTime
     *
     * @return TradeRefundEntity
     */
    public function setPayTime($payTime)
    {
        $this->pay_time = $payTime;

        return $this;
    }

    /**
     * Get payTime.
     *
     * @return int
     */
    public function getPayTime()
    {
        return $this->pay_time;
    }

    /**
     * Set version.
     *
     * @param int $version
     *
     * @return TradeRefundEntity
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version.
     *
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }
}
