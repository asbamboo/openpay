<?php
namespace asbamboo\openpay\apiStore\handler\v1_0\trade;

use asbamboo\api\apiStore\ApiClassAbstract;
use asbamboo\api\apiStore\ApiRequestParamsInterface;
use asbamboo\openpay\apiStore\parameter\v1_0\trade\PayRequest;
use asbamboo\openpay\ClientInterface;
use asbamboo\api\apiStore\ApiResponseParamsInterface;
use asbamboo\openpay\apiStore\parameter\v1_0\trade\PayResponse;
use asbamboo\api\apiStore\ApiResponseRedirectParamsInterface;
use asbamboo\helper\env\Env AS EnvHelper;
use asbamboo\openpay\Env;
use asbamboo\openpay\Constant;
use asbamboo\api\apiStore\traits\CommonApiRequestParamsTrait;

/**
 * @name 交易支付
 * @desc 发起交易支付
 * @request asbamboo\openpay\apiStore\parameter\v1_0\trade\PayRequest
 * @request asbamboo\openpay\apiStore\parameter\v1_0\trade\PayResponse
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月13日
 */
class Pay extends ApiClassAbstract
{
    /**
     * 支持的支付方式
     * @var string
     */
    const   WXPAY_QRCD      = 'WXPAY_QRCD';     // 微信扫码
    const   ALIPAY_QRCD     = 'ALIPAY_QRCD';    // 支付宝扫码

    /**
     * 发起第三方请求的客户端
     *
     * @var ClientInterface
     */
    private $Client;

    /**
     *
     * @param ClientInterface $Client
     */
    public function __construct(ClientInterface $Client)
    {
        $this->Client   = $Client;
    }

    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\api\apiStore\ApiClassAbstract::validate()
     */
    public function validate(ApiRequestParamsInterface $Params): bool
    {
        return true;
    }

    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\api\apiStore\ApiClassAbstract::successApiResponseParams()
     * @var PayRequest $Params
     */
    public function successApiResponseParams(ApiRequestParamsInterface $Params) : ?ApiResponseParamsInterface
    {
        switch($Params->getPayment()){
            case self::WXPAY_QRCD:
                return $this->requestWxpayQRCD($Params);
            case self::ALIPAY_QRCD:
                return $this->requestAlipayQRCD($Params);
        }
    }


    /**
     *
     * @param PayRequest $Params
     * @return ApiResponseRedirectParamsInterface|NULL
     */
    private function requestWxpayQRCD(ApiRequestParamsInterface $Params) : ?ApiResponseParamsInterface
    {
        $request_data           = [
            'appid'             => (string) EnvHelper::get(Env::WXPAY_APP_ID),
            'mch_id'            => (string) EnvHelper::get(Env::WXPAY_MCH_ID),
            'body'              => $Params->getTitle(),
            'out_trade_no'      => $Params->getOutTradeNo(),
            'total_fee'         => $Params->getTotalFee(),
            'spbill_create_ip'  => $Params->getClientIp(),
            'notify_url'        => Constant::WXPAY_QRCD_NOTIFY_URL,
        ];
        $wx_params              = json_decode((string) $Params->getThirdPart(), true);
        if(is_array($wx_params)){
            foreach($wx_params AS $wx_key => $wx_value){
                $request_data[$wx_key] = $wx_value;
            }
        }

        $WxResponse                             = $this->Client->request('wxpay:ScanQRCodeByPayUnifiedorder', $request_data);
        $PayResponse                            = new PayResponse();
        $PayResponse->redirect_data['qr_code']   = $WxResponse->get('code_url');
        return $PayResponse;
    }

    /**
     *
     * @param PayRequest $Params
     * @return ApiResponseRedirectParamsInterface|NULL
     */
    private function requestAlipayQRCD(ApiRequestParamsInterface $Params) : ?ApiResponseParamsInterface
    {
        $request_data           = [
            'app_id'            => (string) EnvHelper::get(Env::ALIPAY_APP_ID),
            'out_trade_no'      => $Params->getOutTradeNo(),
            'total_amount'      => bcdiv($Params->getTotalFee(), 100, 2), //聚合接口接收的单位是分，支付宝的单位是元
            'subject'           => $Params->getTitle(),
            'notify_url'        => Constant::ALIPAY_QRCD_NOTIFY_URL,
        ];
        $alipay_params          = json_decode((string) $Params->getThirdPart(), true);
        if(is_array($alipay_params)){
            foreach($alipay_params AS $alipay_key => $alipay_value){
                $request_data[$alipay_key] = $alipay_value;
            }
        }

        $AlipayResponse                         = $this->Client->request('alipay:TradePrecreate', $request_data);
        $PayResponse                            = new PayResponse();
        $PayResponse->redirect_data['qr_code']  = $AlipayResponse->get('qr_code');
        return $PayResponse;
    }}
