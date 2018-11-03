<?php
namespace asbamboo\openpay\apiStore\handler\v1_0\trade;

use asbamboo\api\apiStore\ApiRequestParamsInterface;
use asbamboo\api\apiStore\ApiResponseParamsInterface;
use asbamboo\openpay\channel\ChannelManagerInterface;
use asbamboo\openpay\apiStore\parameter\v1_0\trade\pay\PayRequest;
use asbamboo\openpay\model\tradePay\TradePayManager;
use asbamboo\openpay\model\tradePayThirdPart\TradePayThirdPartManager;
use asbamboo\openpay\model\tradePay\TradePayEntity;
use asbamboo\api\apiStore\ApiClassInterface;
use asbamboo\openpay\model\tradePayThirdPart\TradePayThirdPartEntity;
use asbamboo\database\Factory;
use asbamboo\openpay\channel\v1_0\trade\payParameter\Request AS RequestByChannel;
use asbamboo\api\apiStore\ApiResponseRedirectParams;
use asbamboo\helper\env\Env AS EnvHelper;
use asbamboo\openpay\Env;
use asbamboo\router\Router;
use asbamboo\router\RouterInterface;
use asbamboo\database\FactoryInterface;
use asbamboo\openpay\apiStore\exception\TradePayChannelInvalidException;
use asbamboo\openpay\channel\v1_0\trade\payParameter\Response;
use asbamboo\api\apiStore\ApiResponseRedirectParamsInterface;

/**
 * @name 交易支付
 * @desc 发起交易支付
 * @request asbamboo\openpay\apiStore\parameter\v1_0\trade\pay\PayRequest
 * @response asbamboo\openpay\apiStore\parameter\v1_0\trade\pay\PayResponse
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月13日
 */
class Pay implements ApiClassInterface
{
    /**
     * 渠道管理器
     *
     * @var ChannelManagerInterface
     */
    private $ChannelManager;

    /**
     *
     * @var FactoryInterface
     */
    private $Db;

    /**
     *
     * @var RouterInterface
     */
    private $Router;

    /**
     *
     * @var TradePayManager
     */
    private $TradePayManager;

    /**
     *
     * @var TradePayThirdPartManager
     */
    private $TradePayThirdPartManager;

    /**
     *
     * @param ChannelManagerInterface $ChannelManager
     * @param Factory $Db
     * @param TradePayManager $TradePayManager
     * @param TradePayThirdPartManager $TradePayThirdPartManager
     * @param Router $Router
     */
    public function __construct(
        ChannelManagerInterface $ChannelManager,
        FactoryInterface $Db,
        TradePayManager $TradePayManager,
        TradePayThirdPartManager $TradePayThirdPartManager,
        RouterInterface $Router
    ){
        $this->Db                       = $Db;
        $this->Router                   = $Router;
        $this->ChannelManager           = $ChannelManager;
        $this->TradePayManager          = $TradePayManager;
        $this->TradePayThirdPartManager = $TradePayThirdPartManager;
    }

    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\api\apiStore\ApiClassAbstract::successApiResponseParams()
     * @var PayRequest $Params
     */
    public function exec(ApiRequestParamsInterface $Params) : ?ApiResponseParamsInterface
    {
        /**
         * 响应值
         *
         * @var ApiResponseParamsInterface $ApiResponseParams
         */
        $ApiResponseParams  = null;

        /**
         * 创建交易数据信息
         *
         * @var \asbamboo\openpay\model\tradePay\TradePayEntity $TradePayEntity
         */
        $TradePayEntity = new TradePayEntity();
        $TradePayEntity->setChannel($Params->getChannel());
        $TradePayEntity->setTitle($Params->getTitle());
        $TradePayEntity->setTotalFee($Params->getTotalFee());
        $TradePayEntity->setOutTradeNo($Params->getOutTradeNo());
        $TradePayEntity->setClientIp($Params->getClientIp());
        $TradePayEntity->setNotifyUrl($Params->getNotifyUrl());
        $TradePayEntity->setReturnUrl($Params->getReturnUrl());
        $this->TradePayManager->insert($TradePayEntity);

        $TradePayThirdPartEntity = new TradePayThirdPartEntity();
        $TradePayThirdPartEntity->setInTradeNo($TradePayEntity->getInTradeNo());
        $TradePayThirdPartEntity->setSendData($Params->getThirdPart());
        $this->TradePayThirdPartManager->insert($TradePayThirdPartEntity);

        /**
         * 发起第三方渠道请求
         *
         * @var PayInterface $Channel
         * @var Response $ChannelResponse
         */
        $channel_name       = $Params->getChannel();
        $Channel            = $this->ChannelManager->getChannel(__CLASS__, $channel_name);
        if(!$Channel){
            throw new TradePayChannelInvalidException(sprintf('支付渠道%s暂不支持。', $Channel));
        }
        $ChannelResponse    = $Channel->execute(new RequestByChannel([
            'channel'       => $TradePayEntity->getChannel(),
            'title'         => $TradePayEntity->getTitle(),
            'in_trade_no'   => $TradePayEntity->getInTradeNo(),
            'total_fee'     => $TradePayEntity->getTotalFee(),
            'client_ip'     => $TradePayEntity->getClientIp(),
            'notify_url'    => $this->Router->generateUrl('notify', ['channel' => $channel_name]),
            'return_url'    => $this->Router->generateUrl('return', ['channel' => $channel_name]),
        ]));

        /**
         * 扫二维码支付时应该有的响应结果
         */
        if($ChannelResponse->getRedirectType() == $ChannelResponse::REDIRECT_TYPE_QRCD && $ChannelResponse->getQrCode()){
            $ApiResponseParams  = $this->makeQrCodeResponse($ChannelResponse);
        }else if($ChannelResponse->getRedirectType() == $ChannelResponse::REDIRECT_TYPE_PC && $ChannelResponse->getRedirectData()){
            $ApiResponseParams  = $this->makePcResponse($ChannelResponse);
        }


        /**
         * 数据保存
         */
        $this->Db->getManager()->flush();

        /**
         * 返回
         */
        return $ApiResponseParams;
    }

    /**
     * 生成跳转到扫码支付页面的响应
     *
     * @param Response $Response
     * @return ApiResponseRedirectParamsInterface
     */
    private function makeQrCodeResponse(Response $Response) : ApiResponseRedirectParamsInterface
    {
        return new class ($Response->getQrCode()) extends ApiResponseRedirectParams{
            private $qr_code;
            public function __construct($qr_code)
            {
                $this->qr_code  = $qr_code;
            }

            public function getRedirectUri() : string
            {
                return EnvHelper::get(Env::QRCODE_URL);
            }

            public function getRedirectResponseData() : array
            {
                return [
                    'qr_code'   => $this->qr_code,
                ];
            }
        };
    }

    /**
     * 生成跳转到PC支付页面的响应
     *
     * @param Response $Response
     * @return ApiResponseRedirectParamsInterface
     */
    private function makePcResponse(Response $Response) :  ApiResponseRedirectParamsInterface
    {
        return new class ($Response->getRedirectUrl(), $Response->getRedirectData()) extends ApiResponseRedirectParams{
            private $url;
            private $data;
            public function __construct($url, $data)
            {
                $this->url      = $url;
                $this->data     = $data;
            }

            public function getRedirectUri() : string
            {
                return $this->url;
            }

            public function getRedirectResponseData() : array
            {
                return $this->data;
            }
        };
    }
}
