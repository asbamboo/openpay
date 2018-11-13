<?php
namespace asbamboo\openpay\_test\fixtures\channel\v1_0;

use asbamboo\openpay\channel\v1_0\trade\RefundInterface;
use asbamboo\openpay\channel\v1_0\trade\RefundParameter\Request;
use asbamboo\openpay\channel\v1_0\trade\RefundParameter\Response;

/**
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年11月13日
 */
class Refund implements RefundInterface
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
        $Response->setIsSuccess(true);
        $Response->setPayYmdhis('2018-11-13 20:07:50');
        $Response->setRefundFee($Request->getRefundFee());
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
            'TEST'  => '测试',
        ];
    }
}