<?php
class DES1 {
    var $key;         
    function  DES1($key) {          
        $this->key = $key;         
    }  
    function encrypt($input) {        
        $size = mcrypt_get_block_size('des', 'ecb');          
        $input = $this->pkcs5_pad($input, $size);          
        $key = $this->key;         
        $td = mcrypt_module_open('des', '', 'ecb', '');       
        $iv = @mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);      
        @mcrypt_generic_init($td, $key, $iv);         
        $data = mcrypt_generic($td, $input);          
        mcrypt_generic_deinit($td);      
        mcrypt_module_close($td);         
        $data = base64_encode($data);         
        return $data;     
        } 

    function pkcs5_pad ($text, $blocksize) {          
        $pad = $blocksize - (strlen($text) % $blocksize);         
        return $text . str_repeat(chr($pad), $pad);   
    }
}

//$key = "t2b4h6y4l5";
//$input = "accNbr=18913535668;actionCode=auth_smscode_001;ztInterSource=200017";  
//$crypt = new DES1($key);  
//$post_data='para='.urlencode($crypt->encrypt($input));
//$url = "http://202.102.111.142/jszt/ipauth/ipgetauthcode64";

/**
$key = "t2b4h6y4l5";
$input = "accNbr=18913535668;code=698027;offerSpecl=300509023650;family=2;goodName=定向流量/无线苏州/10元包1G省内流量;actionCode=auth_smscode_002;ztInterSource=200017";
$crypt = new DES1($key);
$post_data = 'para='.urlencode($crypt->encrypt($input));
$url = "http://202.102.111.142/jszt/ipauth/iporderwithcode64";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
$output = curl_exec($ch);
curl_close($ch);
//打印获得的数据
print_r($output);
*/
?>