<?php
namespace asbamboo\openpay\_test\payMethod\alipay\builder;

use PHPUnit\Framework\TestCase;
use asbamboo\openpay\Client;
use asbamboo\openpay\payMethod\alipay\response\TradePrecreateResponse;

/**
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月12日
 */
class TradePrecreateTest extends TestCase
{
    public function testMain()
    {
        $Client             = new Client($_ENV['ALIPAY_GATEWAY_URI']);
        $Response           = $Client->request('alipay:TradePrecreate', [
            'app_id'        => (string) $_ENV['ALIPAY_APP_ID'],
            'out_trade_no'  => (string) date('YmdHis') . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT),
            'total_amount'  => (string) rand(0, 9999),
            'subject'       => 'testmain' . uniqid(),
        ]);
//         var_dump($Response);
        $this->assertEquals(TradePrecreateResponse::CODE_SUCCESS, $Response->get('code'));
    }
}
