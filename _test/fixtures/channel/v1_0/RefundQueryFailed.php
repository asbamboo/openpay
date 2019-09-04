<?php
namespace asbamboo\openpay\_test\fixtures\channel\v1_0;

use asbamboo\openpay\channel\v1_0\trade\RefundQueryInterface;
use asbamboo\openpay\channel\v1_0\trade\RefundQueryParameter\Request;
use asbamboo\openpay\channel\v1_0\trade\RefundQueryParameter\Response;
use asbamboo\openpay\Constant;

/**
 * 测试退款返回状态:退款处理成功
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年11月13日
 */
class RefundQueryFailed implements RefundQueryInterface
{
    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\openpay\channel\v1_0\trade\CancelInterface::execute()
     */
    public function execute(Request $Request) : Response
    {
        $Response   = new Response();
        $Response->setInRefundNo($Request->getInRefundNo());
        $Response->setRefundStatus(Constant::TRADE_REFUND_STATUS_FAILED);
        return $Response;
    }

    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\openpay\channel\ChannelInterface::supports()
     */
    public function supports() : array
    {
        return [
            'TEST-REFUND-QUERY-FAILED'  => '测试查询退款[失败]',
        ];
    }
}