<?php
namespace asbamboo\openpay\script;

use Composer\Script\Event;
use asbamboo\openpay\channel\ChannelMapping;
use ChannelInterface AS ScriptChannelInterface;
use asbamboo\openpay\channel\ChannelInterface AS OpenpayChannelInterface;
use asbamboo\autoload\Autoload;

/**
 * open pay 模块的一些和composer script配置相关的方法
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月19
 */
class Channel implements ChannelInterface
{
    /**
     * 绑定渠道
     * 生成api-store handler与 chanel interface关联的映射文件
     *
     * @param Event $Event
     */
    public static function generateMappingInfo(Event $Event) : void
    {
        new Autoload();
        $root_dir       = getcwd();
        $Event->getIO()->write('当前项目跟目录:' . $root_dir);
        $channels       = static::findChannel($root_dir);
        $Event->getIO()->write('找出的渠道信息:' . var_export($channels, true));
        $ChannelMapping = new ChannelMapping();
        $ChannelMapping->resetMappingContent();
        $ChannelMapping->addMappingChannels($channels);
        $Event->getIO()->write('接口与渠道映射关系:' . var_export($ChannelMapping->getMappingContent(), true));
    }

    /**
     * 返回所有实现了ChannelInterface接口的实例的类名
     *
     * @param string $root_dir
     * @param string $vendor_dir
     * @return array|\\asbamboo\\openpay\\ChannelInterface[]
     */
    private static function findChannel($root_dir) : array
    {
        // 在openpay模块内不应该添加处理渠道，有也只可能时单元测试用的文件
        if(rtrim($root_dir, DIRECTORY_SEPARATOR) == dirname(__DIR__)){
            return [];
        }

        $channels   = [];
        $paths      = array_diff(scandir($root_dir), ['.', '..']);
        foreach($paths AS $path){
            $path   = $root_dir . DIRECTORY_SEPARATOR . $path;
            if(is_dir($path)){
                $channels   = array_merge($channels, static::findChannel($path));
                continue;
            }
            $file_content       = file_get_contents($path);
            $classname_data     = [];
            $getting_namespace  = false;
            $getting_classname  = false;
            try{
                foreach(token_get_all($file_content, TOKEN_PARSE) AS $token){
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
            }catch(\Throwable $e){
                // 不是php文件，这里只解析php文件
                continue;
            }
            $classname   = implode('\\', $classname_data);
            if(class_exists( $classname )){
                if(in_array(OpenpayChannelInterface::class, class_implements($classname))){
                    $channels[] = $classname;
                }
            }
        }
        return $channels;
    }
}