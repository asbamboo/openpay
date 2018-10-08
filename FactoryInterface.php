<?php
namespace asbamboo\openpay;

/**
 * 接口工厂
 *  - 创建各个平台的接口访问器
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月8日
 */
interface FactoryInterface
{
    /**
     * 创建一个接口访问器
     *
     * @param string $builder_name
     * @return BuilderInterface
     */
    static public function createBuilder(string $builder_name) : BuilderInterface;
}