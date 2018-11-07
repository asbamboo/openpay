<?php
namespace asbamboo\openpay\channel\v1_0\trade\payParameter;

use asbamboo\openpay\exception\OpenpayException;

/**
 * 渠道处理方法处理请求后应该返回的结果
 *
 * @author 李春寅<licy2013@aliyun.com>
 * @since 2018年10月30日
 */
final class Response
{
    /*****************************************************************************
     * 跳转类型
    *****************************************************************************/
    const REDIRECT_TYPE_NONE    = '0';  // 不跳转
    const REDIRECT_TYPE_QRCD    = '1';  // 跳转扫码支付
    const REDIRECT_TYPE_PC      = '2';  // 跳转PC支付
    /****************************************************************************/

    /**
     * 是否需要页面跳转
     *  - 比如扫二维码支付,应该返回true
     *
     * @var string
     */
    private $redirect_type = '0';

    /**
     * 二维码url
     *  - 属于二维码支付时应该不为空
     *
     * @var string
     */
    private $qr_code = '';

    /**
     * 跳转PC支付时 表示表单提交的url
     *
     * @var string
     */
    private $redirect_url = '';

    /**
     * 跳转PC支付时 表示表单提交的数据
     *
     * @var array
     */
    private $redirect_data = [];

    /**
     *
     * @param bool $is_redirect
     * @return self
     */
    public function setRedirectType($redirect_type) : self
    {
        if(!in_array($redirect_type, [self::REDIRECT_TYPE_NONE, self::REDIRECT_TYPE_PC, self::REDIRECT_TYPE_QRCD])){
            throw new OpenpayException('支付需要，页面跳转类型超出有效范围。');
        }
        $this->redirect_type  = $redirect_type;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getRedirectType()
    {
        return $this->redirect_type;
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

    /**
     *
     * @param string $redirect_url
     */
    public function setRedirectUrl(string $redirect_url) : self
    {
        $this->redirect_url   = $redirect_url;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->redirect_url;
    }

    /**
     *
     * @param array $redirect_data
     * @return self
     */
    public function setRedirectData(array $redirect_data) : self
    {
        $this->redirect_data    = $redirect_data;
        return $this;
    }

    /**
     *
     * @return array
     */
    public function getRedirectData()
    {
        return $this->redirect_data;
    }
}
