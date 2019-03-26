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

        $channels       = [];
        $channel_dirs   = static::getChannelDirs($Event);
        foreach($channel_dirs AS $channel_dir){
            $Event->getIO()->write('当前查找渠道处理类的文件目录:' . $channel_dir);
            $channels       = array_merge($channels, static::findChannel($channel_dir, $Event));
        }
        $Event->getIO()->write('找出的渠道信息:' . var_export($channels, true));
        $ChannelMapping = new ChannelMapping();
        $ChannelMapping->resetMappingContent();
        $ChannelMapping->addMappingChannels($channels);
        $Event->getIO()->write('接口与渠道映射关系:' . var_export($ChannelMapping->getMappingContent(), true));
    }

    /**
     * 支付渠道代码库所在的文件目录
     *  - 通过composer 的 extra["asbamboo-openpay-channel"] 配置信息查找。
     *  - extra["asbamboo-openpay-channel"]是支付渠道代码库目录的相对路径，如果在当前项目根目录找不到该路径的话，尝试在项目的vendor目录中查找。
     *  - 如果 extra["asbamboo-openpay-channel"] 的目录没有找到将会抛出异常。
     *  - 如果composer.json没有配置extra["asbamboo-openpay-channel"]，那返回项目根目录。
     *
     * @param Event $Event
     * @return array
     */
    private static function getChannelDirs(Event $Event) : array
    {
        /**
         * init result
         * @var array $channel_dirs
         */
        $channel_dirs   = [];

        /**
         * logic
         *
         * @var string $vendor_dir
         */
        $vendor_dir     = $Event->getComposer()->getConfig()->get('vendor-dir');
        $root_dir       = getcwd();
        $extra          = $Event->getComposer()->getPackage()->getExtra();
        if(isset($extra['asbamboo-openpay-channel'])){
            foreach((array)$extra['asbamboo-openpay-channel'] AS $test_dir){
                $test_dir   = trim($test_dir, DIRECTORY_SEPARATOR);
                if(is_dir( $root_dir . DIRECTORY_SEPARATOR . $test_dir )){
                    $channel_dirs[] = $root_dir . DIRECTORY_SEPARATOR . $test_dir;
                }elseif(is_dir( $vendor_dir . DIRECTORY_SEPARATOR . $test_dir )){
                    $channel_dirs[] = $vendor_dir . DIRECTORY_SEPARATOR . $test_dir;
                }else{
                    throw new \InvalidArgumentException("无效的 asbamboo-openpay-channel 参数。");
                }
            }
        }else{
            $channel_dirs[] = $root_dir;
        }

        /**
         * return
         */
        return $channel_dirs;
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