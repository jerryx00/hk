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
class YtorderController extends BaseorderController {

    protected $ORDER_POST_URL = '/Njauth/order?';
    protected $ORDER_NOTIFY_URL = '/Njauth/notify';


    protected $GOODS_MODEL = 'goods_jh';
    protected $ORDER_MODEL = 'order_jh';

    protected $ORDER_AREAID = '';

    protected $SP_TYPE = '0';
    protected $MODE_TEST = false;
    //protected $supplier = ['order_yt', 'order_nj','order_jwd'];


    public function index() {
        $this->ORDER_MODEL = 'order_v';
        parent::index();

    }
    public function indexyt() {
        $this->ORDER_MODEL = 'order_yt';
        parent::index();

    }
    public function indexsp() {
        $this->ORDER_MODEL = 'order_sp';
        parent::index();
    }
    public function indexrk() {
        $this->ORDER_MODEL = 'order_rk';
        parent::index();
    }


    public function indexyd() {
        $this->ORDER_MODEL = 'order_yd';
        parent::index();
    }
    //南京
    public function indexnj() {
        $this->ORDER_MODEL = 'order_nj';
        $this->ORDER_AREAID = '025';
        parent::index('indexh');
    }
    //连云港
    public function indexlyg() {
        $this->ORDER_MODEL = 'order_nj';
        $this->ORDER_AREAID = '0518';
        parent::index('indexh');
    }
    public function indexha() {
        $this->ORDER_MODEL = 'order_ha';
        $this->ORDER_AREAID = '0517';
        parent::index('indexh');

    }
    public function indexhb() {
        $this->ORDER_MODEL = 'order_hb';
        parent::index('indexh');
    }

    public function indexkm() {
        $areaid = I('get.areaid');
        $this->ORDER_MODEL = 'order_km';
        $this->ORDER_AREAID = $areaid;
        parent::index('indexk');
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

        $d['fluxid'] = $d['flux_id'];
//        dump($d['flux_id']);exit;


        if ($d['flux_id'] == "") {
        	$goodList = M($this->GOODS_MODEL)->where(['id' => $d['id'], 'status'=>'1'])->find();
        	$d['fluxid'] = $goodList['fluxid'];
        	$d['fluxnum'] =  intval($d['fluxnum']);
        }

        if ($goodList == false && $d['flux_id'] == "") {
            $ret['header']['errcode'] = '1000999';
            $ret['header']['errmsg'] = '该商品不存在,请重新提交！';
            $this->ajaxReturn($ret);
        }
        //$d['orderno']=  $h['timestamp'] .$u['uid'].'0'.rand(1, 10000); //客户系统的订单号
        //xugp 2017-01-24 modify
        $d['orderno']=  $u['accesscode'].$h['timestamp'] .$u['uid'].'0'.rand(1, 100000); //客户系统的订单号
        $d['iftype'] = '0';
        $d['created_at'] = time();


        //$ret = M($this->ORDER_MODEL.'_log')->data($d)->add();
//        if ($ret === false) {
//            $ret['header']['errcode'] = '1000999';
//            $ret['header']['errmsg'] = '保存log失败！';
//            $this->ajaxReturn($ret);
//        }



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


//        echo "$sing_ori.$privteKey : $sing_ori.$privteKey" ;
//        echo "md :" . $paras['sign'] ;
//        exit;
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


    public function insert1() {

        \Think\Log::record('提交URL:'.$url.';返回结果:'.$returnContent);
        $u = $this->USER;


        $d = $_POST['data'];
        $d['fluxid'] = I('fluxid');
        $d['fluxid'] = I('fluxid');

        $d['timestamp']= get_date();
        $d['user']= $u['user'];;

        $d['orderno']=  $u['accesscode'].$h['timestamp'] .$u['uid'].'0'.rand(1, 100000); //客户系统的订单号
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


//        echo "$sing_ori.$privteKey : $sing_ori.$privteKey" ;
//        echo "md :" . $paras['sign'] ;
//        exit;
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