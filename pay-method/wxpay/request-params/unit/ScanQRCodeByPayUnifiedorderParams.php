<?php
namespace asbamboo\openpay\payMethod\wxpay\requestParams\unit;

use asbamboo\openpay\payMethod\wxpay\requestParams\RequestParams;

class ScanQRCodeByPayUnifiedorderParams extends RequestParams
{
    /**
     * 必填
     * 商品描述
     * 商品简单描述，该字段请按照规范传递，具体请见参数规定
     *
     * @see https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=4_2
     * @var string(128)
     */
    public $body;

    public $detail;
    public $attach;

    /**
     * 必填
     * 商户订单号
     * 商户系统内部订单号，要求32个字符内，只能是数字、大小写字母_-|* 且在同一个商户号下唯一。详见商户订单号
     *
     * @see https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=4_2
     * @var string(32)
     */
    public $out_trade_no;

    /**
     * @see https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=4_2
     * @var string
     */
    public $fee_type;

    /**
     * 必填
     * 标价金额
     * 订单总金额，单位为分，详见支付金额
     *
     * @see https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=4_2
     * @var int
     */
    public $total_fee;

    /**
     * 必填
     * APP和网页支付提交用户端ip，Native支付填调用微信支付API的机器IP。
     *
     * @var string(16)
     */
    public $spbill_create_ip;

    public $time_start;
    public $time_expire;
    public $goods_tag;

    /**
     * 必填
     * 异步接收微信支付结果通知的回调地址，通知url必须为外网可访问的url，不能携带参数。
     *
     * @var string(256)
     */
    public $notify_url;

    /**
     * 必填
     * JSAPI 公众号支付 NATIVE 扫码支付 APP APP支付 说明详见参数规定
     *
     * @var string(16)
     */
    public $trade_type = 'NATIVE';

    public $product_id;
    public $limit_pay;
    public $openid;
    public $scene_info;
}
