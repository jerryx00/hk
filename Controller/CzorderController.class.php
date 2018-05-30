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
    class CzorderController extends BaseorderController {

        protected $ORDER_POST_URL = '/Czauth/order?';
        protected $ORDER_NOTIFY_URL = '/Czauth/notify';

        protected $ORDER_MODEL = 'order_cz';
        protected $GOODS_MODEL = 'goods_cz';



        public function add() { 
            $fluxlist = M($this->GOODS_MODEL)->where(['status'=>1])->select();
            $this->fluxgroup = $fluxlist;
            $this->display();

        }
        public function insert() { 
            $u = $this->USER;

            //            $d = $_POST['data'];
            $ajax = I('post.ajax');
            if ($ajax == '1') {
                $d = $_POST;    
            }  else {
                $d = $_POST['data'];
            }


            $d['scope'] =  intval($d['scope']);
            $d['activeflag'] =  intval($d['activeflag']);
            $d['expiration'] =  intval($d['expiration']);
            $d['fluxnum'] =  intval($d['fluxnum']);
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


            $goodList = M($this->ORDER_MODEL)->where(['fluxnum' => $d['fluxnum'], 'status'=>'1'])->find();

            if ($goodList) {
                $d['fluxid'] = $goodList['fluxid'];
            } else {
                return $this->errorReturn('该商品不存在,请重新提交！', $reloadUrl);
            }
            $d['orderno']=  $h['timestamp'] .$u['uid'].'0'.rand(1, 100000); //客户系统的订单号 
            $d['iftype'] = '0';
            $d['created_at'] = time();


            $ret = M($this->ORDER_MODEL.'_log')->data($d)->add();
            if ($ret === false) {
                return $this->errorReturn('输入错误,请重新提交！', $reloadUrl);
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

            $hostUrl = C('hostApiUrl');
            //            $hostUrl = 'http://139.224.59.94/llfx/api.php';
            $url = $hostUrl. $this->ORDER_POST_URL;
            $url = $url . 'appid='.$u['actioncode'] .'&';
            $url = $url . 'access_token=' .$u['accesstoken'];

            $jsonStr = json_encode($req);
            //var_dump($url);
            //            var_dump($jsonStr);exit;
            list($result, $returnContent) = http_post($url, $jsonStr);

            //var_dump($jsonStr);exit;

            if ($ajax == '1') {                 
                $retContent = json_decode($returnContent);
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


        /**
        * 测试加密，通过 2016-11-27
        * 
        */
        public function d() { 
            $str = "15251823848";  
            $key = "0Gn1b2uHgj9lnA5qlef4yE7x"; 
            $crypt = new Mycrypt3des();
            $crypt->Mycrypt3des($key);
            $mstr = $crypt->encrypt($str);  
            echo $mstr.'<br/>';         

            $dstr = $crypt->decrypt($mstr);
            echo $dstr.'<br/>';    
        }   

}