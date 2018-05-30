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

    class FlowadvancecController extends FlowadvanceController {

      //  //我的流量 
//        public function index(){
//            $where['province']="1";//省内
//            $where['goodtype']="3";//电信
//            $where['uid']=$this->USER['uid'];
//            $sql = "SELECT a.goodid,a.goodname,a.goodtype,a.province,b.used,b.remainder ,a.desc ".
//            " from qw_goods_group a, qw_fxs_total_flow b where b.goodid=a.goodid ".
//            " and a.province = ".$where['province'] ." and goodtype=" .  $where['goodtype'].
//            " and b.uid = " .$where['uid'] ." ";
//            //            var_dump($sql);exit;
//            $myflow = M('fxs_total_flow')->query($sql);
//
//            //            var_dump(M()->getLastSql());exit;
//            $this->cc = "省内";
//            $this->list = $myflow;
//            $this->display();
//        }

        public function indexc(){
            //$where['province']="1";//省内
//            $where['goodtype']="3";//电信
//            $where['uid']=$this->USER['uid'];
//            $sql = "SELECT a.goodid,a.goodname,a.goodtype,a.province,b.used,b.remainder ,a.desc ".
//            " from qw_goods_group a, qw_fxs_total_flow b where b.goodid=a.goodid ".
//            " and a.province = ".$where['province'] ." and goodtype=" .  $where['goodtype'].
//            " and b.uid = " .$where['uid'] ." ";
//            //            var_dump($sql);exit;
//            $myflow = M('fxs_total_flow')->query($sql);
//
//            //            var_dump(M()->getLastSql());exit;
//            $this->cc = "省内";
//            $this->list = $myflow;
//            $this->display();
        }

        //我的流量 
        public function edit(){
            $where['province']=I('get.province');;//省内
            $where['goodtype']="3";//电信
            $where['uid']= I('get.uid');
            if (!isset($where['uid'])) {
                $this->error("参数错误");
            }
            // $sql = "SELECT a.goodid,a.goodname,a.goodtype,a.province,b.used,b.remainder,a.desc ".
            //            " from qw_goods_group a, qw_fxs_total_flow b where b.goodid=a.goodid ".
            //            " and a.province = ".$where['province'] ." and goodtype=" .  $where['goodtype'].
            //            " and b.uid = " .$where['uid'] ." ";
            //            var_dump($sql);exit;
            $myflow = M('goods_group')->where($where)->select();

            //            var_dump(M()->getLastSql());exit;
            if ($where['province'] == "1") {
                $this->cc = "省内";
            } else {
                $this->cc = "全国";
            }
            $this->province = $where['province'];
            $this->uid = $where['uid'];
            $this->list = $myflow;
            $this->display('Flowadvance/edit');
        }
        public function editc(){
            $where['province']="2";//全国
            $where['goodtype']="3";//电信
            $where['uid']=$this->USER['uid'];
            $myflow = M('fxs_total_flow')->where($where)->select();
            $this->cc = "全国";
            $this->myflow = $myflow;
            $this->province = $where['province'];
            $this->display();
        }
       
}