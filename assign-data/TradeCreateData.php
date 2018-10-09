<?php
namespace asbamboo\openpay;

/**
 * 创建交易
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月9日
 */
class TradeCreateData implements AssignDataInterface
{
    /**
     * 应用id
     * @var string
     */
    public $app_id;

    /**
     * 卖家/商户id
     * @var string
     */
    public $seller_id;

    /**
     * 异步通知地址url
     *
     * @var string
     */
    public $notify_url;

    /**
     * 商家订单号
     *
     * @var string
     */
    public $out_trade_no;

    /**
     * 订单标题
     *
     * @var string
     */
    public $title;

    /**
     * 订单描述
     *
     * @var string
     */
    public $desc;

    /**
     * 买家ID
     *
     * @var string
     */
    public $buyer_id;

    /**
     * 支付宝授权码 [alipay]
     *
     * @var string
     */
    public $app_auth_token;

    /**
     * 可打折金额 [alipay]
     *
     * @var string
     */
    public $discountable_amount;
}