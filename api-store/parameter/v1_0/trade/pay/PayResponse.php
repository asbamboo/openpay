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
     * @var string(45)
     */
    protected $channel;


    /**
     * @desc 交易编号 与支付请求的编号对应的聚合平台生成的交易编号 是一个全局唯一的编号
     * @example 201810131027242582
     * @required 必须
     * @var number(32)
     */
    protected $in_trade_no;

    /**
     * @desc 交易标题
     * @example 支付测试
     * @required 必须
     * @var string(45)
     */
    protected $title;

    /**
     * @desc 交易编号只能是数字
     * @example 2018101310270023
     * @required 必须
     * @var string(45)
     */
    protected $out_trade_no;

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
     * @desc 交易状态
     * @example PAYDONE
     * @range eval:asbamboo\openpay\apiStore\parameter\v1_0\trade\pay\Doc::tradeStatusRange();
     * @var string(45)
     */
    protected $trade_status;

    /**
     * @desc 交易支付成功[可退款]时间
     * @example 2018-10-13 10:27:50
     * @var date(YYYY-mm-dd HH:ii:ss)
     */
    protected $payok_ymdhis;

    /**
     * @desc 交易支付成功[不可退款]时间
     * @example 2018-10-13 10:27:50
     * @var date(YYYY-mm-dd HH:ii:ss)
     */
    protected $payed_ymdhis;

    /**
     * @desc 交易取消时间
     * @example 2018-10-13 10:27:50
     * @var date(YYYY-mm-dd HH:ii:ss)
     */
    protected $cancel_ymdhis;

    /**
     * @desc 二维码(买家扫商家适用),特定的支付渠道返回此参数
     * @example weixin://wxpay/bizpayurl/up?pr=NwY5Mz9&groupid=00
     * @var string(200)
     */
    protected $qr_code;

    /**
     * @desc APP支付时，创建交易订单，并获取APP支付请求参数
     * @example json格式的字符串，相关参数请参考第三分支付渠道相关文档。
     * @var string
     */
    protected $app_pay_json;
}