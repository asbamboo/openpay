<?php
namespace asbamboo\openpay\common\traits;
use asbamboo\http\StreamInterface;
use asbamboo\http\Stream;

/**
 * 用于BuilderInterface实例生成RequestInterface时获取body参数
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月9日
 */
trait MakeRequestBodyTrait
{
    /**
     * 传入一个数组生成stream body
     *
     * @param array $assign_data
     */
    public function makeStream(array $assign_data = []) : StreamInterface
    {
        $Stream = new Stream('php://temp', 'w+b');
        $stream_data    = [];
        foreach($assign_data AS $key => $value){
            $stream_data[]  = $key . '=' . urlencode($value);
        }
        $Stream->write(implode('&', $stream_data));
        return $Stream;
    }
}