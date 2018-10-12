<?php
namespace asbamboo\openpay\payMethod\wxpay\builder\tool;

use asbamboo\http\StreamInterface;
use asbamboo\http\Stream;

/**
 * 处理生成请求http request body
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月11日
 */
trait BodyTrait
{
    /**
     * 传入一个数组生成stream body
     *
     * @param array $assign_data
     */
    public function body() : StreamInterface
    {
        $xml    = [];
        $xml[]  = "<xml>";
        foreach($this->RequestParams AS $key => $value){
            if(is_numeric($value)){
                $xml[]  = '<' . $key . '>' . $value . '</' . $key . '>';
            }else{
                $xml[]  = '<' . $key . '><![CDATA[' . $value . ']]></' . $key . '>';
            }
        }
        $xml[]  = '</xml>';
        $xml    = implode('', $xml);

        $Stream = new Stream('php://temp', 'w+b');
        $Stream->write($xml);
        return $Stream;
    }
}
