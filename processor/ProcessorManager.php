<?php
namespace asbamboo\openpay\processor;

class ProcessorManager implements ProcessorManagerInterface
{
    /**
     * 重写mappingdata
     *
     */
    public function rewriteMappingData()
    {

    }

    /**
     *
     *
     * @param string $api_name
     * @return ProcessorInterface
     */
    public function getProcessor(string $api_name) : ProcessorInterface
    {

    }
}