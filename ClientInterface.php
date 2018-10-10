<?php
namespace asbamboo\openpay;

use asbamboo\http\ResponseInterface;

/**
 * 指定请求接口的 BuilderInterface 实例名称和 assigndata 信息，发起api请求得到相关接口响应
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月9日
 */
interface ClientInterface
{
    /**
     * 发起一个接口请求
     *
     * @param string $builder_name api请求构件名称 如 alipay:TradeCreate
     * @param array $assign_data api请求构件指派的数据集
     * @return ResponseInterface 响应结果
     */
    public function request(string $builder_name, array $assign_data = []) : ResponseInterface;
}