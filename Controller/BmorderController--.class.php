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
            $reloadUrl = U('Bmorder/add');
            $security = '3de2f606-a0dc-11e6-b002-c171f1672fb4';

            $u = $this->USER;

            //            $d = $_POST['data'];
            $ajax = I('post.ajax');
            if ($ajax == '1') {
                $p = $_POST;    
            }  else {
                $p = $_POST['data'];
            }


            //$d['scope'] =  ($d['scope']);
            $d['effecttype'] =  $p['activeflag'];   //生效类型             
            $d['iftype'] = '0';
            $d['created_at'] = time();
            $d['fluxid'] = $p['fluxnum'];            
            $d['productid'] = $p['fluxnum'];
            $d['security'] = $security;
            $d['timestamp']= get_date(); 
            $d['userorderno']  = 'jinwd'.getOrderno($u);  //订单号

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




            $d['mobile'] =  $p['mobile']; 

            $goodList = M('goods_bm')->where(['fluxnum' => $p['fluxnum'], 'status'=>'1'])->find();

            //if ($goodList) {
            //                $d['productid'] = $goodList['fluxid'];
            //            } else {
            //                return $this->errorReturn('该商品不存在,请重新提交！', $reloadUrl);
            //            } 


            $ret = M('order_log_yt')->data($d)->add();                        
            if ($ret === false) {
                return $this->errorReturn('输入错误,请重新提交！', $reloadUrl);
            }    
            unset($d['fluxid']); 
            unset($d['iftype']); 
            unset($d['created_at']); 

            $sign= md5_baimiao($d);  

            $d['sign'] = $sign;
            $d['productid'] =  passport_encrypt($p['fluxnum'], $security);  
            $d['mobile'] =  passport_encrypt($p['mobile'], $security);  
            //            $d['backurl'] = C('hostApiUrl').'Bmauth/notify';  //回调 URL  ,可以考虑用配置文件来实现，以便服务器切换  

            $hostUrl = C('hostApiUrl');
            //            $hostUrl = 'http://139.224.59.94/llfx/index.php';
            $url = $hostUrl.'/Bmauth/order'; 

            unset($d['security']);        

            $jsonStr = json_encode($d);
            //var_dump($url);
            //            var_dump($jsonStr);exit;
            list($result, $returnContent) = http_post($url, $jsonStr);

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

        public function notitydetail() { 
            $id = I('get.id');
            $list = M('json_notify')->where(['id'=>$id])->find();
            $this->vo = $list;
            $this->display();

        }


        //        手机号码为18551820869加密后为  sjujqXnjHX9cIeplqGpQKg==
        //productid加密后为qS3NiiKB5u0ZYpQwjMo78w==

        

}