<?php
namespace asbamboo\openpay\apiStore\handler\v1_0\trade;

use asbamboo\api\apiStore\ApiClassInterface;
use asbamboo\api\apiStore\ApiRequestParamsInterface;
use asbamboo\api\apiStore\ApiResponseParamsInterface;
use asbamboo\openpay\channel\ChannelManagerInterface;
use asbamboo\database\FactoryInterface;
use asbamboo\openpay\model\tradePay\TradePayRespository;
use asbamboo\openpay\model\tradePay\TradePayManager;
use asbamboo\openpay\apiStore\exception\TradeCancelNotFoundInvalidException;
use asbamboo\openpay\Constant;
use asbamboo\openpay\apiStore\exception\TradeCancelNotAllowedException;
use asbamboo\openpay\channel\v1_0\trade\cancelParameter\Request AS RequestByChannel;
use asbamboo\openpay\model\tradePayThirdPart\TradePayThirdPartRespository;
use asbamboo\openpay\apiStore\parameter\v1_0\trade\cancel\CancelResponse;

/**
 * @name 取消交易
 * @desc 取消交易支付
 * @request asbamboo\openpay\apiStore\parameter\v1_0\trade\cancel\CancelRequest
 * @response asbamboo\openpay\apiStore\parameter\v1_0\trade\cancel\CancelResponse
 * @author 李春寅<licy2013@aliyun.com>
 * @since 2018年11月6日
 */
class Cancel implements ApiClassInterface
{
    /**
     * 渠道管理器
     *
     * @var ChannelManagerInterface
     */
    private $ChannelManager;

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
     * @var TradePayThirdPartRespository
     */
    private $TradePayThirdPartRespository;

    /**
     *
     * @var FactoryInterface
     */
    protected $Db;

    /**
     *
     * @param ChannelManagerInterface $Client
     */
    public function __construct(ChannelManagerInterface $ChannelManager, FactoryInterface $Db, TradePayRespository $TradePayRespository, TradePayManager $TradePayManager, TradePayThirdPartRespository $TradePayThirdPartRespository)
    {
        $this->ChannelManager                   = $ChannelManager;
        $this->TradePayRespository              = $TradePayRespository;
        $this->TradePayManager                  = $TradePayManager;
        $this->TradePayThirdPartRespository     = $TradePayThirdPartRespository;
        $this->Db                               = $Db;
    }

    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\api\apiStore\ApiClassInterface::exec()
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
            throw new TradeCancelNotFoundInvalidException('没有找到交易记录,请确认 in_trade_no 或 out_trade_no 参数.');
        }
        if(in_array($TradePayEntity->getTradeStatus() , [Constant::TRADE_PAY_TRADE_STATUS_PAYOK, Constant::TRADE_PAY_TRADE_STATUS_PAYED])){
            throw new TradeCancelNotAllowedException('交易已经支付成功，不允许取消。');
        }

        /**
         * 发起第三方渠道请求
         *
         * @var \asbamboo\openpay\channel\v1_0\trade\CancelInterface $Channel
         * @var \asbamboo\openpay\channel\v1_0\trade\cancelParameter\Response $ChannelResponse
         */
        if($TradePayEntity->getTradeStatus() != Constant::TRADE_PAY_TRADE_STATUS_CANCLE){

            $TradePayThirdPartEntity    = $this->TradePayThirdPartRespository->findOneByInTradeNo($TradePayEntity->getInTradeNo());
            $channel_name               = $TradePayEntity->getChannel();
            $Channel                    = $this->ChannelManager->getChannel(__CLASS__, $channel_name);
            $ChannelResponse            = $Channel->execute(new RequestByChannel([
                'channel'               => $TradePayEntity->getChannel(),
                'in_trade_no'           => $TradePayEntity->getInTradeNo(),
                'third_part'            => $TradePayThirdPartEntity->getSendData(),
            ]));

            if($ChannelResponse->getIsSuccess() == true){
                $this->TradePayManager->updateTradeStatusToCancel($TradePayEntity);
                $this->Db->getManager()->flush();
            }
        }

        return new CancelResponse([
            'channel'           => $TradePayEntity->getChannel(),
            'in_trade_no'       => $TradePayEntity->getInTradeNo(),
            'title'             => $TradePayEntity->getTitle(),
            'out_trade_no'      => $TradePayEntity->getOutTradeNo(),
            'total_fee'         => $TradePayEntity->getTotalFee(),
            'client_ip'         => $TradePayEntity->getClientIp(),
            'trade_status'      => $TradePayEntity->getTradeStatus(),
            'cancel_ymdhis'     => $TradePayEntity->getCancelTime() ? date('Y-m-d H:i:s', $TradePayEntity->getCancelTime()) : '',
        ]);
    }
}