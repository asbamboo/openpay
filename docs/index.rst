asbamboo/openpay
========================

asbamboo/openpay 是微信支付、支付宝支付等第三方支付接口的聚合接口。它能帮助开发者，以简单、快捷的方式接入第三方支付渠道。

如果开发者使用asbamboo/openpay提供的接口实现系统支付功能，在将来添加新的支付渠道时，他不需要修改已有的代码，他只需要安装（或者自行开发）该支付渠道的扩展。

**使用 asbamboo/openpay 需要php7.2的支持**

索引
---------------------------------------

#. `演示示例`_

#. `将asbamboo/openpay引入你的项目`_

#. `如何配置`_

#. `数据库设置`_

#. `支持的支付渠道`_

#. `如何开发新的渠道`_


支持的支付渠道
------------------------------

asbamboo/openpay 需要配合支付渠道代码库一起使用

============ ================= =================================
渠道名称      支持openpay版本     代码库                            
============ ================= =================================
支付宝支付      v1.0              `asbamboo/openpay-alipay`_      
微信支付        v1.0              `asbamboo/openpay-wxpay`_
============ ================= =================================

.. _将asbamboo/openpay引入你的项目: how_to_use_composer.rst
.. _演示示例: example
.. _asbamboo/openpay-alipay: https://github.com/asbamboo/openpay-alipay
.. _asbamboo/openpay-wxpay: https://github.com/asbamboo/openpay-wxpay
.. _如何配置: how_to_use_composer.rst
.. _数据库设置: 数据库设置
.. _如何开发新的渠道: 如何开发新的渠道