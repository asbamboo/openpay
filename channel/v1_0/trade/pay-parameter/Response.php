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
    private $is_redirect = false;

    /**
     * 二维码url
     *  - 属于二维码支付时应该不为空
     *
     * @var string
     */
    private $qr_code = '';

    /**
     *
     * @param bool $is_redirect
     * @return self
     */
    public function setIsRedirect(bool $is_redirect) : self
    {
        $this->is_redirect  = $is_redirect;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getIsRedirect()
    {
        return $this->is_redirect;
    }

    /**
     *
     * @param string $qr_code
     * @return self
     */
    public function setQrCode(string $qr_code) : self
    {
        $this->qr_code    = $qr_code;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getQrCode()
    {
        return $this->qr_code;
    }
}
