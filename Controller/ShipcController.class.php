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
    class ShipcController extends ComController { 

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
                if($field=='flow'){
                    $where = "{$prefix}subscriber.flow LIKE '%$keyword%'";
                }
                if($field=='description'){
                    $where = "{$prefix}subscriber.mark LIKE '%$keyword%'";
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
            //var_dump(M()->getLastSql());exit;


            //echo $user->getLastSql();exit;
            $page    =    new \Think\Page($count,$pagesize); 
            $page = $page->show();
            $this->assign('list',$list);    
            $this->assign('page',$page);            
            $this->cc = "全国"; 
            $this -> display('Ship/index');
        }
        
        /**
        public function dinggou($p) { 
            addlog("发起流量订购.accNbr:".$p['accnbr'].";reqid:".$p['reqid'].";goodid:".$p['goodid']);

            //检查个人的流量包信息,是否还有剩余流量包
            $retchk = checkFlow($this->USER, $p['goodid'], $p['reqid']);

            if (!$retchk) { 
                //记日志================================== 
                $datalog['result'] = "999998";
                $datalog['reqid'] = $p['reqid'];
                $datalog['msg'] = "个人帐户剩余流量不足";
                $datalog['url'] = $url;
                addorderlog($datalog, $p);
                return "-1"; 
            } 

            //替换 /
            $p['goodname'] = trim(getCovertGoodname($p['goodname']));             
            $datalog['phone'] = $p['accnbr'];
            $datalog['goodid'] = $p['goodid'];
            //$datalog['update_time']=time(); 用不到这个字段 

            $url = "http://139.224.59.94/System/jiekou/wx_function.php?r=orderPackageByQiXin";
            $para = "&accNbr=".$p['accnbr']."&offerSpecl=".$p['goodid']."&goodName=".$p['goodname']."&type=".$p['type']."&reqid=".$p['reqid']
            ."&actionCode=".$this->USER['actioncode']
            ."&ztInterSource=".$this->USER['intersource']
            ."&staffValue=".$this->USER['phone']; 

            $url = $url.$para;
            //开始向服务器请求订购
            $result = file_get_contents($url);
            $result = '{
            "TSR_SERIAL":"1525182384820160419100835445814",
            "TSR_RESULT":"0",
            "TSR_MSG":"订购成功"
            }';

            $ret = json_decode($result); 


            if (!$ret) {     
                //记日志==================================
                $datalog['result'] = "999999";
                $datalog['reqid'] = $p['retid'];
                $datalog['msg'] = "可能是服务器无响应";
                $datalog['url'] = $url;
                addorderlog($datalog, $p);
                //记日志==================================
            }  else { 

                //记日志================================== 
                $datalog['result'] = $ret->TSR_RESULT;
                $datalog['reqid'] = $ret->TSR_SERIAL;
                $datalog['msg'] = $ret->TSR_MSG;
                $datalog['url'] = $url;
                addorderlog($datalog, $p);
                //记日志==================================

                $data['id']=$p['id'];
                $data['result']= $ret->TSR_RESULT ;
                if ($ret->TSR_RESULT == "0") {
                   $data['ship_time']=time(); 
                }
                $data['reqid']=$ret->TSR_SERIAL;
                $data['update_time']=time();
                //更新订单状态
                $flag = M('subscriber')->data($data)->save();


                //订购成功后,更新个人的流量包信息 $ret->TSR_RESULT==表示成功
                if ($ret->TSR_RESULT == "0") {       
                    $dflow['id'] = $retchk;           
                    $dflow['uid'] = $this->USER['uid'];
                    $dflow['goodid'] = $p['goodid'];
                    $dflow['remainder'] = array('exp', 'remainder-1');
                    $dflow['used'] = array('exp', 'used+1');
                    
                    $ret2 = M('fxs_order_flow')->data($dflow)->save(); 

                } 


            }
            unset($datalog);
            unset($data);
            unset($p);         

            return $ret;
        } 

        //todo
        function deallog() {

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
            //===============开始记日志


            //向服务器发起请求订购
            $ret = $this->dinggou($p);
            if ($ret == "-1") {
                $this->error("订购失败.失败原因:个人帐户剩余流量不足!"); 
            }  else { 
                if (!$ret) {
                    $this->error("订购失败.可能是服务器无响应:");
                }  else {                
                    if ($ret->TSR_RESULT == "0")   {
                        $this->success("订购成功");
                    }  else {
                        $this->error("订购失败.失败原因:".$ret->TSR_MSG);
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
                $ret = $this->dinggou($p);
                if (!$ret || $ret->TSR_RESULT  <> "0") {
                    break;
                } 
            }
            if ($ret == "-1") {
                $this->error("订购失败.失败原因:个人帐户剩余流量不足!"); 
            } else {
                if (!$ret) {
                    $this->error("订购失败.可能是服务器无响应:");
                }  else {                
                    if ($ret->TSR_RESULT == "0")   {
                        $this->success("订购成功");
                    }  else {
                        $this->error("订购失败.失败原因:".$ret->TSR_MSG);
                    }  
                }
            }

        }
        **/
}