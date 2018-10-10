<?php
namespace asbamboo\openpay\_test\payment\alipay\builder;

use PHPUnit\Framework\TestCase;
use asbamboo\openpay\Client;

/**
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月10日
 */
class TradeCreateTest extends TestCase
{
    public function testMain()
    {
        $Client             = new Client($_ENV['ALIPAY_GATEWAY_URI']);
        $Response           = $Client->request('alipay:TradeCreate', [
            'app_id'        => $_ENV['ALIPAY_APP_ID'],
            'out_trade_no'  => date('YmdHis') . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT),
            'total_amount'  => rand(0, 99999999),
            'title'         => 'test-main' . uniqid(),
        ]);
        var_dump((string)$Response->getBody());
//         exit;
    }
}