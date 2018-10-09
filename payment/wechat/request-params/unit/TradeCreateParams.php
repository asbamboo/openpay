<?php
namespace asbamboo\openpay\payment\wechat\requestParams\unit;

use asbamboo\openpay\payment\wechat\requestParams\RequestParams;

/**
 * 统一下单
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月9日
 */
class TradeCreateParams extends RequestParams
{
    public $body;
    public $detail;
    public $attach;
    public $out_trade_no;
    public $fee_type;
    public $total_fee;
    public $spbill_create_ip;
    public $time_start;
    public $time_expire;
    public $goods_tag;
    public $notify_url;
    public $trade_type;
    public $product_id;
    public $limit_pay;
    public $openid;
    public $scene_info;

    /**
     * 数据映射配置
     *  - 返回的数组时请求参数的key与接受参数的key的映射关系
     *
     * @return array
     */
    private function mappingConfig() : array
    {
        return array_merge(parent::mappingConfig(),[
            'body'          => 'title',
            'detail'        => 'desc',
            'out_trade_no'  => 'out_trade_no',
            'total_fee'     => 'total_amount',
            'notify_url'    => 'notify_url',
            'openid'        => 'buyer_id',
        ]);
    }
}
