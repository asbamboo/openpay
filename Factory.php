<?php
namespace asbamboo\openpay;

use asbamboo\http\RequestInterface;
use asbamboo\http\ResponseInterface AS HttpResponseInterface;
use asbamboo\openpay\exception\NotFoundBuilderException;
use asbamboo\http\Client;
use asbamboo\openpay\common\ResponseInterface;

/**
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月10日
 */
class Factory implements FactoryInterface
{
    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\openpay\FactoryInterface::createBuilder()
     */
    static public function createBuilder(string $builder_name) : BuilderInterface
    {
        @list($type, $name) = explode(':', $builder_name);
        $class_name         = __NAMESPACE__ . "\\payMethod\\{$type}\\builder\\{$name}";
        if(!class_exists($class_name)){
            throw new NotFoundBuilderException(sprintf('不支持的支付渠道接口：%s', $builder_name));
        }
        return new $class_name;
    }

    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\openpay\FactoryInterface::sendRequest()
     */
    static public function sendRequest(RequestInterface $Request) : HttpResponseInterface
    {
        static $Client  = null;
        if(is_null($Client)){
            $Client = new Client();
        }
        return $Client->send($Request);
    }

    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\openpay\FactoryInterface::transformResponse()
     */
    static public function transformResponse(string $builder_name, HttpResponseInterface $HttpResponse) : ResponseInterface
    {
        @list($type, $name) = explode(':', $builder_name);
        $response_class     = __NAMESPACE__ . "\\payMethod\\{$type}\\response\\{$name}Response";
        if(!class_exists($response_class)){
            throw new NotFoundBuilderException(sprintf('%s接口:不支持响应结果转换成实例模式。', $builder_name));
        }
        return new $response_class($HttpResponse);
    }
}