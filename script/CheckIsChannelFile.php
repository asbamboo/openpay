<?php
/**
 * 验证一个文件是不是openpay渠道处理的类的文件
 * 执行方法
 *  - php /www/asbamboo/openpay-alipay/vendor/asbamboo/openpay/script/CheckIsChannelFile.php /www/asbamboo/openpay-alipay/channel/v1_0/trade/PayAlipayQrcd.php
 *  - 如果结果输出 1:${classs_name} 表示这个类是一个渠道处理类
 *  - 如果结果输出 0 表示这个类不是是一个渠道处理类
 *  - 可能会因为这个类文件内部有异常，php抛出致命错误，这种情况下输出异常。
 */
try{
    $classfile          = $_SERVER['argv'][1];
    $file_content       = file_get_contents($classfile);
    if(!preg_match("@^\s*<\?php@i", $file_content)){
        return 0;
    }
    $classname_data     = [];
    $getting_namespace  = false;
    $getting_classname  = false;
    foreach(token_get_all($file_content, TOKEN_PARSE) AS $token){
        if(is_array($token) && $token[0] == T_NAMESPACE){
            $getting_namespace  = true;
        }
        if(is_array($token) && $token[0] == T_CLASS){
            $getting_classname  = true;
        }
        if($getting_namespace == true){
            if(is_array($token) && $token[0] == T_STRING ){
                $classname_data[]   = $token[1];
            }else if($token == ';'){
                $getting_namespace  = false;
            }
        }
        if($getting_classname == true && is_array($token) && $token[0] == T_STRING){
            $classname_data[]   = $token[1];
            $getting_namespace  = false;
            break;
        }
    }

    if($getting_classname == false){
        echo 0;
        return;
    }

    $classname   = implode('\\', $classname_data);

    if(preg_match('@asbamboo\\\openpay\\\channel\\\\.*Interface@i', $file_content)){
        echo '1:' . $classname;
        return;
    }else{
        echo 0;
        return;
    }

}catch(\Throwable $e){
    // 不是php文件，这里只解析php文件
    echo 0;
    return;
}
