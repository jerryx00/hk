<?php
/**
*
* 版权所有：金豌豆<>
* 作    者：国平<8688041@qq.com>
* 日    期：2016-09-20
* 版    本：1.0.0
* 功能说明：文章控制器。
*
**/ 
namespace Qwadmin\Controller;
use Qwadmin\Controller\ComController;
use Think\Controller\RestController;
use Org\Util\DES; 
use Org\Util\Mycrypt3des;


/**
* CZ接口
*/
class HforderController extends BasehforderController {

    protected $ORDER_POST_URL = '/Njauth/order?';
    protected $ORDER_NOTIFY_URL = '/Njauth/notify';


    protected $GOODS_MODEL = 'goods_hf';
    protected $ORDER_MODEL = 'order_hf';

    protected $ORDER_AREAID = '';

    protected $SP_TYPE = '0';
    protected $MODE_TEST = false; 
    //protected $supplier = ['order_yt', 'order_nj','order_jwd'];


    public function index() { 
        $this->ORDER_MODEL = 'order_hf';
        $areaid = I('get.areaid');
        $this->ORDER_AREAID = $areaid;
        parent::index('index');
    }


    public function report() { 
        $areaid = I('get.areaid');
        $this->ORDER_MODEL = $this->ORDER_MODEL;
        $this->ORDER_AREAID = $areaid;
        $sql = "SELECT areaid,batchid AS kid,count(id) as num,sum(fluxnum) as total_fund,sum(price) as real_fund,notify_ret,FROM_UNIXTIME(created_at,'%Y%m%d') as created_at FROM qw_order_hf" .
        "  GROUP BY areaid,batchid,notify_ret ORDER BY FROM_UNIXTIME(created_at,'%Y%m%d') desc,kid";

        $list = M($this->ORDER_MODEL)->query($sql);
        $i = 0;
        
        foreach ($list as $k => $v) {
            if ($v['kid']== '') {
                $bid = '0';
            } else {
                $bid = $v['kid'];
            }
            $kid = $bid;

            
           
            if ($v['notify_ret'] == '255') {
                //待处理
                $r[$kid]['num_2'] = $v['num'];                
                $r[$kid]['num_2_fund'] = $v['total_fund'];                
            } else if ($v['notify_ret'] == '300') {
                //充值中
                $r[$kid]['num_3'] = $v['num'];
                $r[$kid]['num_3_fund'] = $v['total_fund'];
            } else if ($v['notify_ret'] == '0') {
                //成功
                $r[$kid]['num_0'] = $v['num'];
                $r[$kid]['fund_s'] = $v['total_fund'];
                $r[$kid]['fund_r'] = $v['real_fund'];
                $r[$kid]['num_0_fund'] = $v['total_fund'];
            }
            else if ($v['notify_ret'] == '1' ) {
                //失败
                $r[$kid]['num_1'] = $v['num'];
                $r[$kid]['num_1_fund'] = $v['total_fund'];
            } else {

            }

            $r[$kid]['kid'] = $v['kid'];
            $r[$kid]['batchid'] = $v['kid'];
            $r[$kid]['created_at'] = $v['created_at'];
            $r[$kid]['areaid'] = $v['areaid'];

            // $r[$kid]['num'] =  $r[$kid]['num_0'] +  $r[$kid]['num_1'] +  $r[$kid]['num_2']+ $r[$kid]['num_3'];
        }
        foreach ($r as $k => $v) {
           $total = $v['num_0'] +  $v['num_1'] + $v['num_2'] + $v['num_3'];
           $fund_today = $v['num_0_fund'] +  $v['num_1_fund'] + $v['num_2_fund'] + $v['num_3_fund'];
           $r[$k]['total']  =  $total;
           $r[$k]['total_today']  =  $fund_today;
        }
//        dump($r);exit;
        $this->list = $r;
        $this->display();
    }



    public function indexhf() { 
        $areaid = I('get.areaid');
        $this->ORDER_MODEL = $this->ORDER_MODEL;
        $this->ORDER_AREAID = $areaid;
        parent::index('indexh');
    }


    public function manuafix(){
        $this->ORDER_MODEL = I('get.m');           
        parent::manuafix();
    }
    public function add() { 
        $fluxlist = M($this->GOODS_MODEL)->where(['status'=>1])->order('id desc')->select();
        $this->fluxgroup = $fluxlist;            
        $this->display();

    }
    public function insert() {

        \Think\Log::record('提交URL:'.$url.';返回结果:'.$returnContent);  
        $u = $this->USER;


        //            $d = $_POST['data'];
        $ajax = I('post.ajax');
        if ($ajax == '1') {
            $d = $_POST;    
        }  else {
            $d = $_POST['data'];
        }
        $this->ORDER_MODEL = $d['sp'];


        $d['scope'] =  intval($d['scope']);
        $d['activeflag'] =  intval($d['activeflag']);
        $d['expiration'] =  intval($d['expiration']);

        $d['operator'] =  intval($d['operator']);


        foreach ($d as $k => $v) {
            if (!isset($v)) {
                if ($ajax == '1') {
                    $ret['header']['errcode'] = '1000999';
                    $ret['header']['errmsg'] = '输入错误,请重新提交';
                    $this->ajaxReturn($ret);

                }  else {
                    return $this->errorReturn('输入错误,请重新提交！', $reloadUrl); 
                }  
            }
        }

        // for($i=0; $i<=count($d); $i++){
        //                if (empty($d[$i])) {
        //                    return $this->errorReturn('输入错误,请重新提交！', $reloadUrl);
        //                }
        //            }
        $h['timestamp']= get_date(); 


        $goodList = M($this->GOODS_MODEL)->where(['id' => $d['id'], 'status'=>'1'])->find();

        if ($goodList) {
            $d['fluxid'] = $goodList['fluxid'];
            $d['fluxnum'] =  intval($d['fluxnum']);
        } else {

            $ret['header']['errcode'] = '1000999';
            $ret['header']['errmsg'] = '该商品不存在,请重新提交！';
            $this->ajaxReturn($ret);
        }
        //$d['orderno']=  $h['timestamp'] .$u['uid'].'0'.rand(1, 10000); //客户系统的订单号 
        //xugp 2017-01-24 modify
        $d['orderno']=  $u['accesscode'].$h['timestamp'] .$u['uid'].'0'.rand(1, 100000); //客户系统的订单号  
        $d['iftype'] = '0';
        $d['created_at'] = time();


        $ret = M($this->ORDER_MODEL.'_log')->data($d)->add();
        if ($ret === false) {
            $ret['header']['errcode'] = '1000999';
            $ret['header']['errmsg'] = '保存log失败！';
            $this->ajaxReturn($ret);
        }



        $d['backurl'] = C('hostApiUrl').$this->ORDER_NOTIFY_URL;  //回调 URL  ,可以考虑用配置文件来实现，以便服务器切换 

        $p['appid'] = $u['actioncode'];      //平台分配的客户标识
        $p['access_token'] = $u['accesstoken']; 
        $p['appsecret'] = $u['intersource']; 

        $d['timestamp']= get_date(); 
        $d['user']= $u['user'];;

        $h['type']= 'json'; 


        $h['identity']=  $d['orderno'];  //一次请求的唯一标识码，建议调用方生成唯一标识


        $sign = md5_yingtong($h, $d, $p);

        $h['sign']= $sign; 

        $req['header'] = $h;
        $req['payload']['data'] = $d;

        $hostApiUrl = C('hostApiUrl');
        if ($this->MODE_TEST) {
            $hostApiUrl = 'http://localhost/llfxdev/api.php';   
        }             

        $url = $hostApiUrl. $this->ORDER_POST_URL;
        $url = $url . 'appid='.$u['actioncode'] .'&';
        $url = $url . 'access_token=' .$u['accesstoken'];

        $jsonStr = json_encode($req);
        //var_dump($url);
        //            var_dump($jsonStr);exit;

        //            list($result, $returnContent) = http_post($url, $jsonStr);

        //===================================
        $url = "http://139.224.59.94:5160/unicomAync/buy.do";
        $privteKey = 'cb433efc359941b867e6b5ad0a0e5654abb15e5667ab99e7bb67db1471c80f41';
        $userId = '188';
        $paras['userId'] = $userId;            
        $paras['itemId'] = $d['fluxid'];
        $paras['uid'] = $d['mobile'];
        $paras['serialno'] = $d['orderno'];
        $paras['dtCreate'] = $d['timestamp'];
        ksort($paras);
        $i = 0;
        foreach ($paras as $k => $v) {
            $i ++;
            //                if ($i < 5) {
            $sing_ori = $sing_ori.$v;
            //                }                            
        }
        $paras['sign'] = MD5($sing_ori.$privteKey);;

        $returnC = send_get_jh($url, $paras);
        $xml = simplexml_load_string($returnC);
        $xmlRet = object_array($xml);

        if ($xmlRet['code']=='00') {
            $info['errcode'] = '0';
        } else {
            $info['errcode'] = $xmlRet['code'];               
        }
        $info['errmsg'] = $xmlRet['desc'];
        $info['status'] = $xmlRet['status'];
        $header['header'] = $info;

        $returnContent = json_encode($header);
        //===================================
        \Think\Log::record('提交URL:'.$url.';返回结果:'.$returnContent); 


        //var_dump($jsonStr);exit;

        if ($ajax == '1') {                 
            $retContent = json_decode($returnContent);
            if ($retContent == null){
                $retContent['header']['errcode'] = '1000991';
                $retContent['header']['errmsg'] = '网络异常'.$url;
            } 
            $this->ajaxReturn($retContent);
        }  else {
            $ret = json_decode($returnContent);
        } 

    }

    public function view() { 
        $id = I('get.id');
        $vo = M($this->ORDER_MODEL)->where(['id'=>$id])->find();
        $this->vo = $vo;
        $this->display();

    }

    /**
    * 查看回调信息
    * 
    */
    public function notifyinfo() { 
        $orderid = I('get.orderid');
        $list = M('json_notify')->where(['orderid'=>$orderid])->select();
        $this->list = $list;
        $this->display();

    }

    /**
    * 查看某条回信息的明细
    * 
    */
    public function notitydetail() { 
        $id = I('get.id');
        $list = M('json_notify')->where(['id'=>$id])->find();
        $this->vo = $list;
        $this->display();

    }

    /**
    * 查看回调日志
    * 
    */
    public function manua(){
        $id = I('get.id');
        $sql = 'select * from '.$prefix.$this->ORDER_MODEL.' a, qw_json_notify b where a.orderno = b.orderno and b.id='.$id;
        //$list = M('json_notify')->where(['id'=>$id])->find();
        $list = M('json_notify')->query($sql);
        $vo = $list[0];
        $data = $vo['log'];  
        $url = $vo['backurl'];              
        list($result, $returnContent) = http_post($url, $data);
        $ret =  $returnContent;  
        if ($ret == 'ok') {
            $this->success('回调成功('. $ret.')');
        } else {
            $this->error('回调失败('. $ret.')');
        }     

    }  

}