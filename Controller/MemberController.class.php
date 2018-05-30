<?php
namespace Qwadmin\Controller;
use Qwadmin\Controller\ComController;
class MemberController extends ComController {
    public function index(){

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
        $pagesize = 40;#每页数量
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

    public function indexfee(){
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
        $pagesize = 40;#每页数量
        $offset = $pagesize*($p-1);//计算记录偏移量


        $list  = $user->field("{$prefix}member.*,{$prefix}auth_group.id as gid,{$prefix}auth_group.title")->order($order)->join("{$prefix}auth_group_access ON {$prefix}member.uid = {$prefix}auth_group_access.uid")->join("{$prefix}auth_group ON {$prefix}auth_group.id = {$prefix}auth_group_access.group_id")->where($where)->limit($offset.','.$pagesize)->select();
        $count = count($user);   

        //echo  M()->getLastSql();exit;
        $page    =    new \Think\Page($count,$pagesize); 
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
                if($v==1){//uid为1的禁止删除
                    unset($uids[$k]);
                }
                $uids[$k] = intval($v);
            }
            if(!$uids){
                $this->error('参数错误！');
                $uids = implode(',',$uids);
            }
        }

        $map['uid']  = array('in',$uids);
        if(M('member')->where($map)->delete()){
            M('auth_group_access')->where($map)->delete();
            addlog('删除会员UID：'.$uids);
            $this->success('恭喜，用户删除成功！');
        }else{
            $this->error('参数错误！');
        }
    }

    public function edit(){
        $u = $this->USER;
        $uid = isset($_GET['uid'])?intval($_GET['uid']):false;
        if($uid){	
            //$member = M('member')->where("uid='$uid'")->find();
            $prefix = C('DB_PREFIX');
            $user = M('member');
            $member  = $user->field("{$prefix}member.*,{$prefix}auth_group_access.group_id")->join("{$prefix}auth_group_access ON {$prefix}member.uid = {$prefix}auth_group_access.uid")->where("{$prefix}member.uid=$uid")->find();

        }else{
            $this->error('参数错误！');
        }

        if ($u['uid'] > 1) {
            $w['id'] = array('gt',1);
        }	else {
            $w['id'] = array('gt',0);
        }	
        $usergroup = M('auth_group')->field('id,title')->where($w)->order('id desc')->select();
        $this->assign('usergroup',$usergroup);

        $this->assign('member',$member);
        $this -> display();
    }

    public function update($ajax=''){
        if($ajax=='yes'){
            $uid = I('get.uid',0,'intval');
            $gid = I('get.gid',0,'intval');
            M('auth_group_access')->data(array('group_id'=>$gid))->where("uid='$uid'")->save();
            die('1');
        }

        $uid = isset($_POST['uid'])?intval($_POST['uid']):false;
        $user = isset($_POST['user'])?htmlspecialchars($_POST['user'], ENT_QUOTES):'';
        $group_id = isset($_POST['group_id'])?intval($_POST['group_id']):0;
        if(!$group_id){
            $this->error('请选择用户组！');
        }
        $password = isset($_POST['password'])?trim($_POST['password']):false;
        if($password) {
            $data['password'] = password($password);
        } 
        $head = I('post.head','','strip_tags');
        if($head<>'') {
            $data['head'] = $head;
        }
        $data['actioncode'] = isset($_POST['actioncode'])?trim($_POST['actioncode']):'';
        $data['intersource'] = isset($_POST['intersource'])?trim($_POST['intersource']):'';

        $data['sex'] = isset($_POST['sex'])?intval($_POST['sex']):0;
        $data['birthday'] = isset($_POST['birthday'])?strtotime($_POST['birthday']):0;
        $data['phone'] = isset($_POST['phone'])?trim($_POST['phone']):'';
        $data['qq'] = isset($_POST['qq'])?trim($_POST['qq']):'';
        $data['name'] = isset($_POST['name'])?trim($_POST['name']):'';
        $data['email'] = isset($_POST['email'])?trim($_POST['email']):'';
        $data['authip'] = isset($_POST['authip'])?trim($_POST['authip']):'';
        $data['accesstoken'] = isset($_POST['accesstoken'])?trim($_POST['accesstoken']):'';
        $data['accesscode'] = isset($_POST['accesscode'])?trim($_POST['accesscode']):'';
        $data['discount'] = isset($_POST['discount'])?trim($_POST['discount']):'';
        $data['t'] = time();

        //add by xugp 2016-04-23  begin
        $u = $this->USER;
        if($u['uid'] == 1 ){
            $data['pid'] = 0;
        }else{
            $data['pid'] = $u['uid'];
        } 
        $data['uid'] = $uid;
        $data['path']=$u['path'].'-'.$u['uid'];//子类的path为父类的path加上父类的uid
        $data['level'] = intval($u['level']) + 1 ;
        //add by xugp 2016-04-23 end


        if(!$uid){
            if($user==''){
                $this->error('用户名称不能为空！');
            }
            if(!$password){
                $this->error('用户密码不能为空！');
            }
            if(M('member')->where("user='$user}'")->count()){
                $this->error('用户名已被占用！');
            }
            $data['user'] = $user;
            $uid = M('member')->data($data)->add();
            M('auth_group_access')->data(array('group_id'=>$group_id,'uid'=>$uid))->add();
            addlog('新增会员，会员UID：'.$uid);
        }else{
            M('auth_group_access')->data(array('group_id'=>$group_id))->where("uid=$uid")->save();
            M('member')->data($data)->where("uid=$uid")->save();
            addlog('编辑会员信息，会员UID：'.$uid);
        }
        $this->success('操作成功！','index');
    }


    public function add(){
        $u = $this->USER;
        if ($u['uid'] > 1) {
            $w['id'] = array('gt',1);
        }	else {
            $w['id'] = array('gt',0);
        }	
        $usergroup = M('auth_group')->field('id,title')->where($w)->order('id desc')->select();
        $this->assign('usergroup',$usergroup);

        $len = 32;
        $member['password'] = randomkeys(16);
        $member['actioncode'] = randomkeys($len);
        $member['intersource'] = randomkeys($len);
        $member['accesstoken'] = randomkeys($len);
        $member['authip'] = "1.1.1.1";             

        $this->member = $member;
        $this -> display();
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

            $where = $where ." and level > ".$u['level']." and path like '" . $u['path'] ."%'";

        } else {

            $where = $where ."  level > ".$u['level']." and path like '" . $u['path'] ."%'";

        }                
        $where =  $where. ' and status=1';

        $user = M('member');
        $pagesize = 10;#每页数量
        $offset = $pagesize*($p-1);//计算记录偏移量
        $count = $user->where($where)->count();

        $list  = $user->where($where)->limit($offset.','.$pagesize)->select();


        //$user->getLastSql();
        $page    =    new \Think\Page($count,$pagesize); 
        $page = $page->show();
        $this->assign('list',$list);    
        $this->assign('page',$page);
        $group = M('auth_group')->field('id,title')->select();
        $this->assign('group',$group);
        $this -> display();

    }
    public function flow() {
        $flag = "0";

        $uid = I('get.uid');
        $province = I('get.province');
        $uname = I('get.user');

        $where['province']=$province;//省内
        $t = I('get.t');
        //            if (isset($t)) {
        //                $w = " and "
        //            }
        $where['goodtype']="3";//电信
        $where['uid']=$uid;
        $sql = "SELECT a.goodid,a.goodname,a.price, a.discount, a.goodtype,a.province,a.desc,SUM(b.remainder) remainder, SUM(b.used) used, ".
        " ROUND(a.price * discount * used, 2) AS buymoney ".
        " from qw_goods_group a, qw_fxs_order_flow b where b.goodid=a.goodid ";
        if ($flag == "1") {
            $sql = $sql ." and a.province = ".$where['province'] ; 
        } 
        $sql = $sql." and b.uid = " .$where['uid'] ." group by b.goodid ORDER BY a.id";
        //                                   var_dump($sql);exit;
        $myflow = M('fxs_order_flow')->query($sql);


        if ($province == "1") {
            $this->cc = "省内";
        } else {
            $this->cc = "全国";
        }
        $this->uid = $uid;
        $this->province = $province;
        $this->uname = $uname;

        $this->list = $myflow;
        $this->display();
    }

    public function detail(){
        $goodid = I('get.goodid');
        $uid = I('get.uid');
        if (!isset($goodid) || $goodid != ""){
            $where = $where . " and b.goodid=".$goodid;                 
        }
        if (!isset($uid) || $uid != ""){
            $where = $where . " and b.uid=".$uid;                  
        }
        $sql = "SELECT a.goodid,a.goodname,a.goodtype,a.province,a.desc,b.remainder, b.used, b.create_time".
        " from qw_goods_group a, qw_fxs_order_flow b where b.goodid=a.goodid ". $where . " ORDER BY a.id";

        $myflow = M('fxs_order_flow')->query($sql);

        $this->list = $myflow;
        $this->display();


    }
    public function fee() {
        $uid = I('get.uid');
        $user = M('member')->where(['uid'=>$uid])->find();

        $cur_List = M('fxs_fee')->field('SUM(unused) as unused')->where(['uid'=>$uid])->find();

        $f['order_ret'] = ['eq',0]; //提交成功
        $f['status'] = ['eq',1];
        // 0表示第三方平台充值成功, 255表示待充值的，1表示失败的
        // 因此，只要排除失败的订单，其它费用需要算在内

        $f['notify_ret'] = ['neq',1];
        $f['uid'] = ['eq', $uid]; 

        //实时计算出当前已经消耗的费用
        $fee_list = M('order_yt')->field('fluxid,ROUND(SUM(price), 3) as used')->where($f)->find(); 

        $unused = $cur_List['unused'] - $fee_list['used']; 



        echo $user['uid'] .'-'.$user['user'].'-'.$user['name'].'还剩余 ￥:'.($unused);
    }
}