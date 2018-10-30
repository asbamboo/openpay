<?php
namespace asbamboo\openpay\channel\common;

/**
 * 渠道参数传递的类使用的trait
 * 
 * @author 李春寅<licy2013@aliyun.com>
 * @since 2018年10月30日
 */
trait ParameterTrait
{
    /**
     * 
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        foreach($data AS $key => $value){
            if(property_exists($this, $key)){
                $this->{$key} = $value;
            }
        }
    }
}
