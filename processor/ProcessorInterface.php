<?php
namespace asbamboo\openpay\processor;

use asbamboo\api\apiStore\ApiRequestParamsInterface;
use asbamboo\api\apiStore\ApiResponseParamsInterface;

/**
 * api接口处理
 * 每个接口都需要一个与接口对应的ProcessorInterface
 * 主要时execute规定请求参数与响应参数列表
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月18日
 */
interface ProcessorInterface
{
    /**
     * 执行接口处理请求
     * 接口处理完成以后应该返回一个响应参数列表
     *
     * @param ApiRequestParamsInterface $ApiRequestParams
     * @return ApiResponseParamsInterface|NULL
     */
    public function execute(ApiRequestParamsInterface $ApiRequestParams) : ?ApiResponseParamsInterface;

    /**
     * 支持的版本列表
     *
     * @return array
     */
    public function supportedVersions() : array;
}