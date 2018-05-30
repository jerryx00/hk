<?php
    header("content-type:text/html;charset=utf-8");
    require_once "../hyphp/config.ini.php";
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
    $mobile="18961010222";
    $pricePlanCd="1";
    $actionCode="ipsearch005";
    $ztInterSource="wuxian";

    //$retJson = queryFlowJson($mobile,$pricePlanCd);

    //$retJson = testQueryFlowJson();
    // var_dump($retJson);
    //$ret=queryFlowJsonResolve($retJson);


    //echo $ret;
    echo "=============\n";
    //手机号码+渠道号+密钥进行md5加密
    $accNumber="18961010222";
    $enCode1="-200042";
    $enCode2="333996";
    $staffCode="200042";
    $res=queryFlowJson($accNumber,$enCode1,$enCode2,$staffCode);
    var_dump($res);

?>