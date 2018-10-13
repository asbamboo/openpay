<?php
namespace asbamboo\openpay;

use asbamboo\openpay\common\ResponseInterface;

/**
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月10日
 */
class Client implements ClientInterface
{
    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\openpay\ClientInterface::request()
     * @var string $builder_name 接口平台方文件目录名 + ':' + 接口请求构件器名称, 如 alipay:TradeCreate
     */
    public function request(string $builder_name, array $assign_data = []) : ResponseInterface
    {
        $Builder            = $this->findBuilder($builder_name);
        $Builder            = $Builder->assignData($assign_data);
        $Request            = $Builder->create();
        $HttpResponse       = Factory::sendRequest($Request);
        $Response           = Factory::transformResponse($builder_name, $HttpResponse);

        return $Response;
    }

    /**
     * 查找并创建builder_name对应的接口请求构件器实例
     *
     * @param string $builder_name
     * @return BuilderInterface
     */
    private function findBuilder(string $builder_name) : BuilderInterface
    {
        return Factory::createBuilder($builder_name);
    }
}