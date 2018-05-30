<?php
/**
*
* 版权所有：金豌豆<>
* 作    者：国平<8688041@qq.com>
* 日    期：2016-01-21
* 版    本：1.0.0
* 功能说明：友情链接。
*
**/

namespace Qwadmin\Controller;
use Qwadmin\Controller\ComController;

class CheckfeeController extends ComController {


    //步骤1 create view qw_err_fee_view as select ...
    //步骤2 CREATE TABLE qw_err_fee_fix AS SELECT * FROM qw_err_fee_view where 1> 2
    //步骤3 修改 qw_err_fee_fix 中的unused值
    //步骤4 从表 qw_err_fee_fix 重新生成对账单
    // http://localhost/admin.php/Checkfee/fixUnused.html?uid=31&date=201704
    // http://localhost/admin.php/Checkfee/rebuild.html?uid=31&date=201704
    // http://localhost/index.php/order/exportFund.html?uid=31&fid=xgp&start=20170401&end=20170430
    private $SELECT_FIELD = ' uid,user,fluxnum,orderid,orderno,mobile,order_ret,notify_ret ,created_at,price,unused';


    private $SELECT_VIEW = "err_fee_view1";
    private $FIX_FEE = 'err_fee_fix';

    public function fixUnused() {
        $uid = I('uid');
        $date = I('date');
        if (strlen($date) <> 6) {
            $this->error("date参数错误");
        }
        if (strlen($uid) <1) {
            $this->error("uid参数错误");
        }
        echo "fixing unused,please waiting...";
        $str1 = get_date();
        $f['uid'] = ['eq', $uid]; 

//        $sql_del_w = " uid=".$uid." and FROM_UNIXTIME(created_at,'%Y%m')=".$date ;
        $sql_del_w = " uid=".$uid." and FROM_UNIXTIME(created_at,'%Y%m')=".$date ." and order_ret=0";

        $model = M($this->FIX_FEE);

        //清空当前要修复的对账单表
        $ret_d = $model->where($sql_del_w)->delete(); 

        //获取当前全部订单 表
        $list = M($this->SELECT_VIEW)->where($sql_del_w)->select();
        $new_list = [];
        $i = 0;
        $j = 0;
        $timer=[];
        $created_at = '';
        foreach ($list as $k => $v) {
            $new_list[$i] = $v;            
            if ($timer[$v['created_at']] ==  "") 
            {
                unset($timer);
                $unused = $this->getUnused($uid, $v['created_at']);   
                $timer[$v['created_at']] = $unused;
            } else {
                $unused = $timer[$v['created_at']];
                $unused = $unused - $v['price'];
                $timer[$v['created_at']] = $unused;
            }

            $new_list[$i]['unused'] = $unused;
            $i++;
            if ($i >= 100) {
                $ret_a = $model->addAll($new_list);
                $i = 0;
                unset($new_list);
            }
        }
        //        }
        $str2 = get_date();
        echo "fix success! " . get_date() ."(". ($str2-$str1).")";


    }

    //CREATE VIEW qw_err_fee_view AS 
    //SELECT uid,orderno,mobile,order_ret,notify_ret ,created_at,price,unused FROM qw_order_yt WHERE uid=31 and FROM_UNIXTIME(created_at,'%Y%m')='201704' and notify_ret<>0
    //UNION
    //SELECT uid,orderno,mobile,order_ret,notify_ret ,created_at,price,unused FROM qw_order_nj WHERE uid=31 and FROM_UNIXTIME(created_at,'%Y%m')='201704' and notify_ret<>0


    //CREATE VIEW qw_err_fee_view AS 
    //SELECT uid,orderno,mobile,order_ret,notify_ret ,created_at,price,unused FROM qw_order_yt WHERE uid=31 and FROM_UNIXTIME(created_at,'%Y%m')='201704' and notify_ret<>0
    //UNION
    //SELECT uid,orderno,mobile,order_ret,notify_ret ,created_at,price,unused FROM qw_order_nj WHERE uid=31 and FROM_UNIXTIME(created_at,'%Y%m')='201704' and notify_ret<>0

    //http://localhost/admin.php/Checkfee/rebuild.html?uid=31&date=201704
    //重新生成订单
    //注意：只是重新生成，如果订单中的unused不正确，需要先执行 fixUnused
    public function rebuild()  {
        $table =  C('DB_PREFIX').$this->FIX_FEE; //从实际表中修正
        //        $table = "qw_err_fee_view ";  从视图中修正
        //        $table = "qw_err_fee_view1"; //从视图中修正

        $uid = I('uid');
        $date = I('date');
        if (strlen($date) <> 6) {
            $this->error("date参数错误");
        }
        if (strlen($uid) <1) {
            $this->error("uid参数错误");
        }
        echo "fixing,please waiting...";
        $str1 = get_date();
        //$where = " uid=".$uid." and FROM_UNIXTIME(created_at,'%Y%m')=".$date." and order_ret=0 and notify_ret <> 0 and notify_ret <>255 " ;
        //modify by xugp 2017-04-25
        $where = " uid=".$uid." and FROM_UNIXTIME(created_at,'%Y%m')=".$date ." and order_ret=0 " ;

        $sql_del_w = " uid=".$uid." and FROM_UNIXTIME(created_at,'%Y%m')=".$date;
        $sql_del_1 = "DELETE FROM qw_order_fee_detail where ".$sql_del_w;
        $sql_del_2 = "DELETE FROM qw_order_fee_detail_fail where ".$sql_del_w;
        $r1 = M()->execute($sql_del_1);
        $r2 = M()->execute($sql_del_2);

        //DELETE FROM qw_order_fee_detail_fail WHERE FROM_UNIXTIME(created_at,'%Y%m')='201704' and uid=31 
        // foreach (C('ORDER_MODEL_LIST') as $k => $v) {
        $sql = "SELECT uid,user,fluxnum,orderid,orderno,mobile,order_ret,notify_ret ,created_at,price,unused FROM ".$table." WHERE ".$where ;
        $list[$k] = M($v)->query($sql);
        if ($list[$k]) {
            $this->kouKuan($list[$k], $uid);
            $this->tuiKuan($list[$k], $uid);
        }

        //        }
        $str2 = get_date();
        echo "fix success! " . get_date() ."(". ($str2-$str1).")";
    }

    private function kouKuan($list , $uid) {
        $i = 0;
        $j = 0;


        $model_f = M('order_fee_detail'); 

        $col = ['uid','user','mobile','fluxnum','order_ret','notify_ret','created_at','updated_at','orderno','orderid','price','unused'];  
        foreach ($list as $k => $v) {
            $data['orderno'] = $v['orderno'];
            $data['mobile'] = $v['mobile'];

            $data['uid'] = $uid;

            $order_ret = $v['order_ret'];            
            $notify_ret = $v['notify_ret'];
            //            $flag_del = $model_f->where($data)->delete();


            foreach ($col as $key => $value) {
                $v_f[$value] = $v[$value];
            }
            //}else {
            //    continue;
            //}
            $v_f['unused']  = $v['unused']; 
            $v_f['iftype']  =  0;
            $order_detail_f[$j] = $v_f;                                    
            $ret = $model_f->data($order_detail_f[$j])->add();
            $j++;



        }
        //数据量太大，容易导致失败
        //        $f_f = $model_f->addAll($order_detail_f);
    }
    private function tuiKuan($list, $uid) {
        $i = 0;
        $j = 0;

        $model_f = M('order_fee_detail_fail'); 
        $col = ['uid','user','mobile','fluxnum','order_ret','notify_ret','created_at','updated_at','orderno','orderid','price','unused'];  
        foreach ($list as $k => $v) {
            $data['orderno'] = $v['orderno'];
            $data['mobile'] = $v['mobile'];

            $data['uid'] = $uid;

            $order_ret = $v['order_ret'];
            $notify_ret = $v['notify_ret'];


            if ($notify_ret != '0' && $notify_ret != '255') {  //排除成功的，以及处理中的订单 ,即是失败订单               
                //                $flag_del = $model_f->where($data)->delete();

                foreach ($col as $key => $value) {
                    $v_f[$value] = $v[$value];
                }
                $v_f['unused']  = $v['unused'] + $v['price']; //退款
                $v_f['iftype']  =  1;
                $order_detail_f[$j] = $v_f; 
                $ret = $model_f->add($order_detail_f[$j]);
                $j++;


            }



        }
        //        数据量太大，容易导致失败
        //        $f_f = $model_f->addAll($order_detail_f);
    }

    public function u() {
        $date = I('date');        
        $uid = I('uid');
        if (strlen($date) < 15) {
            $this->error("date参数错误");
        }
        if (strlen($uid) <1) {
            $this->error("uid参数错误");
        }
        $time = strtotime($date);

        $left = $this->getUnused($uid, $time);
        echo $str."    ".$left;

    }
    private function getUsed($uid, $time) {
        //        $f['order_ret'] = ['eq',0]; //提交成功
        //        $f['status'] = ['eq',1];
        // 0表示第三方平台充值成功, 255表示待充值的，1表示失败的??
        // 因此，只要排除失败的订单，其它费用需要算在内

        $f['notify_ret'] = ['eq',0];
        $f['uid'] = ['eq', $uid]; 
        $fee_list0 = M('member')->field('used')->where(['uid'=>$uid])->find();


        $union = " UNION ";
        // $_where = " where uid=".$uid. " and (notify_ret=0 or notify_ret=255) and created_at <= ".$time;
//        $_where = " where uid=".$uid. " and (notify_ret=0) and created_at <= ".$time;
        $_where = " where order_ret=0 and status=1 and notify_ret<>1 and uid=".$uid. " and created_at < ".($time-1);
        $order_models_list = C('ORDER_MODEL_LIST');
        $used = 0;
        //foreach ($order_models_list as $key => $value) {
        //        //实时计算出当前已经消耗的费用
        //        $fee_list[$key] = M($value)->field('fluxid,ROUND(SUM(price), 2) as used')->where($f)->find();
        //        if ($fee_list[$key]) {
        //            $used = $used +  $fee_list[$key]['used'];
        //        }
        //    }
        $i = 0;
        $len = count($order_models_list);
        foreach ($order_models_list as $key => $value) {
            $i++;
            $sql =  $sql ."SELECT  fluxid,ROUND(SUM(price), 3) AS used FROM ".C('DB_PREFIX').$value. $_where;
            if ($i < $len){
                $sql = $sql. $union;
            }

            //实时计算出当前已经消耗的费用

        }
        $list_fee = M()->query($sql);

        foreach ($list_fee as $k => $v) {
            if (!is_null($v['used'])) {
                $used = $used +  $v['used'];
            }
        }

        $used = $fee_list0['used'] + $used;
        return $used;
    }

    private function getTotal($uid, $time) {

        //$user = M('member')->where(['uid'=>$uid])->find();
        $filter['uid'] = ['eq', $uid];
        $filter['created_at'] = ['ELT', $time];
        $cur_List = M('fxs_fee')->field('SUM(unused) as unused')->where($filter)->find();
        return ROUND($cur_List['unused'],2);
    }
    private function getUnused($uid, $time) {        
        return round($this->getTotal($uid, $time)-$this->getUsed($uid, $time),2);
    }

}