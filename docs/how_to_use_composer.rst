将asbamboo/openpay引入你的项目
=====================================

你需要通过 `composer`_ 管理项目的依赖，可以参考 `asbamboo/openpay-example`_ (这是一个简单且完整的asbamboo/openpay服务端接口程序)。

在你的项目根目录下创建或修改composer.json文件

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

    * require：添加是依赖的支付渠道库
    * scripts：添加生成openpay渠道映射文件

.. _composer: https://getcomposer.org
.. _asbamboo/openpay-example: https://github.com/asbamboo/openpay-example