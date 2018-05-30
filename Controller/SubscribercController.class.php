<?php

    namespace Qwadmin\Controller;
    use Qwadmin\Controller\ComController;
    class SubscribercController extends ComController {

        public function index(){

            $u = $this->USER;
            $p = isset($_GET['p'])?intval($_GET['p']):'1';
            $field = isset($_GET['field'])?$_GET['field']:'';
            $keyword = isset($_GET['keyword'])?htmlentities($_GET['keyword']):'';
            $order = isset($_GET['order'])?$_GET['order']:'DESC';
            $where = '';
            $w = " result <> 0  and province=2  and actived=1 and uid =" . $u['uid'] ;

            $prefix = C('DB_PREFIX');
            if($order == 'asc'){
                $order = "{$prefix}subscriber.flow asc";
            }elseif(($order == 'desc')){
                $order = "{$prefix}subscriber.flow desc";
            }else{
                $order = "{$prefix}subscriber.uid asc";
            }
            if($keyword <>''){
                if($field=='user'){
                    $where = "{$prefix}subscriber.user LIKE '%$keyword%'";
                }
                if($field=='phone'){
                    $where = "{$prefix}subscriber.phone LIKE '%$keyword%'";
                }
                if($field=='flow'){
                    $where = "{$prefix}subscriber.flow LIKE '%$keyword%'";
                }
                if($field=='description'){
                    $where = "{$prefix}subscriber.mark LIKE '%$keyword%'";
                }
                $where = $where. " and ". $w;
            }   else 
            {
                $where = $where .$w;
            }



            $user = M('subscriber');
            $pagesize = 10;#每页数量
            $offset = $pagesize*($p-1);//计算记录偏移量
            $count = $user->where($where)->count();

            $list  = $user->field("{$prefix}subscriber.*")->order($order)->where($where)->limit($offset.','.$pagesize)->select();


            //echo $user->getLastSql();exit;
            $page    =    new \Think\Page($count,$pagesize); 
            $page = $page->show();
            $this->assign('list',$list);    
            $this->assign('page',$page);            
            $this->cc = "全国";
            $this -> display('index');
        }

        
        public function edit(){

            $uid = isset($_GET['id'])?intval($_GET['id']):false;
            if($uid){    
                //$member = M('member')->where("uid='$uid'")->find();

                $user = M('subscriber');
                $member  = $user->where("id=$uid")->find();

            }else{
                $this->error('参数错误！');
            }

            $where['province']="2";//全国
            $where['goodtype']="3";//电信
            $goodgroup = M('goods_group')->where($where)->select();
            $this->assign('goodgroup',$goodgroup);

            $this->assign('subscriber',$member);
            $this->cc = "全国";
            $this -> display('edit');
        }


        public function add(){
            $where['province']="2";//全国
            $where['goodtype']="3";//电信
            $goodgroup = M('goods_group')->where($where)->select();

            $this->assign('goodgroup',$goodgroup);
            $this->cc = "全国";
            $this-> province = 2;
            $this -> display('add');

        }

        /**
        * 历史订单（订购成功的订单）
        * 
        */
        public function historyOrder(){ 
            $u = $this->USER;
            $p = isset($_GET['p'])?intval($_GET['p']):'1';
            $field = isset($_GET['field'])?$_GET['field']:'';
            $keyword = isset($_GET['keyword'])?htmlentities($_GET['keyword']):'';
            $order = isset($_GET['order'])?$_GET['order']:'DESC';
            $where = '';

            $prefix = C('DB_PREFIX');
            if($order == 'asc'){
                $order = "{$prefix}subscriber.flow asc";
            }elseif(($order == 'desc')){
                $order = "{$prefix}subscriber.flow desc";
            }else{
                $order = "{$prefix}subscriber.id asc";
            }
            if($keyword <>''){
                if($field=='user'){
                    $where = "{$prefix}subscriber.user LIKE '%$keyword%'";
                }
                if($field=='phone'){
                    $where = "{$prefix}subscriber.phone LIKE '%$keyword%'";
                }
                if($field=='flow'){
                    $where = "{$prefix}subscriber.flow LIKE '%$keyword%'";
                }
                if($field=='description'){
                    $where = "{$prefix}subscriber.mark LIKE '%$keyword%'";
                }
                $where = $where. " and result = 0 and uid =" . $u['uid'];
            }   else 
            {
                $where = $where. " result = 0 and uid =" . $u['uid'];
            } 

            $user = M('subscriber');
            $pagesize = 10;#每页数量
            $offset = $pagesize*($p-1);//计算记录偏移量
            $count = $user->count();

            $list  = $user->field("{$prefix}subscriber.*")->order($order)->where($where)->limit($offset.','.$pagesize)->select();


            //echo $user->getLastSql();exit;
            $page    =    new \Think\Page($count,$pagesize); 
            $page = $page->show();
            $this->assign('list',$list);    
            $this->assign('page',$page);            

            $this -> display('history');
        }

}