<?php

namespace asbamboo\openpay\model\tradeRefundClob;

/**
 * TradeRefundClobEntity
 */
class TradeRefundClobEntity
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
    private $third_part = '{}';


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
     * @return TradeRefundClobEntity
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
     * Set thirdPart.
     *
     * @param string $thirdPart
     *
     * @return TradeRefundClobEntity
     */
    public function setThirdPart($thirdPart)
    {
        $this->third_part = $thirdPart;

        return $this;
    }

    /**
     * Get thirdPart.
     *
     * @return string
     */
    public function getThirdPart()
    {
        return $this->third_part;
    }
}
