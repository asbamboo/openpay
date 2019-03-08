安装 asbamboo/openpay
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

安装数据库
--------------------------------------------------------

asbamboo/openpay 数据库操作依赖 `asbamboo/database`_ 模块。 `asbamboo/database`_ 是基于 `doctrione/orm`_ 开发的。 支持的数据库有Mysql、MariaDB、Oracle、Microsoft SQL Server、PostgreSQL、SAP Sybase SQL Anywhere、SQLite。

你需要在你的项目根目录下创建 config/db-config.php 文件, 配置你的数据库信息, 该文件返回一个 asbamboo\\database\\FactoryInterface 实例。

vendor/asbamboo/openpay/config/database/entity 目录中提供了 yaml 类型的 Entity ORM 关系映射文件。

* SQLite 配置示例:

    ::

        <?php
            use asbamboo\database\Connection;
            use asbamboo\database\Factory;

            // SQLite 数据存储文件
            $sqpath             = dirname(__DIR__) . '/var/data/db.sqlite;

            // Entity ORM 映射关系目录
            $sqmetadata         = dirname(__DIR__) . '/vendor/asbamboo/openpay/config/database/entity';
            
            // Entity ORM 映射关系文件类型
            $sqmetadata_type    = Connection::MATADATA_YAML;

            // 如果数据存贮文件还没有的话，创建它。
            $sqdir              = dirname($sqpath);
            if(!is_file($sqpath)){
                @mkdir($sqdir, 0755, true);
                @file_put_contents($sqpath, '');
            }

            // 返回一个 asbamboo\database\FactoryInterface 实例
            $DbFactory          = new Factory();
            $DbFactory->addConnection(Connection::create([
                'driver'    => 'pdo_sqlite',
                'path'      => $sqpath
            ], $sqmetadata, $sqmetadata_type));
            return $DbFactory;

* Mysql 配置示例:

    ::

        <?php
            use asbamboo\database\Connection;
            use asbamboo\database\Factory;

            // SQLite 数据存储文件
            $sqpath             = dirname(__DIR__) . '/var/data/db.sqlite;

            // Entity ORM 映射关系目录
            $sqmetadata         = dirname(__DIR__) . '/vendor/asbamboo/openpay/config/database/entity';
            
            // Entity ORM 映射关系文件类型
            $sqmetadata_type    = Connection::MATADATA_YAML;

            // Mysql 数据库链接信息
            $connection = [
                 'driver'    => "pdo_mysql",
                 'host'      => "XXXXXX",    // 如: 127.0.0.1
                 'dbname'    => "XXXXXXXXX", // asbamboo
                 'user'      => "XXXXXXXXX", // root
                 'password'  => "XXXXXXXXX", // rootpwd
                 'charset'   => "XXXXXXXXX", // utf8
             ];
            
            // 返回一个 asbamboo\database\FactoryInterface 实例
            $DbFactory          = new Factory();
            $DbFactory->addConnection(Connection::create($connection, $sqmetadata, $sqmetadata_type));
            return $DbFactory;

创建了配置文件后在项目根目录运行./vendor/bin/doctrine orm:schema-tool:create完成数据库安装

::

    licy@licy-N501JW:/www/openpay-example$ ./vendor/bin/doctrine orm:schema-tool:create
    
     !
     ! [CAUTION] This operation should not be executed in a production environment!
     !
    
     Creating database schema...
    
    
     [OK] Database schema created successfully!


参数配置
--------------------------------------------------------

你需要在你的项目根目录下创建 config/openpay-config.php 文件, 来配置 asbamboo/openpay 在处理第三方请求参数时的一些必要变量（如秘钥生成的secret, app_id, 请求url）和 将数据库配置db-config.php 返回的 asbamboo\\database\\FactoryInterface 实例加入到$Container 中。

* 支付渠道相关的配置信息:

    支付渠道该如何配置取决于支付渠道，你应该阅读相关支付渠道的配置说明:

    如 `asbamboo/openpay-alipay`_ 相关的配置信息:

    ::

        <?php
            use asbamboo\helper\env\Env AS EnvHelper;
            use asbamboo\openpayAlipay\Env AS AlipayEnv;
            ...
            
            /***************************************************************************************************
             * 环境参数配置
             ***************************************************************************************************/
            // 支付宝网关
            EnvHelper::set(AlipayEnv::ALIPAY_GATEWAY_URI, 'https://openapi.alipaydev.com/gateway.do');
            // 自己生成支付宝rsa私银文件
            EnvHelper::set(AlipayEnv::ALIPAY_RSA_PRIVATE_KEY, dirname(__DIR__) . '/alipay-rsa/app_private_key.pem');
            // 支付宝生成支付宝rsa公银文件
            EnvHelper::set(AlipayEnv::ALIPAY_RSA_ALIPAY_KEY, dirname(__DIR__) . '/alipay-rsa/app_alipay_key.pem');
            // 支付宝app id
            EnvHelper::set(AlipayEnv::ALIPAY_APP_ID, '2016090900468991');
            
            ...
            /***************************************************************************************************/
            ...

* 数据库配置

    ::

        <?php

        ...

        /***************************************************************************************************
         * 数据库配置
         ***************************************************************************************************/
        if(!$Container->has('db')){
            $DbFactory  = require __DIR__ . DIRECTORY_SEPARATOR . 'db-config.php';
            $Container->set('db', $DbFactory);
        }
        /***************************************************************************************************/
        
.. _composer: https://getcomposer.org

.. _asbamboo/openpay-example: https://github.com/asbamboo/openpay-example

.. _asbamboo/database: https://github.com/asbamboo/database

.. _doctrione/orm: https://github.com/doctrine/orm

.. _asbamboo/openpay-alipay: https://github.com/asbamboo/openpay-alipay

.. _支付渠道库: payment.rst
