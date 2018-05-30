<?php

    header('Content-Type:text/html;Charset=utf-8;');  
    require "wx_common.php";     

    /**  
    * 发送post请求  
    * @param string $url 请求地址  
    * @param array $post_data post键值对数据  
    * @return string  
    */
    $r = $_GET['r'];
    if($r == "send_post"){
        $url = $_GET['url'];
        $post_data = $_GET['post_data'];
        send_post($url, $post_data);
    }else if($r == "ipcumulation"){
        $accNbr = $_GET['accNbr'];
        //ipcumulation($accNbr,$actionCode="ipsearch006",$ztInterSource="200042",$cerFile = "./jszt.cer");
        ipcumulation($accNbr);
    }else if($r == "ipordermore3"){
        //echo "123";exit;
        $accNbr = $_GET['accNbr'];
        $offerSpecl = $_GET['offerSpecl'];
        $goodName = $_GET['goodName']; 
        //$ret=ipordermore3($accNbr,$offerSpecl,$goodName,$actionCode="iptrust009",$ztInterSource="200042",$staffValue="",$staffDetail="",$cerFile="./jszt.cer");
        ipordermore3($accNbr,$offerSpecl,$goodName);
        //$ret=ipordermore3($accNbr,$offerSpecl,$goodName);
        //var_dump($ret);exit;
    }else if($r == "ssl_encrypt_public"){
        $source = $_GET['source'];
        $fileName = $_GET['fileName'];
        ssl_encrypt_public($source,$fileName);
    }else if($r == "ipcumulation_des"){
        $accNumber = $_GET['accNumber'];
        //ipcumulation_des($accNumber,$actionCode="ipsearch006",$ztInterSource="200042");
    }else if($r == "registermobile"){
        $accNbr = $_GET['accNbr'];
        $pwd = $_GET['pwd'];
        registermobile($accNbr,$pwd);
    }else if($r == "registermobile_des"){
        $accNbr = $_GET['accNbr'];
        $pwd = $_GET['pwd'];
        registermobile_des($accNbr,$pwd);
    }else if($r == "encrypt"){
        $data = $_GET['data'];
        $key = $_GET['key'];
        encrypt($data, $key);
    }else if($r == "encrypt"){
        $data = $_GET['data'];
        $key = $_GET['key'];
        decrypt($data, $key);
    }else if ($r == 'orderPackageByQiXin') {
        $accNbr = $_GET['accNbr'];
        $offerSpecl = $_GET['offerSpecl'];
        $goodName = $_GET['goodName']; 
        $type = $_GET['type']; 
        $reqid = $_GET['reqid']; 
//        $t = $_GET['t']; 
        orderPackageByQiXin($accNbr,$offerSpecl,$goodName,$type, $reqid);
    }else if ($r == 'queryStock') {
        queryStock();
}else if ($r == 'queryOrder') {
        $reqid = $_GET['reqid']; 
        $accNbr = $_GET['phone']; 
        queryOrder($reqid,$accNbr);
}