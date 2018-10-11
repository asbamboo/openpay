<?php
namespace asbamboo\openpay\payMethod\Alipay\builder\tool;

use asbamboo\http\UriInterface;

/**
 * 处理生成请求http request uri
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月11日
 */
trait UriTrait
{
    /**
     *
     * @return UriInterface
     */
    public function uri() : UriInterface
    {
        $query_data   = $this->assign_data;
        unset($query_data['biz_content']);
        return $this->getGateway()->withQuery(http_build_query($query_data));
    }
}
