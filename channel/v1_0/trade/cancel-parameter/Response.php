<?php
namespace asbamboo\openpay\channel\v1_0\trade\cancelParameter;

/**
 * 渠道处理方法处理请求后应该返回的结果
 *
 * @author 李春寅<licy2013@aliyun.com>
 * @since 2018年10月30日
 */
final class Response
{   
    /**
     * 聚合平台生成的交易编号, 全局唯一
     *
     * @desc 交易编号只能是数字
     * @example 2018101310270023
     * @var number(32)
     */
    protected $in_trade_no;
    
    /**
     * 是否成功
     * 
     * @var bool
     */
    protected $is_success;
    
    /**
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
     * @return \asbamboo\openpay\channel\v1_0\trade\cancelParameter\number(32)
     */
    public function getInTradeNo()
    {
        return $this->in_trade_no;
    }
    
    /**
     * 
     * @param bool $is_success
     * @return self
     */
    public function setIsSuccess(bool $is_success) : self
    {
        $this->is_success = $is_success;
        return $this;
    }
    
    /**
     * 
     * @return boolean
     */
    public function getIsSuccess()
    {
        return $this->is_success;
    }
}