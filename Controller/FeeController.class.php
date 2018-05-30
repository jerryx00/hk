<?php
namespace Qwadmin\Controller;
use Qwadmin\Controller\ComController;
class FeeController extends ComController {

    public function index(){
        $u = $this->USER;
        $uid = I('uid');
        $p = isset($_GET['p'])?intval($_GET['p']):'1';
        $field = isset($_GET['field'])?$_GET['field']:'';
        $keyword = isset($_GET['keyword'])?htmlentities($_GET['keyword']):'';
        $order = isset($_GET['order'])?$_GET['order']:'DESC';
        $where = '';


        $where = "";

        //$model = M('fxs_fee');
        //            $pagesize = 10;#每页数量
        //            $offset = $pagesize*($p-1);//计算记录偏移量
        //            $count = $model->where(['status'=>1])>count();
        //            $sql = 'select f.*, m.user,m.name '.
        //            ' from qw_fxs_fee f, qw_member m '.
        //            ' where f.status=1 and m.status=1 and m.uid = f.uid ';



        $sql='select a.user,a.name,b.*,b.id from qw_member a, qw_fxs_fee b  where a.uid=b.uid and a.uid='.$uid. ' and created_at >1525104000';
        //            echo  M('fxs_fee')->getLastSql();exit;
        $list = M()->query($sql);
        $page	=	new \Think\Page($count,$pagesize);
        $page = $page->show();
        $this->assign('list',$list);
        $this->assign('page',$page);

        $this->assign('list',$list);
        $this -> display();
    }


    public function mindex(){

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

            // $where = $where ." and level > ".$u['level']." and path like '" . $u['path'] ."%' and {$prefix}member.status=1";
            $where = $where ." and {$prefix}member.status=1";

        } else {

            //$where = $where ."  level > ".$u['level']." and path like '" . $u['path'] ."%' and {$prefix}member.status=1";
            $where = $where ."  {$prefix}member.status=1";

        }

        if ($u['uid'] > 1) {
            $w['id'] = array('gt',1);
        }    else {
            $w['id'] = array('gt',0);
        }


        $user = M('member');
        $pagesize = 10;#每页数量
        $offset = $pagesize*($p-1);//计算记录偏移量


        $list  = $user->field("{$prefix}member.*,{$prefix}auth_group.id as gid,{$prefix}auth_group.title")->order($order)->join("{$prefix}auth_group_access ON {$prefix}member.uid = {$prefix}auth_group_access.uid")->join("{$prefix}auth_group ON {$prefix}auth_group.id = {$prefix}auth_group_access.group_id")->where($where)->limit($offset.','.$pagesize)->select();
        $count = count($user);

        //echo  M()->getLastSql();exit;
        $page    =    new \Think\Page($count,$pagesize);
        $page = $page->show();
        $this->assign('list',$list);
        $this->assign('page',$page);
        $group = M('auth_group')->field('id,title')->where($w)->select();

        $this->assign('group',$group);
        $this -> display();
    }

    //public function del(){
    //
    //            $uids = isset($_REQUEST['uids'])?$_REQUEST['uids']:false;
    //            //uid为1的禁止删除
    //            if($uids==1 or !$uids){
    //                $this->error('参数错误！');
    //            }
    //            if(is_array($uids))
    //            {
    //                foreach($uids as $k=>$v){
    //                    if($v==1){//uid为1的禁止删除
    //                        unset($uids[$k]);
    //                    }
    //                    $uids[$k] = intval($v);
    //                }
    //                if(!$uids){
    //                    $this->error('参数错误！');
    //                    $uids = implode(',',$uids);
    //                }
    //            }
    //
    //            $map['uid']  = array('in',$uids);
    //            if(M('member')->where($map)->delete()){
    //                M('auth_group_access')->where($map)->delete();
    //                addlog('删除会员UID：'.$uids);
    //                $this->success('恭喜，用户删除成功！');
    //            }else{
    //                $this->error('参数错误！');
    //            }
    //        }

    public function edit(){
        $u = $this->USER;
        if (intval($u['uid']) > 3) {
            $this->error('权限错误,如需要操作,请联系系统管理员！');
        }
        $uid = isset($_GET['uid'])?intval($_GET['uid']):false;
        $id = isset($_GET['id'])?intval($_GET['id']):0;
        if($uid || $id){
            //$member = M('member')->where("uid='$uid'")->find();
            $prefix = C('DB_PREFIX');
            $user = M('member');
            $sql='select a.uid,a.user,a.name,b.unused, b.desc,b.id from qw_member a, qw_fxs_fee b  where a.uid=b.uid and a.uid='.$uid.' and id='.$id;
            //dump($sql);exit;
            $member  = $user->query($sql)[0];

        }else{
            $this->error('参数错误！');
        }

        if ($u['uid'] > 1) {
            $w['id'] = array('gt',1);
        }	else {
            $w['id'] = array('gt',0);
        }


        $this->assign('member',$member);
        $this -> display();
    }

    public function update($ajax=''){
        $u = $this->USER;
        if (intval($u['uid']) > 3) {
            $this->error('权限错误,如需要操作,请联系系统管理员！');
        }
        $data = $_POST;
        $data['created_at'] = time();
        $data['created_by'] = $u['uid'];

        if($data['unused']==''){
            $this->error('充值金额不能为空！');
        }

        if($data['id']==''){
            $ret = M('fxs_fee')->data($data)->add();

            $data['mobile'] = $ret;
            $data['orderno'] = $ret;
            $data['price'] = $data['unused'];
            $data['unused'] = getUnused($u['uid']);
            $data['iftype'] = 2;
            //ad by xugp 2017-11-11
            M('order_fee_detail')->data($data)->add();
            addlog('增加充值:用户'.$data['uid'].'充值:'.$data['unused'].';记录id'.$ret);
        }   else {
            //unset($data['id']);
//            $ret = M('fxs_fee')->data($data)->save();
//
//            $data['mobile'] = $ret;
//            $data['orderno'] = $ret;
//            $data['price'] = $data['unused'];
//            $data['unused'] = session('unused');
//            $data['iftype'] = 2;
//            unset($data['id']);
//
//
//            $ret = M('order_fee_detail')->data($data)->save();
//
//            addlog('修改充值:用户'.$data['uid'].'修改充值:'.$data['unused'].';记录id'.$data['id']);
        }

        //addlog('编辑会员信息，会员UID：'.$uid);

        $this->success('操作成功！', 'index?uid='.$data['uid']);
    }


    public function add(){
        $u = $this->USER;
        if (intval($u['uid']) > 3) {
            $this->error('权限错误,如需要操作,请联系系统管理员！');
        }
        $uid = I('uid');
        $this->uid = $uid;

        $user = M('fxs_fee');

        //  $sql = 'select f.*, q.user,q.name '.
        //            ' from qw_fxs_fee f, qw_member q '.
        //            ' where f.status=1 and q.status=1 and q.uid = f.uid and f.uid='. $uid;
        //
        //            $sql = $sql .$where;
        //            echo $sql;exit;
        //$list  = $user->field("{$prefix}member.*,{$prefix}fxs_fee.id as gid,{$prefix}auth_group.title")->order($order)->join("{$prefix}auth_group_access ON {$prefix}member.uid = {$prefix}auth_group_access.uid")->join("{$prefix}auth_group ON {$prefix}auth_group.id = {$prefix}auth_group_access.group_id")->where($where)->limit($offset.','.$pagesize)->select();

        //$list =  $user->query($sql);
        $vo = M('member')->where(['uid'=>$uid])->find();


        $this->vo = $vo;
        $this -> display();
    }

}