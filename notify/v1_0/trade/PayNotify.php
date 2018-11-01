<?php
namespace asbamboo\openpay\notify\v1_0\trade;

use asbamboo\openpay\channel\ChannelManagerInterface;
use asbamboo\openpay\apiStore\handler\v1_0\trade\Pay;
use asbamboo\http\ServerRequestInterface;
use asbamboo\http\ResponseInterface;
use asbamboo\http\Response;
use asbamboo\http\Stream;
use asbamboo\database\FactoryInterface;
use asbamboo\openpay\model\tradePay\TradePayRespository;
use asbamboo\openpay\Constant;
use asbamboo\openpay\model\tradePay\TradePayManager;

/**
 * 交易支付接口 trade.pay notify处理
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月31日
 */
class PayNotify
{
    /**
     *
     * @var ChannelManagerInterface
     */
    private $ChannelManagr;

    /**
     *
     * @var ServerRequestInterface
     */
    private $Request;

    /**
     *
     * @var FactoryInterface
     */
    private $Db;

    /**
     *
     * @var TradePayRespository
     */
    private $TradePayRespository;

    /**
     *
     * @var TradePayManager
     */
    private $TradePayManager;

    /**
     *
     * @param ChannelManagerInterface $ChannelManager
     */
    public function __construct(
        ChannelManagerInterface $ChannelManager,
        ServerRequestInterface $Request,
        TradePayManager $TradePayManager,
        TradePayRespository $TradePayRespository,
        FactoryInterface $Db
    ){
        $this->ChannelManager           = $ChannelManager;
        $this->Request                  = $Request;
        $this->TradePayRespository      = $TradePayRespository;
        $this->TradePayManager          = $TradePayManager;
        $this->Db                       = $Db;
    }

    /**
     * 应该让这个方法透过url访问
     *
     * @param string $channel_name
     */
    public function exec(string $channel) : ResponseInterface
    {
        /**
         * @var ResponseInterface $Response
         */
        $Response   = new Response(new Stream('php://temp', 'w+b'));

        /**
         *
         * @var \asbamboo\openpay\channel\v1_0\trade\PayInterface $Channel
         * @var \asbamboo\openpay\channel\v1_0\trade\payParameter\NotifyResult $NotifyResult
         */
        $Channel        = $this->ChannelManager->getChannel(Pay::class, $channel);
        $NotifyResult   = $Channel->notify($this->Request);

        try{
            $in_trade_no    = $NotifyResult->getInTradeNo();
            $third_trade_no = $NotifyResult->getThirdTradeNo();
            $TradePayEntity = $this->TradePayRespository->load($in_trade_no);
            if($TradePayEntity->getTradeStatus() != $NotifyResult->getTradeStatus()){
                //支付成功（可退款）
                if($NotifyResult->getTradeStatus() == Constant::TRADE_PAY_TRADE_STATUS_PAYOK){
                    $this->TradePayManager->updateTradeStatusToPayok($TradePayEntity, $third_trade_no);
                //支付成功（不可退款）
                }else if($NotifyResult->getTradeStatus() == Constant::TRADE_PAY_TRADE_STATUS_PAYED){

                //支付取消（不可退款）
                }else if($NotifyResult->getTradeStatus() == Constant::TRADE_PAY_TRADE_STATUS_CANCLE){

                }
            }
            $Response->getBody()->write($NotifyResult->getResponseSuccess());
        }catch(\asbamboo\openpay\exception\OpenpayException $e){
            $Response->getBody()->write($NotifyResult->getResponseFailed());
        }finally{
            return $Response;
        }
    }
}