<?php
namespace asbamboo\openpay;

use asbamboo\http\ResponseInterface;

/**
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月10日
 */
class Client implements ClientInterface
{
    /**
     * 接口网关uri
     *
     * @var string
     */
    private $gateway_uri;

    /**
     *
     * @param string $gateway_url
     */
    public function __construct(string $gateway_uri = null)
    {
        $this->gateway_uri  = $gateway_uri;
    }

    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\openpay\ClientInterface::request()
     * @var string $builder_name 接口平台方文件目录名 + ':' + 接口请求构件器名称, 如 alipay:TradeCreate
     */
    public function request(string $builder_name, array $assign_data = []) : ResponseInterface
    {
        $AssignDataObject   = $this->findAssignDataObject($builder_name);
        foreach($assign_data AS $key => $value){
            if(property_exists($AssignDataObject, $key)){
                $AssignDataObject->{$key}   = $value;
            }
        }

        $Builder            = $this->findBuilder($builder_name);
        $Builder            = $Builder->assignData($AssignDataObject);
        if($this->gateway_uri){
            $Builder    = $Builder->setGateway($this->gateway_uri);
        }

        $Request            = $Builder->create();

        return Factory::sendRequest($Request);
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

    /**
     * 查找并创建builder_name对应的接口请求数据集实例
     *
     * @param string $builder_name
     * @return AssignDataInterface
     */
    private function findAssignDataObject(string $builder_name) : AssignDataInterface
    {
        @list($type, $name) = explode(':', $builder_name);
        return Factory::createAssignData($name);
    }
}