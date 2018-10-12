<?php
namespace asbamboo\openpay\payMethod\alipay\response\tool;

use asbamboo\http\ResponseInterface AS HttpResponseInterface;
use asbamboo\openpay\exception\ResponseFormatException;
use asbamboo\helper\env\Env AS EnvHelper;
use asbamboo\openpay\Env;
use asbamboo\openpay\common\ResponseInterface;

/**
 * 响应结果公共参数
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月12日
 */
abstract class ResponseAbstract implements ResponseInterface
{
    const CODE_SUCCESS          = '10000';

    /**
     * 必填 10000 接口调用成功，调用结果请参考具体的API文档所对应的业务返回参数
     *
     * @see https://docs.open.alipay.com/common/105806
     * @var string
     */
    protected $code;

    /**
     * 必填
     *
     * @see https://docs.open.alipay.com/common/105806
     * @var string
     */
    protected $msg;

    /**
     * 业务返回码，参见具体的API接口文档
     *
     * @var string
     */
    protected $sub_code;

    /**
     * 业务返回码描述，参见具体的API接口文档
     *
     * @var string
     */
    protected $sub_msg;


    /**
     * 获取响应结果中，响应接口相关业务参数的key
     */
    abstract protected function getResponseRootNode() : string;

    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\openpay\common\ResponseInterface::__construct()
     */
    public function __construct(HttpResponseInterface $Response)
    {
        $this->parseResponse($Response);
    }

    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\openpay\common\ResponseInterface::get()
     */
    public function get(string $key)
    {
        return $this->{$key};
    }

    /**
     * 获取响应结果中，签名字段响应的key
     *
     * @return string
     */
    protected function getSignRootNode() : string
    {
        return 'sign';
    }

    /**
     * 获取响应结果中，报错时响应的key
     *
     * @return string
     */
    protected function getErrorRootNode() : string
    {
        return 'error_response';
    }

    /**
     * 解析响应
     *
     * @param HttpResponseInterface $Response
     */
    protected function parseResponse(HttpResponseInterface $Response) : void
    {
        $json           = $Response->getBody()->getContents();
        $decoded_json   = json_decode($json, true);

        $this->checkResponse($json, $decoded_json);

        $data           = [];
        if(isset($decoded_json[$this->getResponseRootNode()])){
            $data   = $decoded_json[$this->getResponseRootNode()];
        }

        if(isset($decoded_json[$this->getErrorRootNode()])){
            $data   = $decoded_json[$this->getErrorRootNode()];
        }
        foreach($data AS $key => $value){
            if(property_exists($this, $key)){
                $this->{$key}   = $value;
            }
        }
    }

    /**
     * 验证签名规则参阅支付宝的文档，和支付宝的demo
     *
     * @param string $json
     * @param array|null $decoded_json
     * @throws ResponseFormatException
     */
    protected function checkResponse(string $json, $decoded_json) : void
    {

        if(empty($decoded_json)){
            throw new ResponseFormatException(sprintf('支付宝返回的响应结果异常[%s]', $json));
        }

        if(!isset($decoded_json['sign'])){
            throw new ResponseFormatException(sprintf('支付宝返回的响应结果异常,sign不存在[%s]', $json));
        }

        $sign_source_root_index     = strpos($json, $this->getResponseRootNode() . '":');
        $sign_source_start_index    = $sign_source_root_index + strlen($this->getResponseRootNode()) + 2;
        $sign_source_end_index      = strpos($json, ',"' . $this->getSignRootNode() . '"');
        if($sign_source_root_index === false){
            $sign_source_root_index    = strpos($json, $this->getErrorRootNode() . '":');
            $sign_source_start_index    = $sign_source_root_index + strlen($this->getErrorRootNode()) + 2;
        }
        if($sign_source_root_index === false){
            throw new ResponseFormatException(sprintf('支付宝返回的响应结果异常,response node不存在[%s]', $json));
        }
        if($sign_source_end_index === false){
            throw new ResponseFormatException(sprintf('支付宝返回的响应结果异常,sign node不存在[%s]', $json));
        }
        $sign_source    = substr($json, $sign_source_start_index, $sign_source_end_index - $sign_source_start_index);
        if($this->verifySign($sign_source, $decoded_json['sign']) != 1){
            throw new ResponseFormatException(sprintf('支付宝返回的响应结果异常,sign错误[%s]', $json));
        }

    }

    /**
     * 验证签名
     * 如果返回 1 表示验证通过
     *
     * @see http://php.net/manual/zh/function.openssl-verify.php
     * @param string $sign_source
     * @param string $sign
     * @return int
     */
    protected function verifySign($sign_source, $sign) : int
    {
        $public_pem     = EnvHelper::get(Env::ALIPAY_RSA_ALIPAY_KEY);
        if(is_file($public_pem)){
            $public_pem    = 'file://' . $public_pem;
        }
        $sign                       = base64_decode($sign);
        $ssl                        = openssl_get_publickey($public_pem);
        $verify                     = openssl_verify($sign_source, $sign, $ssl, OPENSSL_ALGO_SHA256);
        if($verify != 1){
            $stripslashes_sign_source   = str_replace("\\/", "/", $sign_source);
            if($stripslashes_sign_source != $sign_source){
                $verify = openssl_verify($stripslashes_sign_source, $sign, $ssl, OPENSSL_ALGO_SHA256);
            }
        }
        openssl_free_key($ssl);

        return $verify;
    }
}
