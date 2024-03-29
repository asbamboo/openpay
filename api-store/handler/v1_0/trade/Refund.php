<?php
namespace asbamboo\openpay\apiStore\handler\v1_0\trade;

use asbamboo\api\apiStore\ApiClassInterface;
use asbamboo\api\apiStore\ApiRequestParamsInterface;
use asbamboo\api\apiStore\ApiResponseParamsInterface;
use asbamboo\openpay\apiStore\parameter\v1_0\trade\refund\RefundRequest;
use asbamboo\openpay\apiStore\parameter\v1_0\trade\refund\RefundResponse;
use asbamboo\openpay\apiStore\exception\TradeRefundNotFoundInvalidException;
use asbamboo\openpay\channel\ChannelManagerInterface;
use asbamboo\database\FactoryInterface;
use asbamboo\openpay\model\tradePay\TradePayRepository;
use asbamboo\openpay\model\tradeRefund\TradeRefundRepository;
use asbamboo\openpay\model\tradeRefund\TradeRefundManager;
use asbamboo\openpay\Constant;
use asbamboo\openpay\channel\v1_0\trade\RefundParameter\Request AS RequestByChannel;
use asbamboo\openpay\model\tradeRefundClob\TradeRefundClobRepository;
use asbamboo\openpay\model\tradeRefundClob\TradeRefundClobManager;
use asbamboo\openpay\apiStore\exception\TradeRefundOutRefundNoInvalidException;
use asbamboo\openpay\channel\v1_0\trade\RefundParameter\Response AS RefundParameterResponse;
// use asbamboo\http\Stream;
// use asbamboo\http\Client;
// use asbamboo\http\Uri;
// use asbamboo\http\Request;
// use asbamboo\http\Constant AS HttpConstant;
use asbamboo\router\RouterInterface;
use asbamboo\openpay\apiStore\exception\TradeRefundStatusRequestedException;


/**
 * @name 发起退款
 * @desc 发起一笔交易的退款,一个交易可以有多次退款,退款总的金额不能超过交易金额，发起退款成功后，退款的状态需要根据refund_status判断
 * @request asbamboo\openpay\apiStore\parameter\v1_0\trade\refund\RefundRequest
 * @response asbamboo\openpay\apiStore\parameter\v1_0\trade\refund\RefundResponse
 * @author 李春寅<licy2013@aliyun.com>
 * @since 2018年11月5日
 */
class Refund implements ApiClassInterface
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
    protected $Db;

    /**
     *
     * @var TradePayRepository
     */
    private $TradePayRepository;

    /**
     *
     * @var TradeRefundRepository
     */
    private $TradeRefundRepository;

    /**
     *
     * @var TradeRefundManager
     */
    private $TradeRefundManager;

    /**
     *
     * @var TradeRefundClobRepository
     */
    private $TradeRefundClobRepository;

    /**
     *
     * @var TradeRefundClobManager
     */
    private $TradeRefundClobManager;
    
    /**
     * 
     * @var RouterInterface
     */
    private $Router;

    /**
     *
     * @param ChannelManagerInterface $ChannelManager
     * @param FactoryInterface $Db
     * @param TradePayRepository $TradePayRepository
     * @param TradeRefundRepository $TradeRefundRepository
     * @param TradeRefundManager $TradeRefundManager
     * @param TradeRefundClobRepository $TradeRefundClobRepository
     * @param TradeRefundClobManager $TradeRefundClobManager
     */
    public function __construct(
        ChannelManagerInterface $ChannelManager,
        FactoryInterface $Db,
        TradePayRepository $TradePayRepository,
        TradeRefundRepository $TradeRefundRepository,
        TradeRefundManager $TradeRefundManager,
        TradeRefundClobRepository $TradeRefundClobRepository,
        TradeRefundClobManager $TradeRefundClobManager,
        RouterInterface $Router
    ){
        $this->ChannelManager               = $ChannelManager;
        $this->Db                           = $Db;
        $this->TradePayRepository           = $TradePayRepository;
        $this->TradeRefundRepository        = $TradeRefundRepository;
        $this->TradeRefundManager           = $TradeRefundManager;
        $this->TradeRefundClobRepository    = $TradeRefundClobRepository;
        $this->TradeRefundClobManager       = $TradeRefundClobManager;
        $this->Router                       = $Router;
    }

    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\api\apiStore\ApiClassInterface::exec()
     * @var RefundRequest $Params
     * @return RefundResponse
     */
    public function exec(ApiRequestParamsInterface $Params) : ?ApiResponseParamsInterface
    {
        $TradePayEntity = null;
        if(strlen((string)$Params->getInTradeNo()) > 0){
            $TradePayEntity = $this->TradePayRepository->load($Params->getInTradeNo());
        }elseif(strlen((string)$Params->getOutTradeNo()) > 0){
            $TradePayEntity = $this->TradePayRepository->loadByOutTradeNo($Params->getOutTradeNo());
        }
        if(empty($TradePayEntity)){
            throw new TradeRefundNotFoundInvalidException('没有找到交易记录,请确认 in_trade_no 或 out_trade_no 参数.');
        }
        if(trim($Params->getOutRefundNo()) == ''){
            throw new TradeRefundOutRefundNoInvalidException("缺少参数：out_refund_no");
        }
        $TradeRefundEntity  = $this->TradeRefundRepository->loadByOutRefundNo($Params->getOutRefundNo());
        if(is_null($TradeRefundEntity)){
            $TradeRefundEntity  = $this->TradeRefundManager->load();
            $this->TradeRefundManager->insert($TradePayEntity, $Params->getOutRefundNo(), $Params->getRefundFee(), $Params->getNotifyUrl());
        }else{
            $TradeRefundEntity  = $this->TradeRefundManager->load($TradeRefundEntity->getInRefundNo());
            
            if($TradeRefundEntity->getRefundFee() != $Params->getRefundFee()){
                throw new TradeRefundOutRefundNoInvalidException('一个out_refund_no只能对应一笔退款,当请求失败需要重新请求时,不应该改变退款的金额。');
            }
            
            if($TradeRefundEntity->getInTradeNo() != $TradePayEntity->getInTradeNo()){
                throw new TradeRefundOutRefundNoInvalidException('一个out_refund_no只能对应一笔退款。');
            }
            
            if($TradeRefundEntity->getStatus() == Constant::TRADE_REFUND_STATUS_REQUEST){
                throw new TradeRefundStatusRequestedException('退款单已经请求，请使用退款查询接口查询退款状态。');
            }
        }

        /**
         * 发起第三方渠道请求
         * 如果退款已经成功的话，不要再向第三方渠道发请求
         * @var \asbamboo\openpay\channel\v1_0\trade\RefundInterface $Channel
         * @var \asbamboo\openpay\channel\v1_0\trade\RefundParameter\Response $ChannelResponse
         */
       if($TradeRefundEntity->getStatus() != Constant::TRADE_REFUND_STATUS_SUCCESS){
           $TradeRefundClobEntity = $this->TradeRefundClobRepository->findOneByInRefundNo($TradeRefundEntity->getInRefundNo());
           if(empty($TradeRefundClobEntity)){
               $TradeRefundClobEntity = $this->TradeRefundClobManager->load($TradeRefundEntity->getInRefundNo());
               $this->TradeRefundClobManager->insert($TradeRefundEntity, $Params->getThirdPart());
            }
            $this->TradeRefundManager->updateRequest();
            $this->Db->getManager()->flush();

            $channel_name       = $TradePayEntity->getChannel();
            $Channel            = $this->ChannelManager->getChannel(__CLASS__, $channel_name);
            $ChannelResponse    = $Channel->execute(new RequestByChannel([
                'channel'       => $channel_name,
                'in_trade_no'   => $TradeRefundEntity->getInTradeNo(),
                'in_refund_no'  => $TradeRefundEntity->getInRefundNo(),
                'refund_fee'    => $TradeRefundEntity->getRefundFee(),
                'trade_pay_fee' => $TradePayEntity->getTotalFee(),
                'third_part'    => $TradeRefundClobEntity->getThirdPart(),
                'notify_url'    => $this->makeNotifyUrl($channel_name),
            ]));
            if($ChannelResponse->getIsSuccess() == true){
                if($ChannelResponse->getRefundStatus() == RefundParameterResponse::REFUND_STATUS_SUCCESS){
                    $this->TradeRefundManager->updateRefundSuccess(strtotime($ChannelResponse->getPayYmdhis()));
                }elseif($ChannelResponse->getRefundStatus() == RefundParameterResponse::REFUND_STATUS_FAILED){
                    $this->TradeRefundManager->updateRefundFailed();
                }
            }else{
                $this->TradeRefundManager->updateRefundFailed();
            }

            $this->Db->getManager()->flush();
        }
        
//        这里不发送notify。如果需要发notify的话通过监听event::api.after.exec 事件去实现。
//         if($TradeRefundEntity->getStatus() != Constant::TRADE_REFUND_STATUS_REQUEST && !empty($TradeRefundEntity->getNotifyUrl())){
//             $Body       = new Stream('php://temp', 'w+b');
//             $Client     = new Client();
//             $Uri        = new Uri($TradeRefundEntity->getNotifyUrl());
//             $Request    = new Request($Uri, $Body, HttpConstant::METHOD_POST);
//             $Body->write(http_build_query([
//                 'in_refund_no'      => $TradeRefundEntity->getInRefundNo(),
//                 'in_trade_no'       => $TradeRefundEntity->getInTradeNo(),
//                 'out_refund_no'     => $TradeRefundEntity->getOutRefundNo(),
//                 'out_trade_no'      => $TradeRefundEntity->getOutTradeNo(),
//                 'refund_fee'        => $TradeRefundEntity->getRefundFee(),
//                 'refund_pay_ymdhis' => $TradeRefundEntity->getPayTime() > 0 ? date('Y-m-d H:i:s', $TradeRefundEntity->getPayTime()) : '',
//                 'refund_status'     => Constant::getTradeRefundStatusNames()[$TradeRefundEntity->getStatus()],
//             ]));
//             $Client->send($Request);            
//         }

        return new RefundResponse([
            'in_trade_no'       => $TradeRefundEntity->getInTradeNo(),
            'out_trade_no'      => $TradeRefundEntity->getOutTradeNo(),
            'in_refund_no'      => $TradeRefundEntity->getInRefundNo(),
            'out_refund_no'     => $TradeRefundEntity->getOutRefundNo(),
            'refund_fee'        => $TradeRefundEntity->getRefundFee(),
            'refund_status'     => Constant::getTradeRefundStatusNames()[$TradeRefundEntity->getStatus()],
            'refund_pay_ymdhis' => $TradeRefundEntity->getPayTime() ? date('Y-m-d H:i:s', $TradeRefundEntity->getPayTime()) : '',
        ]);
    }
    
    /**
     * 生成参数notify url
     * 
     * @param string $channel_name
     * @return string
     */
    protected function makeNotifyUrl(string $channel_name) : string
    {
        return $this->Router->generateAbsoluteUrl('refund_notify', ['channel' => $channel_name]);
    }
}