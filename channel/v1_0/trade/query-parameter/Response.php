<?php
namespace asbamboo\openpay\channel\v1_0\trade\queryParameter;

use asbamboo\openpay\Constant;
use asbamboo\openpay\exception\OpenpayException;

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
        if(!in_array($trade_status, [
            Constant::TRADE_PAY_TRADE_STATUS_CANCLE,
            Constant::TRADE_PAY_TRADE_STATUS_NOPAY,
            Constant::TRADE_PAY_TRADE_STATUS_PAYED,
            Constant::TRADE_PAY_TRADE_STATUS_PAYFAILED,
            Constant::TRADE_PAY_TRADE_STATUS_PAYING,
            Constant::TRADE_PAY_TRADE_STATUS_PAYOK,
        ])){
            throw new OpenpayException('交易状态超出聚合平台支持的范围。');
        }
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
        if(ctype_digit((string) $in_trade_no) == false){
            throw new OpenpayException('聚合平台交易编号必须时数字。');
        }
        if(strlen((string) $in_trade_no) > 32){
            throw new OpenpayException('聚合平台交易编号不能超过32个字。');
        }
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
        if(strlen((string) $third_trade_no) > 45){
            throw new OpenpayException('第三方平台交易编号不能超过45个字。');
        }
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
