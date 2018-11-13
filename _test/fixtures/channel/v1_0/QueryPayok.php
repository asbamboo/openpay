<?php
namespace asbamboo\openpay\_test\fixtures\channel\v1_0;

use asbamboo\openpay\channel\v1_0\trade\QueryInterface;
use asbamboo\openpay\channel\v1_0\trade\queryParameter\Request;
use asbamboo\openpay\channel\v1_0\trade\queryParameter\Response;
use asbamboo\openpay\Constant;

/**
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年11月12日
 */
class QueryPayok implements QueryInterface
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
        $Response->setThirdTradeNo('third_trade_no');
        $Response->setTradeStatus(Constant::TRADE_PAY_TRADE_STATUS_PAYOK);
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
            'TEST_QUERY_PAYOK'  => 'QUERY_PAYOK',
        ];
    }
}
