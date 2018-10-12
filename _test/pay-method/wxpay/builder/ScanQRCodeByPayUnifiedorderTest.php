<?php
namespace asbamboo\openpay\_test\payMethod\wxpay\builder;

use PHPUnit\Framework\TestCase;
use asbamboo\openpay\Client;
use asbamboo\openpay\payMethod\wxpay\response\ScanQRCodeByPayUnifiedorderResponse;

/**
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月12日
 */
class ScanQRCodeByPayUnifiedorderTest extends TestCase
{
    public function testMain()
    {
        $Client                 = new Client($_ENV['WXPAY_GATEWAY_URI']);
        $Response               = $Client->request('wxpay:ScanQRCodeByPayUnifiedorder', [
            'appid'             => (string) $_ENV['WXPAY_APP_ID'],
            'mch_id'            => (string) $_ENV['WXPAY_MCH_ID'],
            'body'              => 'testmain' . uniqid(),
            'out_trade_no'      => (string) date('YmdHis') . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT),
            'total_fee'         => (string) rand(0, 9999),
            'spbill_create_ip'  => (string) '123.12.12.123',
            'notify_url'        => (string) 'http://www.weixin.qq.com/wxpay/pay.php',
            'trade_type'        => (string) 'NATIVE',
        ]);

        $this->assertEquals(ScanQRCodeByPayUnifiedorderResponse::RETURN_CODE_SUCCESS, $Response->get('return_code'));
        $this->assertEquals(ScanQRCodeByPayUnifiedorderResponse::RESULT_CODE_SUCCESS, $Response->get('result_code'));
    }
}