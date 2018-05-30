
<?php  
    header('Content-Type:text/html;Charset=utf-8;');  

    include "wx_function.php";   
    //include "des.class.php";

    //$des = new DesCrypt();
    //    
    //    $str="5zI00xKlqKJ6tNYGggYsgTPeCoBmGB9K8F5Fy9oPyJwLUzbDjMwXjrw+A167kq4hi8lt2+aU5ZfolPyS/5XNOyRG2ToMBg1/payWkVlbRVF08WX0bvynpkWKh2W0cODB1igHSUPVkVRuIgpniCHMBs9YYzrRrWF2F1Lkljw8FMao0i3zA9s4EEIZgvkir3LReFt67QBRSmUOjtMcZJGfOelBmCu5euJ9VYUNDXuJbTPWKAdJQ9WRVEIZgvkir3LRNnkw/YBM2FM=";
    //    $str=base64_decode($str);
    //    $rdata=$des->de($str);


    $accNbr="18961010222"; 
    $pwd = "333996";
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

    $encrypted = encrypt($para, $key);
    echo      $encrypted."\n";

    $decrypted= decrypt($encrypted, $key);
    echo      $decrypted."\n";


    echo "\n";
    echo "============返回信息rdata===============>\n" ;
    //var_dump($rdata);
    echo "<================================\n" ;   


?>