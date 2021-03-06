<?php
namespace asbamboo\openpay\apiStore\parameter\v1_0\trade\query;

use asbamboo\api\apiStore\ApiResponseParams;

/**
 * 交易查询接口响应值
 *
 * @author 李春寅<licy2013@aliyun.com>
 * @since 2018年10月27日
 */
class QueryResponse extends ApiResponseParams
{
    /**
     * @desc 支付渠道
     * @example eval:asbamboo\openpay\apiStore\parameter\v1_0\trade\query\Doc::channelExample();
     * @range eval:asbamboo\openpay\apiStore\parameter\v1_0\trade\query\Doc::channelRange()
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
     * @desc 交易编号, 在微信、支付宝等第三方系统内的交易编号（当订单尚未在第三方系统生成时返回空字符串）
     * @example 201810131027242582
     * @required 必须
     * @var string length(45)
     */
    protected $third_trade_no;
    
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
     * @range eval:asbamboo\openpay\apiStore\parameter\v1_0\trade\query\Doc::tradeStatusRange();
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
     * @desc 手机APP调起支付渠道的参数，只有手机APP支付时才应该返回这个参数
     * @example {"key1":"value1","key2":"value2"}
     * @var string json
     */
    protected $app_pay_json;
}
