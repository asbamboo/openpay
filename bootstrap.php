<?php
/**
 * 聚合支付api接口如果文件
 *  - 通过访问url（'/'） 可以查看接口请求的说明文档
 */
use asbamboo\router\Router;
use asbamboo\router\RouteCollection;
use asbamboo\http\ServerRequest;
use asbamboo\router\Route;
use asbamboo\api\Controller;
use asbamboo\api\apiStore\ApiStore;
use asbamboo\di\Container;
use asbamboo\helper\env\Env AS EnvHelper;
use asbamboo\di\ServiceMappingCollection;
use asbamboo\api\apiStore\ApiRequestUris;
use asbamboo\api\apiStore\ApiRequestUri;
use asbamboo\openpay\Env;
use asbamboo\database\Factory;
use asbamboo\database\Connection;
use asbamboo\openpay\notify\v1_0\trade\PayNotify;
use asbamboo\openpay\notify\v1_0\trade\PayReturn;

/***************************************************************************************************
 * 系统服务容器
 ***************************************************************************************************/
$Container          = new Container(new ServiceMappingCollection());
/***************************************************************************************************/

/***************************************************************************************************
 * 读取项目自定义配置
 ***************************************************************************************************/
$guess_config_in_dir    = getcwd();
for($i = 0; $i < 3; $i++ ){
    $custom_config_path = $guess_config_in_dir . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'openpay-config.php';
    if(file_exists($custom_config_path)){
        break;
    }
    $guess_config_in_dir    = dirname($guess_config_in_dir);
}
if(file_exists($custom_config_path)){
    include $custom_config_path;
}
/***************************************************************************************************/

/***************************************************************************************************
 * 环境参数配置
 ***************************************************************************************************/
// 支付接口，接收第三方平台通知的URL
if(!EnvHelper::has(Env::TRADE_PAY_NOTIFY_URL)){
    EnvHelper::set(Env::TRADE_PAY_NOTIFY_URL, '/{channel}/notify');
}
// 支付接口，第三方平台页面跳转回聚合平台的URL
if(!EnvHelper::has(Env::TRADE_PAY_RETURN_URL)){
    EnvHelper::set(Env::TRADE_PAY_RETURN_URL, '/{channel}/return');
}
/***************************************************************************************************/

/***************************************************************************************************
 * 系统服务容器配置
 ***************************************************************************************************/
$RouteCollection    = new RouteCollection();
$Router             = new Router($RouteCollection);
$ApiStore           = new ApiStore('asbamboo\\openpay\\apiStore\\handler\\', __DIR__ . DIRECTORY_SEPARATOR . 'api-store' . DIRECTORY_SEPARATOR . 'handler');
$Request            = new ServerRequest();
$ApiController      = new Controller($ApiStore, $Request);
$Container->set('api-store', $ApiStore);
$Container->set('router', $Router);
$ApiController->setContainer($Container);
/***************************************************************************************************/

/***************************************************************************************************
 * api接口请求的地址(项目可能部署多种环境，在文档中列出的各个环境请求的url地址)
 ***************************************************************************************************/
if(!$Container->has('api-urls')){
    $ApiRequestUris     = new ApiRequestUris(new ApiRequestUri('http://' . ($_SERVER['HTTP_HOST'] ?? 'xxx')  . '/api', '演示请求地址'));
    $Container->set('api-urls', $ApiRequestUris);
}
/***************************************************************************************************/

/***************************************************************************************************
 * 数据库配置
 ***************************************************************************************************/
if(!$Container->has('db')){
    $DbFactory          = new Factory();
    $Container->set('db', $DbFactory);

    $sqpath             = __DIR__ . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'db.sqlite';
    $sqmetadata         = __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'entity';
    $sqmetadata_type    = Connection::MATADATA_YAML;
    $sqdir              = dirname($sqpath);

    if(!is_file($sqpath)){
        @mkdir($sqdir, 0644, true);
        @file_put_contents($sqpath, '');
    }
    $Container->get('db')->addConnection(Connection::create([
        'driver'    => 'pdo_sqlite',
        'path'      => $sqpath
    ], $sqmetadata, $sqmetadata_type));
}
/***************************************************************************************************/

/***************************************************************************************************
 * 路由配置
 ***************************************************************************************************/
$RouteCollection
// 文档
->add(new Route('doc', '/', [$ApiController, 'doc'], ['document_name'=>'Asbamboo Openpay API']))
// 接口处理
->add(new Route('api', '/api', [$ApiController, 'api']))
// 测试工具
->add(new Route('test', '/test', [$ApiController, 'testTool']))
// notify 这个 id在 trade.pay接口中生成url时需要使用到
->add(new Route('notify', EnvHelper::get(Env::TRADE_PAY_NOTIFY_URL), [$Container->get(PayNotify::class), 'exec']))
// return 这个 id在 trade.pay接口中生成url时需要使用到
->add(new Route('return', EnvHelper::get(Env::TRADE_PAY_RETURN_URL), [$Container->get(PayReturn::class), 'exec']))
;
/***************************************************************************************************/

/***************************************************************************************************
 * 响应客户端请求
 ***************************************************************************************************/
$Response   = $Router->matchRequest($Request);
$Response->send();
/***************************************************************************************************/