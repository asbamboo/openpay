<?php
namespace asbamboo\openpay\apiStore\parameter\v1_0\trade\query;

use asbamboo\api\apiStore\ApiRequestParamsInterface;

/**
 * 验证 query request 参数
 * 使用在query接口处理器中
 *
 * @author 李春寅<licy2013@aliyun.com>
 * @since 2018年10月27日
 */
trait QueryRequestValidateTrait
{
    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\api\apiStore\ApiClassAbstract::validate()
     * @var QueryRequest $Params
     */
    public function validate(ApiRequestParamsInterface $Params): bool
    {
        return true;
    }
}