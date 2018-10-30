<?php
namespace asbamboo\openpay\channel\v1_0\trade\payParameter;

/**
 * 渠道处理方法处理请求后应该返回的结果
 * 
 * @author 李春寅<licy2013@aliyun.com>
 * @since 2018年10月30日
 */
final class Response
{
    /**
     * 是否需要页面跳转
     *  - 比如扫二维码支付,应该返回true
     * 
     * @var string
     */
    public $is_redirect = false;
    
    /**
     * 二维码url
     *  - 属于二维码支付时应该不为空
     * 
     * @var string
     */
    public $qr_code = '';
}
