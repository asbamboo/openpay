<?php
namespace asbamboo\openpay\notify\v1_0\trade;

use asbamboo\openpay\channel\ChannelManagerInterface;
use asbamboo\openpay\apiStore\handler\v1_0\trade\Refund;
use asbamboo\http\ServerRequestInterface;
use asbamboo\http\ResponseInterface;
use asbamboo\http\Response;
use asbamboo\http\Stream;
use asbamboo\database\FactoryInterface;
use asbamboo\openpay\Constant;
use asbamboo\http\Client;
use asbamboo\http\Request;
use asbamboo\http\Uri;
use asbamboo\http\Constant AS HttpConstant;
use asbamboo\event\EventScheduler;
use asbamboo\openpay\Event;
use asbamboo\openpay\channel\v1_0\trade\RefundInterface;
use asbamboo\openpay\channel\v1_0\trade\refundParameter\NotifyResult;
use asbamboo\openpay\model\tradeRefund\TradeRefundEntity;
use asbamboo\openpay\model\tradeRefund\TradeRefundManager;

/**
 * 交易支付接口 trade.pay notify处理
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月31日
 */
class RefundNotify
{
    /**
     * @var RefundInterface
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
     * @var TradeRefundManager
     */
    protected $TradeRefundManager;
    
    /**
     *
     * @param ChannelManagerInterface $ChannelManager
     */
    public function __construct(
        ChannelManagerInterface $ChannelManager,
        ServerRequestInterface $Request,
        TradeRefundManager $TradeRefundManager,
        FactoryInterface $Db
    ){
        $this->ChannelManager           = $ChannelManager;
        $this->Request                  = $Request;
        $this->TradeRefundManager       = $TradeRefundManager;
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
            EventScheduler::instance()->trigger(Event::REFUND_NOTIFY_PRE_EXEC, [$this, $channel]);

            $NotifyResult       = $this->getNotifyResult($channel);
            $TradeRefundEntity  = $this->DbFlush($NotifyResult);

            /*
             * 向对接聚合平台的应用推送消息
             * 发送的body 参考 asbamboo\openpay\apiStore\parameter\v1_0\trade\pay\PayResponse
             * 这套代码并没有建立重发机制(如果需要重发机制的话请通过Client中相关事件,自行实现.)
             *  - 但是如果curl请求未成功,第三方平台有重发机制的时候. 由于这个方法抛出了curl client exception,等到第三方重新发送notify过来的时候,这个聚合平台又会再次推送notify.
             */
            if($TradeRefundEntity->getNotifyUrl()){
                $Body       = new Stream('php://temp', 'w+b');
                $Client     = new Client();
                $Uri        = new Uri($TradeRefundEntity->getNotifyUrl());
                $Request    = new Request($Uri, $Body, HttpConstant::METHOD_POST);
                $Body->write(http_build_query([
                    'in_refund_no'      => $TradeRefundEntity->getInRefundNo(),
                    'in_trade_no'       => $TradeRefundEntity->getInTradeNo(),
                    'out_refund_no'     => $TradeRefundEntity->getOutRefundNo(),
                    'out_trade_no'      => $TradeRefundEntity->getOutTradeNo(),
                    'refund_fee'        => $TradeRefundEntity->getRefundFee(),
                    'refund_pay_ymdhis' => $TradeRefundEntity->getPayTime() > 0 ? date('Y-m-d H:i:s', $TradeRefundEntity->getPayTime()) : '',
                    'refund_status'     => Constant::getTradeRefundStatusNames()[$TradeRefundEntity->getStatus()],
                ]));
                $Client->send($Request);
            }

            $Response->getBody()->write($NotifyResult->getResponseSuccess());
            $Response->getBody()->rewind();

            /**
             * 事件触发 可以通过监听这个事件处理一些事情，比如:写入日志,校验请求参数等
             * 在api模块内，event-listener定义了几个监听器，如果你有需要的话，请使用EventScheduler::instance()->bind 方法绑定事件监听器
             */
            EventScheduler::instance()->trigger(Event::REFUND_NOTIFY_AFTER_EXEC, [$this, $NotifyResult, $channel]);
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
     * @return RefundInterface
     */
    public function getChannel(string $channel_name) : RefundInterface
    {
        if(empty($this->Channel)){
            $this->Channel  = $this->ChannelManager->getChannel(Refund::class, $channel_name);
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
     * @return TradeRefundEntity
     */
    protected function dbFlush(NotifyResult $NotifyResult) : TradeRefundEntity
    {
        $in_refund_no       = $NotifyResult->getInRefundNo();
        $TradeRefundEntity  = $this->TradeRefundManager->load($in_refund_no);
        
        /*
         * 修改数据状态
         */
        if($TradeRefundEntity->getStatus() != $NotifyResult->getRefundStatus()){
            //退款成功
            if($NotifyResult->getRefundStatus() == Constant::TRADE_REFUND_STATUS_SUCCESS){
                $this->TradeRefundManager->updateRefundSuccess($NotifyResult->getRefundPayYmdhis());
            //退款失败
            }elseif($NotifyResult->getRefundStatus() == Constant::TRADE_REFUND_STATUS_FAILED){
                $this->TradeRefundManager->updateRefundFailed();
            }
        }
        $this->Db->getManager()->flush();
        return $TradeRefundEntity;
    }
}