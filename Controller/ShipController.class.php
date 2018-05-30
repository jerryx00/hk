<?php
/**
*
* 版权所有：金豌豆<>
* 作    者：国平<8688041@qq.com>
* 日    期：2016-01-20
* 版    本：1.0.0
* 功能说明：用户控制器。
*
**/

namespace Qwadmin\Controller;
use Qwadmin\Controller\ComController;
class ShipController extends ComController { 

    public function index(){

        $u = $this->USER;
        $p = isset($_GET['p'])?intval($_GET['p']):'1';
        $field = isset($_GET['field'])?$_GET['field']:'';
        $keyword = isset($_GET['keyword'])?htmlentities($_GET['keyword']):'';
        $order = isset($_GET['order'])?$_GET['order']:'DESC';
        $where = '';
        $w = " result <> 0  and province=1  and actived=1 and uid =" . $u['uid'] ;

        $prefix = C('DB_PREFIX');
        if($order == 'asc'){
            $order = "{$prefix}subscriber.create_time asc";
        }elseif(($order == 'desc')){
            $order = "{$prefix}subscriber.create_time desc";
        }else{
            $order = "{$prefix}subscriber.create_time desc";
        }
        if($keyword <>''){
            if($field=='user'){
                $where = "{$prefix}subscriber.user LIKE '%$keyword%'";
            }
            if($field=='phone'){
                $where = "{$prefix}subscriber.phone LIKE '%$keyword%'";
            }
            if($field=='flowid'){
                $where = "{$prefix}subscriber.goodsid LIKE '%$keyword%'";
            }
            if($field=='flowname'){
                $where = "{$prefix}subscriber.goodsname LIKE '%$keyword%'";
            }
            $where = $where. " and ". $w;
        }   else 
        {
            $where = $where. $w;
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
        $this->cc = "省内"; 
        $this -> display();
    }


    //todo
    function deallog() {

    }

    public function info() {
        $u = $this->USER;
        $p = isset($_GET['p'])?intval($_GET['p']):'1';
        $field = isset($_GET['field'])?$_GET['field']:'';
        $keyword = isset($_GET['keyword'])?htmlentities($_GET['keyword']):'';
        $order = isset($_GET['order'])?$_GET['order']:'DESC';
        $where = '';

        $UID = $u['uid'];
        $prefix = C('DB_PREFIX');
        if($order == 'asc'){
            $order = "{$prefix}member.t asc";
        }elseif(($order == 'desc')){
            $order = "{$prefix}member.t desc";
        }else{
            $order = "{$prefix}member.uid asc";
        }  
        if($keyword <>''){
            if($field=='user'){
                $where = "{$prefix}member.user LIKE '%$keyword%'";
            }
            if($field=='phone'){
                $where = "{$prefix}member.phone LIKE '%$keyword%'";
            }
            if($field=='qq'){
                $where = "{$prefix}member.qq LIKE '%$keyword%'";
            }
            if($field=='email'){
                $where = "{$prefix}member.email LIKE '%$keyword%'";
            }

            //$where = $where ." and level > ".$u['level']." and path like '" . $u['path'] ."%'";
            if ($UID > 2) {
                $w = ' and {$prefix}member.uid = '.$UID;
            }    else {
                $w = ' and {$prefix}member.uid > 0' ;
            }   
        } else {

            //$where = $where ."  level > ".$u['level']." and path like '" . $u['path'] ."%'";
            if ($UID > 2) {
                $w = ' qw_member.uid = '.$UID;
            }    else {
                $w = ' qw_member.uid > 0' ;
            }   
        }                
        
        $where = $where. $w;
        $user = M('member');
        $pagesize = 10;#每页数量
        $offset = $pagesize*($p-1);//计算记录偏移量
        $count = $user->where($where)->count();

        $list  = $user->field("{$prefix}member.*,{$prefix}auth_group.id as gid,{$prefix}auth_group.title")->order($order)->join("{$prefix}auth_group_access ON {$prefix}member.uid = {$prefix}auth_group_access.uid")->join("{$prefix}auth_group ON {$prefix}auth_group.id = {$prefix}auth_group_access.group_id")->where($where)->limit($offset.','.$pagesize)->select();


        //echo $user->getLastSql();exit;
        $page    =    new \Think\Page($count,$pagesize); 
        $page = $page->show();
        $this->assign('list',$list);    
        $this->assign('page',$page);
        $group = M('auth_group')->field('id,title')->select();
        $this->assign('group',$group);
        $this -> display();

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

        $flowid = I('get.goodid');

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

        if (!isset($flowid) || $flowid != ""){
            $where = $where . " and flowid=".$flowid;
        }

        $user = M('subscriber');
        $pagesize = 10;#每页数量
        $offset = $pagesize*($p-1);//计算记录偏移量
        $count = $user->count();

        $list  = $user->field("{$prefix}subscriber.*")->order($order)->where($where)->order('ship_time')->limit($offset.','.$pagesize)->select();


        //echo $user->getLastSql();exit;
        $page    =    new \Think\Page($count,$pagesize); 
        $page = $page->show();
        $this->assign('list',$list);    
        $this->assign('page',$page);            

        $this -> display('history');
    }

    public function success(){ 
        $u = $this->USER;
        $p = isset($_GET['p'])?intval($_GET['p']):'1';
        $field = isset($_GET['field'])?$_GET['field']:'';
        $keyword = isset($_GET['keyword'])?htmlentities($_GET['keyword']):'';
        $order = isset($_GET['order'])?$_GET['order']:'DESC';
        $where = '';

        
        $prefix = C('DB_PREFIX');
        $where = ' result = 0 and uid=' .$u['uid'];
        
        $user = M('order_log');
        $pagesize = 10;#每页数量
        $offset = $pagesize*($p-1);//计算记录偏移量
        $count = $user->count();

        $list  = $user->field("{$prefix}subscriber.*")->order($order)->where($where)->order('ship_time')->limit($offset.','.$pagesize)->select();


        echo $user->getLastSql();exit;
        $page    =    new \Think\Page($count,$pagesize); 
        $page = $page->show();
        $this->assign('list',$list);    
        $this->assign('page',$page);            

        $this -> display('ship/history');
    }
    public function order(){            
        $p['id'] = I('get.id');
        $p['accnbr']=I('get.phone');
        $p['goodid']=I('get.goodsid');
        //$p['goodname']=I('get.goodsname');
        $p['goodname']=M('goods_group')->getFieldByGoodid($p['goodid'], 'goodname');

        //type 1普通包；2兑换码；3其他
        $p['type']="1";
        $p['reqid']=I('get.reqid');

        $p['uid'] =  $this->USER['uid'];
        $p['user'] =  $this->USER['user'];

        //向服务器发起请求订购
        $ret = A('Dinggou')->dinggou($p);
        if ($ret == "-1") {
            $this->error("订购失败.个人帐户剩余流量不足!"); 
        }  else { 
            if (!$ret) {
                $this->error("订购失败.服务器无响应:");
            }  else {                
                if ($ret->TSR_RESULT == "0")   {
                    $this->success("订购成功");
                }  else {
                    $this->error("订购失败.".$ret->TSR_MSG);
                } 
            }
        }  

        $this -> display();
    }


    public function orderAll(){ 
        $uids = isset($_REQUEST['uids'])?$_REQUEST['uids']:false;
        //uid为1的禁止删除
        if(!$uids){
            $this->error('参数错误！');
        }
        $ids = implode(',',$uids);
        $map['id']  = array('in',$ids);

        $ret = M('subscriber')->where($map)->select();

        $r = true;

        foreach($ret as $k=>$v){      
            $p['id']  = $v['id'];
            $p['reqid'] = $v['reqid'];
            $p['goodid'] = $v['goodsid'];
            $p['goodname'] = $v['goodsname'];
            $p['goodname'] = "1";
            $p['accnbr'] = $v['phone'];
            $p['type'] = $v['type'];

            //向服务器发起请求订购
            //$ret = $this->dinggou($p);
            //$ret = D('Api://Subscriber', 'Logic')->
            
            if (!$ret || $ret->TSR_RESULT  <> "0") {
                break;
            } 
        }
        if ($ret == "-1") {
            $this->error("订购失败.个人帐户剩余流量不足"); 
        } else {
            if (!$ret) {
                $this->error("订购失败.服务器无响应");
            }  else {                
                if ($ret->TSR_RESULT == "0")   {
                    $this->success("订购成功");
                }  else {
                    $this->error($ret->TSR_MSG);
                }  
            }
        }

    }
}