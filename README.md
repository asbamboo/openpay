# asbamboo\openpay

asbamboo\openpay是一个聚合支付工具，它实现了通过一个统一的接口去调用第三方支付接口(如微信、支付宝等)。

你只需要围绕asbamboo\openpay提供的接口，实现支付程序。然后安装你需要的第三方支付扩展，就能实现多种支付渠道的支付对接。

将来你需要添加新的支付渠道时，你不需要修改你已有的代码，你只需要安装(或者时自行开发)你需要的扩展就可以。

## 如何使用

可以参考 asbamboo\openepay-example 创建 asbamboo\openpay的web程序。

第一步创建composer.json, 声明聚合支付项目依赖的扩展。

注意: 必须配置scripts,post-install-cmd和post-update-cmd将asbamboo\\openpay\\script\\Channel::generateMappingInfo脚本配置为composer install/update 执行后的运行脚本

如,asbamboo\openepay-example的composer.json例子中：安装了asbamboo/openpay-wxpay和asbamboo/openpay-alipay两个扩展
```
{
	"name" : "asbamboo/openpay-example",
	"description" : "聚合支付使用示例",
	"type" : "project",
	"require" : {
		"php" : "^7.2",
		"asbamboo/autoload" : "^1.0",
		"asbamboo/helper" : "^1.0",
		"asbamboo/openpay-wxpay" : "^1.0",
		"asbamboo/openpay-alipay" : "^1.0"
	},
	"require-dev" : {
		"phpunit/phpunit" : "^7.2"
	},
	"scripts" : {
		"openpay-scripts" : "asbamboo\\openpay\\script\\Channel::generateMappingInfo",
		"post-install-cmd" : "@openpay-scripts",
		"post-update-cmd" : "@openpay-scripts"
	},
	"authors" : [{
			"name" : "李春寅",
			"email" : "licy2013@aliyun.com"
		}
	],
	"license" : "BSD-3-Clause",
	"minimum-stability" : "dev"
}
```

第二步 执行composer install将相关的支付渠道下载到项目中。

第三步 创建web入口文件

web入口文件是一个通过http请求访问的入口。这个入口文件需要载入asbamboo\openpay中的bootsstrap程序，和配置第三方支付渠道扩展需要的参数。

如asbamboo\openepay-example的入口文件：
```
<?php
use asbamboo\helper\env\Env AS EnvHelper;
use asbamboo\openpayAlipay\Env AS AlipayEnv;
use asbamboo\openpayWxpay\Env as WxpayEnv;
/***************************************************************************************************
 * 系统文件加载
 ***************************************************************************************************/
require_once dirname(__DIR__) . '/vendor/autoload.php';
/***************************************************************************************************/

/***************************************************************************************************
 * 参数配置
***************************************************************************************************/
// 支付宝网关
EnvHelper::set(AlipayEnv::ALIPAY_GATEWAY_URI, 'https://openapi.alipaydev.com/gateway.do');
// 自己生成支付宝rsa私银文件
EnvHelper::set(AlipayEnv::ALIPAY_RSA_PRIVATE_KEY, dirname(__DIR__) . '/alipay-rsa/app_private_key.pem');
// 支付宝生成支付宝rsa公银文件
EnvHelper::set(AlipayEnv::ALIPAY_RSA_ALIPAY_KEY, dirname(__DIR__) . '/alipay-rsa/app_alipay_key.pem');
// 支付宝app id
EnvHelper::set(AlipayEnv::ALIPAY_APP_ID, '2016090900468991');
// 支付宝扫码支付的notify url
EnvHelper::set(AlipayEnv::ALIPAY_QRCD_NOTIFY_URL, 'http://example.org');

// 微信网关
EnvHelper::set(WxpayEnv::WXPAY_GATEWAY_URI, 'https://api.mch.weixin.qq.com/');
// 微信加密使用的key值
EnvHelper::set(WxpayEnv::WXPAY_SIGN_KEY, '8934e7d15453e97507ef794cf7b0519d');
// 微信 appid
EnvHelper::set(WxpayEnv::WXPAY_APP_ID, 'wx426b3015555a46be');
// 微信商户号
EnvHelper::set(WxpayEnv::WXPAY_MCH_ID, '1900009851');
// 微信扫码支付的notify url
EnvHelper::set(WxpayEnv::WXPAY_QRCD_NOTIFY_URL, 'http://example.org');
/***************************************************************************************************/

/***************************************************************************************************
 * 启动openpay引导程序
 ***************************************************************************************************/
require dirname(__DIR__) . '/vendor/asbamboo/openpay/bootstrap.php';
/***************************************************************************************************/
```

第四步 启动web程序

在浏览器输入相关的url 127.0.0.1:8000 可以查看调用接口的文档和测试接口的调用

如asbamboo\openepay-example：进入public目录执行
```
php -S 127.0.0.1:8000
```

## 可使用的第三方支付扩展

第三方 | 支持的接口版本 | Composer代码库 | 相关文档
--- | --- | --- | ---
支付宝 | 1.0 | asbamboo\openpay-alipay | [asbamboo/openpay-alipay](https://github.com/asbamboo/openpay-alipay)
微信 | 1.0 | asbamboo\openpay-wxpay | [asbamboo/openpay-wxpay](https://github.com/asbamboo/openpay-wxpay)

## 开发新的扩展

简单来说,目前1.0版本，开发新的第三方支付扩展只需要实现openpay中的四个interface接口。

这四个接口实现的实现说明请参考这几个接口中的注释，和已经开发好的扩展如（asbamboo\openpay-alipay）

* asbamboo\openpay\channel\v1_0\trade\PayInterface.php 创建交易
* asbamboo\openpay\channel\v1_0\trade\CancelInterface 取消支付
* asbamboo\openpay\channel\v1_0\trade\QueryInterface 查询交易
* asbamboo\openpay\channel\v1_0\trade\RefundInterface 创建退款

## 报告bug或与我交流

加我请备注:asbamboo项目交流

* 我的QQ号: 787211820
* 我的微信号: lichunyin860302
* 邮箱：licy2013#aliyun.com 
