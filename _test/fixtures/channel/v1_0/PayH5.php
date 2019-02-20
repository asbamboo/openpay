<?php
namespace asbamboo\openpay\_test\fixtures\channel\v1_0;

use asbamboo\openpay\channel\v1_0\trade\PayInterface;
use asbamboo\openpay\channel\v1_0\trade\payParameter\Request;
use asbamboo\openpay\channel\v1_0\trade\payParameter\Response;
use asbamboo\http\ServerRequestInterface;
use asbamboo\openpay\channel\v1_0\trade\payParameter\NotifyResult;

/**
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年11月12日
 */
class PayH5 implements PayInterface
{
    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\openpay\channel\v1_0\trade\CancelInterface::execute()
     */
    public function execute(Request $Request) : Response
    {
        $Response   = new Response();
        $Response->setType(Response::TYPE_H5);
        $Response->setRedirectUrl('redirect_url');
        $Response->setRedirectData(['data'=>'test']);
        return $Response;
    }

    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\openpay\channel\v1_0\trade\PayInterface::notify()
     */
    public function notify(ServerRequestInterface $Request) : NotifyResult
    {
        $NotifyResult = new NotifyResult();
        $NotifyResult->setInTradeNo($Request->getRequestParam('in_trade_no'));
        $NotifyResult->setResponseSuccess('SUCCESS');
        $NotifyResult->setResponseFailed('FAILED');
        $NotifyResult->setThirdPart('third_part_data');
        $NotifyResult->setThirdTradeNo('third_trade_no');
        $NotifyResult->setTradeStatus($Request->getRequestParam('test_pay_status'));
        return $NotifyResult;
    }

    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\openpay\channel\v1_0\trade\PayInterface::return()
     */
    public function return(ServerRequestInterface $Request) : NotifyResult
    {
        return $this->notify($Request);
    }

    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\openpay\channel\ChannelInterface::supports()
     */
    public function supports() : array
    {
        return [
            'TEST_PAY_H5'  => '测试H5支付',
        ];
    }
}