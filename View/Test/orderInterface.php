<?php
    header("content-type:text/html;charset=utf-8");
    //require_once "../hyphp/config.ini.php";
    require_once "wx_function.php";
    /**
    * 入参说明：
    参数名称    参数说明    数据类型    非空    备注
    para    Json格式受理资费    Sring    Y    如1.1.1
    sign    加密字符串    String    Y    手机号码+渠道号+密钥进行md5加密，密钥由公信分配。
    staffCode    渠道编号    String    Y    为鉴权

    * 
    *   
    * @var mixed
    */
    $mobile="13776620166";
    $pricePlanCd="1";
    $actionCode="ipsearch005";
    $ztInterSource="wuxian";

    $retJson = testOrderInterfaceJson();
    
    $ret=orderInterfaceJsonResolve($retJson);


    echo $ret;
    
    echo "=============\n";
    $res=orderInterfaceJson("18961010222","300509001315");
    var_dump($res);

?>