<?php
namespace asbamboo\openpay\payment\alipay\requestParams\bizContent;

use asbamboo\openpay\payment\alipay\requestParams\BizContentInterface;
use asbamboo\openpay\common\traits\MappingDataTrait;

/**
 * alipay.trade.create(统一收单交易创建接口) 请求参数
 *
 * @see https://docs.open.alipay.com/api_1/alipay.trade.create/
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月9日
 */
class TradeCreateParams implements BizContentInterface
{
    use MappingDataTrait;

    public $out_trade_no;
    public $seller_id;
    public $total_amount;
    public $discountable_amount;
    public $subject;
    public $body;
    public $buyer_id;
    public $goods_detail;
    public $operator_id;
    public $store_id;
    public $terminal_id;
    public $extend_params;
    public $timeout_express;
    public $settle_info;
    public $business_params;
    public $receiver_address_info;
    public $logistics_detail;

    /**
     * 数据映射配置
     *  - 返回的数组时请求参数的key与接受参数的key的映射关系
     *
     * @return array
     */
    private function mappingConfig() : array
    {
        return [
            'out_trade_no'          => 'out_trade_no',
            'seller_id'             => 'seller_id',
            'total_amount'          => 'total_amount',
            'discountable_amount'   => 'discountable_amount',
            'subject'               => 'title',
            'body'                  => 'desc',
            'buyer_id'              => 'buyer_id',
        ];
    }
}
