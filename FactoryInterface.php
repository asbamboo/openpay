<?php
namespace asbamboo\openpay;

use asbamboo\http\ResponseInterface;
use asbamboo\http\RequestInterface;

/**
 * 接口工厂
 *  - 创建各个平台的接口访问器
 *  - 创建指派参数实例，为了代码结构中，清晰列出接口参数。
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月8日
 */
interface FactoryInterface
{
    /**
     * 创建一个接口访问器
     *
     * @param string $builder_name
     * @return BuilderInterface
     */
    static public function createBuilder(string $builder_name) : BuilderInterface;

    /**
     * 发送请求并且得到响应的响应值
     *
     * @param RequestInterface $Request
     * @return ResponseInterface
     */
    static public function sendRequest(RequestInterface $Request) : ResponseInterface;
}