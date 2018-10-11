<?php
namespace asbamboo\openpay\payMethod\alipay\requestParams;

/**
 * 用于接口参数映射关系处理
 * 使用到这个trait的类必须要有mappingConfig方法
 * mappingConfig方法是一个配置映射关系的方法
 * mappingConfig方法返回一个数组，表示请求参数的key与接收参数的key之间的映射关系
 *  - 例如return ['appid' => 'appid', 'amount'=>'amount'];
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月9日
 */
trait MappingDataTrait
{
    /**
     *
     * @param array $assign_data
     */
    public function mappingData(array $assign_data) : void
    {
        foreach($assign_data AS $key => $value){
            if(property_exists($this, $key)){
                $this->{$key}   = $value;
            }
        }
    }
}