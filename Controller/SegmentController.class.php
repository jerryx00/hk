<?php
namespace Qwadmin\Controller;
use Qwadmin\Controller\ComController;
class SegmentController extends ComController {

    protected $MODEL_NAME='mobile_segment';

    public function index(){


        $u = $this->USER;
        $p = isset($_GET['p'])?intval($_GET['p']):'1';
        $field = isset($_GET['field'])?$_GET['field']:'';
        $keyword = isset($_GET['keyword'])?htmlentities($_GET['keyword']):'';
        $order = isset($_GET['order'])?$_GET['order']:'DESC';
        $where = '';
        $areaid = I('areaid');
        $s = I('keyword');
        if ($areaid != '') {
            $filter['areaid'] = ['eq',$areaid];
        }

        if ($s != '') {
            $filter['segment'] = ['like', '%'.$s.'%'];
        }
        $UID = $u['uid']; 

        $user = M($this->MODEL_NAME);
        $pagesize = 10;#每页数量
        $offset = $pagesize*($p-1);//计算记录偏移量

        $count = $user->where($filter)->count(); 

        $list  = $user->where($filter)->limit($offset.','.$pagesize)->select();

        //echo  M()->getLastSql();exit;
        $page	=	new \Think\Page($count,$pagesize); 
        $page = $page->show();
        $this->assign('list',$list);	
        $this->assign('page',$page);

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
            addlog('删除号码段ID：'.$uids);
            $this->success('恭喜，删除号码段成功！');
        }else{
            $this->error('参数错误！');
        }
    }
    public function add(){
        $this -> display('form');
    }
    public function edit(){
        $u = $this->USER;
        $id = isset($_GET['id'])?intval($_GET['id']):false;
        if($id){    
            $vo = M($this->MODEL_NAME)->where("id='$id'")->find();                

        }

        $this->assign('vo',$vo);
        $this -> display('form');
    }
    public function update($ajax=''){
        $id = I('id');
        $data = I('data');        
        $data['code']='025';

        $t = time();
        //$cnt = M('mobile_segment')->where($filter)->count();
        if($id ==''){                 
            //                $data['areaid'] = $areaid;
            $id = M($this->MODEL_NAME)->data($data)->add();
            addlog('新增号码段，areaid：'.$id);
        }else{
            $ret = M($this->MODEL_NAME)->data($data)->where("id='$id'")->save();
            addlog('编辑号码段，areaid：'.$id);
        }
        $this->success('操作成功！','index');
    }

}