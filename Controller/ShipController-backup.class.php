<?php
    /**
    *
    * 版权所有：金豌豆<qwadmin.qiawei.com>
    * 作    者：国平<8688041@qq.com>
    * 日    期：2016-01-20
    * 版    本：1.0.0
    * 功能说明：用户控制器。
    *
    **/

    namespace Qwadmin\Controller;
    use Qwadmin\Controller\ComController;
    class ShipController extends SubscriberController { 

        public function dinggou($p) {
            $url = "http://139.224.59.94/System/jiekou/wx_function.php?r=orderPackageByQiXin";
            $para = "&accNbr=".$p['accNbr']."&offerSpecl=".$p['offerSpecl']."&goodName=".$p['goodname']."&type=".$p['type']."&reqid=".$p['reqid']
            ."&actionCode=".$this->USER['actioncode']
            ."&ztInterSource=".$this->USER['intersource']
            ."&staffValue=".$this->USER['phone']; 

            $url = $url.$para;
            $result = file_get_contents($url);
        }

        public function order(){            
            $uid = I('get.uid');
            $accNbr=I('get.phone');
            $goodid=I('get.goodsid');
            $goodname=I('get.goodsname');
            $goodname="1";
            //type 1普通包；2兑换码；3其他
            $type=1;
            $reqid=I('get.reqid');
            //===============开始记日志
            addlog("发起流量订购.accNbr:".$accNbr.";reqid:".$reqid.";goodid:".$goodid) ;
            unset($datalog);   

          
            //$data['goodname'] = $goodname;
            //===============结束记日志  

            $url = "http://139.224.59.94/System/jiekou/wx_function.php?r=orderPackageByQiXin";
            $para = "&accNbr=".$accNbr."&offerSpecl=".$goodid."&goodName=".$goodname."&type=".$type."&reqid=".$reqid
            ."&actionCode=".$this->USER['actioncode']
            ."&ztInterSource=".$this->USER['intersource']
            ."&staffValue=".$this->USER['phone']; 

            $url = $url.$para;
            $result = file_get_contents($url);

            //            $result = '{
            //    "TSR_SERIAL":"1525182384820160419100835445814",
            //    "TSR_RESULT":"0",
            //    "TSR_MSG":"您的库存不足！"
            //}';

            $datalog['phone'] = $accNbr;
            $datalog['goodid'] = $goodid;

            if (!$result) {     
                //记日志==================================
                $datalog['status'] = "0";
                $datalog['reqid'] = $retid;
                $datalog['msg'] = "可能是服务器无响应";
                addorderlog($datalog);
                //记日志==================================

                $this->error("订购失败.可能是服务器无响应:");
            }  else { 
                $ret = json_decode($result);  
                if ($ret->TSR_RESULT == "0")   {                    
                    $data['uid']=$uid;
                    $data['status']='1';
                    $data['reqid']=$ret->TSR_SERIAL;
                    $data['update_time']=time();

                    //更新订单状态
                    $flag = M('subscriber')->data($data)->save();

                    //更新个人的流量包信息
                    $dw['uid'] = $this->USER['uid'];
                    $dw['goodid'] = $goodid;
                    $dflow['remainder'] = array('exp', 'remainder-1');
                    $dflow['used'] = array('exp', 'used+1');
                    $ret2 = M('fxs_total_flow')->where($dw)->data($dflow)->save();   

                    //记日志==================================

                    $datalog['result'] = $ret->TSR_RESULT;
                    $datalog['reqid'] = $ret->TSR_SERIAL;
                    $datalog['msg'] = $ret->TSR_MSG;
                    addorderlog($datalog);
                    //记日志==================================

                    $this->success("订购成功");
                }  else {
                    //记日志==================================

                    $datalog['result'] = $ret->TSR_RESULT;
                    $datalog['reqid'] = $ret->TSR_SERIAL;
                    $datalog['msg'] = $ret->TSR_MSG;
                    addorderlog($datalog);
                    //记日志==================================

                    $this->error("订购失败.失败原因:".$ret->TSR_MSG);
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
            // if(is_array($uids)) 
            //            {
            //                foreach($uids as $k=>$v){                   
            //                    $uids[$k] = intval($v);
            //                }
            //                
            //            }
            $ids = implode(',',$uids);
            $map['id']  = array('in',$ids);

            $ret = M('subscriber')->where($map)->select();
            foreach($ret as $k=>$v){                   
                $data['reqid'] = $v['reqid'];
                $data['goodsid'] = $v['goodsid'];
                $data['goodsname'] = $v['goodsname'];
                $data['phone'] = $v['phone'];
            }

        }
}