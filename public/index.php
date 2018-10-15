<?php
/**
 * 聚合支付api接口如果文件
 *  - 通过访问url（'/doc'） 可以查看接口请求的说明文档
 */
use asbamboo\router\Router;
use asbamboo\router\RouteCollection;
use asbamboo\http\ServerRequest;
use asbamboo\router\Route;
use asbamboo\api\Controller;
use asbamboo\api\apiStore\ApiStore;
use asbamboo\di\Container;
use asbamboo\openpay\Env;
use asbamboo\helper\env\Env AS EnvHelper;
use asbamboo\di\ServiceMappingCollection;
use asbamboo\api\apiStore\ApiRequestUris;
use asbamboo\api\apiStore\ApiRequestUri;
use asbamboo\openpay\Constant;
use asbamboo\http\Stream;
use asbamboo\http\Response;
use asbamboo\http\Constant AS HttpConstant;

/***************************************************************************************************
 * 自动加载
 ***************************************************************************************************/
$autoload   = require_once dirname(__DIR__) . '/vendor/asbamboo/autoload/bootstrap.php';
$autoload->addMappingDir('asbamboo\\openpay\\', dirname(__DIR__));
require dirname(__DIR__) . '/phpqrcode/phpqrcode.php';
/***************************************************************************************************/

/***************************************************************************************************
 * 参数配置
***************************************************************************************************/
// 支付宝网关
EnvHelper::set(Env::ALIPAY_GATEWAY_URI, 'https://openapi.alipaydev.com/gateway.do');
// 自己生成支付宝rsa私银文件
EnvHelper::set(Env::ALIPAY_RSA_PRIVATE_KEY, dirname(__DIR__) . '/_test/fixtures/alipay-rsa/app_private_key.pem');
// 自己生成支付宝rsa公银文件
EnvHelper::set(Env::ALIPAY_RSA_PUBLIC_KEY, dirname(__DIR__) . '/_test/fixtures/alipay-rsa/app_public_key.pem');
// 支付宝生成支付宝rsa公银文件
EnvHelper::set(Env::ALIPAY_RSA_ALIPAY_KEY, dirname(__DIR__) . '/_test/fixtures/alipay-rsa/app_alipay_key.pem');
// 支付宝app id
EnvHelper::set(Env::ALIPAY_APP_ID, '2016090900468991');

// 微信网关
EnvHelper::set(Env::WXPAY_GATEWAY_URI, 'https://api.mch.weixin.qq.com/');
// 微信加密使用的key值
EnvHelper::set(Env::WXPAY_SIGN_KEY, '8934e7d15453e97507ef794cf7b0519d');
// 微信 appid
EnvHelper::set(Env::WXPAY_APP_ID, 'wx426b3015555a46be');
// 微信商户号
EnvHelper::set(Env::WXPAY_MCH_ID, '1900009851');
/***************************************************************************************************/


/***************************************************************************************************
 * 程序处理
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

$RouteCollection
    ->add(new Route('doc', '/', [$ApiController, 'doc'], ['document_name'=>'Asbamboo Openpay API']))
    ->add(new Route('api', '/api', [$ApiController, 'api']))
    ->add(new Route('test', '/test', [$ApiController, 'testTool']))
    ->add(new Route('qrcode', Constant::QRCODE_URL, function($qr_code){
        $Stream = new Stream('php://temp', 'w+b');
        ob_start();
        QRcode::png($qr_code);
        $png    = ob_get_contents();
        ob_clean();
        $Stream->write($png);
        return new Response($Stream, HttpConstant::STATUS_OK, ['content-type' => 'image/png']);
    }))
;


$Response   = $Router->matchRequest($Request);
$Response->send();
/***************************************************************************************************/