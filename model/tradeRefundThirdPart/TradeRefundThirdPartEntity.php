<?php

namespace asbamboo\openpay\model\tradeRefundThirdPart;

/**
 * TradeRefundThirdPartEntity
 */
class TradeRefundThirdPartEntity
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
    private $send_data = '[]';


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
     * @return TradeRefundThirdPartEntity
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
     * Set sendData.
     *
     * @param string $sendData
     *
     * @return TradeRefundThirdPartEntity
     */
    public function setSendData($sendData)
    {
        $this->send_data = $sendData;

        return $this;
    }

    /**
     * Get sendData.
     *
     * @return string
     */
    public function getSendData()
    {
        return $this->send_data;
    }
}
