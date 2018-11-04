<?php
namespace asbamboo\openpay\channel\v1_0\trade\queryParameter;

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
     *
     * @param string|int $trade_status
     */
    public function setTradeStatus($trade_status) : self
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
     * @return self
     */
    public function setInTradeNo($in_trade_no) : self
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
     * @return self
     */
    public function setThirdTradeNo($third_trade_no) : self
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
}
