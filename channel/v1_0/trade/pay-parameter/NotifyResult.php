<?php
namespace asbamboo\openpay\channel\v1_0\trade\payParameter;

use asbamboo\openpay\Constant;

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
     * @see Constant 取值范围参考常量中的 TRADE_PAY_TRADE_STATUS_XXXX
     * @var integer
     */
    private $trade_status;

    /**
     * 聚合平台中的交易编号
     *
     * @var string
     */
    private $in_trade_no;

    /**
     * 第三方平台的交易编号
     *
     * @var string
     */
    private $third_trade_no;

    /**
     * 整个第三方平台的notify数据
     * 传递json格式
     *
     * @var string
     */
    private $third_part;

    /**
     *
     * @param string|int $trade_status
     */
    public function setTradeStatus($trade_status) : NotifyResult
    {
        $this->trade_status = $trade_status;
        return $this;
    }

    /**
     *
     * @return number
     */
    public function getTradeStatus()
    {
        return $this->trade_status;
    }

    /**
     * 聚合平台中的交易编号
     *
     * @param string $in_trade_no
     * @return NotifyResult
     */
    public function setInTradeNo($in_trade_no) : NotifyResult
    {
        $this->in_trade_no    = $in_trade_no;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getInTradeNo()
    {
        return $this->in_trade_no;
    }

    /**
     * 第三方平台的交易编号
     *
     * @param string $third_trade_no
     * @return NotifyResult
     */
    public function setThirdTradeNo($third_trade_no) : NotifyResult
    {
        $this->third_trade_no   = $third_trade_no;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getThirdTradeNo()
    {
        return $this->third_trade_no;
    }

    /**
     * 整个第三方平台的notify数据
     * 传递json格式
     *
     * @param string $third_part
     * @return NotifyResult
     */
    public function setThirdPart(string $third_part) : NotifyResult
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
}