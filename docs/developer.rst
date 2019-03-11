开发新的支付渠道
=============================

*说明*

* asbamboo\\openpay\\channel\\v1_0\\trade\\PayInterface 以下简称 PayInterface
* asbamboo\\openpay\\channel\\v1_0\\trade\\CancelInterface 以下简称 CancelInterface
* asbamboo\\openpay\\channel\\v1_0\\trade\\QueryInterface 以下简称 QueryInterface
* asbamboo\\openpay\\channel\\v1_0\\trade\\RefundInterface 以下简称 RefundInterface

你可以参考已经实现的 `第三方支付扩展`_ 进行开发。

新的第三方支付扩展开发的方式，是实现asbamboo\\openpay\\channel下的相应接口:

#. PayInterface 接入 `trade.pay`_ 接口，创建交易支付_
#. CancelInterface 接入 `trade.cancel`_ 接口，取消支付_
#. QueryInterface 接入 `trade.query`_ 接口，查询交易_
#. RefundInterface 接入 `trade.refund`_ 接口，创建退款_

创建交易支付
---------------------------

接入 `trade.pay`_ 接口，创建交易支付。你需要创建一个类实现 PayInterface规定的四个方法：

* PayInterface::execute 用于向第三方支付平台发起支付请求

    * 这个方法接收参数 asbamboo\\openpay\\channel\\v1_0\\trade\\payParameter\\Request 实例

        该实例具有如下属性:

        :channel: 支付渠道，使用getChannel方法取值。
        :title: 交易标题，使用getTitle方法取值。
        :in_trade_no: asbamboo\\openpay中的交易编号，全局唯一，使用getIntradeNo方法取值。
        :total_fee: 交易金额，单位为分，使用getTotalFee方法取值。
        :client_ip: 客户端IP，使用getClientIp方法取值。
        :third_part: json格式的第三方平台特殊专用的参数，使用getThirdPart方法取值。
        :notify_url: 异步通知url，使用getNotifyUrl方法取值。
        :return_url: 同步通知（页面跳转）url，使用getReturnUrl方法取值。
        
    * 这个方法返回值 asbamboo\\openpay\\channel\\v1_0\\trade\\payParameter\\Response 实例

        该实例具有如下属性:

        :type: 支付类型(1:扫码支付-顾客手机扫描商户，2:PC支付，3:H5支付，4:APP支付)，使用setType方法设置值。
        :qr_code: 二维码url，扫码支付-顾客手机扫描商户时必须设置值。使用setQrCode方法设置值。
        :app_pay_json: app支付第三方请求的参数，APP支付时必须设置值。使用setAppPayJson方法设置值。
        :redirect_url: 页面跳转url，PC、H5必须设置值。使用setRedirectUrl方法设置值。
        :redirect_data: 页面跳转携带参数，PC、H5必须设置值。使用setRedirectData方法设置值。
            
* PayInterface::notify 用于接收第三方支付平台的异步通知

    * 这个方法接收参数 asbamboo\\http\\ServerRequestInterface 实例

        你可以通过该实例对象的getRequestParams或getrequestParam('$param_name')，获取接收的请求值。

    * 这个方法返回值 asbamboo\\openpay\\channel\\v1_0\\trade\\payParameter\\NotifyResult 实例

        该实例具有如下属性:

        :trade_status: 交易状态, 取值范围见 `trade.pay`_ 接口文档，通过setTradeStatus方法设置值。
        :in_trade_no: asbamboo/openpay中的交易编号，通过setInTradeNo方法设置值。
        :third_trade_no: 第三方支付平台中的交易编号，通过setThirdTradeNo方法设置值。
        :third_part: 第三方支付平台完整的响应值，使用json格式，通过setThirdPart方法设置值。
        :response_success: 处理成功时应该返回给第三方的响应值，通过setResponseSuccess方法设置值。
        :response_failed: 处理失败时应该返回给第三方的响应值，通过setResponseFailed方法设置值。

* PayInterface::return 用于接收第三方支付平台的同步（页面跳转）通知

    这个方法与 PayInterface::notify 方法类似，参数一致。只是这个方法处理的时页面跳转的同步通知。

* PayInterface::supports 用于声明支持的支付渠道

    通过这个方法设置类支持的支付渠道。格式为: [渠道参数名=>渠道标签名][]

    如：['ALIPAY_PC'=>'支付宝PC支付']

取消支付
-----------------------

接入 `trade.cancel`_ 接口，取消支付。你需要创建一个类实现 CancelInterface 规定的两个方法：

* CancelInterface::execute 用于向第三方支付平台发起取消支付请求

    * 这个方法接收参数 asbamboo\\openpay\\channel\\v1_0\\trade\\cancelParameter\\Request 实例

        该实例具有如下属性:

        :channel: 支付渠道，使用getChannel方法取值。
        :in_trade_no: asbamboo\\openpay中的交易编号，全局唯一，使用getIntradeNo方法取值。
        :third_part: json格式的第三方平台特殊专用的参数，使用getThirdPart方法取值。

    * 这个方法返回值 asbamboo\\openpay\\channel\\v1_0\\trade\\cancelParameter\\Response 实例

        该实例具有如下属性:

        :in_trade_no: asbamboo/openpay中的交易编号，通过setInTradeNo方法设置值。
        :is_success: 是否取消成功，通过setIsSuccess方法设置值。

* PayInterface::supports 用于声明支持的支付渠道

    通过这个方法设置类支持的支付渠道。格式为: [渠道参数名=>渠道标签名][]

    如：['ALIPAY_QRCD'=>'支付宝扫码', 'ALIPAY_PC'=>'支付宝PC支付']

查询交易
----------------------

接入 `trade.query`_ 接口，查询交易。你需要创建一个类实现 QueryInterface 规定的两个方法：

* QueryInterface::execute 用于向第三方支付平台发起交易查询请求

    * 这个方法接收参数 asbamboo\\openpay\\channel\\v1_0\\trade\\queryParameter\\Request 实例

        该实例具有如下属性:

        :channel: 支付渠道，使用getChannel方法取值。
        :in_trade_no: asbamboo\\openpay中的交易编号，全局唯一，使用getIntradeNo方法取值。
        :third_part: json格式的第三方平台特殊专用的参数，使用getThirdPart方法取值。

    * 这个方法返回值 asbamboo\\openpay\\channel\\v1_0\\trade\\queryParameter\\Response 实例

        该实例具有如下属性:

        :trade_status: 交易状态, 取值范围见 `trade.pay`_ 接口文档，通过setTradeStatus方法设置值。
        :in_trade_no: asbamboo/openpay中的交易编号，通过setInTradeNo方法设置值。
        :third_trade_no: 第三方支付平台中的交易编号，通过setThirdTradeNo方法设置值。

* QueryInterface::supports 用于声明支持的支付渠道

    通过这个方法设置类支持的支付渠道。格式为: [渠道参数名=>渠道标签名][]

    如：['ALIPAY_QRCD'=>'支付宝扫码', 'ALIPAY_PC'=>'支付宝PC支付']


创建退款
----------------

接入 `trade.refund`_ 接口，创建退款。你需要创建一个类实现 RefundInterface 规定的两个方法：

* RefundInterface::execute 用于向第三方支付平台发起退款请求

    * 这个方法接收参数 asbamboo\\openpay\\channel\\v1_0\\trade\\refundParameter\\Request 实例

        该实例具有如下属性:

        :channel: 支付渠道，使用getChannel方法取值。
        :in_trade_no: asbamboo\\openpay中的交易编号，全局唯一，使用getIntradeNo方法取值。
        :trade_pay_fee: 订单支付总金额 单位是分, 使用getTradePayFee方法取值。
        :in_refund_no: asbamboo\\openpay中的退款编号，全局唯一，使用getInRefundNo方法取值。
        :refund_fee: 退款金额 单位是分，使用getRefundFee方法取值。
        :third_part: json格式的第三方平台特殊专用的参数，使用getThirdPart方法取值。

    * 这个方法返回值 asbamboo\\openpay\\channel\\v1_0\\trade\\refundParameter\\Response 实例

        该实例具有如下属性:

        :in_refund_no: asbamboo\\openpay中的退款编号，全局唯一，使用setInRefundNo方法设置值。
        :refund_fee: 退款金额 单位是分，使用setRefundFee方法设置值。
        :is_success: 是否退款成功，通过setIsSuccess方法设置值。
        :pay_ymdhis: 退款支付时间，通过setPayYmdhis方法设置值。

* RefundInterface::supports 用于声明支持的支付渠道

    通过这个方法设置类支持的支付渠道。格式为: [渠道参数名=>渠道标签名][]

    如：['ALIPAY_QRCD'=>'支付宝扫码', 'ALIPAY_PC'=>'支付宝PC支付']
    
.. _第三方支付扩展: payment.rst
.. _trade.pay: http://demo.asbamboo.com/openpay-example/public/?api_name=trade.pay
.. _trade.cancel: http://demo.asbamboo.com/openpay-example/public/?api_name=trade.cancel
.. _trade.query: http://demo.asbamboo.com/openpay-example/public/?api_name=trade.query
.. _trade.refund: http://demo.asbamboo.com/openpay-example/public/?api_name=trade.refund