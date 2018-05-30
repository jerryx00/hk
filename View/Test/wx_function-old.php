<?php
    require "des.class.php";
    require "des_simple.class.php";

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
        ipcumulation($accNbr,$actionCode="ipsearch006",$ztInterSource="200042",$cerFile = "./jszt.cer");
    }else if($r == "ipordermore3"){
        //echo "123";exit;
        $accNbr = $_GET['accNbr'];
        $offerSpecl = $_GET['offerSpecl'];
        $goodName = $_GET['goodName'];
        ipordermore3($accNbr,$offerSpecl,$goodName,$actionCode="iptrust009",$ztInterSource="200042",$staffValue="",$staffDetail="",$cerFile="./jszt.cer");
    }else if($r == "ssl_encrypt_public"){
        $source = $_GET['source'];
        $fileName = $_GET['fileName'];
        ssl_encrypt_public($source,$fileName);
    }else if($r == "ipcumulation_des"){
        $accNumber = $_GET['accNumber'];
        ipcumulation_des($accNumber,$actionCode="ipsearch006",$ztInterSource="200042");
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
    }

    
    
    
    
    
    
    function send_post($url, $post_data) {   

        $postdata = http_build_query($post_data);   
        $options = array(   
            'http' => array(   
                'method' => 'POST',   
                'header' => 'Content-type:application/x-www-form-urlencoded',   
                'content' => $postdata,   
                'timeout' => 15 * 60 // 超时时间（单位:s）   
            )   
        );   
        $context = stream_context_create($options);   
        $result = file_get_contents($url, false, $context);   

        return $result;   
    }

    function ipcumulation($accNbr,$actionCode="ipsearch006",$ztInterSource="200042",$cerFile = "./jszt.cer")
    {
        $para=
        "accNbr="       .$accNbr
        .";actionCode="       .$actionCode
        .";ztInterSource="       .$ztInterSource ;
         /**
        echo "============加密前的param===============>\n" ;
        echo $para;
        echo "<============加密前的param===============\n" ;
        */
        $param = ssl_encrypt_public($para,$cerFile);
        $crypttext = base64_encode("".$param);
        //$param = publickey_encodeing_long($para,$cerFile);
        //echo "============加密后的param===============\n" ;
        //        var_dump($param);
        //        echo "==============================\n" ;

        $param=array(
            "para"       =>$crypttext,            
        );
         /**
        echo "======参数如下=====>\n";
        var_dump($param);
        */
        //$url="http://202.102.111.142/jszt/ipauth/ipcumulation";
        //如下为测试环境 
        $url="http://202.102.111.142:8080/jszt/ipauth/ipcumulationpublic";
        //$url="http://202.102.111.142/jszt/ztauth/getAuthCode";
        $rdata=send_post($url,$param);
        return  $rdata;

    }  

    /**
    * @param mixed $accNbr              手机号码              
    * @param mixed $offerSpecl          销售品ID              
    * @param mixed $goodName            销售品名称            
    * @param mixed $actionCode          接口编号 iptrust009   
    * @param mixed $ztInterSource       渠道号   200040       
    * @param mixed $staffValue          协销工号ID  没有则不传
    * @param mixed $staffDetail         协销工号   没有则不传 
    * @param mixed $cerFile              cert文件路径
    */
    function ipordermore3($accNbr,$offerSpecl,$goodName,$actionCode="iptrust009",$ztInterSource="200042",$staffValue="",$staffDetail="",$cerFile="./jszt.cer")
    {


        $para=
        "accNbr=" .$accNbr
        .";offerSpecl="    .$offerSpecl
        .";goodName="       .$goodName
        .";actionCode="       .$actionCode
        .";ztInterSource="       .$ztInterSource;
        if (!empty($staffValue))  {
            $para .=";staffValue="       .$staffValue  ;    
        }
        if (!empty($staffValue)) {
            $para .=";staffDetail="       .$staffDetail ;
        }   
         /**     
        echo "============加密前的param===============>\n" ;
        echo $para;
        echo "<============加密前的param===============\n" ;
        */
        $param = ssl_encrypt_public($para,$cerFile);
        $crypttext = base64_encode("".$param);
        //$param = publickey_encodeing_long($para,$cerFile);
        //echo "============加密后的param===============\n" ;
        //        var_dump($param);
        //        echo "==============================\n" ;

        $param=array(
            "para"       =>$crypttext,            
        );
         /**
        echo "======参数如下=====>\n";
        var_dump($param);
        */

        //$url="http://202.102.111.142/jszt/ipauth/ipordermore3";
        ////如下为测试环境 
        $url="http://202.102.111.142:8080/jszt/ipauth/ipordermore3";
        $rdata=send_post($url,$param);
        return  $rdata;
    }


    function ssl_encrypt_public($source,$fileName){
        //Assumes 1024 bit key and encrypts in chunks.
        //var_dump($source);exit;
        $key_content = file_get_contents($fileName);  
        $key = openssl_get_publickey($key_content);  
        $maxlength=117;
        $output='';
        while($source){
            $input= substr($source,0,$maxlength);
            $source=substr($source,$maxlength);   
            $ok= openssl_public_encrypt($input,$encrypted,$key);  
            $output.=$encrypted;
        }
        //var_dump($output);
        return $output;
    }

    function ipcumulation_des($accNumber,$actionCode="ipsearch006",$ztInterSource="200042"){
        $para=
        "accNbr="       .$accNumber
        .";actionCode="       .$actionCode
        .";ztInterSource="       .$ztInterSource ;
         /**
        echo "============加密前的param===============>\n" ;
        echo $para;
        echo "<============加密前的param===============\n" ;
        */
        $des = new DesCrypt();
        $crypttext=$des->en($para_encrypt);
        //$crypttext = encrypt($para);

        //$param = publickey_encodeing_long($para,$cerFile);
        //echo "============加密后的param===============\n" ;
        //        var_dump($param);
        //        echo "==============================\n" ;
        $param=array(
            "para"       =>$crypttext,            
        );
         /**
        echo "======参数如下=====>\n";
        var_dump($param); exit;
        */

        //$url="http://202.102.111.142/jszt/ipauth/ipordermore3";
        ////如下为测试环境 
        $url="http://202.102.111.142/jszt/ipauth/ipcumulation";
        $rdata=send_post($url,$param);
        return  $rdata;
        return  $para_encrypt;

    }  

    function registermobile($accNbr,$pwd)
    {
        // $accNbr=$accNbr;
        //    $password=$password;   
        $areaCode="";
        $PWDType="-1";
        $actionCode="aq010";
        $ztInterSource="200042";
        $userLogAccNbr= $accNbr;//  同$accNbr
        $pubAreaCode=$areaCode;  //同   $areaCode
        $accNbrType="2000004";

        $userTokenAccNbrType="2";
        $userLogAccNbrType="2";

        $para=
        "accNbr="       .$accNbr
        .";password="       .$pwd
        .";areaCode="       .$areaCode 
        .";PWDType="       .$PWDType 
        .";actionCode="       .$actionCode 
        .";ztInterSource="       .$ztInterSource
        .";userLogAccNbr="       .$userLogAccNbr
        .";pubAreaCode="       .$pubAreaCode
        .";accNbrType="       .$accNbrType 
        .";userTokenAccNbrType="       .$userTokenAccNbrType 
        .";userLogAccNbrType="       .$userLogAccNbrType; 

         /**
        echo "============加密前的param===============>\n" ;
        echo $para;

        echo "<============加密前的param===============\n" ;


        echo "============DES/BASE64后的param===============>\n" ; 
         */
        $key = "t2b4h6y4l5"; 
        $crypt = new DES1($key);  
        $crypttext=$crypt->encrypt($para);
         /**
        echo $crypttext;

        echo "<============DES/BASE64后的param===============\n" ;   
         */
        $param=array(
            "para"       =>$crypttext,            
        );
         /**
        echo "============加密后的param===============>\n" ;
        var_dump($param);
        echo "<============加密后的param===============\n" ;
        */

        //http://202.102.111.142/jszt/ztserverauth/getAuthToken";
        //如下为测试环境 
        $url="http://202.102.111.142:8080/jszt/ztserverauth/getAuthToken";
        $rdata=send_post($url,$param);
        return  $rdata;
        //返回$json_format;
    }


    function registermobile_des($accNbr,$pwd)
    {
        // $accNbr=$accNbr;
        //    $password=$password;   
        $areaCode="";
        $PWDType="-1";
        $actionCode="aq010";
        $ztInterSource="200042";
        $userLogAccNbr= $accNbr;//  同$accNbr
        $pubAreaCode=$areaCode;  //同   $areaCode
        $accNbrType="2000004";

        $userTokenAccNbrType="2";
        $userLogAccNbrType="2";

        $para=
        "accNbr="       .$accNbr
        .";password="       .$pwd
        .";areaCode="       .$areaCode 
        .";PWDType="       .$PWDType 
        .";actionCode="       .$actionCode 
        .";ztInterSource="       .$ztInterSource
        .";userLogAccNbr="       .$userLogAccNbr
        .";pubAreaCode="       .$pubAreaCode
        .";accNbrType="       .$accNbrType 
        .";userTokenAccNbrType="       .$userTokenAccNbrType 
        .";userLogAccNbrType="       .$userLogAccNbrType; 


        $key="t2b4h6y4"; 
        $crypt = new DES_SIMPLE($key); 
        $crypttext = $crypt->encrypt($$para);
         /**
        echo "<============DES/BASE64加密后的param===============\n" ;   

        echo $crypttext;
        */
        $param=array(
            "para"       =>$crypttext,            
        );
         /**
        echo "============加密后的param===============>\n" ;
        var_dump($param);
        echo "<============加密后的param===============\n" ;
        */

        //http://202.102.111.142/jszt/ztserverauth/getAuthToken";
        //如下为测试环境 
        $url="http://202.102.111.142:8080/jszt/ztserverauth/getAuthToken";
        $rdata=send_post($url,$param);
        return  $rdata;
        //返回$json_format;
    }

    function encrypt($data, $key){
        return base64_encode(
            mcrypt_encrypt(
                MCRYPT_RIJNDAEL_128,
                $key,
                $data,
                MCRYPT_MODE_CBC,
                "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0"
            )
        );
    }
    function decrypt($data, $key){
        $decode = base64_decode($data);
        return mcrypt_decrypt(
            MCRYPT_RIJNDAEL_128,
            $key,
            $decode,
            MCRYPT_MODE_CBC,
            "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0"
        );


    }
