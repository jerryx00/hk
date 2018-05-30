<?php
/**
*
* 版权所有：金豌豆<>
* 作    者：国平<8688041@qq.com>
* 日    期：2015-09-17
* 版    本：1.0.0
* 功能说明：后台公共文件。
*
**/

/**
*
* 函数：日志记录
* @param  string $log   日志内容。
* @param  string $name （可选）用户名。
*
**/


function IP($ip = '', $file = 'UTFWry.dat') {
    $_ip = array ();
    if (isset ( $_ip [$ip] )) {
        return $_ip [$ip];
    } else {
        //            import ( "ORG.Net.IpLocation" );
        //            $iplocation = new \IpLocation ( $file );
        // $iplocation = new \ORG\Net\IpLocation( $file );
        //
        //            $location = $iplocation->getlocation ( $ip );
        //            $_ip [$ip] = $location ['country'] . $location ['area'];
    }
    return $_ip [$ip];
}





function addytorderlog($p){
    $model = M('order_log');
    $data['uid'] = $p->uid;    //无效
    $data['uname'] = $p->user;
    $data['create_time'] = time();
    $data['goodid'] = $p->fluxnum;  //fluxnum
    $data['goodname'] = $p->orderno;  //orderno
    $data['phone'] = $p->mobile;
    $data['reqid'] = $p->orderno;
    $data['ip'] = get_client_ip();
    $ret = $model->data($data)->add();
    return $ret;
}



function updorderlog($data,$name=false){
    $Model = M('order_log');
    if(!$name){
        //$user = cookie('user');
        $user = session('user');
        $data['name'] = $user['user'];
    }else{
        $data['name'] = $name;
    }
    $data['update_time'] = time();
    $data['ip'] = $_SERVER["REMOTE_ADDR"];
    $Model->data($data)->save();
}




function getResult($ret) {
    if($ret == "" || $ret == "2"){
        return "Init";
    }else if($ret == "0"){
        return "Success";
    } else {
        return "Fail";
    }
}

function getProvince($ret) {
    if($ret == "1"){
        $ret = "省内";
    } else if($ret == "2"){
        $ret = "全国";
    } else {
        $ret = "--";
    }
    return $ret;
}

function getCardStatus($ret) {
    if($ret == "0"){
        $ret = "已用";
    } else if($ret == "1"){
        $ret = "未用";
    } else {
        $ret = "--";
    }
    return $ret;
}


function getSaveFileDir(){
    $y = date('Y',time());
    $m = date('m',time());
    $d = date('d',time());
    $upload_dir=is_file("../Uploads/")?"../Uploads/":"./Uploads/";
    $dir=$upload_dir.$y;
    if (!is_dir($dir)) {
        mkdir($dir, 0777);
    }
    $dir.='/'.$m;
    if (!is_dir($dir)) {
        mkdir($dir, 0777);
    }
    $dir.='/'.$d;
    if (!is_dir($dir)) {
        mkdir($dir, 0777);
    }
    $dir.='/';
    return $dir;
}

function randomkeys($length)
{
    $pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ@';    //字符池
    for($i=0; $i<$length; $i++)
    {
        $key .= $pattern{mt_rand(0,64)};    //生成php随机数
    }
    return $key;
}

/** 将数组元素进行urlencode
* @param String $val
*/
function jsonFormatProtect(&$val){
    if($val!==true && $val!==false && $val!==null){
        $val = urlencode($val);
    }
}

/** Json数据格式化
* @param  Mixed  $data   数据
* @param  String $indent 缩进字符，默认4个空格
* @return JSON
*/
function jsonFormat($data, $indent=null){

    // 对数组中每个元素递归进行urlencode操作，保护中文字符
    array_walk_recursive($data, 'jsonFormatProtect');

    // json encode
    $data = json_encode($data);

    // 将urlencode的内容进行urldecode
    $data = urldecode($data);

    // 缩进处理
    $ret = '';
    $pos = 0;
    $length = strlen($data);
    $indent = isset($indent)? $indent : '    ';
    $newline = "\n";
    $prevchar = '';
    $outofquotes = true;

    for($i=0; $i<=$length; $i++){

        $char = substr($data, $i, 1);

        if($char=='"' && $prevchar!='\\'){
            $outofquotes = !$outofquotes;
        }elseif(($char=='}' || $char==']') && $outofquotes){
            $ret .= $newline;
            $pos --;
            for($j=0; $j<$pos; $j++){
                $ret .= $indent;
            }
        }

        $ret .= $char;

        if(($char==',' || $char=='{' || $char=='[') && $outofquotes){
            $ret .= $newline;
            if($char=='{' || $char=='['){
                $pos ++;
            }

            for($j=0; $j<$pos; $j++){
                $ret .= $indent;
            }
        }

        $prevchar = $char;
    }

    return $ret;
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


function replace_unicode_escape_sequence($match) {
    return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
}

function getthemonth($date)
{
    $firstday = date('Y-m-01', strtotime('2070309'));
    $lastday = date('Y-m-d', strtotime("$firstday +1 month -1 day"));
    return array($firstday,$lastday);
}

/**
* 精确到时分秒
*
* @param mixed $date
*/
function getthemonthS($date)
{
    $bs = ' 00:00:01';
    $es = ' 23:59:59';
    $firstday = date('Y-m-01', strtotime($date));
    $lastday = date('Y-m-d', strtotime("$firstday +1 month -1 day"));

    return array($firstday.$bs,$lastday.$es);
}



function getMyResultInfo($p) {
    $ret = getMyResult($p);
    return $ret['info'];
}
function getMyResult($p) {
    $order_ret = $p['order_ret'];
    $notify_ret = $p['notify_ret'];



    if ($order_ret != '0') {
        //如果为255，说明刚入库，未提交上游接口
        $ret['status'] = '255';
        $ret['info'] = '失败';
        $ret['label'] = 'label label-sm label-warning';  //表示提交上游失败
    } else {
        if ($notify_ret == '0') {
            $ret['status'] = '0';
            $ret['info'] = '成功';
            $ret['label'] = 'label label-success arrowed-in arrowed-in-right';
        } else  if ($notify_ret == '255') {
            // if (time()-$p['notify_time'] > 600) {//卡单了

            $ret['status'] = '2';
            $ret['info'] = '处理中';
            $ret['label'] = 'label label-warning arrowed-in-right'; //待充值中

        }  else {
            $ret['status'] = '1';
            $ret['info'] = '失败';
            $ret['label'] = '';
        }
    }
    $ret['msg'] = 'order_ret='.$order_ret.';notify_ret='.$notify_ret;
    return $ret;

}

function repEnter($str) {
    $order=array("\r\n","\n","\r");
    $replace=',';
    $i = 0;
    foreach ($str as $k => $v) {
        if ($v != '' && $v != ',')  {
            $newstr[$i] = str_replace($order,$replace,$v);
        }
        $i ++;
    }
    return $newstr;
}

function getCity($str) {
    $ret = M('mobile_area')->getFieldByAreaid($str, 'areaname');
    return $ret;
}

function getCityFromPort($str) {
    $filter['port_km'] = ['like', "%{$str}%"];
    $ret = M('mobile_channel')->where($filter)->select();
    if (!$ret) {
        return "-*";
    }else if (count($ret) != 1 ) {
        return "--";
    } else {
        return $ret[0]['areaname'];
    }

}


function NumToStr($num){
    if (stripos($num,'e')===false) return $num;
    $num = trim(preg_replace('/[=\'"]/','',$num,1),'"');//出现科学计数法，还原成字符串
    $result = "";
    while ($num > 0){
        $v = $num - floor($num / 10)*10;
        $num = floor($num / 10);
        $result   =   $v . $result;
    }
    return $result;
}


function ret1($cmd1){
    $cmd1List = explode(',' ,$cmd1);

    foreach ($cmd1List as $k1 => $v1) {
        $str =  $v1;
        echo iconv("UTF-8","GB2312", $str). "<br>";
    }

}

function ret2($cmd1,$cmd2){
    $cmd1List = explode(',' ,$cmd1);
    $cmd2List = explode(',' ,$cmd2);
    foreach ($cmd1List as $k => $v) {
        foreach ($cmd2List as $k1 => $v1) {
            $str =  $v.$v1;
            echo iconv("UTF-8","GB2312", $str). "<br>";
        }
    }
}

function ret3($cmd1,$cmd2,$cmd3){
    $cmd1List = explode(',' ,$cmd1);
    $cmd2List = explode(',' ,$cmd2);
    $cmd3List = explode(',' ,$cmd3);

    foreach ($cmd1List as $k1 => $v1) {
        foreach ($cmd2List as $k2 => $v2) {
            foreach ($cmd3List as $k3 => $v3) {
                $str =  $v1.$v2.$v3;
                echo iconv("UTF-8","GB2312", $str). "<br>";
            }
        }
    }
}

function ret4($cmd1,$cmd2,$cmd3, $cmd4){
    $cmd1List = explode(',' ,$cmd1);
    $cmd2List = explode(',' ,$cmd2);
    $cmd3List = explode(',' ,$cmd3);
    $cmd4List = explode(',' ,$cmd4);

    foreach ($cmd1List as $k1 => $v1) {
        foreach ($cmd2List as $k2 => $v2) {
            foreach ($cmd3List as $k3 => $v3) {
                foreach ($cmd4List as $k4 => $v4) {
                    $str =  $v1.$v2.$v3.$v4;
                    echo iconv("UTF-8","GB2312", $str). "<br>";
                }
            }
        }
    }
}

function ret5($cmd1,$cmd2,$cmd3, $cmd4,$cmd5){
    $cmd1List = explode(',' ,$cmd1);
    $cmd2List = explode(',' ,$cmd2);
    $cmd3List = explode(',' ,$cmd3);
    $cmd4List = explode(',' ,$cmd4);
    $cmd5List = explode(',' ,$cmd5);

    foreach ($cmd1List as $k1 => $v1) {
        foreach ($cmd2List as $k2 => $v2) {
            foreach ($cmd3List as $k3 => $v3) {
                foreach ($cmd4List as $k4 => $v4) {
                    foreach ($cmd5List as $k5 => $v5) {
                        $str =  $v1.$v2.$v3.$v4.$v5;
                        echo iconv("UTF-8","GB2312", $str). "<br>";
                    }
                }
            }
        }
    }
}

function ret6($cmd1,$cmd2,$cmd3, $cmd4,$cmd5,$cmd6){
    $cmd1List = explode(',' ,$cmd1);
    $cmd2List = explode(',' ,$cmd2);
    $cmd3List = explode(',' ,$cmd3);
    $cmd4List = explode(',' ,$cmd4);
    $cmd5List = explode(',' ,$cmd5);
    $cmd6List = explode(',' ,$cmd6);

    foreach ($cmd1List as $k1 => $v1) {
        foreach ($cmd2List as $k2 => $v2) {
            foreach ($cmd3List as $k3 => $v3) {
                foreach ($cmd4List as $k4 => $v4) {
                    foreach ($cmd5List as $k5 => $v5) {
                        foreach ($cmd6List as $k6 => $v6) {
                            $str =  $v1.$v2.$v3.$v4.$v5.$v6;
                            echo iconv("UTF-8","GB2312", $str). "<br>";
                        }
                    }
                }
            }
        }
    }
}

function zm_print($arr){

    if (!is_array($arr)) {
        echo "para error, it should be array";
        exit;
    }else {
        $i = 0;
        foreach ($arr as $k => $v) {
            if (trim($v) != "" && !is_null($v)) {
                $newArr[$i] = $v;
                $i ++;
            }

        }
    }
    $arr = $newArr;
    $cnt = count($arr);
    if ($cnt == 1) {
        ret1($arr[0]);
    } else  if($cnt == 2) {
        ret2($arr[0], $arr[1]);
    } else  if($cnt == 3) {
        ret3($arr[0], $arr[1],  $arr[2]);
    } else  if($cnt == 4) {
        ret4($arr[0], $arr[1],  $arr[2], $arr[3]);
    } else  if($cnt === 5) {
        ret5($arr[0], $arr[1],  $arr[2], $arr[3], $arr[4]);
    } else  if($cnt === 6) {
        ret6($arr[0], $arr[1],  $arr[2], $arr[3], $arr[4], $arr[5]);
    } else {
        echo "para error, not support";
        exit;
    }
}

function getMsgTitleOfKM($str) {

    $mobile_arr = explode('#',$str);
    $mobile = $mobile_arr[3];
    return $mobile;
}

/**
* 话费利润
*
* @param mixed $mz
* @param mixed $price
*/
function getHfProfit($mz, $price) {
    //利润 =  售价 - 面值*0.81
    $ret = $price - ROUND($mz * 0.81,2) ;
    return $ret;
}

/**
* 话费利润
*
* @param mixed $vo
*/
function getHfProfitVo($vo) {
    //利润 =  售价 - 面值*0.81
    $ret = $vo['total'] - ROUND($vo['mz'] * 0.82,2) ;
    return $ret;
}


function getDefaultZero($str) {
    if ($str == "") {
        $str = 0;
    }
    return $str;
}



