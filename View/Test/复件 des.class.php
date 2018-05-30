<?php
    /**
    *@param  DES/3DES加密解密    
    *@param  如果是3des，将MCRYPT_DES修改为MCRYPT_3DES，
    */
    class DesCrypt{
        var $key   = 't2b4h6y4l5';
        var $deviceid   = '';
        var $user   = '';
        var $lsh   = '';
        var $cipherText = '';
        var $HcipherText = '';
        var $decrypted_data ='';

        function DesCrypt(){}

        //加密
        function en($str) {
            $cipher = mcrypt_module_open(MCRYPT_DES, '', MCRYPT_MODE_ECB, '');
            $iv     = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_DES,MCRYPT_MODE_ECB), MCRYPT_RAND);

            if (mcrypt_generic_init($cipher, substr($this->key,0,8), $iv) != -1){
                $this->cipherText = mcrypt_generic($cipher,$this->pad($str));
                mcrypt_generic_deinit($cipher);
                // 以十六进制字符显示加密后的字符
                //                $this->HcipherText=bin2hex($this->cipherText);
                $this->HcipherText=$this->strbin2hex($this->cipherText,true);
                //printf("<p>3DES encrypted:\n%s</p>",$this->cipherText);
                //printf("<p>3DES HexEncrypted:\n%s</p>",$this->HcipherText);
            }
            mcrypt_module_close($cipher);
            //return $this->cipherText;
            return strtoupper($this->HcipherText);
        } 

        function strbin2hex($bin, $pad=false, $upper=false){
            $last = strlen($bin)-1;
            for($i=0; $i<=$last; $i++){ $x += $bin[$last-$i] * pow(2,$i); }
            $x = dechex($x);
            if($pad){ while(strlen($x) < intval(strlen($bin))/4){ $x = "0$x"; } }
            if($upper){ $x = strtoupper($x); }
            return $x;
        }
        

        public function binhex($bin){
            $hex='';
            echo "--len---".$bin."====".strlen($bin)."--";exit;
            for($i=strlen($bin)-8;$i>=0;$i-=8)
            {
                //$hex.=dechex(bindec(substr($bin,$i,4)));
                $tmp=substr($bin,$i,8);
                if (strlen($tmp) < 8)
                {
                    $num=16-strlen($tmp);
                    $tmp.="0"  * $num;
                    echo "-tmp-". $tmp;
                }
                $hex.=dechex(bindec($tmp));

            }
            return strrev($hex);
        }
        //解密
        function de($str)
        {
            $str    = pack('H*', $str);
            $cipher = mcrypt_module_open(MCRYPT_DES, '', MCRYPT_MODE_ECB, '');
            $iv     = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_DES,MCRYPT_MODE_ECB), MCRYPT_RAND);
            if (mcrypt_generic_init($cipher, substr($this->key,0,8), $iv) != -1)
            {
                $this->decrypted_data = mdecrypt_generic($cipher,$str);
                mcrypt_generic_deinit($cipher);
            }
            mcrypt_module_close($cipher);
            return $this->unpad($this->decrypted_data);
        }

        private function pad ($data)
        {
            $data = str_replace("\n","",$data);
            $data = str_replace("\t","",$data);
            $data = str_replace("\r","",$data);
            return $data;
        }

        private function unpad ($text)
        {
            $pad = ord($text{strlen($text) - 1});
            if ($pad > strlen($text)) {
                return false;
            }
            if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) {
                return false;
            }
            return substr($text, 0, - 1 * $pad);

        }
    }
?>