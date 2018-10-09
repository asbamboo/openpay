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
     * 应用id(必填)
     * @var string
     */
    public $app_id;

    /**
     * 子商户公众账号ID(可选)
     *
     * 微信分配的子商户公众账号ID，如需在支付完成后获取sub_openid则此参数必传。
     *
     * 以下渠道的字段[wxpay]
     *
     * @var string
     */
    public $sub_appid;

    /**
     * 卖家/商户id
     * (微信必填)
     * @var string
     */
    public $seller_id;

    /**
     * 微信支付分配的子商户号(可选)
     * 作为微信服务商时必须传
     *
     * 以下渠道的字段[wxpay]
     *
     * @var string
     */
    public $sub_mch_id;

    /**
     * 设备号 终端设备号(门店号或收银设备ID)，注意：PC网页或公众号内支付请传"WEB"
     *
     * 以下渠道的字段[wxpay]
     *
     * @var string
     */
    public $device_info;

    /**
     * 异步通知地址url
     * (微信必填)
     *
     * @var string
     */
    public $notify_url;

    /**
     * 商家订单号(必填)
     *
     * @var string
     */
    public $out_trade_no;

    /**
     * 订单标题(必填)
     *
     * @var string
     */
    public $title;

    /**
     * 订单描述(可选)
     *
     * 以下渠道的字段[alipay:body]
     *
     * @var string
     */
    public $desc;

    /**
     * 买家ID/用户标识（可选）
     *
     * @var string
     */
    public $buyer_id;

    /**
     * 用户子标识(可选)
     *
     * trade_type=JSAPI，此参数必传，用户在子商户appid下的唯一标识。
     * openid和sub_openid可以选传其中之一，如果选择传sub_openid,则必须传sub_appid。
     * 下单前需要调用【网页授权获取用户信息】接口获取到用户的Openid。
     *
     * 以下渠道的字段[wxpay]
     *
     * @var string
     */
    public $sub_openid;

    /**
     * 场景信息(可选)
     * 该字段用于上报场景信息，目前支持上报实际门店信息。该字段为JSON对象数据，对象格式为
     *  {
     *      "store_info":{"id": "门店ID","name": "名称","area_code": "编码","address": "地址" }
     *
     *  }
     *
     *  1，IOS移动应用
     *   {"h5_info": //h5支付固定传"h5_info"
     *      {"type": "",  //场景类型
     *       "app_name": "",  //应用名
     *       "bundle_id": ""  //bundle_id
     *       }
     *  }
     *
     *  2，安卓移动应用
     *  {"h5_info": //h5支付固定传"h5_info"
     *      {"type": "",  //场景类型
     *       "app_name": "",  //应用名
     *       "package_name": ""  //包名
     *       }
     *  }
     *
     *  3，WAP网站应用
     *  {"h5_info": //h5支付固定传"h5_info"
     *     {"type": "",  //场景类型
     *      "wap_url": "",//WAP网站URL地址
     *      "wap_name": ""  //WAP 网站名
     *      }
     *  }
     *
     * 以下渠道的字段[wxpay]
     *
     * @var string
     */
    public $scene_info;

    /**
     * 商品列表（可选），json格式
     * 支付宝包含字段[{
     *  - goods_id:商品的编号:必须,
     *  - goods_name:商品名称:必须,
     *  - quantity:商品数量:必须,price:商品单价，单位为元:必须,
     *  - goods_category:商品类目:可选,
     *  - body:	商品描述信息:可选,
     *  - show_url:商品的展示地址:可选
     * }]
     * 以下渠道的字段[alipay]
     *
     * @var string
     */
    public $goods_detail;


    /**
     * 商品详细描述，对于使用单品优惠的商户，改字段必须按照规范上传，详见“单品优惠参数说明”(可选)
     *
     * 微信包含字段{
     *  - cost_price:订单原价:可选
     *  - receipt_id:商品小票ID:可选
     *  - goods_detail:单品列表:必须[{
     *      - goods_id:商品编码:必须
     *      - wxpay_goods_id:微信侧商品编码:可选
     *      - goods_name:商品名称:可选
     *      - quantity:商品数量:必须
     *      - price:商品单价:必须
     *  }]
     * }
     *
     * 以下渠道的字段[wxpay]
     *
     * @var string
     */
    public $detail;

    /**
     * 附加数据(可选)
     *
     * 附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据
     *
     * 以下渠道的字段[wxpay]
     *
     * @var string
     */
    public $attach;

    /**
     * 货币类型(可选)
     * 默认CNY
     *
     * 以下渠道的字段[wxpay]
     *
     * @var string
     */
    public $fee_type;

    /**
     * 终端IP(可选)
     * 微信必须
     *
     * APP和网页支付提交用户端ip，Native支付填调用微信支付API的机器IP。
     *
     * 以下渠道的字段[wxpay]
     *
     * @var string
     */
    public $spbill_create_ip;

    /**
     * 商户操作员编号（可选）
     * 以下渠道的字段[alipay]
     *
     * @var string
     */
    public $operator_id;

    /**
     * 商户门店编号(可选)
     *
     * 以下渠道的字段[alipay]
     *
     * @var string
     */
    public $store_id;

    /**
     * 商户机具终端编号(可选)
     * 以下渠道的字段[alipay]
     *
     * @var string
     */
    public $terminal_id;

    /**
     * 业务扩展参数(可选)
     * {
     *  - sys_service_provider_id:系统商编号:可选
     *  - industry_reflux_info:行业数据回流信息, 详见：地铁支付接口参数补充说明:可选
     *  - card_type:卡类型:可选
     * }
     * 以下渠道的字段[alipay]
     *
     * @var string
     */
    public $extend_params;

    /**
     * 该笔订单允许的最晚付款时间，逾期将关闭交易。(可选	)
     * 取值范围：1m～15d。
     * m-分钟，h-小时，d-天，1c-当天（1c-当天的情况下，无论交易何时创建，都在0点关闭）。
     * 该参数数值不接受小数点， 如 1.5h，可转换为 90m。
     * 以下渠道的字段[alipay]
     * @var string
     */
    public $timeout_express;

    /**
     * 交易起始时间(可选)
     *
     * 以下渠道的字段[wxpay]
     *
     * @var string
     */
    public $time_start;

    /**
     * 交易结束时间(可选)
     *
     * 以下渠道的字段[wxpay]
     *
     * @var string
     */
    public $time_expire;

    /**
     * 订单优惠标记(可选)
     *
     * 以下渠道的字段[wxpay]
     *
     * @var string
     */
    public $goods_tag;

    /**
     * 交易类型(可选)
     * 微信必须
     * 以下渠道的字段[wxpay]
     *
     * @var string
     */
    public $trade_type;

    /**
     * 商品ID(可选)
     *
     * trade_type=NATIVE，此参数必传。此id为二维码中包含的商品ID，商户自行定义。
     *
     * 以下渠道的字段[wxpay]
     *
     * @var string
     */
    public $product_id;

    /**
     * 指定支付方式(可选)
     *
     * 以下渠道的字段[wxpay]
     *
     * @var string
     */
    public $limit_pay;

    /**
     * 描述结算信息，json格式，详见结算参数说明(可选)
     * {
     *  - settle_detail_infos:结算详细信息:必须[{
     *      - trans_in_type:结算收款方的账户类型:必须
     *      - trans_in:结算收款方:必须
     *      - summary_dimension:结算汇总维度，按照这个维度汇总成批次结算，由商户指定:可选
     *      - settle_entity_id:结算主体标识:可选
     *      - settle_entity_type:结算主体类型:可选
     *      - amount:结算的金额:必须
     *  }]
     *  - merchant_type:商户id类型:可选
     * }
     *
     * 以下渠道的字段[alipay]
     *
     * @var string
     */
    public $settle_info;

    /**
     * 商户传入业务信息，具体值要和支付宝约定，应用于安全，营销等参数直传场景，格式为json格式(可选)
     *
     * 以下渠道的字段[alipay]
     *
     * @var string
     */
    public $business_params;

    /**
     * 收货人及地址信息(可选)
     * {
     *  - name:收货人的姓名:可选
     *  - address:收货地址:可选
     *  - mobile:收货人手机号:可选
     *  - zip:收货地址邮编:可选
     *  - division_code:中国标准城市区域码:可选
     * }
     *
     * 以下渠道的字段[alipay]
     *
     * @var string
     */
    public $receiver_address_info;

    /**
     * 物流信息(可选)
     * {
     *  - logistics_type:物流类型:可选
     * }
     *
     * @var string
     */
    public $logistics_detail;

    /**
     * 支付宝授权码（特殊可选）
     *
     * 以下渠道的字段[alipay]
     *
     * @var string
     */
    public $app_auth_token;

    /**
     * 可打折金额 (可选)
     *
     * 以下渠道的字段[alipay]
     *
     * @var string
     */
    public $discountable_amount;
}