<?php
namespace asbamboo\openpay\channel;

/**
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月22日
 */
class ChannelManager implements ChannelManagerInterface
{
    /**
     * 渠道映射关系
     *
     * @var ChannelMappingInterface
     */
    private $ChannelMapping;

    /**
     *
     * @param ChannelMappingInterface $ChannelMapping
     */
    public function __construct(ChannelMappingInterface $ChannelMapping)
    {
        $this->ChannelMapping   = $ChannelMapping;
    }

    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\openpay\channel\ChannelManagerInterface::getChannels()
     */
    public function getChannels(string $handler_class) : array
    {
        $mapping_info   = $this->ChannelMapping->getMappingContent();
        return isset( $handler_class[$mapping_info] ) ? $handler_class[$mapping_info] : [];
    }

    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\openpay\channel\ChannelManagerInterface::getChannel()
     */
    public function getChannel(string $handler_class, string $name) : ?ChannelInterface
    {
        $channels   = $this->getChannels($handler_class);
        if(isset($channels[$name])){
            $channel    = unserialize($channels[$name]);
            return $channel;
        }
        return null;
    }
}