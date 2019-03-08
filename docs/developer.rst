开发新的支付渠道
=============================

简单来说,目前1.0版本，开发新的第三方支付扩展只需要实现openpay中的四个interface接口。

这四个接口实现的实现说明请参考这几个接口中的注释，和已经开发好的扩展如（asbamboo\openpay-alipay）

* asbamboo\\openpay\\channel\\v1_0\\trade\\PayInterface.php 创建交易
* asbamboo\\openpay\\channel\\v1_0\\trade\\CancelInterface 取消支付
* asbamboo\\openpay\\channel\\v1_0\\trade\\QueryInterface 查询交易
* asbamboo\\openpay\\channel\\v1_0\\trade\\RefundInterface 创建退款