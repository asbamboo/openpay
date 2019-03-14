<?php
namespace asbamboo\openpay;

/**
 * 相关常量
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月31日
 */
final class Constant
{
    /************************************************************************************************
     * model trade pay trade status
     ***********************************************************************************************/
    /*
     * PAYFAILED 后可以 取消支付，也可以重新支付
     * 支付取消 CANCLE 或者支付成功 PAYOK、PAYED 后，不能再重新支付
     * 支付取消 CANCLE 或者支付成功 PAYED 后，状态不会再变更
     * 支付成功(可退款) PAYOK 以后状态只有可能变更为 支付成功(不可退款) PAYED
     * 状态可能的变更流程
     *  - NOPAY -> PAYOK
     *  - NOPAY -> PAYOK -> PAYED
     *  - NOPAY -> PAYING -> PAYOK -> PAYED
     *  - NOPAY -> PAYING -> PAYOK
     *  - NOPAY -> CANCLE
     *  - NOPAY -> PAYFAILED -> CANCLE
     *  - NOPAY -> PAYING -> PAYFAILED -> CANCLE
     *  - NOPAY -> PAYFAILED
     *  - NOPAY -> PAYING -> PAYFAILED
     *  - NOPAY -> PAYFAILED -> PAYOK -> PAYED
     *  - NOPAY -> PAYFAILED -> PAYING -> PAYOK -> PAYED
     */
    const TRADE_PAY_TRADE_STATUS_NOPAY      = '0';  // 尚未支付
    const TRADE_PAY_TRADE_STATUS_PAYOK      = '1';  // 支付成功（可退款）
    const TRADE_PAY_TRADE_STATUS_PAYED      = '2';  // 支付成功（不可退款）
    const TRADE_PAY_TRADE_STATUS_PAYING     = '3';  // 正在支付
    const TRADE_PAY_TRADE_STATUS_CANCEL     = '4';  // 交易取消（状态不可再变更）
    const TRADE_PAY_TRADE_STATUS_PAYFAILED  = '9';  // 支付失败

    /*
     * 交易支付状态api接口传递的名称
     *
     * @return array
     */
    public static function getTradePayTradeStatusNames() : array
    {
        return [
            static::TRADE_PAY_TRADE_STATUS_NOPAY        => 'NOPAY',
            static::TRADE_PAY_TRADE_STATUS_PAYOK        => 'PAYOK',
            static::TRADE_PAY_TRADE_STATUS_PAYED        => 'PAYED',
            static::TRADE_PAY_TRADE_STATUS_PAYING       => 'PAYING',
            static::TRADE_PAY_TRADE_STATUS_CANCEL       => 'CANCEL',
            static::TRADE_PAY_TRADE_STATUS_PAYFAILED    => 'PAYFAILED',
        ];
    }
    /***********************************************************************************************/


    /************************************************************************************************
     * model trade refund status
     ***********************************************************************************************/
    const TRADE_REFUND_STATUS_REQUEST   = '0'; // 请求退款
    const TRADE_REFUND_STATUS_SUCCESS   = '1'; // 退款成功
    const TRADE_REFUND_STATUS_FAILED    = '9'; // 退款失败

    /*
     * 退款状态api接口传递的名称
     *
     * @return array
     */
    public static function getTradeRefundStatusNames() : array
    {
        return [
            static::TRADE_REFUND_STATUS_REQUEST     => 'REQUEST',
            static::TRADE_REFUND_STATUS_SUCCESS     => 'SUCCESS',
            static::TRADE_REFUND_STATUS_FAILED      => 'FAILED',
        ];
    }
    /***********************************************************************************************/
}