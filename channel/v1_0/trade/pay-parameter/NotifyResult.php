<?php
namespace asbamboo\openpay\channel\v1_0\trade\payParameter;

use asbamboo\openpay\Constant;
use asbamboo\openpay\exception\OpenpayException;

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
    private $trade_status = '0';

    /**
     * 聚合平台中的交易编号
     *
     * @var string
     */
    private $in_trade_no = '';

    /**
     * 第三方平台的交易编号
     *
     * @var string
     */
    private $third_trade_no = '';

    /**
     * 整个第三方平台的notify数据
     * 传递json格式
     *
     * @var string
     */
    private $third_part = '';

    /**
     * 成功时，返回给第三方平台的响应内容
     *
     * @var string
     */
    private $response_success = 'SUCCESS';

    /**
    * 失败时，返回给第三方平台的响应内容
    *
    * @var string
    */
    private $response_failed = 'FAILED';

    /**
     *
     * @param string|int $trade_status
     */
    public function setTradeStatus($trade_status) : self
    {
        if(!in_array($trade_status, [
            Constant::TRADE_PAY_TRADE_STATUS_CANCEL,
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
     * @return string|int
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
     * @return NotifyResult
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

    /**
     * 整个第三方平台的notify数据
     * 传递json格式
     *
     * @param string $third_part
     * @return NotifyResult
     */
    public function setThirdPart(string $third_part) : self
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

    /**
     *
     * @param string $response_success
     * @return NotifyResult
     */
    public function setResponseSuccess(string $response_success) : self
    {
        $this->response_success    = $response_success;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getResponseSuccess()
    {
        return $this->response_success;
    }

    /**
     *
     * @param string $response_failed
     * @return NotifyResult
     */
    public function setResponseFailed(string $response_failed) : self
    {
        $this->response_failed    = $response_failed;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getResponseFailed()
    {
        return $this->response_failed;
    }
}