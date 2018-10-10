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
            'app_id'        => (string) $_ENV['ALIPAY_APP_ID'],
            'out_trade_no'  => (string) date('YmdHis') . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT),
            'total_amount'  => (string) rand(0, 9999),
            'title'         => 'testmain',
        ]);
        $b = (string)$Response->getBody();
        var_dump($b);
        var_dump(json_decode($Response->getBody()->getContents(), true));
//         exit;
    }
}