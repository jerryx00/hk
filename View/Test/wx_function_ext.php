<?php
//include "snoopy.class.php";

function logdebug($text){
    file_put_contents('./data/log.txt',$text."\n",FILE_APPEND);     
}

//业务受理接口
/**
* put your comment there...
* 
* @param mixed $accNumber
* @param mixed $pricePlanCd   销售品id 如果有多个，以英文,号进行分隔
* @param mixed $enCode1
* @param mixed $enCode2
* @param mixed $staffCode
*/
function orderInterfaceJsonResolve($data)
{
    //0为查询成功，其它为失败
    if($data->result==0){         
        $content ="查询成功:"." \n";
        $content .="resultMsg:".$data->resultMsg." \n";
        $content .="olId :".$data->olId." \n";
        
    }else{
        $content="查询失败,失败原因:".$data->resultMsg;
    }
    return $content;
    //return array($content,"text");
}

//业务受理接口
/**
* put your comment there...
* 
* @param mixed $accNumber
* @param mixed $pricePlanCd   销售品id 如果有多个，以英文,号进行分隔
* @param mixed $enCode1
* @param mixed $enCode2
* @param mixed $staffCode
*/
function orderInterfaceJson($accNumber,$pricePlanCd,$enCode1="-10880",$enCode2="123654",$staffCode="-10880"){
    $tmpstr=array(
        "accNumber"       =>$accNumber,
        "idcard"    =>$pricePlanCd       
    );
    //$para=implode(",", $str);

    $sign=md5($accNumber.$enCode1.$enCode2);
    
    $url="http://58.223.0.75:8123/crm/orderInterface.do";

    $param=array(
        "para"       =>implode(",", $tmpstr),
        "sign"    =>$sign,
        "staffCode"    =>$staffCode,
        "format"    =>"json"
    );
     //$rdata=http( $url . '?' . http_build_query($param));     
     $rdata=http($url,$param,"POST"); 
    //var_dump( $rdata);
//    exit;
//返回为:{"result":"0","resultMsg":"250659296053","olId":"250068957242"}
    $data=json_decode($rdata); 
    return $data;   
}

/**
查询流量接口
返回为:
{
" result":"0",
" resultMsg":"查询成功",
"CumUlationResp":
[
{"accuName":"手机上网国内流量",
"cumulationAlready":"5037.00",
"cumulationLeft":"404563.00",
"cumulationTotal":"409600.00",
"endTime":"20131201000000",
"offerName":"天翼乐享3G上网版89元套餐",
"startTime":"20131101000000",
"unitName":"KB"}
*/

function queryFlowJson($accNumber,$pricePlanCd,$enCode1="-10880",$enCode2="123654",$staffCode="-10880"){
    $tmpstr=array(
        "accNumber"       =>$accNumber     
    );
    //$para=implode(",", $str);

    $sign=md5($accNumber.$enCode1.$enCode2);
    
    $url="http://58.223.0.75:8123/crm/queryFlow.do";

    $param=array(
        "para"       =>implode(",", $tmpstr),
        "sign"    =>$sign,
        "staffCode"    =>$staffCode,
        "format"    =>"json"
    );
   //  $rdata=http_post_data( $url . '?' . http_build_query($param));     
    $rdata=http($url,$param,"POST"); 
    //var_dump( $rdata);
//    exit;
//返回为:{"result":"0","resultMsg":"250659296053","olId":"250068957242"}
    $dataTmp=json_decode($rdata);
    if ($dataTmp->result == 0) 
    {
        $data=json_decode($rdata,true);    
        return $data;
    }
     else
     {
      return $dataTmp;     
     }
    
    
}


/**
查询流量接口
返回为:
{
" result":"0",
" resultMsg":"查询成功",
"CumUlationResp":
[
{"accuName":"手机上网国内流量",
"cumulationAlready":"5037.00",
"cumulationLeft":"404563.00",
"cumulationTotal":"409600.00",
"endTime":"20131201000000",
"offerName":"天翼乐享3G上网版89元套餐",
"startTime":"20131101000000",
"unitName":"KB"}
*/

function queryFlowJsonResolve($data)
{ 
    
    $unit="KB";
    //0为查询成功，其它为失败
     
    if($data->result==0){        
         $content ="".$data->resultMsg." \n";
         $obj =$data->CumUlationResp[0]; 
         
        $content .="套餐名称:".$obj->offerName." \n";
        $content .="".$obj->accuName." \n";
        $tmpstrStart=getYYMMDD($obj->startTime);
        $tmpstrEnd=getYYMMDD($obj->endTime);                   
        
        $content .="开始日期:".$tmpstrStart." \n";
        $content .="截至日期:".$tmpstrEnd." \n";
        $content .="总流量:".$obj->cumulationTotal." ".$unit." \n";
        $content .="已使用流量:".$obj->cumulationAlready." ".$unit." \n";
        $content .="剩余流量:".$obj->cumulationLeft." ".$unit." \n";  
        
        
    }else{
        $content="查询失败,失败原因:".$data->resultMsg;
    }
    
    return $content;
    //return array($content,"text");
}

 
 //业务受理接口
 /**
 * put your comment there...
 * 
 * @param  $data Json format
 */
function queryJsonResolve($data)
{   
    //0为未开通，其它为开通或查询失败
    if($data->result==0){         
       // $content="您未开通此业务.";
        $content ="查询结果:".$data->resultMsg." \n";
        //$content.="olId :".$data->olId." \n";
        
    }else{
        $content ="查询结果:".$data->resultMsg." \n";
    }
    //write_to_log($content);
    return $content;
    //return array($content,"text");
}
 
 //业务受理接口
function queryJson($accNumber,$pricePlanCd,$enCode1="-10880",$enCode2="123654",$staffCode="-10880",$debug_flag=false){
     $tmpstr=array(
        "accNumber"       =>$accNumber,
        "idcard"    =>$pricePlanCd       
    );  
    //$para=implode(",", $str);

    $sign=md5($accNumber.$enCode1.$enCode2);
    
    $url="http://58.223.0.75:8123/crm/query.do";

    $param=array(
        "para"       =>implode(",", $tmpstr),
        "sign"    =>$sign,
        "staffCode"    =>$staffCode,
        "format"    =>"json"
    );
    // $rdata=http_post_data($url . '?' . http_build_query($param));  
    
    $rdata=http($url,$param,"POST");   
    //var_dump( $rdata);
//    exit;
//返回为:{"result":"0","resultMsg":"250659296053","olId":"250068957242"}
    $data=json_decode($rdata);
    return $data;
}

 /**
 * put your comment there...
 * 
 * @param  $data Json format
 */
function ipcumulationJsonResolve($data)
{   
    //0为未开通，其它为开通或查询失败
    $unit="分钟";
    if($data->TSR_RESULT==0){         
       // $content="您未开通此业务.";
        $content ="查询结果:".$data->TSR_MSG." \n";  
        // 
        $arrNum= count($data->catalogs)  ; 
        
        //$info = $data->catalogs;
//        echo $arrNum;exit;
        for($i=0;$i<$arrNum;$i++)
        {
              $objCata =$data->catalogs[$i]; 
              //var_dump($objCata);
             
              $catalogName[$i] ="[套餐名称:".$objCata->catalogName."] \n"; 
              //echo $catalogName."\n";
              
              //下面这个是items组 
              $objItems =$objCata->items[0]; 
              
               //var_dump($objItems);
               
              //echo "-------------".count($dataItem)."--------------";
              //exit;
              // var_dump($objItems[$i]);   
              $contextItem[$i]  = $catalogName[$i]."\n";
              $contextItem[$i] .= $objItems->accuName."\n";
              $contextItem[$i] .= "开始时间:".$objItems->startTime."\n";
              $contextItem[$i] .= "截至时间:".$objItems->endTime."\n";
             
              $contextItem[$i] .= "总时长:".$objItems->cumulationTotal.$unit."\n";
              $contextItem[$i] .= "已经使用时长:".$objItems->cumulationAlready.$unit."\n";
              $contextItem[$i] .= "剩余时长:".$objItems->cumulationLeft.$unit."\n";
              $contextItem[$i] .= "状态:".$objItems->state."\n";
              $contextItem[$i] .= ""."\n";
              //echo $contextItem[$i];
              $content .= $contextItem[$i];
        }
       
       
        //$content .="".$obj->accuName." \n";
        
    }else{
        $content ="查询结果:".$data->TSR_MSG." \n";
    }
    //write_to_log($content);
    return $content;
    //return array($content,"text");
} 

 /**
 * 
 * 
 * @param mixed $accNumber   用户号码
 * @param mixed $actionCode   接口编号   auth_smscode_001
 * @param mixed $ztInterSource   渠道来源
 */

 function ipcumulationJson($accNumber,$actionCode="ipsearch005",$ztInterSource="wuxian"){
    $tmpstr=array(
        "accNumber"       =>$accNumber,
        "actionCode"       =>$actionCode,
        "ztInterSource"       =>$ztInterSource
    );
    //$para=implode(",", $str);

    $sign=md5($accNumber.$enCode1.$enCode2);
    
    $url="http://202.102.111.142/jszt/ipauth/ipcumulation";

    //$param=array(
//        "para"       =>implode(",", $tmpstr),
//        "sign"    =>$sign,
//        "staffCode"    =>$staffCode,
//        "format"    =>"json"
//    );
    // $rdata=http_post_data( $url . '?' . http_build_query($param));     
    $rdata=http($url,$param,"POST");   
    //var_dump( $rdata);
//    exit;
//返回为:{"result":"0","resultMsg":"250659296053","olId":"250068957242"}
    $dataTmp=json_decode($rdata);
    if ($dataTmp->TSR_RESULT == 0) 
    {
        $data=json_decode($rdata,true);    
        return $data;
    }
     else
     {
      return $dataTmp;     
     }
}

/**
 * 发送HTTP请求方法，目前只支持CURL发送请求
 * @param  string $url    请求URL
 * @param  array  $params 请求参数
 * @param  string $method 请求方法GET/POST
 * @return array  $data   响应数据
 */
function http($url, $params, $method = 'GET', $header = array(), $multi = false){
  $header = array(
        "Content-Type:text/html;charset=utf-8"
        //"Content-Type: application/json;charset=utf-8"
       
        );  
  
    $opts = array(
      CURLOPT_TIMEOUT        => 30,
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_SSL_VERIFYHOST => false,      
      CURLOPT_HTTPHEADER     => $header
  );
  
  /* 根据请求类型设置特定参数 */
  switch(strtoupper($method)){
    case 'GET':
      $opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
     // print_r($opts);
      //exit;
    
      break;
    case 'POST':
      //判断是否传输文件
      //$params = $multi ? $params : http_build_query($params);
      $opts[CURLOPT_URL] = $url;
      $opts[CURLOPT_POST] = 1;
      $opts[CURLOPT_POSTFIELDS] = $params; 
  
      break;
    default:
      throw new Exception('不支持的请求方式！');
  }
  
  /* 初始化并执行curl请求 */
  $ch = curl_init();
  curl_setopt_array($ch, $opts);
  $data  = curl_exec($ch);
  $error = curl_error($ch);
  curl_close($ch);
  if($error) throw new Exception('请求发生错误：' . $error);

  return  $data;
}

 function http_get($url){
        $oCurl = curl_init();
        if(stripos($url,"https://")!==FALSE){
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if(intval($aStatus["http_code"])==200){
            return $sContent;
        }else{
            return false;
        }
    }
    
    
    
 function sendNews($title, $content, $picurl, $url)
{
    $arr[]=array(
        'Title'=>$title,
        'Description'=>$content,
        'PicUrl'=>$picurl,
        'Url'=>$url,
    );
    return $arr;
   
}
 function sendNewsAbs($title, $content, $picurl, $url)
{
     $dirurl='http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
     $url = dirname($dirurl).$url;
     $picurl = dirname($dirurl).$picurl;
    $arr[]=array(
        'Title'=>$title,
        'Description'=>$content,
        'PicUrl'=>$picurl,
        'Url'=>$url,
    );
    return $arr;
    
}

function getAbsUrl($url)
{
     $dirurl='http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
     $url = dirname($dirurl).$url;     
     return $url;
    
}

 
 function getYYMMDD($time){
        return $time;
 }
 
 
function funcMtel($str)
{
    return (preg_match("/(?:1[3|4|5|8]d{1})d{9}$/",$str))?true:false;
    //return (preg_match("/(?:13d{1}|15[03689])d{8}$/",$str))?true:false;
}


function get_impumulation($mobile,$actionCode,$ztInterSource,$debug_flag=false)
{       
    if ($debug_flag)   {
        $ret = Ipcumulation();
    }
    //调用真实接口
    else
    {
       $ret = ipcumulationJson($mobile,$actionCode,$ztInterSource);
    }
    
}

function get_query($mobile,$debug_flag=false)
{       
    if ($debug_flag)   {
        $ret = testQueryJson();
    }
    //调用真实接口
    else
    {
       $ret = query($accNumber,$pricePlanCd);
    }
    
}


function get_queryFlow($accNumber,$pricePlanCd,$debug_flag=false)
{       
    if (!$debug_flag)   {
        $ret = testQueryFlowJson();
    }
    //调用真实接口
    else
    {
       $ret = queryFlow($mobile,$actionCode,$ztInterSource);
    }
    
}
function get_queryOrderInterface($mobile,$actionCode,$ztInterSource,$debug_flag=false)
{       
    if ($debug_flag)   {
        $ret = testoOderInterfaceJson();
    }
    //调用真实接口
    else
    {
       $ret = orderInterface($mobile,$actionCode,$ztInterSource);
    }
    
}

function testOrderInterfaceJson()
{
    $data='{
    "result": "0",
    "resultMsg": "250659296053",
    "olId": "250068957242"
    } ';
     $data=json_decode($data);
    return $data;
}

function testQueryFlowJson()
{
    $data='
    {
    "result": "0",
    "resultMsg": "查询成功",
    "CumUlationResp": [
        {
            "accuName": "手机上网国内流量",
            "cumulationAlready": "5037.00",
            "cumulationLeft": "404563.00",
            "cumulationTotal": "409600.00",
            "endTime": "20131201000000",
            "offerName": "天翼乐享3G上网版89元套餐",
            "startTime": "20131101000000",
            "unitName": "KB"
        }
    ]
}
    ';
    $data=json_decode($data);
    return $data;
}

function testQueryJson()
{
    
    $data='{
    "result":"0",
    "resultMsg":"未开通此业务"
    }
    ';
    $data=json_decode($data);
    return $data;
}

function testIpcumulationJson()
{
     $data='{
    "TSR_RESULT": "0",
    "TSR_CODE": "0",
    "TSR_MSG": "成功！",
    "catalogs": [
        {
            "catalogName": "天翼乐享3G上网版49元套餐",
            "items": [
                {
                    "accuName": "天翼语音时长",
                    "cumulationAlready": "100.00",
                    "cumulationLeft": "0.00",
                    "cumulationTotal": "100.00",
                    "unitName": "分钟",
                    "endTime": "2014-04-01 00:00:00",
                    "offerName": "天翼乐享3G上网版49元套餐",
                    "startTime": "2014-03-01 00:00:00",
                    "state": "0"
                },
                {
                    "accuName": "天翼点对点短信条数",
                    "cumulationAlready": "5.00",
                    "cumulationLeft": "25.00",
                    "cumulationTotal": "30.00",
                    "unitName": "条",
                    "endTime": "2014-04-01 00:00:00",
                    "offerName": "天翼乐享3G上网版49元套餐",
                    "startTime": "2014-03-01 00:00:00",
                    "state": "0"
                },
                {
                    "accuName": "天翼彩信条数",
                    "cumulationAlready": "0.00",
                    "cumulationLeft": "6.00",
                    "cumulationTotal": "6.00",
                    "unitName": "条",
                    "endTime": "2014-04-01 00:00:00",
                    "offerName": "天翼乐享3G上网版49元套餐",
                    "startTime": "2014-03-01 00:00:00",
                    "state": "0"
                },
                {
                    "accuName": "WLAN上网时长",
                    "cumulationAlready": "0.00",
                    "cumulationLeft": "1800.00",
                    "cumulationTotal": "1800.00",
                    "unitName": "分钟",
                    "endTime": "2014-04-01 00:00:00",
                    "offerName": "天翼乐享3G上网版49元套餐",
                    "startTime": "2014-03-01 00:00:00",
                    "state": "0"
                },
                {
                    "accuName": "手机上网国内流量",
                    "cumulationAlready": "0.00",
                    "cumulationLeft": "200.00",
                    "cumulationTotal": "200.00",
                    "unitName": "MB",
                    "endTime": "2014-04-01 00:00:00",
                    "offerName": "天翼乐享3G上网版49元套餐",
                    "startTime": "2014-03-01 00:00:00",
                    "state": "0"
                }
            ]
        },
        {
            "catalogName": "综合VPN",
            "items": [
                {
                    "accuName": "主叫时长",
                    "cumulationAlready": "8.00",
                    "cumulationLeft": "1492.00",
                    "cumulationTotal": "1500.00",
                    "unitName": "分钟",
                    "endTime": "2014-04-01 00:00:00",
                    "offerName": "综合VPN",
                    "startTime": "2014-03-01 00:00:00",
                    "state": "0"
                }
            ]
        },
        {
            "catalogName": "院线通赠送300M手机上网流量",
            "items": [
                {
                    "accuName": "手机上网全国流量",
                    "cumulationAlready": "10.11",
                    "cumulationLeft": "289.89",
                    "cumulationTotal": "300.00",
                    "unitName": "MB",
                    "endTime": "2014-04-01 00:00:00",
                    "offerName": "院线通赠送300M手机上网流量",
                    "startTime": "2014-03-01 00:00:00",
                    "state": "0"
                }
            ]
        },
        {
            "catalogName": "流量优惠/流量赠送/30M/当月-JS2012",
            "items": [
                {
                    "accuName": "手机上网全国流量",
                    "cumulationAlready": "11.23",
                    "cumulationLeft": "18.77",
                    "cumulationTotal": "30.00",
                    "unitName": "MB",
                    "endTime": "2014-04-01 00:00:00",
                    "offerName": "流量优惠/流量赠送/30M/当月-JS2012",
                    "startTime": "2014-03-03 00:00:00",
                    "state": "0"
                }
            ]
        },
        {
            "catalogName": "每月赠送手机上网流量300M，连送2个月",
            "items": [
                {
                    "accuName": "手机上网全国流量",
                    "cumulationAlready": "0.00",
                    "cumulationLeft": "300.00",
                    "cumulationTotal": "300.00",
                    "unitName": "MB",
                    "endTime": "2014-04-01 00:00:00",
                    "offerName": "每月赠送手机上网流量300M，连送2个月",
                    "startTime": "2014-03-03 00:00:00",
                    "state": "0"
                }
            ]
        },
        {
            "catalogName": "流量优惠/流量券/200M省内流量/当月",
            "items": [
                {
                    "accuName": "手机上网省内流量",
                    "cumulationAlready": "18.57",
                    "cumulationLeft": "181.43",
                    "cumulationTotal": "200.00",
                    "unitName": "MB",
                    "endTime": "2014-04-01 00:00:00",
                    "offerName": "流量优惠/流量券/200M省内流量/当月",
                    "startTime": "2014-03-03 00:00:00",
                    "state": "0"
                }
            ]
        },
        {
            "catalogName": "流量优惠/流量券/100M省内流量/当月",
            "items": [
                {
                    "accuName": "手机上网省内流量",
                    "cumulationAlready": "44.67",
                    "cumulationLeft": "55.33",
                    "cumulationTotal": "100.00",
                    "unitName": "MB",
                    "endTime": "2014-04-01 00:00:00",
                    "offerName": "流量优惠/流量券/100M省内流量/当月",
                    "startTime": "2014-03-03 00:00:00",
                    "state": "0"
                }
            ]
        },
        {
            "catalogName": "流量优惠/流量券/30M省内流量/当月",
            "items": [
                {
                    "accuName": "手机上网省内流量",
                    "cumulationAlready": "30.00",
                    "cumulationLeft": "0.00",
                    "cumulationTotal": "30.00",
                    "unitName": "MB",
                    "endTime": "2014-04-01 00:00:00",
                    "offerName": "流量优惠/流量券/30M省内流量/当月",
                    "startTime": "2014-03-03 00:00:00",
                    "state": "0"
                }
            ]
        }
    ]
}';

    $data=json_decode($data);
    return $data;
}

