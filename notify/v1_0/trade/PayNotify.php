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
use asbamboo\http\Client;
use asbamboo\http\Request;
use asbamboo\http\Uri;
use asbamboo\http\Constant AS HttpConstant;
use asbamboo\openpay\channel\v1_0\trade\payParameter\NotifyResult;
use asbamboo\openpay\model\tradePay\TradePayEntity;

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
    protected $ChannelManagr;

    /**
     *
     * @var ServerRequestInterface
     */
    protected $Request;

    /**
     *
     * @var FactoryInterface
     */
    protected $Db;

    /**
     *
     * @var TradePayRespository
     */
    protected $TradePayRespository;

    /**
     *
     * @var TradePayManager
     */
    protected $TradePayManager;

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

        try{
            $NotifyResult   = $this->getNotifyResult($channel);
            $TradePayEntity = $this->DbFlush($NotifyResult);

            /*
             * 向对接聚合平台的应用推送消息
             * 发送的body 参考 asbamboo\openpay\apiStore\parameter\v1_0\trade\pay\PayResponse
             * 这套代码并没有建立重发机制(如果需要重发机制的话请通过Client中相关事件,自行实现.)
             *  - 但是如果curl请求未成功,第三方平台有重发机制的时候. 由于这个方法抛出了curl client exception,等到第三方重新发送notify过来的时候,这个聚合平台又会再次推送notify.
             */
            if($TradePayEntity->getNotifyUrl()){
                $Body       = new Stream('php://temp', 'w+b');
                $Client     = new Client();
                $Uri        = new Uri($TradePayEntity->getNotifyUrl());
                $Request    = new Request($Uri, $Body, HttpConstant::METHOD_POST);
                $Body->write(http_build_query([
                    'channel'       => $TradePayEntity->getChannel(),
                    'in_trade_no'   => $TradePayEntity->getInTradeNo(),
                    'title'         => $TradePayEntity->getTitle(),
                    'out_trade_no'  => $TradePayEntity->getOutTradeNo(),
                    'total_fee'     => $TradePayEntity->getTotalFee(),
                    'client_ip'     => $TradePayEntity->getClientIp(),
                    'trade_status'  => Constant::getTradePayTradeStatusNames()[$TradePayEntity->getTradeStatus()],
                    'payok_ymdhis'  => $TradePayEntity->getPayokTime() ? date('Y-m-d H:i:s', $TradePayEntity->getPayokTime()) : '',
                    'payed_ymdhis'  => $TradePayEntity->getPayedTime() ? date('Y-m-d H:i:s', $TradePayEntity->getPayedTime()) : '',
                    'cancel_ymdhis' => $TradePayEntity->getCancelTime() ? date('Y-m-d H:i:s', $TradePayEntity->getCancelTime()) : '',
                ]));
                $Client->send($Request);
            }

            $Response->getBody()->write($NotifyResult->getResponseSuccess());
            $Response->getBody()->rewind();
        }catch(\asbamboo\openpay\exception\OpenpayException $e){
            $Response->getBody()->write($NotifyResult->getResponseFailed());
            $Response->getBody()->rewind();
        }finally{
            return $Response;
        }
    }

    /**
     *
     * @param string $channel_name
     * @return NotifyResult
     */
    protected function getNotifyResult(string $channel_name) : NotifyResult
    {
        /**
         *
         * @var \asbamboo\openpay\channel\v1_0\trade\PayInterface $Channel
         */
        $Channel    = $this->ChannelManager->getChannel(Pay::class, $channel_name);
        return $Channel->notify($this->Request);
    }

    /**
     * 更新数据状态
     *
     * @param NotifyResult $NotifyResult
     * @return TradePayEntity
     */
    protected function dbFlush(NotifyResult $NotifyResult) : TradePayEntity
    {
        $in_trade_no    = $NotifyResult->getInTradeNo();
        $third_trade_no = $NotifyResult->getThirdTradeNo();
        $TradePayEntity = $this->TradePayRespository->load($in_trade_no);

        /*
         * 修改数据状态
         */
        if($TradePayEntity->getTradeStatus() != $NotifyResult->getTradeStatus()){
            //支付成功（可退款）
            if($NotifyResult->getTradeStatus() == Constant::TRADE_PAY_TRADE_STATUS_PAYOK){
                $this->TradePayManager->load($TradePayEntity)->updateTradeStatusToPayok($third_trade_no);
                //支付成功（不可退款）
            }else if($NotifyResult->getTradeStatus() == Constant::TRADE_PAY_TRADE_STATUS_PAYED){
                $this->TradePayManager->load($TradePayEntity)->updateTradeStatusToPayed($third_trade_no);
                //支付取消（不可退款）
            }else if($NotifyResult->getTradeStatus() == Constant::TRADE_PAY_TRADE_STATUS_CANCEL){
                $this->TradePayManager->load($TradePayEntity)->updateTradeStatusToCancel($third_trade_no);
            }
        }
        $this->Db->getManager()->flush();
        return $TradePayEntity;
    }
}