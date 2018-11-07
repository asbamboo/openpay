<?php
namespace asbamboo\openpay\apiStore\handler\v1_0\trade;

use asbamboo\api\apiStore\ApiClassInterface;
use asbamboo\api\apiStore\ApiRequestParamsInterface;
use asbamboo\api\apiStore\ApiResponseParamsInterface;
use asbamboo\openpay\apiStore\parameter\v1_0\trade\refund\RefundRequest;
use asbamboo\openpay\apiStore\parameter\v1_0\trade\refund\RefundResponse;
use asbamboo\openpay\apiStore\exception\TradeRefundNotFoundInvalidException;
use asbamboo\openpay\apiStore\exception\TradeRefundRefundFeeInvalidException;
use asbamboo\openpay\channel\ChannelManagerInterface;
use asbamboo\database\FactoryInterface;
use asbamboo\openpay\model\tradePay\TradePayRespository;
use asbamboo\openpay\model\tradePay\TradePayManager;
use asbamboo\openpay\model\tradeRefund\TradeRefundRespository;
use asbamboo\openpay\model\tradeRefund\TradeRefundManager;
use asbamboo\openpay\Constant;
use asbamboo\openpay\channel\v1_0\trade\RefundParameter\Request AS RequestByChannel;
use asbamboo\openpay\model\tradeRefundThirdPart\TradeRefundThirdPartRespository;
use asbamboo\openpay\model\tradeRefundThirdPart\TradeRefundThirdPartManager;

/**
 * @name 发起退款
 * @desc 发起一笔交易的退款,一个交易可以有多次退款,退款总的金额不能超过交易金额
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
     * @var TradeRefundRespository
     */
    private $TradeRefundRespository;
    
    /**
     * 
     * @var TradeRefundManager
     */
    private $TradeRefundManager;
    
    /**
     * 
     * @var TradeRefundThirdPartRespository
     */
    private $TradeRefundThirdPartRespository;
    
    /**
     * 
     * @var TradeRefundThirdPartManager
     */
    private $TradeRefundThirdPartManager;
    
    /**
     * 
     * @param ChannelManagerInterface $ChannelManager
     * @param FactoryInterface $Db
     * @param TradePayRespository $TradePayRespository
     * @param TradePayManager $TradePayManager
     * @param TradeRefundRespository $TradeRefundRespository
     * @param TradeRefundManager $TradeRefundManager
     * @param TradeRefundThirdPartRespository $TradeRefundThirdPartRespository
     * @param TradeRefundThirdPartManager $TradeRefundThirdPartManager
     */
    public function __construct(
        ChannelManagerInterface $ChannelManager, 
        FactoryInterface $Db, 
        TradePayRespository $TradePayRespository, 
        TradePayManager $TradePayManager, 
        TradeRefundRespository $TradeRefundRespository,
        TradeRefundManager $TradeRefundManager,
        TradeRefundThirdPartRespository $TradeRefundThirdPartRespository,
        TradeRefundThirdPartManager $TradeRefundThirdPartManager
    ){
        $this->ChannelManager                       = $ChannelManager;
        $this->Db                                   = $Db;
        $this->TradePayRespository                  = $TradePayRespository;
        $this->TradePayManager                      = $TradePayManager;
        $this->TradeRefundRespository               = $TradeRefundRespository;
        $this->TradeRefundManager                   = $TradeRefundManager;
        $this->TradeRefundThirdPartRespository      = $TradeRefundThirdPartRespository;
        $this->TradeRefundThirdPartManager          = $TradeRefundThirdPartManager;
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
            $TradePayEntity = $this->TradePayRespository->load($Params->getInTradeNo());
        }elseif(strlen((string)$Params->getOutTradeNo()) > 0){
            $TradePayEntity = $this->TradePayRespository->loadByOutTradeNo($Params->getOutTradeNo());
        }
        if(empty($TradePayEntity)){
            throw new TradeRefundNotFoundInvalidException('没有找到交易记录,请确认 in_trade_no 或 out_trade_no 参数.');
        }
        $TradeRefundEntity  = $this->TradeRefundRespository->loadByOutTradeNo($Params->getOutRefundNo());
        if(is_null($TradeRefundEntity)){
            $TradeRefundEntity = $this->TradeRefundManager->insert($TradePayEntity, $Params->getOutRefundNo(), $Params->getRefundFee());
        }
        if($TradeRefundEntity->getRefundFee() != $Params->getRefundFee()){
            throw new TradeRefundRefundFeeInvalidException('一个out_refund_no只能对应一笔退款,当请求失败需要重新请求时,不应该改变退款的金额。');
        }
        
        /**
         * 发起第三方渠道请求
         * 如果退款已经成功的话，不要再向第三方渠道发请求
         * @var \asbamboo\openpay\channel\v1_0\trade\RefundInterface $Channel
         * @var \asbamboo\openpay\channel\v1_0\trade\RefundParameter\Response $ChannelResponse
         */
       if($TradeRefundEntity->getStatus() != Constant::TRADE_REFUND_STATUS_SUCCESS){
            $TradeRefundThirdPartEntity = $this->TradeRefundThirdPartRespository->findOneByInRefundNo($TradeRefundEntity->getInRefundNo());
            if(empty($TradeRefundThirdPartEntity)){
                $TradeRefundThirdPartEntity = $this->TradeRefundThirdPartManager->insert($TradeRefundEntity, $Params->getThirdPart());
            }
            $this->TradeRefundManager->updateRequest($TradeRefundEntity);
            $this->Db->getManager()->flush();
            
            $channel_name       = $TradePayEntity->getChannel();
            $Channel            = $this->ChannelManager->getChannel(__CLASS__, $channel_name);
            $ChannelResponse    = $Channel->execute(new RequestByChannel([
                'channel'       => $channel_name,
                'in_trade_no'   => $TradeRefundEntity->getInTradeNo(),
                'in_refund_no'  => $TradeRefundEntity->getInRefundNo(),
                'refund_fee'    => $TradeRefundEntity->getRefundFee(),
                'third_part'    => $TradeRefundThirdPartEntity->getSendData(),
            ]));
            
            if($ChannelResponse->getIsSuccess() == true){
                $this->TradeRefundManager->updateRefundSuccess($TradeRefundEntity, strtotime($ChannelResponse->getPayYmdhis()));
            }else{
                $this->TradeRefundManager->updateRefundFailed($TradeRefundEntity); 
            }
        
            $this->Db->getManager()->flush();
        }
        
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
}