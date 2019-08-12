<?php
namespace asbamboo\openpay\notify\v1_0\trade;

use asbamboo\openpay\channel\ChannelManagerInterface;
use asbamboo\openpay\apiStore\handler\v1_0\trade\Pay;
use asbamboo\http\ServerRequestInterface;
use asbamboo\http\ResponseInterface;
use asbamboo\http\Response;
use asbamboo\http\Stream;
use asbamboo\database\FactoryInterface;
use asbamboo\openpay\model\tradePay\TradePayRepository;
use asbamboo\openpay\Constant;
use asbamboo\openpay\model\tradePay\TradePayManager;
use asbamboo\http\Client;
use asbamboo\http\Request;
use asbamboo\http\Uri;
use asbamboo\http\Constant AS HttpConstant;
use asbamboo\openpay\channel\v1_0\trade\payParameter\NotifyResult;
use asbamboo\openpay\model\tradePay\TradePayEntity;
use asbamboo\event\EventScheduler;
use asbamboo\openpay\Event;
use asbamboo\openpay\channel\v1_0\trade\PayInterface;

/**
 * 交易支付接口 trade.pay notify处理
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月31日
 */
class PayNotify
{
    /**
     * @var PayInterface
     */
    protected $Channel;

    /**
     *
     * @var ChannelManagerInterface
     */
    protected $ChannelManager;

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
     * @var TradePayRepository
     */
    protected $TradePayRepository;

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
        TradePayRepository $TradePayRepository,
        FactoryInterface $Db
    ){
        $this->ChannelManager           = $ChannelManager;
        $this->Request                  = $Request;
        $this->TradePayRepository      = $TradePayRepository;
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
            /**
             * 事件触发 可以通过监听这个事件处理一些事情，比如:写入日志,校验请求参数等
             * 在api模块内，event-listener定义了几个监听器，如果你有需要的话，请使用EventScheduler::instance()->bind 方法绑定事件监听器
             */
            EventScheduler::instance()->trigger(Event::PAY_NOTIFY_PRE_EXEC, [$this, $channel]);

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
                    'channel'           => $TradePayEntity->getChannel(),
                    'in_trade_no'       => $TradePayEntity->getInTradeNo(),
                    'title'             => $TradePayEntity->getTitle(),
                    'out_trade_no'      => $TradePayEntity->getOutTradeNo(),
                    'third_trade_no'    => $TradePayEntity->getThirdTradeNo(),
                    'total_fee'         => $TradePayEntity->getTotalFee(),
                    'client_ip'         => $TradePayEntity->getClientIp(),
                    'trade_status'      => Constant::getTradePayTradeStatusNames()[$TradePayEntity->getTradeStatus()],
                    'payok_ymdhis'      => $TradePayEntity->getPayokTime() ? date('Y-m-d H:i:s', $TradePayEntity->getPayokTime()) : '',
                    'payed_ymdhis'      => $TradePayEntity->getPayedTime() ? date('Y-m-d H:i:s', $TradePayEntity->getPayedTime()) : '',
                    'cancel_ymdhis'     => $TradePayEntity->getCancelTime() ? date('Y-m-d H:i:s', $TradePayEntity->getCancelTime()) : '',
                ]));
                $Client->send($Request);
            }

            $Response->getBody()->write($NotifyResult->getResponseSuccess());
            $Response->getBody()->rewind();

            /**
             * 事件触发 可以通过监听这个事件处理一些事情，比如:写入日志,校验请求参数等
             * 在api模块内，event-listener定义了几个监听器，如果你有需要的话，请使用EventScheduler::instance()->bind 方法绑定事件监听器
             */
            EventScheduler::instance()->trigger(Event::PAY_NOTIFY_AFTER_EXEC, [$this, $NotifyResult, $channel]);
        }catch(\asbamboo\openpay\exception\OpenpayException $e){
            $Response->getBody()->write($NotifyResult->getResponseFailed());
            $Response->getBody()->rewind();
        }

        return $Response;
    }

    /**
     * 返回渠道支付操作对象
     *
     * @param string $channel_name
     * @return PayInterface
     */
    public function getChannel(string $channel_name) : PayInterface
    {
        if(empty($this->Channel)){
            $this->Channel  = $this->ChannelManager->getChannel(Pay::class, $channel_name);
        }
        return $this->Channel;
    }

    /**
     *
     * @param string $channel_name
     * @return NotifyResult
     */
    public function getNotifyResult(string $channel_name) : NotifyResult
    {
        $Channel    = $this->getChannel($channel_name);
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
        $TradePayEntity = $this->TradePayRepository->load($in_trade_no);

        /*
         * 修改数据状态
         */
        if($TradePayEntity->getTradeStatus() != $NotifyResult->getTradeStatus()){
            //支付成功（可退款）
            if($NotifyResult->getTradeStatus() == Constant::TRADE_PAY_TRADE_STATUS_PAYOK){
                $TradePayEntity = $this->TradePayManager->load($TradePayEntity->getInTradeNo());
                $this->TradePayManager->updateTradeStatusToPayok($third_trade_no);
                //支付成功（不可退款）
            }else if($NotifyResult->getTradeStatus() == Constant::TRADE_PAY_TRADE_STATUS_PAYED){
                $TradePayEntity = $this->TradePayManager->load($TradePayEntity->getInTradeNo());
                $this->TradePayManager->updateTradeStatusToPayed($third_trade_no);
                //支付取消（不可退款）
            }else if($NotifyResult->getTradeStatus() == Constant::TRADE_PAY_TRADE_STATUS_CANCEL){
                $TradePayEntity = $this->TradePayManager->load($TradePayEntity->getInTradeNo());
                $this->TradePayManager->updateTradeStatusToCancel($third_trade_no);
            }
        }
        $this->Db->getManager()->flush();
        return $TradePayEntity;
    }
}