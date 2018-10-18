<?php
namespace asbamboo\openpay\processor\v1_0\trade;

use asbamboo\openpay\apiStore\parameter\v1_0\trade\PayRequest;
use asbamboo\openpay\apiStore\parameter\v1_0\trade\PayResponse;
use asbamboo\api\exception\ApiException;

/**
 * 处理 trade.pay [1.0] 接口
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月18日
 */
interface PayProcessorInterface
{
    /**
     * 执行接口处理请求
     * 接口处理完成以后应该返回一个响应参数列表
     *
     * @param PayRequest $PayRequest
     * @return PayResponse
     * @throws ApiException
     */
    public function execute(PayRequest $PayRequest) : PayResponse;
}