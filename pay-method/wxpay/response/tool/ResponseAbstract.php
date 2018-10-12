<?php
namespace asbamboo\openpay\payMethod\wxpay\response\tool;

use asbamboo\http\ResponseInterface AS HttpResponseInterface;
use asbamboo\openpay\common\ResponseInterface;
use asbamboo\openpay\exception\ResponseFormatException;
use asbamboo\openpay\payMethod\wxpay\sign\SignType;
use asbamboo\openpay\payMethod\wxpay\sign\SignTrait;

/**
 * 响应结果公共参数
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月12日
 */
abstract class ResponseAbstract implements ResponseInterface
{
    use SignTrait;

    /**
     * 接口请求结果
     *
     * @var string
     */
    CONST RETURN_CODE_SUCCESS   = 'SUCCESS';

    /**
     * 业务处理结果
     *
     * @var string
     */
    CONST RESULT_CODE_SUCCESS   = 'SUCCESS';

    /**
     * 必填
     * 返回状态码
     *
     * @var string(16)
     */
    protected $return_code;

    /**
     * 必填
     * 返回信息
     *
     * @var string(128)
     */
    protected $return_msg;

    /**
     * [RETURN_CODE_SUCCESS] 必填
     * 公众账号ID
     *
     * @var string(32)
     */
    protected $appid;

    /**
     * [RETURN_CODE_SUCCESS] 必填
     * 商户号
     *
     * @var string(32)
     */
    protected $mch_id;

    /**
     * [RETURN_CODE_SUCCESS] 可选
     *
     * @var string(32)
     */
    protected $device_info;

    /**
     * [RETURN_CODE_SUCCESS] 必填
     * 随机字符串
     *
     * @var string(32)
     */
    protected $nonce_str;

    /**
     * [RETURN_CODE_SUCCESS] 必填
     * 签名
     *
     * @var string(32)
     */
    protected $sign;

    /**
     * [RETURN_CODE_SUCCESS] 必填
     * 业务结果
     *
     * @var string(16)
     */
    protected $result_code;

    /**
     * [RETURN_CODE_SUCCESS] 可选
     * 错误代码
     *
     * @var string(32)
     */
    protected $err_code;

    /**
     * [RETURN_CODE_SUCCESS] 可选
     * 错误代码描述
     *
     * @var string(128)
     */
    protected $err_code_des;

    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\openpay\common\ResponseInterface::__construct()
     */
    public function __construct(HttpResponseInterface $Response)
    {
        $this->parseResposne($Response);
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
     * 解析响应结果生成实体类属性列表
     *
     * @param HttpResponseInterface $Response
     */
    private function parseResposne(HttpResponseInterface $Response)
    {
        /**
         * 将XML转为array 禁止引用外部xml实体
         */
        libxml_disable_entity_loader(true);
        $xml            = $Response->getBody()->getContents();
        $decoded_xml    = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);

        $this->checkResponse($xml, $decoded_xml);

        foreach($decoded_xml AS $key => $value){
            if(property_exists($this, $key)){
                $this->{$key}   = $value;
            }
        }
    }

    /**
     * 检查响应结果是否有效
     *
     * @param string $xml
     * @param array|null $decoded_xml
     * @throws ResponseFormatException
     */
    private function checkResponse(string $xml, $decoded_xml)
    {
        if(!isset($decoded_xml['sign'])){
            throw new ResponseFormatException(sprintf('微信返回的响应结果异常,sign不存在[%s]', $xml));
        }
        $sign_type  = isset( $decoded_xml['sign_type'] ) ? $decoded_xml['sign_type'] : null;
        if(is_null($sign_type)){
            $sign_type  = strlen( $decoded_xml['sign'] ) > 32 ? SignType::HMAC_SHA256 : SignType::MD5;
        }
        if($decoded_xml['sign'] != $this->makeSign($decoded_xml, $sign_type)){
            throw new ResponseFormatException(sprintf('微信返回的响应结果异常,sign错误[%s]', $json));
        }
    }
}
