<?php
namespace asbamboo\openpay\channel;

use asbamboo\api\apiStore\ApiClassInterface;

/**
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月22日
 */
class ChannelMapping implements ChannelMappingInterface
{
    /**
     * 报错映射关系的文件路径，json格式
     *
     * @var string
     */
    const MAPPING_FILE  = __DIR__ . DIRECTORY_SEPARATOR . 'mapping.json';

    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\openpay\channel\ChannelMappingInterface::setMappingChannels()
     */
    public function addMappingChannels(array $channels) : ChannelMappingInterface
    {
        $handlers       = $this->findHandler();
        $mappings       = [];
        foreach($handlers AS $handler){
            $cancel_interface   = str_replace('apiStore\\handler', 'channel', $handler) . 'Interface';
            $mappings[$handler] = [];
            foreach($channels AS $channel){
                if($channel instanceof $cancel_interface){
                    $ChannelObj                                 = new $channel();
                    $mappings[$handler][$ChannelObj->getName()] = serialize($ChannelObj);
                }
            }
        }

        $mappings           = array_merge_recursive($this->getMappingContent(), $mappings);
        $encode_content     = json_encode($mappings);
        file_put_contents(self::MAPPING_FILE, $encode_content);

        return $this;
    }

    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\openpay\channel\ChannelMappingInterface::getMappingContent()
     */
    public function getMappingContent() : array
    {
        return json_decode(file_get_contents(self::MAPPING_FILE), true);
    }


    /**
     * 返回api接口中处理用户请求的类的集合
     *
     * @param string $dir
     * @param string $namespace
     * @return array
     */
    private function findHandler(string $dir = null, string $namespace = null) : array
    {
        $handlers   = [];
        if($dir == null){
            $dir        = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'api-store' . DIRECTORY_SEPARATOR . 'handler';
        }
        if($namespace == null){
            $namespace_data = explode('\\', __NAMESPACE__);
            array_pop($namespace_data);
            $namespace      = implode('\\', $namespace_data) . '\\apiStore\\handler\\';
        }
        $paths  = array_diff(scandir($dir), ['.', '..']);
        foreach($paths AS $path){
            $filepath   = $dir . DIRECTORY_SEPARATOR . $path;
            if(is_dir($filepath)){
                $namespace  = $namespace . lcfirst(implode('', array_map(ucfirst($path), explode('-', substr($path, 0, -4/*.php*/))))) . '\\';
                $handlers   = array_merge($this->findHandler($filepath, $namespace));
            }
            $class  = $namespace . substr($path, 0, -4/*.php*/);
            if(is_file($class) instanceof ApiClassInterface){
                $handlers[] = $class;
            }
        }
        return $handlers;
    }
}
