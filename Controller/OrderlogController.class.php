<?php

    namespace Qwadmin\Controller;
    use Qwadmin\Controller\ComController;
    class OrderlogController extends ComController {

        public function index(){


            $p = isset($_GET['p'])?intval($_GET['p']):'1';
            $field = isset($_GET['field'])?$_GET['field']:'';
            $keyword = isset($_GET['keyword'])?htmlentities($_GET['keyword']):'';
            $order = isset($_GET['order'])?$_GET['order']:'DESC';
            $where = '';

            $u = $this->USER;
            if ($u['uid'] > 2) {
                $w = ' uid = '.$u['uid'];
            }    else {
                $w = ' uid > 0' ;
            }    


            $prefix = C('DB_PREFIX');
            if($order == 'asc'){
                $order = "{$prefix}order_log.create_time asc";
            }elseif(($order == 'desc')){
                $order = "{$prefix}order_log.create_time desc";
            }else{
                $order = "{$prefix}order_log.create_time desc";
            }
            if($keyword <>''){
                if($field=='user'){
                    $where = "{$prefix}order_log.user LIKE '%$keyword%'";
                }
                if($field=='phone'){
                    $where = "{$prefix}order_log.phone LIKE '%$keyword%'";
                }                 
                if($field=='reqid'){
                    $where = "{$prefix}order_log.reqid LIKE '%$keyword%'";
                }

                if($field=='ip'){
                    $where = "{$prefix}order_log.ip LIKE '%$keyword%'";
                }
                //$where = $where. " and result <> 0 ";
            }   else 
            {
                //$where = $where. " result <> 0 ";
            }

            $where = $where. $w;



            $user = M('order_log');
            $pagesize = 10;#每页数量
            $offset = $pagesize*($p-1);//计算记录偏移量
            $count = $user->count();

            $list  = $user->field("{$prefix}order_log.*")->order($order)->where($where)->limit($offset.','.$pagesize)->select();


            //echo $user->getLastSql();exit;
            $page	=	new \Think\Page($count,$pagesize); 
            $page = $page->show();
            $this->assign('list',$list);	
            $this->assign('page',$page);            

            $this -> display();
        }

        public function del(){

            $uids = isset($_REQUEST['uids'])?$_REQUEST['uids']:false;
            //uid为1的禁止删除
            if($uids==1 or !$uids){
                $this->error('参数错误！');
            }
            if(is_array($uids)) 
            {
                foreach($uids as $k=>$v){				
                    $uids[$k] = intval($v);
                }
                if(!$uids){
                    $this->error('参数错误！');
                    $uids = implode(',',$uids);
                }
            }

            $map['uid']  = array('in',$uids);
            if(M('order_log')->where($map)->delete()){
                addlog('删除用户UID：'.$uids);
                $this->success('恭喜，用户删除成功！');
            }else{
                $this->error('参数错误！');
            }
        } 

        public function indexyt(){ 
            $p = isset($_GET['p'])?intval($_GET['p']):'1';   
            $field = isset($_GET['field'])?$_GET['field']:'';
            $keyword = isset($_GET['keyword'])?htmlentities($_GET['keyword']):'';

            $u = $this->USER;
                                
            if($keyword <>''){
                if($field=='mobile'){
                    $where = "qw_json_log.log LIKE '%$keyword%' and status=1";
                }
                if($field=='orderno'){
                    $where = "qw_json_log.orderno LIKE '%$keyword%' and status=1";
                } 
                 if($field=='fluxnum'){
                    $where = "qw_json_log.orderno LIKE '%$keyword%' and fluxnum=1";
                } 
                if($field=='ip'){
                    $where = "qw_order_log.ip LIKE '%$keyword%'";
                }
                if($field=='orderid'){
                    $where = "qw_order_log.orderid LIKE '%$keyword%'";
                }
                
            }  

            $user = M('json_log');
            $pagesize = 10;#每页数量
            $offset = $pagesize*($p-1);//计算记录偏移量
            $count = $user->where(['status'=>1])->count();

            $list  = $user->order('t')->where($where)->order('t desc')->limit($offset.','.$pagesize)->select();

            //echo $user->getLastSql();exit;
            $page    =    new \Think\Page($count,$pagesize); 
            $page = $page->show();
            $this->assign('list',$list);    
            $this->assign('page',$page);
            $this->display();
        }  
}