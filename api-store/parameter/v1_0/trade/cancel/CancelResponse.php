<?php
namespace asbamboo\openpay\apiStore\parameter\v1_0\trade\cancel;

use asbamboo\api\apiStore\ApiResponseParams;

/**
 * 交易取消接口请求参数
 * 
 * @author 李春寅<licy2013@aliyun.com>
 * @since 2018年11月6日
 */
class CancelResponse extends ApiResponseParams
{
    /**
     * @desc 支付渠道
     * @example eval:asbamboo\openpay\apiStore\parameter\v1_0\trade\cancel\Doc::channelExample();
     * @range eval:asbamboo\openpay\apiStore\parameter\v1_0\trade\cancel\Doc::channelRange()
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
     * @range eval:asbamboo\openpay\apiStore\parameter\v1_0\trade\cancel\Doc::tradeStatusRange();
     * @var string(45)
     */
    protected $trade_status;
    
    /**
     * @desc 交易取消时间
     * @example 2018-10-13 10:27:50
     * @var date(YYYY-mm-dd HH:ii:ss)
     */
    protected $cancel_ymdhis;
}