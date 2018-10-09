<?php
namespace asbamboo\openpay\common\traits;

use asbamboo\openpay\AssignDataInterface;
use asbamboo\openpay\payment\alipay\requestParams\CommonParamsInterface;

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
     * {@inheritDoc}
     * @see \asbamboo\openpay\payment\alipay\requestParams\CommonParamsInterface::mappingData()
     */
    public function mappingData(AssignDataInterface $AssignData) : CommonParamsInterface
    {
        foreach($this->mappingConfig() AS $this_key => $assign_data_key){
            if(property_exists($AssignData, $assign_data_key)){
                $this->{$this_key}  = $assign_data_key;
            }
        }
        return $this;
    }
}