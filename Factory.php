<?php
namespace asbamboo\openpay;

use asbamboo\http\RequestInterface;
use asbamboo\http\ResponseInterface;
use asbamboo\openpay\exception\NotFoundBuilderException;
use asbamboo\openpay\exception\NotFoundAssignDataClassException;
use asbamboo\http\Client;

/**
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月10日
 */
class Factory implements FactoryInterface
{
    /**
     * 创建一个接口访问器
     *
     * @param string $builder_name
     * @throws NotFoundBuilderException
     * @return BuilderInterface
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
     * @param RequestInterface $Request
     * @return ResponseInterface
     */
    static public function sendRequest(RequestInterface $Request) : ResponseInterface
    {
        static $Client  = null;
        if(is_null($Client)){
            $Client = new Client();
        }
        return $Client->send($Request);
    }
}