<?php
namespace asbamboo\openpay\alipay;

use asbamboo\openpay\BuilderInterface;
use asbamboo\http\ClientInterface;
use asbamboo\http\RequestInterface;

/**
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月8日
 */
class AlipayBuilder implements BuilderInterface
{
    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\openpay\BuilderInterface::createClient()
     */
    public function createClient() : ClientInterface
    {

    }

    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\openpay\BuilderInterface::createRequest()
     */
    public function createRequest(array $request_name, string $request_env = null) : RequestInterface
    {

    }
}