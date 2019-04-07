<?php
namespace asbamboo\openpay\apiStore\parameter\v1_0\trade\pay;

use asbamboo\api\apiStore\ApiResponseParams;

/**
 * 交易支付接口请求响应值
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月13日
 */
class PayResponse extends ApiResponseParams
{
    /**
     * @desc 支付渠道
     * @example eval:asbamboo\openpay\apiStore\parameter\v1_0\trade\pay\Doc::channelExample();
     * @range eval:asbamboo\openpay\apiStore\parameter\v1_0\trade\pay\Doc::channelRange()
     * @required 必须
     * @var string length(45)
     */
    protected $channel;


    /**
     * @desc 交易编号 与支付请求的编号对应的聚合平台生成的交易编号 是一个全局唯一的编号
     * @example 201810131027242582
     * @required 必须
     * @var string length(32)
     */
    protected $in_trade_no;

    /**
     * @desc 交易标题
     * @example 支付测试
     * @required 必须
     * @var string length(45)
     */
    protected $title;

    /**
     * @desc 交易编号只能是数字
     * @example 2018101310270023
     * @required 必须
     * @var string length(45)
     */
    protected $out_trade_no;

    /**
     * @desc 交易金额 单位为分
     * @example 100
     * @required 必须
     * @var int
     */
    protected $total_fee;

    /**
     * @desc 客户ip
     * @example 123.123.123.123
     * @required 必须
     * @var string
     */
    protected $client_ip;

    /**
     * @desc 交易状态
     * @example PAYDONE
     * @range eval:asbamboo\openpay\apiStore\parameter\v1_0\trade\pay\Doc::tradeStatusRange();
     * @var string length(45)
     */
    protected $trade_status;

    /**
     * @desc 交易支付成功[可退款]时间
     * @example 2018-10-13 10:27:50
     * @var string date(YYYY-mm-dd HH:ii:ss)
     */
    protected $payok_ymdhis;

    /**
     * @desc 交易支付成功[不可退款]时间
     * @example 2018-10-13 10:27:50
     * @var string date(YYYY-mm-dd HH:ii:ss)
     */
    protected $payed_ymdhis;

    /**
     * @desc 交易取消时间
     * @example 2018-10-13 10:27:50
     * @var string date(YYYY-mm-dd HH:ii:ss)
     */
    protected $cancel_ymdhis;

    /**
     * @desc 二维码(买家扫商家适用),特定的支付渠道返回此参数
     * @example weixin://wxpay/bizpayurl/up?pr=NwY5Mz9&groupid=00
     * @var string length(200)
     */
    protected $qr_code;

    /**
     * @desc APP支付时，创建交易订单，并获取APP支付请求参数, json格式的字符串，相关参数请参考第三分支付渠道相关文档。
     * @example {"gateway":"https:\/\/openapi.alipaydev.com\/gateway.do","data":{"notify_url":"\/ALIPAY_APP\/notify","app_id":"2016090900468991","method":"alipay.trade.app.pay","format":"JSON","charset":"UTF-8","sign_type":"RSA2","sign":"E5TM1I9rbgrseKFm+SD9klYiV4NE\/sXv3aYWk\/6EhVY8twpbQavv6he0KpKYRtG9ECyayYmAJ\/yXGpJPl6s4YqgOeIV81+bq5xjVuKcYONYTjrVZNXG+srfgqWq7EFQhT7J+FXzatGeEaS2gBQbH1JtXCPc7XMo+3aWie0v0ESsvyPNCrS3o8ykqk9BdBy7xPGP+lwhUximHdb7fL+SUje17xTEj1uHoVqjf6Wiedcih60SkLSBxFKFEgLw6pwdOZcTHOG1Y8U2\/bYBmRlU1c3A3j8dN4fY1mdvTRMiJK5qIeXBLgXHwCzr5SwctW\/L7VjsiFtipQoj\/OcEzZOs5\/Q==","timestamp":"2019-02-27 01:01:45","version":"1.0","app_auth_token":null,"biz_content":"{\"body\":null,\"subject\":\"\u652f\u4ed8\u6d4b\u8bd5\",\"out_trade_no\":\"19057084932006201\",\"timeout_express\":null,\"total_amount\":\"1.00\",\"product_code\":\"QUICK_MSECURITY_PAY\",\"goods_type\":null,\"passback_params\":null,\"promo_params\":null,\"extend_params\":null,\"enable_pay_channels\":null,\"disable_pay_channels\":null,\"store_id\":null,\"ext_user_info\":null}"}}
     * @var string
     */
    protected $app_pay_json;
}