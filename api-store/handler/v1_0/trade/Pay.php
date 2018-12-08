<?php
namespace asbamboo\openpay\apiStore\handler\v1_0\trade;

use asbamboo\api\apiStore\ApiRequestParamsInterface;
use asbamboo\api\apiStore\ApiResponseParamsInterface;
use asbamboo\openpay\channel\ChannelManagerInterface;
use asbamboo\openpay\apiStore\parameter\v1_0\trade\pay\PayRequest;
use asbamboo\openpay\model\tradePay\TradePayManager;
use asbamboo\openpay\model\tradePayThirdPart\TradePayThirdPartManager;
use asbamboo\api\apiStore\ApiClassInterface;
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
use asbamboo\openpay\apiStore\parameter\v1_0\trade\pay\PayResponse;
use asbamboo\openpay\Constant;
use asbamboo\openpay\model\tradePay\TradePayEntity;
use asbamboo\openpay\model\tradePayThirdPart\TradePayThirdPartEntity;

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
    protected $ChannelManager;

    /**
     *
     * @var FactoryInterface
     */
    protected $Db;

    /**
     *
     * @var RouterInterface
     */
    protected $Router;

    /**
     *
     * @var TradePayManager
     */
    protected $TradePayManager;

    /**
     *
     * @var TradePayThirdPartManager
     */
    protected $TradePayThirdPartManager;

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
     * @see \asbamboo\api\apiStore\ApiClassAbstract::exec()
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
         * @var \asbamboo\openpay\model\tradePayThirdPart\TradePayThirdPartEntity $TradePayThirdPartEntity;
         */
        $TradePayEntity             = $this->TradePayManager->load();
        $this->TradePayManager->insert(
            $Params->getChannel(),
            $Params->getTitle(),
            $Params->getTotalFee(),
            $Params->getOutTradeNo(),
            $Params->getClientIp(),
            $Params->getNotifyUrl(),
            $Params->getReturnUrl()
        );
        $TradePayThirdPartEntity    = $this->TradePayThirdPartManager->load($TradePayEntity->getInTradeNo());
        $this->TradePayThirdPartManager->insert($TradePayEntity, $Params->getThirdPart());

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
            'third_part'    => $TradePayThirdPartEntity->getSendData(),
        ]));

        /**
         * 扫二维码支付时应该有的响应结果
         */
        if($ChannelResponse->getRedirectType() == Response::REDIRECT_TYPE_QRCD && $ChannelResponse->getQrCode()){
            $ApiResponseParams  = $this->makeQrCodeResponse($ChannelResponse);
        }elseif($ChannelResponse->getRedirectType() == Response::REDIRECT_TYPE_PC && $ChannelResponse->getRedirectData()){
            $ApiResponseParams  = $this->makePcResponse($ChannelResponse);
        }else{
            $ApiResponseParams  = new PayResponse([
                'channel'       => $TradePayEntity->getChannel(),
                'in_trade_no'   => $TradePayEntity->getInTradeNo(),
                'title'         => $TradePayEntity->getTitle(),
                'out_trade_no'  => $TradePayEntity->getOutTradeNo(),
                'total_fee'     => $TradePayEntity->getTotalFee(),
                'client_ip'     => $TradePayEntity->getClientIp(),
                'trade_status'  => Constant::getTradePayTradeStatusNames()[$TradePayEntity->getTradeStatus()],
                'payok_ymdhis'  => '',
                'payed_ymdhis'  => '',
                'cancel_ymdhis' => '',
            ]);
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
    protected function makeQrCodeResponse(Response $Response) : ApiResponseRedirectParamsInterface
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
    protected function makePcResponse(Response $Response) :  ApiResponseRedirectParamsInterface
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
