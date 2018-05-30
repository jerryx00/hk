<?php
    namespace Qwadmin\Controller;
    use Qwadmin\Controller\ComController;
    class MigrateController extends ComController {
        
           
        public function index(){ 
            $u = $this->USER;

            $UID = $u['uid']; 
            
            $prefix = C('DB_PREFIX');
            $yearmonth = I('yyyymm');
            if ($yearmonth == "") {
                $this->error("请输入迁移数据的年月信息");
            }
            $member = M('member')->select();
            if ($member==false) {
                $this->error("用户信息为空");
            }

            $fsx_arr = ['order_yt', 'order_sp'];
            foreach ($fsx_arr as $k => $m) {
                $table = $m;
                $sql_del = M('report_statistics') ->where(['uid'=>$uid, 'year_month'=>$year_month])->delete();
                
                
                $sql = "insert into ".$prefix."report_statistics (uid,USER,yearmonth,used,stype) .".
                " select uid,user,from_unixtime(created_at,'%Y%m') as yearmonth,round(sum(price) ,2) used,1 ".
                " from ".$prefix. $table." where from_unixtime(created_at,'%Y%m')=".$yearmonth." and notify_ret=0 group by uid;";
                $list = M()->execute($sql);                 
                
                foreach ($list as $k1 => $v1) {
                    $fund_uid= $v1['uid'];
                    $fund_total = $v1['total'];                     
                    $sql_update = 'update report_statistics set used=used + '.$fund_total;
                    $ins_flag = M('report_statistics')->execute($sql_update);
                    
                    
                    foreach ($member as $k2 => $v2) {
                        $m_uid = $v2['uid'];
                        if ($fund_uid == $m_uid) {
                            $upd_used = M('member')->where(['uid'=>$fund_uid])->setInc('used', $fund_total);
                             
                        }
                    
                    }
                
                }                
            }
            
            
            
            
            
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

            $map['id']  = array('in',$uids);
            if(M($this->MODEL_NAME)->where($map)->delete()){
                addlog('删除维护区域ID：'.$uids);
                $this->success('恭喜，维护区域删除成功！');
            }else{
                $this->error('参数错误！');
            }
        }
        public function add(){
            $this -> display('add');
        }
        public function edit(){
            $u = $this->USER;
            $id = isset($_GET['id'])?intval($_GET['id']):false;
            if($id){	
                $vo = M($this->MODEL_NAME)->where("id='$id'")->find();                

            }else{
                $this->error('参数错误！');
            }

            $this->assign('vo',$vo);
            $this -> display('edit');
        }

        public function update($ajax=''){
            $id = I('id');
            $data = I('data'); 
            $data['status'] = I('status','0','intval');
            if($id ==''){                 
//                $data['areaid'] = $areaid;
                $id = M($this->MODEL_NAME)->data($data)->add();
                addlog('新增维护区域，areaid：'.$id);
            }else{
                $ret = M($this->MODEL_NAME)->data($data)->where("id='$id'")->save();
                addlog('编辑维护区域，areaid：'.$id);
            }
            $this->success('操作成功！','index');
        }
      
}