<?php
    header("content-type:text/html;charset=utf-8");
    require_once "wx_function_ext.php";

    //真实接口
    //$retJson = ipcumulationJson($mobile,$pricePlanCd);
    //模拟输出
    $retJson = testIpcumulationJson();
    $ret=ipcumulationJsonResolve($retJson);
    echo $ret;
?>