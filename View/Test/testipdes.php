
<?php  
    header('Content-Type:text/html;Charset=utf-8;');  

    include "wx_function.php";   
  //  include "des.class.php"; 
    


    $accNbr="18961010222"; 
    //$actionCode="ipsearch006";
//    $ztInterSource="200042";
//    $cerPath = "./jszt.cer";
//


    echo "----ipcumulation test as below=====>";  
    $para_encrypt=ipcumulation_des($accNbr,$actionCode,$ztInterSource);
   
    

    //    exit;
    //返回为:{"result":"0","resultMsg":"250659296053","olId":"250068957242"}
    echo "\n";
    echo "============返回信息rdata===============>\n" ;
    var_dump($rdata);
    echo "<================================\n" ;   


?>