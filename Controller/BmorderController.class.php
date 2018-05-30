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


    /**
    * 百妙接口
    */
    class BmorderController extends ComController {

        private $AUTH_POST = '/Czauth/order.html?';
        private $AUTH_NOTIFY = '/Czauth/notify';
        
        public function index() {
            $u = $this->USER;
            $p = isset($_GET['p'])?intval($_GET['p']):'1';
            $field = isset($_GET['field'])?$_GET['field']:'';
            $keyword = isset($_GET['keyword'])?htmlentities($_GET['keyword']):'';
            $order = isset($_GET['order'])?$_GET['order']:'DESC';
            $where = '';

            if ($u['uid'] != '1') {
                $w = " status=1 " ;    
            }   else {
                $w = " status=1 " ;
            }


            $prefix = C('DB_PREFIX');
            if($order == 'asc'){
                $order = "{$prefix}order_bm.created_at asc";
            }elseif(($order == 'desc')){
                $order = "{$prefix}order_bm.created_at desc";
            }else{
                $order = "{$prefix}order_bm.created_at desc";
            }
            if($keyword <>''){
                if($field=='user'){
                    $where = "{$prefix}order_bm.user LIKE '%$keyword%'";
                }
                if($field=='mobile'){
                    $where = "{$prefix}order_bm.mobile LIKE '%$keyword%'";
                }
                if($field=='fluxnum'){
                    $where = "{$prefix}order_bm.fluxnum LIKE '%$keyword%'";
                }
                if($field=='order_ret'){
                    $where = "{$prefix}order_bm.order_ret LIKE '%$keyword%'";
                } if($field=='notify_ret'){
                    $where = "{$prefix}order_bm.notify_ret LIKE '%$keyword%'";
                }


                $where = $where. " and ". $w;
            }   else 
            {
                $where = $where. $w;
            }

            //$s = D('OrderYt', 'Service')->encryptPassword($w);



            $user = M('order_bm');
            $pagesize = 10;#每页数量
            $offset = $pagesize*($p-1);//计算记录偏移量
            $count = $user->where($where)->count();

            $list  = $user->field("{$prefix}order_bm.*")->order($order)->where($where)->limit($offset.','.$pagesize)->select();


            //echo $user->getLastSql();exit;
            $page    =    new \Think\Page($count,$pagesize); 
            $page = $page->show();
            $this->assign('list',$list);    
            $this->assign('page',$page);            
            $this->cc = "省内"; 
            $this -> display();
        }

        public function add() { 
            $fluxlist = M('goods_bm')->where(['status'=>1])->select();
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


            $goodList = M('goods_bm')->where(['fluxnum' => $d['fluxnum'], 'status'=>'1'])->find();

            if ($goodList) {
                $d['fluxid'] = $goodList['fluxid'];
            } else {
                return $this->errorReturn('该商品不存在,请重新提交！', $reloadUrl);
            }
            $d['orderno']=  $h['timestamp'] .$u['uid'].'0'.rand(1, 100000); //客户系统的订单号 
            $d['iftype'] = '0';
            $d['created_at'] = time();


            $ret = M('order_log_bm')->data($d)->add();
            if ($ret === false) {
                return $this->errorReturn('输入错误,请重新提交！', $reloadUrl);
            }



            $d['backurl'] = C('hostApiUrl').$this->AUTH_NOTIFY;  //回调 URL  ,可以考虑用配置文件来实现，以便服务器切换  

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
            //            $hostUrl = 'http://139.224.59.94/llfx/index.php';
             $url = $hostUrl. $this->AUTH_POST;
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
            $vo = M('order_bm')->where(['id'=>$id])->find();
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

        public function notify1() { 
            $returnContent = $GLOBALS['HTTP_RAW_POST_DATA'];
            $returnContent = 'ok';
            $data = ['ok'];
            //            $this->ajaxReturn($data) ;
            echo 'ok';
            return;

        }

        public function notify() {            
            //            $url = 'http://localhost/llfxtest/admin.php/Bmorder/notify';
            //            $url = 'http://120.24.219.210:8080/nanjing/jwd/callback.action';
            //            $a = file_get_contents($url);
            $p['a'] = 'a';
            $jsonStr = json_encode($p);
            //$url = 'http://120.24.219.210:8080/nanjing/jwd/callback.action';
            $url = 'http://211.142.30.9:8080/nanjing/jwd/callback.action';

            list($result, $returnContent) = http_post($url, $jsonStr);  
            $ret = $returnContent;
            if (empty($ret)) {
                $ret = 'fail-3';
            }
            $ddd['msg'] = trim($ret); 
            $ddd['log'] = $jsonStr;  //不需要加 json_decode
            return $ddd;          



        }   

        public function des() { 
            $str = '18551820869';   //加密后 sjujqXnjHX9cIeplqGpQKg== 
//            $str = '17798566769';   //加密后 Y1CeK5rQBmnLG62PNzEbiw==
            $key= '3de2f606-a0dc-11e6-b002-c171f1672fb4';
            $des = new DES();
            $des->DES($key);
            $mstr = $des->encrypt($str);
            
            $dstr = $des->decrypt($mstr);


            echo $mstr. '  |   '.$dstr ;            
        }   
}