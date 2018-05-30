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

    class FlowadvanceController extends ComController {

        //我的流量 
        public function index(){
            $where['province']="1";//省内
            $where['goodtype']="3";//电信
            $where['uid']=$this->USER['uid'];
            $sql = "SELECT a.goodid,a.goodname,a.goodtype,a.province,b.used,b.remainder ,a.desc ".
            " from qw_goods_group a, qw_qw_fxs_order_flow b where b.goodid=a.goodid ".
            " and a.province = ".$where['province'] ." and goodtype=" .  $where['goodtype'].
            " and b.uid = " .$where['uid'] ." ";
            //            var_dump($sql);exit;
            $myflow = M('qw_fxs_order_flow')->query($sql);

            //            var_dump(M()->getLastSql());exit;
            $this->cc = "省内";
            $this->list = $myflow;
            $this->display();
        }

        public function indexc(){
            $where['province']="1";//省内
            $where['goodtype']="3";//电信
            $where['uid']=$this->USER['uid'];
            $sql = "SELECT a.goodid,a.goodname,a.goodtype,a.province,b.used,b.remainder ,a.desc ".
            " from qw_goods_group a, qw_qw_fxs_order_flow b where b.goodid=a.goodid ".
            " and a.province = ".$where['province'] ." and goodtype=" .  $where['goodtype'].
            " and b.uid = " .$where['uid'] ." ";
            //            var_dump($sql);exit;
            $myflow = M('qw_fxs_order_flow')->query($sql);

            //            var_dump(M()->getLastSql());exit;
            $this->cc = "省内";
            $this->list = $myflow;
            $this->display();
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
            //            " from qw_goods_group a, qw_qw_fxs_order_flow b where b.goodid=a.goodid ".
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
            $this->display();
        }
        public function editc(){
            $where['province']="2";//全国
            $where['goodtype']="3";//电信
            $where['uid']=$this->USER['uid'];
            $myflow = M('qw_fxs_order_flow')->where($where)->select();
            $this->cc = "全国";
            $this->myflow = $myflow;
            $this->province = $where['province'];
            $this->display();
        }

        public function save(){

            $uid = I('post.uid');
            $province = I('post.province');
            $flow = I('post.data');
            $t = time();
            $model = M('fxs_order_flow');
//            $model->startTrans();
            $i = 0;

            foreach($flow as $k => $v){
                if ($v !== "" && $v != "0") {
                    $data[$i]['remainder'] = $v;
                    $data[$i]['goodid'] = $k;
                    $data[$i]['create_time'] = $t;
                    $data[$i]['update_time'] = $t;
                    $data[$i]['uid'] = $uid;                         

                    // $ret1 = M('fxs_order_flow')->data($data)->add();
                    //                    $cnt = M('qw_fxs_order_flow')->where(['uid'=>$uid, 'goodid'=>$k])->count();
                    //                    if ($cnt < 1) {
                    //                        $ret2 = M('qw_fxs_order_flow')->data($data[$i])->add();  
                    //                    } else {
                    //                        $ret2 = M('qw_fxs_order_flow')->where(['uid'=>$uid, 'goodid'=>$k])->setInc('remainder', $v);   
                    //                    }
                    $i ++; 
                    //                    if ($ret1 === false || $ret2 === false) {
                    //                        $model->rollback();
                    //                        break;
                    //                    } 
                }

            }
            $ret1 = $model->addAll($data);
            if ($ret1 != false) {
                 addlog("批量分配流量给用户:".$uid);
            }
            unset($data);
            // if ($ret1 === true && $ret2 === true) {
            //                $model->commit();
            $this->redirect('Member/flow',['uid'=>$uid, 'province'=>$province, 't'=> $t]);
            //            } 

            //$where['province']="2";//全国
            //            $where['goodtype']="3";//电信
            //            $where['uid']=$this->USER['uid'];






        }
}