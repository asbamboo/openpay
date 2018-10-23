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

/***************************************************************************************************
 * 系统文件加载
 ***************************************************************************************************/
$autoload_bootstrap = dirname(__DIR__) . '/vendor/asbamboo/autoload/bootstrap.php';
if(file_exists($autoload_bootstrap)){
    $autoload   = require_once $autoload_bootstrap;
    $autoload->addMappingDir('asbamboo\\openpay\\', dirname(__DIR__));
}
require dirname(__DIR__) . '/phpqrcode/phpqrcode.php';
/***************************************************************************************************/


/***************************************************************************************************
 * 参数配置
***************************************************************************************************/
// 二维码生成的url
EnvHelper::set(Env::QRCODE_URL, '/code_url');

// 第三方支付平台相关的环境变量配置
// // 支付宝网关
// use asbamboo\openpayAlipay\Env AS AlipayEnv;
// EnvHelper::set(AlipayEnv::ALIPAY_GATEWAY_URI, 'https://openapi.alipaydev.com/gateway.do');
// // 自己生成支付宝rsa私银文件
// EnvHelper::set(AlipayEnv::ALIPAY_RSA_PRIVATE_KEY, dirname(__DIR__) . '/_test/fixtures/alipay-rsa/app_private_key.pem');
// // 支付宝生成支付宝rsa公银文件
// EnvHelper::set(AlipayEnv::ALIPAY_RSA_ALIPAY_KEY, dirname(__DIR__) . '/_test/fixtures/alipay-rsa/app_alipay_key.pem');
// // 支付宝app id
// EnvHelper::set(AlipayEnv::ALIPAY_APP_ID, '2016090900468991');
// // 支付宝扫码支付的notify url
// EnvHelper::set(AlipayEnv::ALIPAY_QRCD_NOTIFY_URL, 'http://example.org');

// // 微信网关
// use asbamboo\openpayWxpay\Env as WxpayEnv;
// EnvHelper::set(WxpayEnv::WXPAY_GATEWAY_URI, 'https://api.mch.weixin.qq.com/');
// // 微信加密使用的key值
// EnvHelper::set(WxpayEnv::WXPAY_SIGN_KEY, '8934e7d15453e97507ef794cf7b0519d');
// // 微信 appid
// EnvHelper::set(WxpayEnv::WXPAY_APP_ID, 'wx426b3015555a46be');
// // 微信商户号
// EnvHelper::set(WxpayEnv::WXPAY_MCH_ID, '1900009851');
// // 微信扫码支付的notify url
// EnvHelper::set(WxpayEnv::WXPAY_QRCD_NOTIFY_URL, 'http://example.org');
/***************************************************************************************************/


/***************************************************************************************************
 * 系统服务容器配置
 ***************************************************************************************************/
$Container          = new Container(new ServiceMappingCollection());
$ApiStore           = new ApiStore('asbamboo\\openpay\\apiStore\\handler\\', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'api-store' . DIRECTORY_SEPARATOR . 'handler');
$Request            = new ServerRequest();
$ApiController      = new Controller($ApiStore, $Request);
$RouteCollection    = new RouteCollection();
$Router             = new Router($RouteCollection);
$ApiRequestUris     = new ApiRequestUris(new ApiRequestUri('http://' . ($_SERVER['HTTP_HOST'] ?? 'xxx')  . '/api', '演示请求地址'));

$Container->set('api-urls', $ApiRequestUris);
$Container->set('api-store', $ApiStore);
$Container->set('router', $Router);
$ApiController->setContainer($Container);
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