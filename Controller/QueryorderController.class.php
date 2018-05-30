<?php
    /**
    *
    * 版权所有：金豌豆<>
    * 作    者：国平<8688041@qq.com>
    * 日    期：2016-09-20
    * 版    本：1.0.0
    * 功能说明：文章控制器。
    *
    **/

    namespace Qwadmin\Controller;
    use Qwadmin\Controller\ComController;
    use Vendor\Tree;
    use Think\Controller\RestController;
    use Org\Util\DES3;
    use Org\Util\CryptAES;

    class QueryorderController extends RestController {


        //protected $allowMethod    = array('get','post','put'); // REST允许的请求类型列表
        //protected $allowType      = array('html','xml','json'); // REST允许请求的资源类型列表

        /**
        * http://139.224.59.94/llfx/queryorder/flows?token=164ae90365041d76e906c34f1e7fe590
        * 
        * 
        * http://localhost/index.php/queryorder/flows?token=164ae90365041d76e906c34f1e7fe590
        */
        public function flows(){           
            $p['token'] = I('token');

            //检查1，参数是否合法============================================================
            if ($p['token'] == "")  {
                $info['TSR_RESULT'] = 9000001;   
                $info['TSR_MSG'] = "对不起，请检查输入的信息是否有误" ;  
                $result=$this->response($info,'json');  ;  
                return $result;  
            }      

            //检查2,用户是否合法============================================================
            $model = M("Member");
            //$user = $model-> where(array('token'=>$p['token'])) -> find();
            $sql = "select * from qw_member where status=1 and accesstoken='".$p['token']."'";
            //$list = $model->where(['accesstoken'=>$p['token']])->find();        
            $list = $model->query($sql);
            if ($list == false) {
                $info['TSR_RESULT'] = 9000003 ;
                $info['TSR_MSG'] = "该用户未申请接入分销接口" ;   
                $p['uid'] = 0;
                $p['uname'] = "unkown";


            } else {
                $user = $list[0];
                $p['uid'] = $user['uid'];
                $p['uname'] = $user['user'];
                //检查3,IP是否合法============================================================
                $ip = get_client_ip();
                $isLoged = false;

                if ($user['authip'] != $ip && $user['authip']!='0.0.0.0' ) {
                    $info['TSR_RESULT'] = 9000002 ;   
                    $info['TSR_MSG'] = "不合法的请求,非信任IP" ; 

                } else {
                    //再次校验$token
                    $_token = $user['accesstoken'];
                    if ($_token != $p['token']) {
                        $info['TSR_RESULT'] = 9000004 ; 
                        $info['TSR_MSG'] = "未知用户" ; 

                    } else {
                        //$retchk = checkFlow($user['uid'], $p['goodid'], $p['reqid']);
                        //向服务器发起请求订购
                        //$p['goodname']=M('goods_group')->getFieldByGoodid($p['goodid'], 'goodname');

                        $where['uid']=$p['uid'];
                        $sql = "SELECT a.goodid,a.goodname,a.goodtype,a.province,a.desc,SUM(b.remainder) remainder, SUM(b.used) used ".
                        " from qw_goods_group a, qw_fxs_order_flow b where b.goodid=a.goodid ";

                        $sql = $sql." and b.uid = " .$where['uid'] ." group by b.goodid ORDER BY a.id";
                        //var_dump($sql);exit;
                        $myflow = M('fxs_order_flow')->query($sql);

                        $info['TSR_RESULT'] = 0 ; 
                        $info['TSR_MSG'] = "成功" ; 
                        $info['data'] =  $myflow;
                        $info['result_cnt'] =  count($myflow);                      
                    }     
                }
            }
            $result=$this->response($info,'json'); 
            unset($info);
            return $result;  

        }

        /**
        * http://139.224.59.94/llfx/queryorder/order?reqId=2003050120160826220449230816&token=164ae90365041d76e906c34f1e7fe590
        * 
        * 
        * http://localhost/index.php/queryorder/order?reqId=2003050120160826220449230816&token=164ae90365041d76e906c34f1e7fe590
        */
        public function order(){
            $p['reqid'] = I('reqId');            
            $p['token'] = I('token');

            //检查1，参数是否合法============================================================
            if ($p['reqid'] == "" || $p['token'] == "")  {
                $info['TSR_RESULT'] = 9000001;   
                $info['TSR_MSG'] = "对不起，请检查输入的信息是否有误" ;  
                $result=$this->response($info,'json');  ;  
                return $result;  
            }      

            //检查2,用户是否合法============================================================
            $model = M("Member");
            //$user = $model-> where(array('token'=>$p['token'])) -> find();
            $sql = "select * from qw_member where status=1 and accesstoken='".$p['token']."'";
            //$list = $model->where(['accesstoken'=>$p['token']])->find();        
            $list = $model->query($sql);
            if ($list == false) {
                $info['TSR_RESULT'] = 9000003 ;
                $info['TSR_MSG'] = "该用户未申请接入分销接口" ;   
                $p['uid'] = 0;
                $p['uname'] = "unkown";
            } else {
                $user = $list[0];
                $p['uid'] = $user['uid'];
                $p['uname'] = $user['user'];
                //检查3,IP是否合法============================================================
                $ip = get_client_ip();
                $isLoged = false;

                if ($user['authip'] != $ip && $user['authip']!='0.0.0.0' ) {
                    $info['TSR_RESULT'] = 9000002 ;   
                    $info['TSR_MSG'] = "不合法的请求,非信任IP" ;                
                } else {
                    //再次校验$token
                    $_token = $user['accesstoken'];
                    if ($_token != $p['token']) {
                        $info['TSR_RESULT'] = 9000004 ; 
                        $info['TSR_MSG'] = "未知用户" ;                  
                    } else {
                        //$retchk = checkFlow($user['uid'], $p['goodid'], $p['reqid']);
                        //向服务器发起请求订购
                        //$p['goodname']=M('goods_group')->getFieldByGoodid($p['goodid'], 'goodname');

                        $where['uid']=$p['uid'];
                        $where['reqid']=$p['reqid'];                         
                        //                                   var_dump($sql);exit;
                        $myflow = M('order_log')->field('reqid, create_time,goodid, goodname,phone, result, msg')->where($where)->find();
                        if ($myflow === false) {
                            $info['TSR_RESULT'] = 9000015 ; 
                            $info['TSR_MSG'] = "未知记录" ;
                        } else {
                            $info['TSR_RESULT'] = 0 ; 
                            $info['TSR_MSG'] = "成功" ; 
                            $info['data'] =  $myflow;
                        }

                    }     
                }
            }


            $result=$this->response($info,'json'); 
            unset($info);
            return $result;
        }

}