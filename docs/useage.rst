如何使用
=============================  

asbamboo/openpay 中通过 bootstrap.php 为 web 服务提供了5个可访问的url（入口文件后跟随一下5种path）：

#. / api接口帮助文档_

#. /api 各个接口请求与处理的url_

#. /test API接口调试工具_

#. {channel}/notify asbamboo/openpay 接收第三方的支付结果异步通知_

#. {channel}/return asbamboo/openpay 接收第三方的支付结果页面回跳通知_

api接口帮助文档
----------------------------

在浏览器中访问 http(s)://自定义host/(自定义入口文件)/， 页面将展示 asbamboo/openpay 的帮助文档。

帮助文档页面会展示，支持的接口的名称列表、接口的版本列表、接口的请求url列表、接口的请求与响应详情等信息。

例如，演示示例 `asbamboo/openpay-example`_ : http://demo.asbamboo.com/openpay-example/public/


各个接口请求与处理的url
-------------------------------

你可以完全按照 `asbamboo/openpay-example`_ 的方式，配置 asbamboo/openpay 服务。然后通过http(s)请求:

http(s)://xxxxx/(入口文件)/api 来处理支付、退款等请求。

每个接口的说明文档都有相应的请求示例，例如 trade.query 接口:

::

    curl http://demo.asbamboo.com/openpay-example/public/index.php/api \
    -d api_name=trade.query \
    -d format=json \
    -d in_trade_no= \
    -d out_trade_no=2018101310270023 \
    -d third_part=%7B%22limit_pay%22%3A%22no_credit%22%7D \
    -d version=v1.0 \


API接口调试工具
---------------------------

在浏览器中访问 http(s)://自定义host/(自定义入口文件)/test?api_name=API 名称, 进入API 名称的调试页面。

例如: http://demo.asbamboo.com/openpay-example/public/test?api_name=trade.pay

调试页面，可以用来输入既定的参数，查看应当的请求值和应当的响应值。

接收第三方的支付结果异步通知
-----------------------------------

asbamboo/openpay 中 url http(s)://自定义host/(自定义入口文件)/支付渠道名/notify 来处理接收到第三方支付渠道的支付结果的异步通知。

你不需要关心这个接口内部是如何工作的，这个 notify 处理结果会通过你请求 trade.pay 接口传入的 notify_url，向你发出异步通知。*异步通知的响应信息与trade.pay接口的响应信息中的data部分*。http://demo.asbamboo.com/openpay-example/public/index.php?api_name=trade.pay

你应该通过你请求trade.pay接口传入的notify_url对响应结果做对应的逻辑处理。

接收第三方的支付结果页面回跳通知
-----------------------------------------------------------------

asbamboo/openpay 中 url http(s)://自定义host/(自定义入口文件)/支付渠道名/return 来处理接收到第三方支付渠道的支付结果的页面回跳（同步）通知。

你不需要关心这个接口内部是如何工作的，这个 return 处理结果会通过你请求 trade.pay 接口传入的 reutrn_url，页面回跳到你的页面。*同步通知的响应信息与trade.pay接口的响应信息中的data部分*。http://demo.asbamboo.com/openpay-example/public/index.php?api_name=trade.pay

你应该通过你请求trade.pay接口传入的notify_url对响应结果做对应的逻辑处理。

.. _asbamboo/openpay-example: https://www.github.com/asbamboo/openpay-example