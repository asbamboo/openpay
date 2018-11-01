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
use asbamboo\http\Stream;
use asbamboo\http\Response;
use asbamboo\http\Constant AS HttpConstant;
use asbamboo\openpay\Env;
use asbamboo\database\Factory;
use asbamboo\database\Connection;
use asbamboo\openpay\notify\v1_0\trade\PayNotify;

/***************************************************************************************************
 * 系统文件加载
 ***************************************************************************************************/
require __DIR__ . '/phpqrcode/phpqrcode.php';
/***************************************************************************************************/

/***************************************************************************************************
 * 参数配置
 ***************************************************************************************************/
// 二维码生成的url
EnvHelper::set(Env::QRCODE_URL, '/code_url');
EnvHelper::set(Env::TRADE_PAY_NOTIFY_URL, '/{channel}/notify');
/***************************************************************************************************/

/***************************************************************************************************
 * 系统服务容器配置
 ***************************************************************************************************/
$Container          = new Container(new ServiceMappingCollection());
$ApiStore           = new ApiStore('asbamboo\\openpay\\apiStore\\handler\\', __DIR__ . DIRECTORY_SEPARATOR . 'api-store' . DIRECTORY_SEPARATOR . 'handler');
$Request            = new ServerRequest();
$ApiController      = new Controller($ApiStore, $Request);
$RouteCollection    = new RouteCollection();
$Router             = new Router($RouteCollection);
$ApiRequestUris     = new ApiRequestUris(new ApiRequestUri('http://' . ($_SERVER['HTTP_HOST'] ?? 'xxx')  . '/api', '演示请求地址'));
$DbFactory          = new Factory();

$Container->set('api-urls', $ApiRequestUris);
$Container->set('api-store', $ApiStore);
$Container->set('router', $Router);
$Container->set('db', $DbFactory);
$ApiController->setContainer($Container);
/***************************************************************************************************/

/***************************************************************************************************
 * 数据库配置
 ***************************************************************************************************/
$sqpath             = __DIR__ . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'db.sqlite';
$sqmetadata         = __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database';
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
// 二维码生成
->add(new Route('qrcode', EnvHelper::get(Env::QRCODE_URL), function($qr_code){
    $Stream = new Stream('php://temp', 'w+b');
    ob_start();
    QRcode::png($qr_code);
    $png    = ob_get_contents();
    ob_clean();
    $Stream->write($png);
    return new Response($Stream, HttpConstant::STATUS_OK, ['content-type' => 'image/png']);
}))
;
/***************************************************************************************************/


/***************************************************************************************************
 * 响应客户端请求
 ***************************************************************************************************/
$Response   = $Router->matchRequest($Request);
$Response->send();
/***************************************************************************************************/