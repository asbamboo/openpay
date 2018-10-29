<?php

namespace asbamboo\openpay\model\tradePay;

/**
 * TradePayThirdPartEntity
 */
class TradePayThirdPartEntity
{
    /**
     * @var int
     */
    private $seq;

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
     * Set sendData.
     *
     * @param string $sendData
     *
     * @return TradePayThirdPartEntity
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
