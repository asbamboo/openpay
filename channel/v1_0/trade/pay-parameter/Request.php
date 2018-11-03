<?php
namespace asbamboo\openpay\channel\v1_0\trade\payParameter;

use asbamboo\openpay\channel\common\ParameterTrait;
use asbamboo\helper\env\Env AS EnvHelper;
use asbamboo\openpay\Env;

/**
 * 传递给渠道处理方法的请求参数
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月30日
 */
final class Request
{
    use ParameterTrait;

    /**
     * @desc 支付渠道
     * @required 必须
     * @var string(45)
     */
    protected $channel;

    /**
     * @desc 交易标题
     * @example 支付测试
     * @required 必须
     * @var string(45)
     */
    protected $title;

    /**
     * 聚合平台生成的交易编号, 全局唯一
     * @desc 交易编号只能是数字
     * @example 2018101310270023
     * @var number(32)
     */
    protected $in_trade_no;

    /**
     * @desc 交易金额 单位为分
     * @example 100
     * @required 必须
     * @var price(10)
     */
    protected $total_fee;

    /**
     * @desc 客户ip
     * @example 123.123.123.123
     * @required 必须
     * @var string(20)
     */
    protected $client_ip;

    /**
     * @desc 第三方支付平台的参数，请自行查阅相关支付平台相关文档中的参数列表
     * @example {"limit_pay":"no_credit"}
     * @required 可选
     * @var json()
     */
    protected $third_part;

    /**
     * @desc 这个notify url时聚合支付平台接收第三方平台推送信息的url并不是对接应用发送的notify url
     * @example http://api.test.asbamboo.com/notify/trade/pay
     * @var string(200)
     */
    protected $notify_url;

    /**
     * @desc 这个return url时聚合支付平台接收第三方平台推送信息的url并不是对接应用发送的return url
     * @example http://api.test.asbamboo.com/return/trade/pay
     * @var string(200)
     */
    protected $return_url;

    /**
     *
     * @return \asbamboo\openpay\channel\v1_0\trade\payParameter\string(45)
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     *
     * @return \asbamboo\openpay\channel\v1_0\trade\payParameter\string(45)
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     *
     * @return \asbamboo\openpay\channel\v1_0\trade\payParameter\string(45)
     */
    public function getInTradeNo()
    {
        return $this->in_trade_no;
    }

    /**
     *
     * @return \asbamboo\openpay\channel\v1_0\trade\payParameter\price(10)
     */
    public function getTotalFee()
    {
        return $this->total_fee;
    }

    /**
     *
     * @return \asbamboo\openpay\channel\v1_0\trade\payParameter\string(20)
     */
    public function getClientIp()
    {
        return $this->client_ip;
    }

    /**
     *
     * @return \asbamboo\openpay\channel\v1_0\trade\payParameter\json()
     */
    public function getThirdPart()
    {
        return $this->third_part;
    }

    /**
     *
     * @return \asbamboo\openpay\channel\v1_0\trade\payParameter\string(200)
     */
    public function getNotifyUrl()
    {
        if(!$this->notify_url){
            $this->notify_url   = EnvHelper::get(Env::TRADE_PAY_NOTIFY_URL);
        }
        return $this->notify_url;
    }

    /**
     *
     * @return \asbamboo\openpay\channel\v1_0\trade\payParameter\string(200)
     */
    public function getReturnUrl()
    {
        if(!$this->return_url){
            $this->return_url   = EnvHelper::get(Env::TRADE_PAY_RETURN_URL);
        }
        return $this->return_url;
    }
}
