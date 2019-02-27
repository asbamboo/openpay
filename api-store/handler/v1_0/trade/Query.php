<?php
namespace asbamboo\openpay\apiStore\handler\v1_0\trade;

use asbamboo\openpay\channel\ChannelManagerInterface;
use asbamboo\api\apiStore\ApiRequestParamsInterface;
use asbamboo\api\apiStore\ApiResponseParamsInterface;
use asbamboo\openpay\model\tradePay\TradePayRepository;
use asbamboo\openpay\apiStore\parameter\v1_0\trade\query\QueryRequest;
use asbamboo\api\apiStore\ApiClassInterface;
use asbamboo\openpay\apiStore\exception\TradeQueryNotFoundInvalidException;
use asbamboo\openpay\Constant;
use asbamboo\openpay\channel\v1_0\trade\queryParameter\Request AS RequestByChannel;
use asbamboo\openpay\model\tradePay\TradePayManager;
use asbamboo\openpay\apiStore\parameter\v1_0\trade\query\QueryResponse;
use asbamboo\database\FactoryInterface;

/**
 * @name 交易查询
 * @desc 交易查询
 * @request asbamboo\openpay\apiStore\parameter\v1_0\trade\query\QueryRequest
 * @response asbamboo\openpay\apiStore\parameter\v1_0\trade\query\QueryResponse
 * @author 李春寅<licy2013@aliyun.com>
 * @since 2018年10月27日
 */
class Query implements ApiClassInterface
{
    /**
     * 渠道管理器
     *
     * @var ChannelManagerInterface
     */
    private $ChannelManager;

    /**
     *
     * @var TradePayRepository
     */
    private $TradePayRepository;

    /**
     *
     * @var TradePayManager
     */
    private $TradePayManager;

    /**
     *
     * @var FactoryInterface
     */
    protected $Db;

    /**
     *
     * @param ChannelManagerInterface $Client
     */
    public function __construct(ChannelManagerInterface $ChannelManager, FactoryInterface $Db, TradePayRepository $TradePayRepository, TradePayManager $TradePayManager)
    {
        $this->ChannelManager           = $ChannelManager;
        $this->TradePayRepository      = $TradePayRepository;
        $this->TradePayManager          = $TradePayManager;
        $this->Db                       = $Db;
    }

    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\api\apiStore\ApiClassAbstract::exec()
     * @var QueryRequest $Params
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
            throw new TradeQueryNotFoundInvalidException('没有找到交易记录,请确认 in_trade_no 或 out_trade_no 参数.');
        }

        /**
        * 发起第三方渠道请求
        *
        * @var QueryInterface $Channel
        * @var Response $ChannelResponse
        */
        if(!in_array($TradePayEntity->getTradeStatus() , [Constant::TRADE_PAY_TRADE_STATUS_PAYOK, Constant::TRADE_PAY_TRADE_STATUS_PAYED, Constant::TRADE_PAY_TRADE_STATUS_CANCEL])){
            $channel_name       = $TradePayEntity->getChannel();
            $Channel            = $this->ChannelManager->getChannel(__CLASS__, $channel_name);
            $ChannelResponse    = $Channel->execute(new RequestByChannel([
                'channel'       => $TradePayEntity->getChannel(),
                'in_trade_no'   => $TradePayEntity->getInTradeNo(),
                'third_part'    => $Params->getThirdPart(),
            ]));
            //支付成功（可退款）
            if($ChannelResponse->getTradeStatus() == Constant::TRADE_PAY_TRADE_STATUS_PAYOK){
                $TradePayEntity = $this->TradePayManager->load($TradePayEntity->getInTradeNo());
                $this->TradePayManager->updateTradeStatusToPayok($ChannelResponse->getThirdTradeNo());
                $this->Db->getManager()->flush();
                //支付成功（不可退款）
            }else if($ChannelResponse->getTradeStatus() == Constant::TRADE_PAY_TRADE_STATUS_PAYED){
                $TradePayEntity = $this->TradePayManager->load($TradePayEntity->getInTradeNo());
                $this->TradePayManager->updateTradeStatusToPayed($ChannelResponse->getThirdTradeNo());
                $this->Db->getManager()->flush();
                //支付取消（不可退款）
            }else if($ChannelResponse->getTradeStatus() == Constant::TRADE_PAY_TRADE_STATUS_CANCEL){
                $TradePayEntity = $this->TradePayManager->load($TradePayEntity->getInTradeNo());
                $this->TradePayManager->updateTradeStatusToCancel($ChannelResponse->getThirdTradeNo());
                $this->Db->getManager()->flush();
            }
        }

        return new QueryResponse([
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
            'qr_code'       => $TradePayEntity->getQrCode(),
        ]);
    }
}