asbamboo/openpay
=============================

#. 简介_

#. 支付渠道_

#. 如何使用_

#. `开发新的支付渠道`_

#. 演示_

简介
-------------------------

asbamboo/openpay 是微信支付、支付宝支付等第三方支付接口的聚合接口。它能帮助开发者，以最简单、最快捷的方式接入第三方支付渠道。

如果开发者使用asbamboo/openpay提供的接口实现系统支付功能，在将来添加新的支付渠道时，他不需要修改已有的代码，他只需要安装（或者自行开发）该支付渠道的扩展。

支付渠道
------------------------------

asbamboo/openpay 需要配合支付渠道代码库一起使用

============ ================= =================================
渠道名称      支持openpay版本     代码库                            
============ ================= =================================
支付宝支付      v1.0              `asbamboo/openpay-alipay`_      
微信支付        v1.0              `asbamboo/openpay-wxpay`_
============ ================= =================================


如何使用
-----------------

你需要通过 `composer`_ 管理项目的依赖，可以参考 `asbamboo/openpay-example`_ (这是一个简单且完整的asbamboo/openpay服务端接口程序)。

#. 在项目根目录下创建composer.json文件，配置你的程序支持的支付渠道：

    * composer require 中添加依赖的支付渠道库
    * composer scripts 中添加 asbamboo\\openpay\\script\\Channel::generateMappingInfo
    
    ::
    
        {
            ...
            
            "require": {
                ...
                 
                "asbamboo/openpay-wxpay": "^1.0",
                "asbamboo/openpay-alipay": "^1.0"
    
                ...
            },
            "scripts": {
                "openpay-scripts": [
                    "asbamboo\\openpay\\script\\Channel::generateMappingInfo"
                ],
                "post-install-cmd": [
                    "@openpay-scripts"
                ],
                "post-update-cmd": [
                    "@openpay-scripts"
                ]
            },
            
            ...
        }
    
#. 在项目根目录下执行composer install安装项目依赖的代码库

    ::

        php composer install

#. `安装数据库`_

#. `创建web入口`_

#. 运行测试

   进入web跟目录运行

   ::
   
       php -S 127.0.0.1:8000

演示
----------------------

演示地址: http://demo.asbamboo.com/openpay-example

.. _composer: https://getcomposer.org
.. _asbamboo/openpay-alipay: https://github.com/asbamboo/openpay-alipay
.. _asbamboo/openpay-wxpay: https://github.com/asbamboo/openpay-wxpay
.. _asbamboo/openpay-example: https://github.com/asbamboo/openpay-example
.. _开发新的支付渠道: 准备添加doc
.. _安装数据库: 安装数据库
.. _创建web入口: 创建web入口 