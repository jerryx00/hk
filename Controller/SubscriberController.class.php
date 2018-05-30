<?php

    namespace Qwadmin\Controller;
    use Qwadmin\Controller\ComController;
    class SubscriberController extends ComController {

        public function index(){

            $u = $this->USER;
            $p = isset($_GET['p'])?intval($_GET['p']):'1';
            $field = isset($_GET['field'])?$_GET['field']:'';
            $keyword = isset($_GET['keyword'])?htmlentities($_GET['keyword']):'';
            $order = isset($_GET['order'])?$_GET['order']:'DESC';
            $where = '';
            $w = " result <> 0  and province=1 and actived=1 and uid =" . $u['uid'] ;

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
                if($field=='flowid'){
                    $where = "{$prefix}subscriber.goodsid LIKE '%$keyword%'";
                }
                if($field=='flowname'){
                    $where = "{$prefix}subscriber.goodsname LIKE '%$keyword%'";
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


            //echo $user->getLastSql();exit;
            $page	=	new \Think\Page($count,$pagesize); 
            $page = $page->show();
            $this->assign('list',$list);	
            $this->assign('page',$page);            
            $this->cc = "省内"; 
            $this -> display();
        }

        
        public function del(){

            $uids = isset($_REQUEST['ids'])?$_REQUEST['ids']:false;
            //uid为1的禁止删除
            if($uids==1 or !$uids){
                $this->error('参数错误！');
            }
            if(is_array($uids)) 
            {
                foreach($uids as $k=>$v){				
                    $uids[$k] = intval($v);
                }
                if(!$uids){
                    $this->error('参数错误！');
                    $uids = implode(',',$uids);
                }
            }

//            $w['id']  = array('in',$uids);
//            $data['actived']  = array('eq',0);
            $sql = "update qw_subscriber set actived=0 where id in ('". $uids ."')";
            $ret = M('subscriber')->execute($sql);
            if($ret){
                addlog('删除订单ID：'.$uids);
                $this->success('订单删除成功！');
            }else{
                $this->error('参数错误！');
            }
        }

        public function edit(){

            $uid = isset($_GET['id'])?intval($_GET['id']):false;
            if($uid){	
                //$member = M('member')->where("uid='$uid'")->find();

                $user = M('subscriber');
                $member  = $user->where("id=$uid")->find();

            }else{
                $this->error('参数错误！');
            }

            $where['province']="1";//省内
            $where['goodtype']="3";//电信
            $goodgroup = M('goods_group')->where($where)->select();
            $this->assign('goodgroup',$goodgroup);

            $this->assign('subscriber',$member);
            $this->cc = "省内";
            $this -> display();
        }

        public function update($ajax=''){
            $u = $this->USER;
            if($ajax=='yes'){
                $uid = I('get.uid',0,'intval');
                $gid = I('get.gid',0,'intval');
                die('1');
            }

            $id = isset($_POST['id'])?intval($_POST['id']):false;
            $user = I('post.user');
            $phone = I('post.phone');;
            if(!$phone){
                $this->error('请输入用户手机号码！');
            }

            $goodsid = I('post.good_id');
            //$goods_name = I('post.goods_name');
            $goodList = M('goods_group')->where(array('goodid'=>$goodsid))->find();
            $goodsname = $goodList['goodname'];
            if(!$goodsid){
                $this->error('请选择流量包！');
            }

            $data['phone'] = $phone;
            $data['goodsid'] = $goodsid;
            $data['goodsname'] = $goodsname;
            $data['description'] = I('post.description');


            //add by xugp 2016-04-23  begin
            $u = $this->USER;
            //这里暂时固定，后续可以考虑从用户个人信息中获取
            $staffValue = $u['phone'];
            $intersource = $u['intersource']; //渠道号


            //if($u['uid'] == 1 ){
//                $data['uid'] = 0;
//            }else{
                $data['uid'] = $u['uid'];
//            } 
            $data['path']=$u['path'].'-'.$u['uid'];//子类的path为父类的path加上父类的uid
            $data['level'] = intval($u['level']) + 1 ;
            //add by xugp 2016-04-23 end
            $data['id'] = $id;
            $data['result'] = C('DEFAULT_DD_RESULT');
            $data['user'] = $user;
            
            if(!$id){
                if($user==''){
                    //$this->error('用户名称不能为空！');
                }
                $data['create_time'] = time();
                $data['update_time'] = time();
                $data['user'] = $user;
                $rnd =  substr($phone, -7);
                $data['reqid'] = $intersource. date('YmdHis') .$rnd;
                //$data['reqid'] = getReqId($u, $phone);
                //            var_dump($data);exit;
                $data['province'] = I('post.province');
                $id = M('subscriber')->data($data)->add(); 
                addlog('新增订单，用户手机号码：'.$phone);
            }else{
                $data['update_time'] = time();
                M('subscriber')->data($data)->where("id=$id")->save();
                addlog('编辑订单信息，用户手机号码：'.$phone);
            }
            $this->success('操作成功！');
        }  

        public function add(){
            $where['province']="1";//省内
            $where['goodtype']="3";//电信
            $goodgroup = M('goods_group')->where($where)->select();

            $this->assign('goodgroup',$goodgroup);
            $this->cc = "省内";
            $this-> province = 1;
            $this -> display();

        }
         

}