将asbamboo/openpay引入你的项目
=====================================

你需要通过 `composer`_ 管理项目的依赖，可以参考 `asbamboo/openpay-example`_ (这是一个简单且完整的asbamboo/openpay服务端接口程序)。

在你的项目根目录下创建或修改composer.json文件
-----------------------------------------------------

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
    
* require：添加是依赖的 `支付渠道库`_

* scripts：添加生成openpay渠道映射文件

composer.json 文件修改后需要执行 composer update 使之生效

::

    composer update

引入bootstrap.php文件
------------------------------------------------------------

以 `asbamboo/openpay-example`_ 为例（那是一个单一web入口的系统）。在入口文件 public/index.php 中引入了bootstap.php文件

::

    <?php
    
    /**
     * 在当前目录执行 php -S 127.0.0.1:8001后浏览器可以运行 http://127.0.0.1:8001
     */
    require_once dirname(__DIR__) . '/vendor/autoload.php';
    
    /***************************************************************************************************
     * 启动openpay引导程序
     ***************************************************************************************************/
    require dirname(__DIR__) . '/vendor/asbamboo/openpay/bootstrap.php';
    /***************************************************************************************************/

配置
-------------------------------------------------------

cli-config.php

open-config

.. _composer: https://getcomposer.org

.. _asbamboo/openpay-example: https://github.com/asbamboo/openpay-example

.. _支付渠道库: payment.rst
