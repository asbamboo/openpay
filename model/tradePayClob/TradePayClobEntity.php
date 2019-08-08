<?php

namespace asbamboo\openpay\model\tradePayClob;

/**
 * TradePayClobEntity
 */
class TradePayClobEntity
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
    private $app_pay_json = '{}';

    /**
     * @var string
     */
    private $onecd_pay_json = '{}';

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
     * Set inTradeNo.
     *
     * @param string $inTradeNo
     *
     * @return TradePayClobEntity
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
     * Set appPayJson.
     *
     * @param string $appPayJson
     *
     * @return TradePayClobEntity
     */
    public function setAppPayJson($appPayJson)
    {
        $this->app_pay_json = $appPayJson;

        return $this;
    }

    /**
     * Get appPayJson.
     *
     * @return string
     */
    public function getAppPayJson()
    {
        return $this->app_pay_json;
    }

    /**
     * Set onecdPayJson.
     *
     * @param string $onecdPayJson
     *
     * @return TradePayClobEntity
     */
    public function setOnecdPayJson($onecdPayJson)
    {
        $this->onecd_pay_json = $onecdPayJson;

        return $this;
    }

    /**
     * Get onecdPayJson.
     *
     * @return string
     */
    public function getOnecdPayJson()
    {
        return $this->onecd_pay_json;
    }

    /**
     * Set thirdPart.
     *
     * @param string $thirdPart
     *
     * @return TradePayClobEntity
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
