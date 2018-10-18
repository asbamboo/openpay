<?php
namespace asbamboo\openpay\processor;

use Composer\Script\Event;

class ProcessorManager implements ProcessorManagerInterface
{
    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\openpay\processor\ProcessorManagerInterface::rewriteMappingData()
     */
    public function rewriteMappingData(Event $Event) : void
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