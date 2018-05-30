<?php

    require "des_simple.class.php";  

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
         //特别处理一下
        $rdata=str_replace_dx($rdata);
        
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

        //特别处理一下
        $rdata=str_replace_dx($rdata);
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

        //特别处理一下
        $rdata=str_replace_dx($rdata);
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

        //特别处理一下
        $rdata=str_replace_dx($rdata);

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

    function str_replace_dx($rdata){ 
        if   (strlen($rdata)>6)
        {
            $str1 = substr($rdata,0,5);
            $str2 = substr($rdata,-1);
            if ($str1 == "null(")
            {
                $rdata = substr($rdata,5);
            }
            if ($str2 == ")")
            {
                $rdata = substr($rdata,0,strlen($rdata)-1);
            }
            //判断首尾是否有[   ] ,没有加上
           // var_dump($rdata);
            //echo "--$str1--$str2-----";
            $str1 = substr($rdata,0,1);
            $str2 = substr($rdata,-1);
            if ($str1 == "{")
            {
                $rdata = "[".$rdata;
            }
            if ($str2 == "}")
            {
                $rdata = $rdata."]";
            }
        }
        $rdata=str_replace_n($rdata);
        return $rdata;     
    }

    
 
 /**
 * 业务办理接口
 * 
 * @param mixed $accNbr
 * @param mixed $offerSpecl
 * @param mixed $goodName
 * @param mixed $type
 * @param mixed $actionCode
 * @param mixed $ztInterSource
 * @param mixed $staffValue
 * @param mixed $cerFile
 */
    function orderPackageByQiXin($accNbr,$offerSpecl,$goodName,$type,$reqid="0",$actionCode="order_qixin_001",
    $ztInterSource="200305",$staffValue="15251823848",$cerFile="./jszt.cer")
    {   
        if ($reqid=="0" || !isset($reqid)) {
           $reqId= $staffValue. date('YmdHis') . rand(100000,999999) ;
        }
        $para=
        "reqId=".$reqid
        .";accNbr=" .$accNbr
        .";offerSpecl="    .$offerSpecl
        .";goodName="       .str_replace("|", "/", $goodName)
        .";actionCode="       .$actionCode
        .";ztInterSource="       .$ztInterSource
        .";staffValue="       .$staffValue
        .";type=".$type ;                             
            
//        echo "============加密前的param===============>\n" ;
        //echo $para;  exit;  
//        echo "<============加密前的param===============\n" ;
       
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
        $url="http://202.102.111.142/jszt/ipauth/orderPackageByQiXin";
        $rdata=send_post($url,$param);
        echo $rdata;
        //特别处理一下
        //remove by xugp 2016-04-19
//        $rdata=str_replace_dx($rdata);
//        var_dump($rdata);
        return  $rdata;
    }
    
    /**
    * 1.3    分销商库存同步接口
    * 
    * @param mixed $accNbr
    * @param mixed $actionCode
    * @param mixed $ztInterSource
    * @param mixed $cerFile
    */
    function queryStock($accNbr="15251823848",$actionCode="order_qixin_001", $ztInterSource="200305",$cerFile="./jszt.cer")
    {   
        $para=
        ";accNbr=".$accNbr 
        .";actionCode="       .$actionCode
        .";ztInterSource="       .$ztInterSource;                             
            
        echo "============加密前的param===============>\n" ;
        echo $para;
        echo "<============加密前的param===============\n" ;
       
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

        //$url="http://202.102.111.142/jszt/ipauth/queryStock";
        ////如下为测试环境 
        $url="http://202.102.111.142/jszt/ipauth/queryStock";
        $rdata=send_post($url,$param);

        //特别处理一下
        $rdata=str_replace_dx($rdata);
        echo $rdata;
        return  $rdata;
    }

    /**
    * 1.6    订购查询接口
    * 
    * @param mixed $accNbr       协销工号ID  在分销平台注册的手机号
    * @param mixed $actionCode
    * @param mixed $ztInterSource
    * @param mixed $cerFile
    */
      function queryOrder($reqid, $accNbr="15251823848",$actionCode="order_qixin_001", $ztInterSource="200305",$cerFile="./jszt.cer")
    {   
        $para=                  
        ";reqId=".$reqid 
        .";accNbr=".$accNbr 
        .";actionCode="       .$actionCode
        .";ztInterSource="       .$ztInterSource;                             
            
       
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

        //$url="http://202.102.111.142/jszt/ipauth/queryStock";
        ////如下为测试环境 
        $url="http://202.102.111.142/jszt/ipauth/queryStock";
        $rdata=send_post($url,$param);

        //特别处理一下
        $rdata=str_replace_dx($rdata);
        echo $rdata;
        return  $rdata;
    }
    
    function str_replace_n($rdata){ 
        $pattern = "}{";
        $replacement = "},{";           
        $pos=strpos($rdata,$pattern);
        if ($pos || !empty($pos))  {         
            $rdata=substr($rdata,0,$pos). $replacement .substr($rdata,$pos+2);
            $rdata=str_replace_n($rdata);            
        }
        return $rdata;
    } 

