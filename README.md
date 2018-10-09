# openpay
聚合支付模块，集成alipay/微信支付等第三方支付接口

* 如何使用
    <?php
        use asbamboo\openpay\Factory;
        $builder    = Factory::createBuilder('alipay.trade_create_sandbox');
        $builder->assignData([
            'trade_no'      => 'test_no',
            'total_amount'  => '99.99',
        ]);
        $Request    = $builder->create();
        $Response   = Factory::sendRequest($Request);