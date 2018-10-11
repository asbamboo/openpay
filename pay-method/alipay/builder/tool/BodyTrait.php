<?php
namespace asbamboo\openpay\payMethod\Alipay\builder\tool;

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
        $Stream = new Stream('php://temp', 'w+b');
        $Stream->write(http_build_query(['biz_content' => $this->assign_data['biz_content']]));
        return $Stream;
    }
}
