<?php

namespace asbamboo\openpay\model\tradePay;

/**
 * TradePayEntity
 */
class TradePayEntity
{
    /**
     * @var int
     */
    private $seq;

    /**
     * @var string
     */
    private $in_trade_no = '';

    /**
     * @var string
     */
    private $channel = '';

    /**
     * @var string
     */
    private $title = '';

    /**
     * @var string
     */
    private $out_trade_no = '';

    /**
     * @var int
     */
    private $total_fee = '0';

    /**
     * @var string
     */
    private $client_ip = '';

    /**
     * @var string
     */
    private $notify_url = '';

    /**
     * @var string
     */
    private $return_url = '';

    /**
     * @var string
     */
    private $third_trade_no = '';

    /**
     * @var string
     */
    private $qr_code = '';

    /**
     * @var int
     */
    private $trade_status = '0';

    /**
     * @var int
     */
    private $payok_time = '0';

    /**
     * @var int
     */
    private $payed_time = '0';

    /**
     * @var int
     */
    private $cancel_time = '0';

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
     * Set inTradeNo.
     *
     * @param string $inTradeNo
     *
     * @return TradePayEntity
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
     * Set channel.
     *
     * @param string $channel
     *
     * @return TradePayEntity
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;

        return $this;
    }

    /**
     * Get channel.
     *
     * @return string
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return TradePayEntity
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set outTradeNo.
     *
     * @param string $outTradeNo
     *
     * @return TradePayEntity
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
     * Set totalFee.
     *
     * @param int $totalFee
     *
     * @return TradePayEntity
     */
    public function setTotalFee($totalFee)
    {
        $this->total_fee = $totalFee;

        return $this;
    }

    /**
     * Get totalFee.
     *
     * @return int
     */
    public function getTotalFee()
    {
        return $this->total_fee;
    }

    /**
     * Set clientIp.
     *
     * @param string $clientIp
     *
     * @return TradePayEntity
     */
    public function setClientIp($clientIp)
    {
        $this->client_ip = $clientIp;

        return $this;
    }

    /**
     * Get clientIp.
     *
     * @return string
     */
    public function getClientIp()
    {
        return $this->client_ip;
    }

    /**
     * Set notifyUrl.
     *
     * @param string $notifyUrl
     *
     * @return TradePayEntity
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
     * Set returnUrl.
     *
     * @param string $returnUrl
     *
     * @return TradePayEntity
     */
    public function setReturnUrl($returnUrl)
    {
        $this->return_url = $returnUrl;

        return $this;
    }

    /**
     * Get returnUrl.
     *
     * @return string
     */
    public function getReturnUrl()
    {
        return $this->return_url;
    }

    /**
     * Set thirdTradeNo.
     *
     * @param string $thirdTradeNo
     *
     * @return TradePayEntity
     */
    public function setThirdTradeNo($thirdTradeNo)
    {
        $this->third_trade_no = $thirdTradeNo;

        return $this;
    }

    /**
     * Get thirdTradeNo.
     *
     * @return string
     */
    public function getThirdTradeNo()
    {
        return $this->third_trade_no;
    }

    /**
     * Set qrCode.
     *
     * @param string $qrCode
     *
     * @return TradePayEntity
     */
    public function setQrCode($qrCode)
    {
        $this->qr_code = $qrCode;

        return $this;
    }

    /**
     * Get qrCode.
     *
     * @return string
     */
    public function getQrCode()
    {
        return $this->qr_code;
    }

    /**
     * Set tradeStatus.
     *
     * @param int $tradeStatus
     *
     * @return TradePayEntity
     */
    public function setTradeStatus($tradeStatus)
    {
        $this->trade_status = $tradeStatus;

        return $this;
    }

    /**
     * Get tradeStatus.
     *
     * @return int
     */
    public function getTradeStatus()
    {
        return $this->trade_status;
    }

    /**
     * Set payokTime.
     *
     * @param int $payokTime
     *
     * @return TradePayEntity
     */
    public function setPayokTime($payokTime)
    {
        $this->payok_time = $payokTime;

        return $this;
    }

    /**
     * Get payokTime.
     *
     * @return int
     */
    public function getPayokTime()
    {
        return $this->payok_time;
    }

    /**
     * Set payedTime.
     *
     * @param int $payedTime
     *
     * @return TradePayEntity
     */
    public function setPayedTime($payedTime)
    {
        $this->payed_time = $payedTime;

        return $this;
    }

    /**
     * Get payedTime.
     *
     * @return int
     */
    public function getPayedTime()
    {
        return $this->payed_time;
    }

    /**
     * Set cancelTime.
     *
     * @param int $cancelTime
     *
     * @return TradePayEntity
     */
    public function setCancelTime($cancelTime)
    {
        $this->cancel_time = $cancelTime;

        return $this;
    }

    /**
     * Get cancelTime.
     *
     * @return int
     */
    public function getCancelTime()
    {
        return $this->cancel_time;
    }

    /**
     * Set version.
     *
     * @param int $version
     *
     * @return TradePayEntity
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
