<?php
namespace asbamboo\openpay\_test\fixtures\channel\v1_0;

use asbamboo\openpay\channel\v1_0\trade\CancelInterface;
use asbamboo\openpay\channel\v1_0\trade\cancelParameter\Request;
use asbamboo\openpay\channel\v1_0\trade\cancelParameter\Response;

/**
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年11月12日
 */
class Cancel implements CancelInterface
{
    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\openpay\channel\v1_0\trade\CancelInterface::execute()
     */
    public function execute(Request $Request) : Response
    {
        $Response   = new Response();
        $Response->setInTradeNo($Request->getInTradeNo());
        $Response->setIsSuccess(true);
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
