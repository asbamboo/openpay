<?php
namespace asbamboo\openpay\apiStore\parameter\v1_0\trade;

use asbamboo\api\apiStore\ApiRequestParamsInterface;
use asbamboo\openpay\apiStore\exception\TradePayChannelInvalidException;
use asbamboo\openpay\apiStore\exception\TradePayTitleInvalidException;
use asbamboo\openpay\apiStore\exception\TradePayTotalFeeInvalidException;
use asbamboo\openpay\apiStore\exception\TradePayOutTradeNoInvalidException;
use asbamboo\openpay\apiStore\exception\TradePayClientIpInvalidException;
use asbamboo\openpay\apiStore\exception\TradePayThirdPartInvalidException;
use asbamboo\openpay\apiStore\handler\v1_0\trade\Pay;

/**
 * 验证 pay request 参数
 * 使用在pay接口处理器中
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月14日
 */
trait PayRequestValidateTrait
{
    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\api\apiStore\ApiClassAbstract::validate()
     * @var PayRequest $Params
     */
    public function validate(ApiRequestParamsInterface $Params): bool
    {
        $this->validateChannel($Params->getChannel());
        $this->validateTitle($Params->getTitle());
        $this->validateOutTradeNo($Params->getOutTradeNo());
        $this->validateTotalFee($Params->getTotalFee());
        $this->validateClientIp($Params->getClientIp());
        $this->validateThirdPart($Params->getThirdPart());
        return true;
    }

    /**
     *
     * @param string $payment
     * @throws TradePayChannelInvalidException
     */
    private function validateChannel($channel)
    {
        $exist_channels   = $this->ChannelManager->getChannels(Pay::class);
        if(!array_key_exists($channel, $exist_channels)){
            throw new TradePayChannelInvalidException(sprintf('支付渠道%s暂不支持。', $channel));
        }
    }

    /**
     *
     * @param string $title
     * @throws TradePayTitleInvalidException
     */
    private function validateTitle($title)
    {
        if(trim($title) === ''){
            throw new TradePayTitleInvalidException('title 是必填项。');
        }
        if(mb_strlen($title) > 45){
            throw new TradePayTitleInvalidException('title 超长。长度不能超过45字。');
        }
    }

    /**
     *
     * @param number $out_trade_no
     * @throws TradePayOutTradeNoInvalidException
     */
    private function validateOutTradeNo($out_trade_no)
    {
        if(trim($out_trade_no) === ''){
            throw new TradePayOutTradeNoInvalidException('out_trade_no 是必填项。');
        }

        if(ctype_digit((string) $out_trade_no) == false){
            throw new TradePayOutTradeNoInvalidException('out_trade_no 只能是数字。');
        }

        if(strlen($out_trade_no) > 32){
            throw new TradePayOutTradeNoInvalidException('out_trade_no 长度不能超过32字。');
        }
    }

    /**
     *
     * @param number $total_fee
     * @throws TradePayTotalFeeInvalidException
     */
    private function validateTotalFee($total_fee)
    {
        if(trim($total_fee) === ''){
            throw new TradePayTotalFeeInvalidException('total_fee 是必填项。');
        }

        if(ctype_digit((string) $total_fee) == false){
            throw new TradePayTotalFeeInvalidException('total_fee 只能是数字。');
        }

        if($total_fee > 10000000000 || $total_fee < 1){
            throw new TradePayTotalFeeInvalidException('total_fee 超出范围，1 < total_fee < 10000000000。');
        }
    }

    /**
     *
     * @param string $client_ip
     * @throws TradePayClientIpInvalidException
     */
    private function validateClientIp($client_ip)
    {
        if(trim($client_ip) === ''){
            throw new TradePayClientIpInvalidException('client_ip 是必填项。');
        }
        if(long2ip(ip2long($client_ip)) != $client_ip){
            throw new TradePayClientIpInvalidException('client_ip 的值不是一个有效的ip地址。');
        }
    }

    /**
     *
     * @param string $third_part json
     * @throws TradePayThirdPartInvalidException
     */
    private function validateThirdPart($third_part)
    {
        if(trim($third_part) === ''){
            return;
        }
        json_decode($third_part);
        if(json_last_error()){
            throw new TradePayThirdPartInvalidException('third_part 的值不是有效的json格式');
        }
    }
}