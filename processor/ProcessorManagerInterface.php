<?php
namespace asbamboo\openpay\processor;

use asbamboo\console\ProcessorInterface;

/**
 *
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月18日
 */
interface ProcessorManagerInterface
{
    /**
     * 重写mappingdata
     */
    public function rewriteMappingData() : void;

    /**
     *
     * @param string $api_name
     * @return ProcessorInterface
     */
    public function getProcessor(string $api_name) : ProcessorInterface;
}