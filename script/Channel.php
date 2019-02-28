<?php
namespace asbamboo\openpay\script;

use Composer\Script\Event;
use asbamboo\openpay\channel\ChannelMapping;
use asbamboo\openpay\script\ChannelInterface AS ScriptChannelInterface;
use asbamboo\openpay\channel\ChannelInterface;

/**
 * open pay 模块的一些和composer script配置相关的方法
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月19
 */
class Channel implements ScriptChannelInterface
{
    /**
     * 绑定渠道
     * 生成api-store handler与 chanel interface关联的映射文件
     *
     * @param Event $Event
     */
    public static function generateMappingInfo(Event $Event) : void
    {
        $vendor_dir = $Event->getComposer()->getConfig()->get('vendor-dir');
        include $vendor_dir . DIRECTORY_SEPARATOR . 'asbamboo' . DIRECTORY_SEPARATOR . 'autoload' . DIRECTORY_SEPARATOR . 'bootstrap.php';

        $root_dir       = getcwd();
        $Event->getIO()->write('当前项目跟目录:' . $root_dir);
        $channels       = static::findChannel($root_dir, $Event);
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
     * @return array|\\asbamboo\\openpay\\ChannelInterface[]
     */
    private static function findChannel($root_dir, Event $Event) : array
    {
        // 在openpay模块内不应该添加处理渠道，有也只可能时单元测试用的文件
        if(rtrim($root_dir, DIRECTORY_SEPARATOR) == dirname(__DIR__)){
            return [];
        }

        $channels           = [];
        $paths              = [];
        try{
            $paths              = array_diff(scandir($root_dir), ['.', '..']);
        }catch(\ErrorException $e){
            /*
             * 可能由于目录权限等原因无法读取目录
             */
            print (string) $e;
        }
        foreach($paths AS $path){
            $path   = $root_dir . DIRECTORY_SEPARATOR . $path;
            if(is_dir($path)){
                $channels   = array_merge($channels, static::findChannel($path, $Event));
                continue;
            }
            $Event->getIO()->write("检查是否为接口渠道：" . $path);
            $php_bin                = 'php';
            if(isset($_SERVER['_'])){
                $php_bin            = $_SERVER['_'] == $_SERVER['SCRIPT_FILENAME'] ? 'php' : $_SERVER['_'];
            }elseif(isset($_SERVER['PHP_INI_DIR'])){
                $php_bin            = $_SERVER['PHP_INI_DIR'] == $_SERVER['SCRIPT_FILENAME'] ? 'php' : $_SERVER['PHP_INI_DIR'];
            }
            $check_channel_script   = __DIR__ . DIRECTORY_SEPARATOR . 'CheckIsChannelFile.php';
            $test_channel           = exec(addslashes("{$php_bin} {$check_channel_script} {$path}"));
            if(strpos($test_channel, '1:') === 0){
                $classname  = substr($test_channel, 2);
                if(class_exists($classname) && in_array(ChannelInterface::class, class_implements($classname))){
                    $channels[] = $classname;
                }
            }
        }
        return $channels;
    }
}