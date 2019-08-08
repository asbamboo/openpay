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
     * 支付类型
     *****************************************************************************/
    const TYPE_GENERAL  = '0';  // 普通类型
    const TYPE_QRCD     = '1';  // 扫码支付（顾客手机扫描商户）
    const TYPE_PC       = '2';  // PC支付
    const TYPE_H5       = '3';  // H5支付
    const TYPE_APP      = '4';  // APP支付
    const TYPE_ONECD    = '5'; // 一码支付（商户展示聚合静态码）
    /****************************************************************************/
    
    /**
     * 支付类型
     *  - TYPE_GENERAL：返回值没有什么特殊字段的状况
     *  - TYPE_QRCD：扫码支付（顾客手机扫描商户）这是比如返回$qr_code
     *  - TYPE_PC: PC页面支付 这是必须返回$redirect_url和$redirect_data
     *  - TYPE_H5：H5页面支付 这是必须返回$redirect_url和$redirect_data
     * @var string
     */
    private $type   = '0';
    
    /**
     * 二维码url
     *  - $type == TYPE_QRCD 时，不能为空
     *
     * @var string
     */
    private $qr_code = '';
    
    /**
     * 用户APP支付时的，app客户端请求用到的参数
     * - app支付时不能为空
     * - 请求参数的格式为由各渠道API接口说明的相关字段组成的json字符串。
     *
     * @var string
     */
    private $app_pay_json = "{}";
    
    /**
     * 一码支付订单创建时, 客户端js调用支付通道使用的参数
     * - 一码支付不能为空
     * - 请求参数的格式为由各渠道API接口说明的相关字段组成的json字符串。
     *
     * @var string
     */
    private $onecd_pay_json = "{}";
    
    /**
     * 页面跳转目标URL
     *  - $type == TYPE_PC 时，不能为空
     *  - $type == TYPE_H5 时，不能为空
     *
     * @var string
     */
    private $redirect_url = '';
    
    /**
     * 像跳转目标URL传递的参数
     *  - $type == TYPE_PC 时，不能为空
     *  - $type == TYPE_H5 时，不能为空
     *
     * @var array
     */
    private $redirect_data = [];
    
    /**
     *
     * @param string|int $type
     * @throws OpenpayException
     * @return self
     */
    public function setType($type) : self
    {
        if(!in_array($type, [self::TYPE_GENERAL, self::TYPE_QRCD, self::TYPE_PC, self::TYPE_H5, self::TYPE_APP, self::TYPE_ONECD])){
            throw new OpenpayException('支付需要，页面跳转类型超出有效范围。');
        }
        $this->type  = $type;
        return $this;
    }
    
    /**
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
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
     * @param string $app_pay_json
     * @return self
     */
    public function setAppPayJson(string $app_pay_json) : self
    {
        $this->app_pay_json  = $app_pay_json;
        return $this;
    }
    
    /**
     *
     * @return string
     */
    public function getAppPayJson() : string
    {
        return $this->app_pay_json;
    }

    /**
     * 
     * @param string $onecd_pay_json
     * @return self
     */
    public function setOnecdPayJson(string $onecd_pay_json) : self
    {
        $this->onecd_pay_json   = $onecd_pay_json;
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getOnecdPayJson() : string
    {
        return $this->onecd_pay_json;
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
