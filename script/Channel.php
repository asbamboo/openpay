<?php
namespace asbamboo\openpay\script;

use Composer\Script\Event;
use asbamboo\openpay\channel\ChannelMapping;

/**
 * open pay 模块的一些和composer script配置相关的方法
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月19日
 */
class Channel implements ChannelInterface
{
    /**
     * 绑定渠道
     * 生成api-store handler与 chanel interface关联的映射文件
     *
     * @param Event $Event
     */
    public function generateMappingInfo(Event $Event) : void
    {
        $root_dir       = getcwd();
        $channels       = $this->findChannel($root_dir);
        $ChannelMapping = new ChannelMapping();
        $ChannelMapping->addMappingChannels($channels);
    }

    /**
     * 返回所有实现了ChannelInterface接口的实例的类名
     *
     * @param string $root_dir
     * @param string $vendor_dir
     * @return array|\\asbamboo\\openpay\\ChannelInterface[]
     */
    private function findChannel($root_dir) : array
    {
        // 在openpay模块内不应该添加处理渠道，有也只可能时单元测试用的文件
        if(rtrim($root_dir, DIRECTORY_SEPARATOR) == dirname(__DIR__)){
            return [];
        }

        $channels   = [];
        $paths      = array_diff(scandir($root_dir), ['.', '..', $vendor_dir]);
        foreach($paths AS $path){
            $path   = $root_dir . DIRECTORY_SEPARATOR . $path;
            if(is_dir($path)){
                $channels   = array_merge($channels, $this->findChannel($path));
            }
            $file_contents      = file_get_contents($path);
            $classname_data     = [];
            $getting_namespace  = false;
            $getting_classname  = false;
            foreach(token_get_all($file_contents, TOKEN_PARSE) AS $token){
                if(is_array($token) && $token[0] == T_NAMESPACE){
                    $getting_namespace  = true;
                }
                if(is_array($token) && $token[0] == T_CLASS){
                    $getting_classname  = true;
                }
                if($getting_namespace == true){
                    if(is_array($token) && $token[0] == T_STRING ){
                        $classname_data[]   = $token[1];
                    }else if($token == ';'){
                        $getting_namespace  = false;
                    }
                }
                if($getting_classname == true && is_array($token) && $token[0] == T_STRING){
                    $classname_data[]   = $token[1];
                    $getting_namespace  = false;
                    break;
                }
            }
            if(!empty( $classname_data )){
               $classname  = implode('\\', $classname_data);
               if(is_file($class) && $classname instanceof ChannelInterface){
                   $channels[] = $classname;
               }
            }
        }
        return $channels;
    }
}