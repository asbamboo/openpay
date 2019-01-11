<?php
namespace asbamboo\openpay\_test\fixtures\channel;

use asbamboo\openpay\channel\ChannelManagerInterface;
use asbamboo\openpay\channel\ChannelInterface;
use asbamboo\openpay\apiStore\handler\v1_0\trade\Cancel;
use asbamboo\openpay\apiStore\handler\v1_0\trade\Pay;
use asbamboo\openpay\apiStore\handler\v1_0\trade\Query;
use asbamboo\openpay\apiStore\handler\v1_0\trade\Refund;

/**
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年11月13日
 */
class ChannelManager implements ChannelManagerInterface
{
    /**
     * 返回接口 handler 可用的渠道
     *
     * @param string $handler_class
     * @return array
     */
    public function getChannels(string $handler_class) : array
    {
        return [
            Cancel::class               => ['TEST' => ['测试', serialize(new \asbamboo\openpay\_test\fixtures\channel\v1_0\Cancel())]],
            Pay::class                  => [
                'TEST_PAY_PC'           => ['测试PC支付', serialize(new \asbamboo\openpay\_test\fixtures\channel\v1_0\PayPc())],
                'TEST_PAY_QRCD'         => ['测试扫码支付', serialize(new \asbamboo\openpay\_test\fixtures\channel\v1_0\PayQrcd())],
                'TEST_PAY_GENERAL'      => ['测试GENERAL支付', serialize(new \asbamboo\openpay\_test\fixtures\channel\v1_0\PayGeneral())],
            ],
            Query::class                => [
                'TEST_QUERY'            => ['QUERY', serialize(new \asbamboo\openpay\_test\fixtures\channel\v1_0\Query())],
                'TEST_QUERY_CANCEL'     => ['QUERY_CANCEL', serialize(new \asbamboo\openpay\_test\fixtures\channel\v1_0\QueryCancel())],
                'TEST_QUERY_PAYED'      => ['QUERY_PAYED', serialize(new \asbamboo\openpay\_test\fixtures\channel\v1_0\QueryPayed())],
                'TEST_QUERY_PAYOK'      => ['QUERY_PAYOK', serialize(new \asbamboo\openpay\_test\fixtures\channel\v1_0\QueryPayok())],
            ],
            Refund::class               => ['TEST' => ['测试', serialize(new \asbamboo\openpay\_test\fixtures\channel\v1_0\Refund())]],
        ][$handler_class];
    }

    /**
     * 返回一个名称为$name的$handler_class可用的ChannelInterface实例
     *
     * @param string $handler_class
     * @param string $name
     * @return ChannelInterface|null
     */
    public function getChannel(string $handler_class, string $name) : ?ChannelInterface
    {
        return isset($this->getChannels($handler_class)[$name][1]) ? unserialize($this->getChannels($handler_class)[$name][1]) : null;
    }
}
