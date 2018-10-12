<?php
namespace asbamboo\openpay\common;

use asbamboo\http\ResponseInterface AS HttpResponseInterface;

/**
 * 将第三方平台响应结果转换为实体类
 * 该类的属性应该是列出响应值的每一个字段，应该初始化后只能获取，不能修改
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月12日
 */
interface ResponseInterface
{
    /**
     * 构造方法需要接收一个请求第三方平台接口得到的响应结果
     * 构造方法负责解析第三方平台响应结果，将响应结果映射到响应实体类
     * 构造方法解析响应结果时，需要对响应进行有效性验证
     *
     * @param HttpResponseInterface $Response
     */
    public function __construct(HttpResponseInterface $Response);

    /**
     * 获取响应结果中的某个字段
     *
     * @param string $key
     */
    public function get(string $key);
}
