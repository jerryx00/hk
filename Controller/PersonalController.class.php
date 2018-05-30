<?php
    /**
    *
    * 版权所有：金豌豆<>
    * 作    者：国平<8688041@qq.com>
    * 日    期：2015-09-18
    * 版    本：1.0.0
    * 功能说明：个人中心控制器。
    *
    **/

    namespace Qwadmin\Controller;
    use Qwadmin\Controller\ComController;

    class PersonalController extends ComController {

        public function profile(){

            $member = M('member')->where('uid='.$this->USER['uid'])->find();
            $this->assign('nav',array('Personal','profile',''));//导航
            
            $cur_List = M('fxs_fee')->field('SUM(unused) as unused')->where(['uid'=>$this->USER['uid']])->find();
            $unused_fee = $cur_List['unused'];
            //实时计算出当前已经消耗的费用
            $fee_list = M('order_yt')->field('fluxid,ROUND(SUM(price), 2) as used')->where($f)->find();
            
            $total_fee = 0;
            if ($cur_List !== false) {
                $total_fee = $cur_List['unused'];  
            }
            
            $this->assign('member',$member);
            $this->unused_fee = $unused_fee;
            
            $this -> display();
        }
        
          public function flow() {
            $flag = "0";

            $uid = $this->USER['uid'];
            $province = I('get.province');
            $uname = I('get.user');

            $where['province']=$province;//省内
            $t = I('get.t');
            //            if (isset($t)) {
            //                $w = " and "
            //            }
            $where['goodtype']="3";//电信
            $where['uid']=$uid;
            $sql = "SELECT a.goodid,a.goodname,a.goodtype,a.province,a.desc,SUM(b.remainder) remainder, SUM(b.used) used ".
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
      

        public function update(){

            $uid = $this->USER['uid'];
            //$password = isset($_POST['password'])?trim($_POST['password']):false;
            $password = I('post.password');
            if($password) {
                $data['password'] = password($password);
            }

            $head = I('post.head','','strip_tags');
            if($head<>'') {
                $data['head'] = $head;
            }

            $data['sex'] = isset($_POST['sex'])?intval($_POST['sex']):0;
            $data['birthday'] = isset($_POST['birthday'])?strtotime($_POST['birthday']):0;
            //$data['phone'] = isset($_POST['phone'])?trim($_POST['phone']):'';
            $data['qq'] = isset($_POST['qq'])?trim($_POST['qq']):'';
            $data['email'] = isset($_POST['email'])?trim($_POST['email']):'';

            //$data['actioncode'] = isset($_POST['actioncode'])?trim($_POST['actioncode']):'';
            //$data['intersource'] = isset($_POST['intersource'])?trim($_POST['intersource']):'';
            $data['authip'] = isset($_POST['authip'])?trim($_POST['authip']):'';
             
            $isadmin = isset($_POST['isadmin'])?$_POST['isadmin']:'';
            if($uid <> 1) {#禁止最高管理员设为普通会员。
                $data['isadmin'] = $isadmin=='on'?1:0;
            }
            $Model = M('member');
            $ret = $Model->data($data)->where("uid=$uid")->save();
            addlog('修改个人资料');
            if ($ret !== false)    {
                //'uid,user,phone,actioncode,intersource
                $this->USER['phone'] = $data['phone'];
                $this->USER['actioncode'] = $data['actioncode'];
                $this->USER['intersource'] = $data['intersource'];

                session('user', $this->USER);
                $this->success('操作成功！');
            }


        }
       
        
        //我的流量 
        public function edit(){
            $where['province']="1";//全国
            $where['goodtype']="3";//电信
            $where['uid']=$this->USER['uid'];
            $myflow = M('mygoods')->where($where)->select();
            $this->cc = "省内";
            $this->myflow = $myflow;
            $this->display();
        }
        public function editc(){
            $where['province']="2";//全国
            $where['goodtype']="3";//电信
            $where['uid']=$this->USER['uid'];
            $myflow = M('mygoods')->where($where)->select();
            $this->cc = "全国";
            $this->myflow = $myflow;
            $this->province = $where['province'];
            $this->display();
        }
        
         public function save(){
            $where['province']="2";//全国
            $where['goodtype']="3";//电信
            $where['uid']=$this->USER['uid'];
            $myflow = M('mygoods')->where($where)->select();
            $this->cc = "全国";
            $this->myflow = $myflow;
            $this->province = $where['province'];
            $this->display();
        }
        
        public function jiekou(){

            $member = M('member')->where('uid='.$this->USER['uid'])->find();
            $this->assign('nav',array('Personal','profile',''));//导航             
            $this->assign('member',$member);     
            $this -> display();
        }
        //public function myflow() {
//            $uid = $this->USER['uid'];
//            $province = I('get.province');
//            
//            $where['province']=$province;//省内
//            $t = I('get.t');
////            if (isset($t)) {
////                $w = " and "
////            }
//            $where['goodtype']="3";//电信
//            $where['uid']=$uid;
//            $sql = "SELECT a.goodid,a.goodname,a.goodtype,a.province,b.used,b.remainder ,a.desc ".
//            " from qw_goods_group a, qw_fxs_total_flow b where b.goodid=a.goodid ".
//            " and a.province = ".$where['province'] ." and goodtype=" .  $where['goodtype'].
//            " and b.uid = " .$where['uid'] ." ";
//            //            var_dump($sql);exit;
//            $myflow = M('fxs_total_flow')->query($sql);
//
//            //            var_dump(M()->getLastSql());exit;
//            if ($province == "1") {
//                $this->cc = "省内";
//            } else {
//                $this->cc = "全国";
//            }
//            $this->uid = $uid;
//            $this->province = $province;
//            $this->list = $myflow;
//            $this->display();
//        }
        
}