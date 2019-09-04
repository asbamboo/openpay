<?php
namespace asbamboo\openpay\apiStore\handler\v1_0\trade;

use asbamboo\api\apiStore\ApiClassInterface;
use asbamboo\api\apiStore\ApiRequestParamsInterface;
use asbamboo\api\apiStore\ApiResponseParamsInterface;
use asbamboo\openpay\model\tradeRefund\TradeRefundRepository;
use asbamboo\openpay\apiStore\exception\TradeRefundNotFoundInvalidException;
use asbamboo\openpay\Constant;
use asbamboo\openpay\apiStore\parameter\v1_0\trade\refundQuery\RefundQueryResponse;
use asbamboo\openpay\model\tradePay\TradePayRepository;
use asbamboo\openpay\channel\v1_0\trade\refundQueryParameter\Request AS RequestByChannel;
use asbamboo\openpay\channel\v1_0\trade\refundQueryParameter\Response;
use asbamboo\openpay\model\tradeRefund\TradeRefundManager;
use asbamboo\database\FactoryInterface;
use asbamboo\openpay\channel\ChannelManagerInterface;

/**
 * @name 退款查询
 * @desc 退款查询，一笔交易申请退款后，通过这个接口可以查询目前的退款状态（refund_status）。
 * @request asbamboo\openpay\apiStore\parameter\v1_0\trade\refund\RefundRequest
 * @response asbamboo\openpay\apiStore\parameter\v1_0\trade\refund\RefundResponse
 * @author 李春寅<licy2013@aliyun.com>
 * @since 2018年11月5日
 */
class RefundQuery implements ApiClassInterface
{
    /**
     * 
     * @var TradeRefundRepository $TradeRefundRepository
     * @var TradePayRepository $TradePayRepository
     * @var TradeRefundManager $TradeRefundManager
     * @var FactoryInterface $Db
     * @var ChannelManagerInterface $ChannelManager
     */
    private $TradeRefundRepository, $TradePayRepository, $TradeRefundManager, $Db, $ChannelManager;
    
    /**
     * 
     * @param FactoryInterface $Db
     * @param TradeRefundRepository $TradeRefundRepository
     * @param TradeRefundManager $TradeRefundManager
     * @param TradePayRepository $TradePayRepository
     * @param ChannelManagerInterface $ChannelManager
     */
    public function __construct(FactoryInterface $Db,TradeRefundRepository $TradeRefundRepository, TradeRefundManager $TradeRefundManager, TradePayRepository $TradePayRepository, ChannelManagerInterface $ChannelManager)
    {
        $this->Db                       = $Db;
        $this->TradeRefundManager       = $TradeRefundManager;
        $this->TradeRefundRepository    = $TradeRefundRepository;
        $this->TradePayRepository       = $TradePayRepository;
        $this->ChannelManager           = $ChannelManager;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \asbamboo\api\apiStore\ApiClassInterface::exec()
     */
    public function exec(ApiRequestParamsInterface $Params): ?ApiResponseParamsInterface
    {
        $TradeRefundEntity  = null;
        if($Params->getInRefundNo()){
            $TradeRefundEntity = $this->TradeRefundRepository->load($Params->getInRefundNo());
        }else if($Params->getOutRefundNo()){
            $TradeRefundEntity = $this->TradeRefundRepository->loadByOutRefundNo($Params->getOutRefundNo());
        }
        if(empty($TradeRefundEntity)){
            throw new TradeRefundNotFoundInvalidException('没有找到退款记录,请确认 in_refund_no 或 out_refund_no 参数.');
        }
        
        if($TradeRefundEntity->getStatus() == Constant::TRADE_REFUND_STATUS_REQUEST){
            $TradePayEntity     = $this->TradePayRepository->load($TradeRefundEntity->getInTradeNo());
            $channel_name       = $TradePayEntity->getChannel();
            $Channel            = $this->ChannelManager->getChannel(__CLASS__, $channel_name);
            /**
             * 发起第三方渠道请求
             *
             * @var Response $ChannelResponse
             */
            $ChannelResponse    = $Channel->execute(new RequestByChannel([
                'in_refund_no'  => $TradeRefundEntity->getInRefundNo(),
                'third_part'    => $Params->getThirdPart(),
            ]));
            
            if($ChannelResponse->getRefundStatus() == Constant::TRADE_REFUND_STATUS_SUCCESS){
                $this->TradeRefundManager->load($TradeRefundEntity->getInRefundNo());
                $this->TradeRefundManager->updateRefundSuccess(strtotime($ChannelResponse->getRefundPayYmdhis()));
                $this->Db->getManager()->flush();
            }elseif($ChannelResponse->getRefundStatus() == Constant::TRADE_REFUND_STATUS_FAILED){
                $this->TradeRefundManager->load($TradeRefundEntity->getInRefundNo());
                $this->TradeRefundManager->updateRefundFailed();
                $this->Db->getManager()->flush();
            }            
        }
        
        return new RefundQueryResponse([
            'in_trade_no'       => $TradeRefundEntity->getInTradeNo(),
            'out_trade_no'      => $TradeRefundEntity->getOutTradeNo(),
            'in_refund_no'      => $TradeRefundEntity->getInRefundNo(),
            'out_refund_no'     => $TradeRefundEntity->getOutRefundNo(),
            'refund_fee'        => $TradeRefundEntity->getRefundFee(),
            'refund_status'     => Constant::getTradeRefundStatusNames()[$TradeRefundEntity->getStatus()],
            'refund_pay_ymdhis' => $TradeRefundEntity->getPayTime() ? date('Y-m-d H:i:s', $TradeRefundEntity->getPayTime()) : '',
        ]);
    }    
}
