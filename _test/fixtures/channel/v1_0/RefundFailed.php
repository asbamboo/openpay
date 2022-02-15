<?php
namespace asbamboo\openpay\_test\fixtures\channel\v1_0;

use asbamboo\openpay\channel\v1_0\trade\RefundInterface;
use asbamboo\openpay\channel\v1_0\trade\RefundParameter\Request;
use asbamboo\openpay\channel\v1_0\trade\RefundParameter\Response;
use asbamboo\http\ServerRequestInterface;
use asbamboo\openpay\channel\v1_0\trade\refundParameter\NotifyResult;

/**
 * 测试退款返回状态:退款处理失败
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年11月13日
 */
class RefundFailed implements RefundInterface
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
        $Response->setRefundStatus(Response::REFUND_STATUS_FAILED);
        $Response->setRefundFee($Request->getRefundFee());
        return $Response;
    }

    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\openpay\channel\v1_0\trade\RefundInterface::notify()
     */
    public function notify(ServerRequestInterface $Request) : NotifyResult
    {
        $NotifyResult = new NotifyResult();
        $NotifyResult->setInRefundNo($Request->getRequestParam('in_refund_no'));
        $NotifyResult->setRefundPayYmdhis('2018-11-13 20:07:50');
        $NotifyResult->setRefundStatus(Response::REFUND_STATUS_SUCCESS);
        $NotifyResult->setResponseSuccess('SUCCESS');
        $NotifyResult->setResponseFailed('FAILED');
        $NotifyResult->setThirdPart('third_part_data');
        return $NotifyResult;
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\openpay\channel\ChannelInterface::supports()
     */
    public function supports() : array
    {
        return [
            'TEST-FAILED'  => '测试退款失败',
        ];
    }
}