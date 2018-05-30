<?php
namespace Qwadmin\Controller;
use Qwadmin\Controller\ComController;
class AreaController extends ComController {

    protected $MODEL_NAME='mobile_area';

    public function index(){

        $u = $this->USER;
        $p = isset($_GET['p'])?intval($_GET['p']):'1';
        $field = isset($_GET['field'])?$_GET['field']:'';
        $keyword = isset($_GET['keyword'])?htmlentities($_GET['keyword']):'';
        $order = isset($_GET['order'])?$_GET['order']:'DESC';
        $where = '';

        $UID = $u['uid']; 

        $user = M($this->MODEL_NAME);
        $pagesize = 15;#每页数量
        $offset = $pagesize*($p-1);//计算记录偏移量


        $list  = $user->limit($offset.','.$pagesize)->select();
        $count = $user->count();   

        //echo  M()->getLastSql();exit;
        $page	=	new \Think\Page($count,$pagesize); 
        $page = $page->show();
        $this->assign('list',$list);	
        $this->assign('page',$page);

        $this -> display();
    }

    public function del(){

        $uids = isset($_REQUEST['aids'])?$_REQUEST['aids']:false;


        //uid为1的禁止删除
        if(!$uids){
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
        $action = I('ntype');

        if ($action =='1') {
            $ret = M('mobile_area')->where($map)->data(['status'=>1])->save();
            $this->clear();
            $this->success('修改成功！');
        } else if ($action =='2') {
            $ret = M('mobile_area')->where($map)->data(['status'=>0])->save();
            $this->clear();
            $this->success('修改成功！');
        } else {
            if(M($this->MODEL_NAME)->where($map)->delete()){
                addlog('删除维护区域ID：'.$uids);
                $this->clear();
                $this->success('恭喜，维护区域删除成功！');
            }else{
                $this->error('参数错误！');
            }
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
        $this->clear();

        $this->success('操作成功！','index');
    }

    public function clear() {
        $cache = \Think\Cache::getInstance();
        $cache->clear();
    }



}