<?php
namespace asbamboo\openpay;

use asbamboo\http\RequestInterface;
use asbamboo\http\UriInterface;

/**
 * 接口请求对象[asbamboo\http\RequestInterface]实例创建器
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月9日
 */
interface BuilderInterface
{
    /**
     * 设置请求网关
     *
     * @param string $uri
     * @return BuilderInterface
     */
    public function setGateway(string $uri) : BuilderInterface;

    /**
     * 返回请求网关
     *
     * @return string|NULL
     */
    public function getGateway() : ?UriInterface;

    /**
     * 指定请求数据
     *  - 在这个方法中处理映射关系，不同的支付方式$assign_data中的key映射为不同支付方式请求的参数
     *
     * @param array $AssignData
     * @return BuilderInterface
     */
    public function assignData(array $assign_data) : BuilderInterface;

    /**
     * 创建请求对象
     *
     * @return RequestInterface
     */
    public function create() : RequestInterface;
}