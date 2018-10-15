<?php

use asbamboo\event\EventScheduler;
use asbamboo\http\Event;
use asbamboo\http\ClientInterface;
use asbamboo\http\RequestInterface;
use asbamboo\http\ResponseInterface;
use asbamboo\helper\env\Env AS EnvHelper;
use asbamboo\openpay\Env;

/**
 * @var \asbamboo\autoload\Autoload $autoload
 */
$autoload   = include dirname(__DIR__) . '/vendor/asbamboo/autoload/bootstrap.php';
$autoload->addMappingDir('asbamboo\\openpay\\', dirname(__DIR__));


EnvHelper::set(Env::ALIPAY_GATEWAY_URI, 'https://openapi.alipaydev.com/gateway.do');
EnvHelper::set(Env::ALIPAY_RSA_PRIVATE_KEY, __DIR__ . '/fixtures/alipay-rsa/app_private_key.pem');
EnvHelper::set(Env::ALIPAY_RSA_ALIPAY_KEY, __DIR__ . '/fixtures/alipay-rsa/app_alipay_key.pem');
EnvHelper::set(Env::ALIPAY_APP_ID, '2016090900468991');


EnvHelper::set(Env::WXPAY_GATEWAY_URI, 'https://api.mch.weixin.qq.com/');
EnvHelper::set(Env::WXPAY_SIGN_KEY, '8934e7d15453e97507ef794cf7b0519d');
EnvHelper::set(Env::WXPAY_APP_ID, 'wx426b3015555a46be');
EnvHelper::set(Env::WXPAY_MCH_ID, '1900009851');


@mkdir(__DIR__ . '/cache/', 0744, true);

EventScheduler::instance()->bind(Event::HTTP_CLIENT_SEND_PRE_EXEC, function(
    ClientInterface $Client,
    /*Resource*/ $curl,
    RequestInterface $Request,
    ResponseInterface $Response
){
    file_put_contents(__DIR__ . '/cache/http_client_send_exec.log', '=======================================', FILE_APPEND | LOCK_EX);
    file_put_contents(__DIR__ . '/cache/http_client_send_exec.log', "\n", FILE_APPEND | LOCK_EX);
    file_put_contents(__DIR__ . '/cache/http_client_send_exec.log', date('Y-m-d H:i:s'), FILE_APPEND | LOCK_EX);
    file_put_contents(__DIR__ . '/cache/http_client_send_exec.log', "\n", FILE_APPEND | LOCK_EX);
    file_put_contents(__DIR__ . '/cache/http_client_send_exec.log', '---------------------------------------', FILE_APPEND | LOCK_EX);
    file_put_contents(__DIR__ . '/cache/http_client_send_exec.log', "\n", FILE_APPEND | LOCK_EX);
    file_put_contents(__DIR__ . '/cache/http_client_send_exec.log', var_export((string) $Request->getBody(), true), FILE_APPEND | LOCK_EX);
    file_put_contents(__DIR__ . '/cache/http_client_send_exec.log', "\n", FILE_APPEND | LOCK_EX);
});

EventScheduler::instance()->bind(Event::HTTP_CLIENT_SEND_AFTER_EXEC, function(
        ClientInterface $Client,
        /*Resource*/ $curl,
        RequestInterface $Request,
        ResponseInterface $Response
){
    file_put_contents(__DIR__ . '/cache/http_client_send_exec.log', "\n", FILE_APPEND | LOCK_EX);
    file_put_contents(__DIR__ . '/cache/http_client_send_exec.log', var_export((string) $Response->getBody(), true), FILE_APPEND | LOCK_EX);
    file_put_contents(__DIR__ . '/cache/http_client_send_exec.log', "\n", FILE_APPEND | LOCK_EX);
    file_put_contents(__DIR__ . '/cache/http_client_send_exec.log', var_export(curl_getinfo($curl), true), FILE_APPEND | LOCK_EX);
    file_put_contents(__DIR__ . '/cache/http_client_send_exec.log', "\n", FILE_APPEND | LOCK_EX);
    file_put_contents(__DIR__ . '/cache/http_client_send_exec.log', '=======================================', FILE_APPEND | LOCK_EX);
    file_put_contents(__DIR__ . '/cache/http_client_send_exec.log', "\n", FILE_APPEND | LOCK_EX);
});

return $autoload;