<?php
namespace asbamboo\openpay;

use asbamboo\http\ClientInterface;
use asbamboo\http\RequestInterface;

/**
 * 接口访问构件器
 *  - 通过builder格式化接口请求的参数、请求地址、响应值等。
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月8日
 */
Interface BuilderInterface
{
    /**
     * 创建的client用于发起request请求
     *
     * @return ClientInterface
     */
    public function createClient() : ClientInterface;

    /**
     * 创建的request 通过client请求
     * 通过请求的参数创建与参数响应的 Request 实例
     *
     * @param array $request_name request 名称
     * @param string $request_env request 环境 应该不同的env对应有不同的request uri，也有可能不同的env有不同的参数。
     * @return RequestInterface
     */
    public function createRequest(array $request_name, string $request_env = null) : RequestInterface;
}