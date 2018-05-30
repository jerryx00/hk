<?php

namespace Qwadmin\Service;
class HlyService  extends HlyCommonService{

    public function getToken() {
        $vo = M('hlytoken')->where($d)->find();
        $flag = I('flag');

        $d['id'] = ['eq', '1'];
        // if ($vo) {
        //            $token = $vo['token'];
        //            $expired_at = $vo['expired_at'];
        //            $created_at = $vo['created_at'];
        //            $t=time();
        //            $d['created_at'] = ['gt', $t];
        //            $d['expired_at'] = ['lt', $t];
        //        } else {
        //返回是数组
        $auth = $this->getNewToken();
        //更新
        $data['token']=$auth['Token'];
        $created_at = strtotime($auth['CreatedTime']);
        $expired_at = strtotime($auth['ExpiredTime']);
        $data['created_at']=$created_at;
        $data['expired_at']=$created_at;

        \Think\Log::record('getToken ==>result: '.$auth." ,token ==> " .$data['token']);
        $ret = M('hlytoken')->where(['id'=>'1'])->data($data)->save();
        //        }
        if ($flag == '1') {
			dump($auth);
        }
        return $auth['Token'];
    }
    
    public function getNewToken() {
        $time = getMillisecond();
        $appkey = C('AppKey');
        $SecretKey = C('SecretKey');

        $sign = get_hly_sign($time);

        $xmldata['Datetime'] = $time;
        $xmldata['Authorization']['AppKey'] = $appkey;
        $xmldata['Authorization']['sign'] = $sign;
        $url = C('HLY_AUTH_URL');

        $paras = xml_encode($xmldata, 'Request');

        \Think\Log::record('getNewToken url ==> '.$url);
        \Think\Log::record('getNewToken paras ==> '.$paras);
        list($result, $returnContent) = http_post_hly($url, $paras, '', '');

        //        $result = 200;
        //$returnContent = '<Response>
        //        <Datetime>2017-05-04T11:23:13.729+08:00</Datetime>
        //        <Authorization>
        //        <Token>eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJmbG93LXBsYXRmb3JtIiwiYXVkIjoiYjM5NWU2NmRlNWQ5NDU4YjhiOGVlZWRkNjAyY2U3ZDM6OjoiLCJleHAiOjE0OTM4Njk5OTN9.NHQ2PKaW45Q_l_WOx0-YzZ-TjY7o_TL3yJGHvcIFJp8</Token>
        //        <ExpiredTime>2017-05-04T11:53:13.729+08:00</ExpiredTime>
        //        <CreatedTime>2017-05-04T11:23:13.729+08:00</CreatedTime>
        //        </Authorization>
        //        </Response>
        //        ';

        if ($result == '200'){
            $xml = (array)simplexml_load_string($returnContent);
        }
        $token = object_array($xml['Authorization']);
//        $token = $Authorization['Token'];
         \Think\Log::record('getNewToken result ==> '.$token);

        return $token;
    }

    public function getNum($d=[]) {
        $time = getMillisecond();
        $appkey = C('AppKey');
        $SecretKey = C('SecretKey');

        $token = $this->getToken();
        \Think\Log::record('getNum token ==> '.$token);
        $sign = get_hly_sign($time);

        $xmldata['Datetime'] = $time;
        $Region = $d['region'];
        $Fitmod = $d['fitmod'];
        $MaxPrice = $d['maxprice'];
        $MinPrice = $d['minprice'];
        $MaxCount = $d['maxcount'];
        $PageIndex = $d['pageindex'];



        $xmldata['Content']['Region'] = $Region;
        $xmldata['Content']['Fitmod'] = $Fitmod;
        $xmldata['Content']['MaxPrice'] = $MaxPrice;
        $xmldata['Content']['MinPrice'] = $MinPrice;
        $xmldata['Content']['MaxCount'] = $MaxCount;
        $xmldata['Content']['PageIndex'] = $PageIndex;

        $url = C('HLY_GETNUM_URL');

        $paras = xml_encode($xmldata, 'Request');

        list($result, $returnContent) = http_post_hly($url, $paras, $token, $sign);

        // $result = 200;
        //        $returnContent = '
        //        <Response>
        //        <Datetime>1516153980960</Datetime>
        //        <Content>
        //        <ReturnCode>0000</ReturnCode>
        //        <ReturnMessage>success</ReturnMessage>
        //        <TelnumList>
        //        <TelNum>14100128022</TelNum>
        //        <TelPrice>0</TelPrice>
        //        </TelnumList>
        //        <TelnumList>
        //        <TelNum>14100128023</TelNum>
        //        <TelPrice>0</TelPrice>
        //        </TelnumList>
        //        <TelnumList>
        //        <TelNum>14100128024</TelNum>
        //        <TelPrice>0</TelPrice>
        //        </TelnumList>
        //        <TelnumList>
        //        <TelNum>14100128025</TelNum>
        //        <TelPrice>0</TelPrice>
        //        </TelnumList>
        //        <TelnumList>
        //        <TelNum>14100127964</TelNum>
        //        <TelPrice>0</TelPrice>
        //        </TelnumList>
        //        <TelnumList>
        //        <TelNum>14100127965</TelNum>
        //        <TelPrice>0</TelPrice>
        //        </TelnumList>
        //        <TelnumList>
        //        <TelNum>14100127966</TelNum>
        //        <TelPrice>0</TelPrice>
        //        </TelnumList>
        //        <TelnumList>
        //        <TelNum>14100127967</TelNum>
        //        <TelPrice>0</TelPrice>
        //        </TelnumList>
        //        <TelnumList>
        //        <TelNum>14100127968</TelNum>
        //        <TelPrice>0</TelPrice>
        //        </TelnumList>
        //        <TelnumList>
        //        <TelNum>14100127969</TelNum>
        //        <TelPrice>0</TelPrice>
        //        </TelnumList>
        //        </Content>
        //        </Response>
        //        ';
        //
        if ($result == '200'){
            $xml = (array)simplexml_load_string($returnContent);
        }
        \Think\Log::record('getNum ==>result: '.$result. ";ReturnMessage:".$returnContent);
        $Content = object_array($xml['Content']);
        $ReturnCode = $Content['ReturnCode'];
        $ReturnMessage = $Content['ReturnMessage'];

        if ($ReturnCode == '0000') {
            $con = object_array($Content);
            $telList = $con['TelnumList'];
            foreach ($telList as $k => $v) {
                $telList[$k]['status'] = $ReturnCode;
                $telList[$k]['remark'] = $ReturnMessage;
            }
            return $telList;
        }
    }

    public function idenCheck($d = []){
        $time = getMillisecond();
        $sign = get_hly_sign($time);
        if (count($d) == 0) {
            $d['idcard'] = I('idcard');
            $d['mobile'] = I('mobile');
            $d['username'] = I('username');
        }
        $url = C('HLY_IDENCHECK_URL');

        $xmldata['Datetime'] = $time;
        $xmldata['IdenCheck']['IdCard'] = $d['idcard'];
        $xmldata['IdenCheck']['Name'] = $d['username'];
        $xmldata['IdenCheck']['Mobile'] = $d['telnum'];

        $paras = xml_encode($xmldata, 'Request');

        //后续要优化，半小时取一次
        $token = $this->getToken();

        $sign = get_hly_sign($time);
        list($result, $returnContent) = http_post_hly($url, $paras, $token, $sign);

        //$result = 200;
        //        $returnContent = '
        //        <Response>
        //        <Datetime>1516155816683</Datetime>
        //        <Content>
        //        <ReturnCode>0000</ReturnCode>
        //        <ReturnMessage>校验通过</ReturnMessage>
        //        </Content>
        //        </Response>
        //        ';

        if ($result == '200'){
            $xml = (array)simplexml_load_string($returnContent);
        }
        $Content = object_array($xml['Content']);
        return $Content;

    }
    public function lockNum($d=[]) {
        $time = getMillisecond();
        $appkey = C('AppKey');
        $SecretKey = C('SecretKey');

        $token = $this->getToken();
        $sign = get_hly_sign($time);



        $Region = $d['region'];
        $TelNum = $d['telnum'];

        $xmldata['Datetime'] = $time;
        $xmldata['Content']['Region'] = $Region;
        $xmldata['Content']['Telnum'] = $TelNum;
        $xmldata['Content']['Type'] = '2';

        $url = C('HLY_LOCKNUM_URL');

        $paras = xml_encode($xmldata, 'Request');

        list($result, $returnContent) = http_post_hly($url, $paras, $token, $sign);

        //$result = 200;
        //        $returnContent = '
        //        <Response>
        //        <Datetime>1516155816683</Datetime>
        //        <Content>
        //        <ReturnCode>0</ReturnCode>
        //        <ReturnMessage></ReturnMessage>
        //        </Content>
        //        </Response>
        //
        //        ';

        $data = [];
        if ($result == '200'){
            $xml = (array)simplexml_load_string($returnContent);
            $Content = object_array($xml['Content']);
            $data['ReturnCode'] = $Content['ReturnCode'];
            $data['ReturnMessage'] = $Content['ReturnMessage'];
            $data['Datetime'] = $xml['Datetime'];
        }
        return $data;

    }
    
     public function unLockNum($d=[]) {
        $time = getMillisecond();
        $appkey = C('AppKey');
        $SecretKey = C('SecretKey');

        $token = $this->getToken();
        $sign = get_hly_sign($time);



        $Region = $d['region'];
        $TelNum = $d['telnum'];

        $xmldata['Datetime'] = $time;
        $xmldata['Content']['Region'] = $Region;
        $xmldata['Content']['Telnum'] = $TelNum;
        $xmldata['Content']['Type'] = '2';

        $url = C('HLY_UNLOCK_URL');

        $paras = xml_encode($xmldata, 'Request');

        list($result, $returnContent) = http_post_hly($url, $paras, $token, $sign);

        //$result = 200;
        //        $returnContent = '
        //        <Response>
        //        <Datetime>1516155816683</Datetime>
        //        <Content>
        //        <ReturnCode>0</ReturnCode>
        //        <ReturnMessage></ReturnMessage>
        //        </Content>
        //        </Response>
        //
        //        ';

        $data = [];
        if ($result == '200'){
            $xml = (array)simplexml_load_string($returnContent);
            $Content = object_array($xml['Content']);
            $data['ReturnCode'] = $Content['ReturnCode'];
            $data['ReturnMessage'] = $Content['ReturnMessage'];
            $data['Datetime'] = $xml['Datetime'];
        }
        return $data;

    }

    public function order($order_info_dt_t,$delivery_info_dt_t, $biz_info_dt_t) {
        $time = getMillisecond();

        $order_info_dt['booking_id'] = $order_info_dt_t['booking_id'];
        $order_info_dt['offer_id'] = $order_info_dt_t['offer_id'];
        $order_info_dt['Remark'] = $order_info_dt_t['Remark'];

        $delivery_info_dt['delivery_addr'] = $delivery_info_dt_t['delivery_addr'];
        $delivery_info_dt['delivery_period'] = $delivery_info_dt_t['delivery_period'];
        $delivery_info_dt['delivery_name'] = $delivery_info_dt_t['delivery_name'];
        $delivery_info_dt['delivery_phone'] = $delivery_info_dt_t['delivery_phone'];

        $biz_info_dt['cust_name'] = $biz_info_dt_t['cust_name'];
        $biz_info_dt['ic_no'] = $biz_info_dt_t['ic_no'];
        $biz_info_dt['contact_name'] = $biz_info_dt_t['cust_name'];
        $biz_info_dt['contact_phone'] = $biz_info_dt_t['contact_phone'];
        $biz_info_dt['acc_nbr'] = $biz_info_dt_t['acc_nbr'];

        $appkey = C('AppKey');
        $SecretKey = C('SecretKey');

        $token = $this->getToken();
        $sign = get_hly_sign($time);

        $xmldata['Datetime'] = $time;
        $xmldata['Content']['order_info_dt'] = $order_info_dt;
        $xmldata['Content']['delivery_info_dt'] = $delivery_info_dt;
        $xmldata['Content']['biz_info_dt'] = $biz_info_dt;


        $url = C('HLY_ORDER_URL');

        $paras = xml_encode($xmldata, 'Request');


        list($result, $returnContent) = http_post_hly($url, $paras, $token, $sign);

        //         $result = 200;
        //         $returnContent = '<Response><Datetime>1516155816683</Datetime><Content><resp__code>0000</resp_code><resp__msg>已下单</resp_msg><order__id>6352052992978554880</order_id></Content></Response>';
        //
        $data = [];
        if ($result == '200'){
            $xml = (array)simplexml_load_string($returnContent);
            $Content = object_array($xml['Content']);
            $data['respcode'] = $Content['resp__code'];
            $data['respmsg'] = $Content['resp__msg'];
            $data['respid'] = $Content['order__id'];
            $data['datetime'] = $xml['Datetime'];
        }
        return $data;

    }


    public function orderCancle($d=[]) {
        $time = getMillisecond();
        $appkey = C('AppKey');
        $SecretKey = C('SecretKey');

        $token = $this->getToken();
        $sign = get_hly_sign($time);



        $orderId = $d['orderId'];
        $cancel_reason = $d['cancel_reason'];
        $comments = $d['comments'];

        $xmldata['Datetime'] = $time;
        $xmldata['Content']['orderId'] = $d['orderId'];
        $xmldata['Content']['cancel_reason'] = $d['cancel_reason'];
        $xmldata['Content']['comments'] = $d['comments'];

        $url = C('HLY_ORDERCANCEL_URL');

        $paras = xml_encode($xmldata, 'Request');

        list($result, $returnContent) = http_post_hly($url, $paras, $token, $sign);

        $data = [];
        if ($result == '200'){
            $xml = (array)simplexml_load_string($returnContent);
            $Content = object_array($xml['Content']);
            $data['ReturnCode'] = $Content['retCode'];
            $data['ReturnMessage'] = $Content['retMsg'];
            $data['Datetime'] = $xml['Datetime'];
        }
        return $data;

    }
}
