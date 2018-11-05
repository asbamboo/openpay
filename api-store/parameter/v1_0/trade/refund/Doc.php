<?php
namespace asbamboo\openpay\apiStore\parameter\v1_0\trade\refund;

/**
 * 帮助文档中 动态生成的帮助信息
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年10月25日
 */
class Doc
{    
    /**
     * 交易状态取值范围
     *
     * @return string
     */
    public static function tradeStatusRange()
    {
        return implode(' ', [
            'REQUEST[正在请求]',
            'SUCCESS[申请成功]',
            'FAILED[申请失败]',
        ]);
    }
}