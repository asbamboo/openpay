<?php
namespace asbamboo\openpay\model\tradeRefund;

use asbamboo\openpay\apiStore\exception\TradeRefundOutRefundNoInvalidException;
use asbamboo\openpay\apiStore\exception\TradeRefundRefundFeeInvalidException;

/**
 * 数据表 trade pay 各个字段的验证器
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年11月1日
 */
trait TradeRefundValidator
{
    /**
     *
     * @param number $out_trade_no
     * @throws TradeRefundOutRefundNoInvalidException
     */
    private function validateOutRefundNo($out_refund_no)
    {
        if(trim($out_refund_no) === ''){
            throw new TradeRefundOutRefundNoInvalidException('out_refund_no 是必填项。');
        }
        
        if(strlen($out_refund_no) > 45){
            throw new TradeRefundOutRefundNoInvalidException('out_refund_no 长度不能超过45字。');
        }
    }
    
    /**
     * 
     * @param string $refund_fee
     * @throws TradeRefundRefundFeeInvalidException
     */
    public function validateRefundFee($refund_fee)
    {
        if(trim((string )$refund_fee) === ''){
            throw new TradeRefundRefundFeeInvalidException('refund_fee 是必填项。');
        }
        
        if(ctype_digit((string) $refund_fee) == false){
            throw new TradeRefundRefundFeeInvalidException('refund_fee 只能是数字。');
        }
        
        if($refund_fee > 10000000000 || $refund_fee < 1){
            throw new TradeRefundRefundFeeInvalidException('refund_fee 超出范围，1 < refund_fee < 10000000000。');
        }
        
    }
}