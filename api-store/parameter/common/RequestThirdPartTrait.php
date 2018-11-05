<?php
namespace asbamboo\openpay\apiStore\parameter\common;

/**
 * 第三方支付平台的支持的其他参数
 * 
 * @author 李春寅<licy2013@aliyun.com>
 * @since 2018年10月27日
 */
trait RequestThirdPartTrait
{
    
    /**
     * @desc 第三方支付平台的参数，请自行查阅相关支付平台相关文档中的参数列表
     * @example {"limit_pay":"no_credit"}
     * @required 可选
     * @var json()
     */
    protected $third_part = '[]';
    
    /**
     *
     * @return \asbamboo\openpay\apiStore\parameter\v1_0\trade\json()
     */
    public function getThirdPart()
    {
        return $this->third_part;
    }
}