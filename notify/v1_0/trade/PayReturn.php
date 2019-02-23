<?php
namespace asbamboo\openpay\notify\v1_0\trade;

use asbamboo\http\ResponseInterface;
use asbamboo\api\apiStore\ApiResponseRedirectParams;
use asbamboo\openpay\Constant;
use asbamboo\openpay\channel\v1_0\trade\payParameter\NotifyResult;
use asbamboo\openpay\model\tradePay\TradePayEntity;
use asbamboo\event\EventScheduler;
use asbamboo\openpay\Event;
use asbamboo\http\JsonResponse;

/**
 * 交易支付接口 trade.pay notify处理
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月31日
 */
class PayReturn extends PayNotify
{
    /**
     * 应该让这个方法透过url访问
     *
     * @param string $channel_name
     */
    public function exec(string $channel) : ResponseInterface
    {
        try{
            /**
             * 事件触发 可以通过监听这个事件处理一些事情，比如:写入日志,校验请求参数等
             * 在api模块内，event-listener定义了几个监听器，如果你有需要的话，请使用EventScheduler::instance()->bind 方法绑定事件监听器
             */
            EventScheduler::instance()->trigger(Event::PAY_RETURN_PRE_EXEC, [$this, $channel]);

            $NotifyResult           = $this->getNotifyResult($channel);
            $TradePayEntity         = $this->dbFlush($NotifyResult);
            $trade_pay_info         =   [
                'channel'           => $TradePayEntity->getChannel(),
                'in_trade_no'       => $TradePayEntity->getInTradeNo(),
                'title'             => $TradePayEntity->getTitle(),
                'out_trade_no'      => $TradePayEntity->getOutTradeNo(),
                'total_fee'         => $TradePayEntity->getTotalFee(),
                'client_ip'         => $TradePayEntity->getClientIp(),
                'trade_status'      => Constant::getTradePayTradeStatusNames()[$TradePayEntity->getTradeStatus()],
                'payok_ymdhis'      => $TradePayEntity->getPayokTime() ? date('Y-m-d H:i:s', $TradePayEntity->getPayokTime()) : '',
                'payed_ymdhis'      => $TradePayEntity->getPayedTime() ? date('Y-m-d H:i:s', $TradePayEntity->getPayedTime()) : '',
                'cancel_ymdhis'     => $TradePayEntity->getCancelTime() ? date('Y-m-d H:i:s', $TradePayEntity->getCancelTime()) : '',
            ];

            /*
             * 向对接聚合平台的应用推送消息
             * 发送的body 参考 asbamboo\openpay\apiStore\parameter\v1_0\trade\pay\PayResponse
             * 这套代码并没有建立重发机制(如果需要重发机制的话请通过Client中相关事件,自行实现.)
             *  - 但是如果curl请求未成功,第三方平台有重发机制的时候. 由于这个方法抛出了curl client exception,等到第三方重新发送notify过来的时候,这个聚合平台又会再次推送notify.
             */
            if($TradePayEntity->getReturnUrl()){
                $ApiResponseRedirectParams  = new class ($TradePayEntity->getReturnUrl(), $trade_pay_info) extends ApiResponseRedirectParams{
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

                    protected function getRedirectType() : string
                    {
                        return self::REDIRECT_TYPE_GET_REQUEST;
                    }
                };
                $Response   = $ApiResponseRedirectParams->makeRedirectResponse();
            }else{
                $Response   = new JsonResponse(['status' => 'success', 'data' => $trade_pay_info]);
            }
            /**
             * 事件触发 可以通过监听这个事件处理一些事情，比如:写入日志,校验请求参数等
             * 在api模块内，event-listener定义了几个监听器，如果你有需要的话，请使用EventScheduler::instance()->bind 方法绑定事件监听器
             */
            EventScheduler::instance()->trigger(Event::PAY_RETURN_AFTER_EXEC, [$this, $NotifyResult, $channel]);
        }catch(\asbamboo\openpay\exception\OpenpayException $e){
            $Response   = new JsonResponse(['status' => 'failed', 'error' => $e->getMessage()]);
        }

        return $Response;
    }

    /**
     *
     * @param string $channel_name
     * @return NotifyResult
     */
    public function getNotifyResult(string $channel_name) : NotifyResult
    {
        $Channel    = $this->getChannel($channel_name);
        return $Channel->return($this->Request);
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
        if(!in_array($TradePayEntity->getTradeStatus(), [Constant::TRADE_PAY_TRADE_STATUS_PAYOK, Constant::TRADE_PAY_TRADE_STATUS_PAYED])){
            //支付成功（可退款）
            $TradePayEntity = $this->TradePayManager->load($TradePayEntity->getInTradeNo());
            $this->TradePayManager->updateTradeStatusToPayok($third_trade_no);
        }
        $this->Db->getManager()->flush();
        return $TradePayEntity;
    }
}