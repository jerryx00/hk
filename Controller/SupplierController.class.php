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

    class SupplierController extends ComController {

        public function add(){

            $category = M('category')->field('id,pid,name')->order('o asc')->select();
            $tree = new Tree($category);
            $str = "<option value=\$id \$selected>\$spacer\$name</option>"; //生成的形式
            $category = $tree->get_tree(0,$str,0);
            $this->assign('category',$category);//导航
            $this -> display();
        }

        public function index($sid=0,$p=1){

            $sid = intval($sid);
            $p = intval($p)>0?$p:1;

            $supplier = M('supplier');
            $pagesize = 20;#每页数量
            $offset = $pagesize*($p-1);//计算记录偏移量
            $prefix = C('DB_PREFIX');
            $where = ' 1=1 and status=1 ';
            if($sid){
                $where = $where ."{$prefix}supplier.sid=$sid";
            }
            $count = $supplier->where($where)->count();
            $list  = $supplier->field("{$prefix}supplier.*,{$prefix}category.name as typename")->where($where)->order("{$prefix}supplier.aid desc")->join("{$prefix}category ON {$prefix}category.id = {$prefix}supplier.sid")->limit($offset.','.$pagesize)->select();

            $page	=	new \Think\Page($count,$pagesize); 
            $page = $page->show();
            $this->assign('list',$list);	
            $this->assign('page',$page);
            $this -> display();
        }

        public function del(){

            $aids = isset($_REQUEST['aids'])?$_REQUEST['aids']:false;
            if($aids){
                if(is_array($aids)){
                    $aids = implode(',',$aids);
                    $map['aid']  = array('in',$aids);
                }else{
                    $map = 'aid='.$aids;
                }
                $ret = M('supplier')->where($map)->data(['status'=>0])->save();
                if($ret){
                    addlog('删除商家，AID：'.$aids);
                    $this->success('恭喜，商家删除成功！');
                }else{
                    $this->error('参数错误！');
                }
            }else{
                $this->error('参数错误！');
            }

        }

        public function edit($aid){

            $aid = intval($aid);
            $supplier = M('supplier')->where('aid='.$aid)->find();
            if($supplier){

                $category = M('category')->field('id,pid,name')->order('o asc')->select();
                $tree = new Tree($category);
                $str = "<option value=\$id \$selected>\$spacer\$name</option>"; //生成的形式
                $category = $tree->get_tree(0,$str,$supplier['sid']);
                $this->assign('category',$category);//导航

                $this->assign('supplier',$supplier);
            }else{
                $this->error('参数错误！');
            }
            $this -> display();
        }


        public function stock($aid){
            if ($aid == '1') {
                $ret = send_post(C('hostApiUrl').'/Ipauth/querystock');
                $ret = json_decode($ret);
            }
            $msg =  $ret->TSR_MSG .'('. $ret->TSR_RESULT.')';

            if ($ret->TSR_RESULT == '0') {                  
                $tmp = $ret->data;
                $this->info = $tmp ;
                $msg = '查询成功!'.$msg ;
            }   else {
                $msg = '查询失败!'.$msg ;
            }
            $this->returnMsg = $msg;               
            $this->list = $ret;
            $this->info = $ret->data;;

            $this->display();
        }

        public function update($aid=0){

            $aid = intval($aid);
            $data['sid'] = isset($_POST['sid'])?intval($_POST['sid']):0;
            $data['name'] = isset($_POST['name'])?$_POST['name']:false;
            $data['keywords'] = I('post.keywords','','strip_tags');
            $data['ipaddr'] = I('post.ipaddr');
            $data['discount'] = I('post.discount');
            $data['priority'] = I('post.priority');
            $data['ipaddr'] = I('post.ipaddr');

            $data['description'] = I('post.description','','strip_tags');
            $data['content'] = isset($_POST['content'])?$_POST['content']:false;
            $data['thumbnail'] = I('post.thumbnail','','strip_tags');
            $data['t'] = time();
            if(!$data['sid'] or !$data['name'] ){
                $this->error('警告！商家分类、商家名称为必填项目。');
            }
            if ( $data['discount'] > 1 || $data['discount'] < 0 || !isset($data['discount'])){
                $this->error('警告！折扣错误(0~1之间)!');
            }
            if($aid){
                M('supplier')->data($data)->where('aid='.$aid)->save();
                addlog('编辑商家，AID：'.$aid);
                $this->success('恭喜！商家编辑成功！');
            }else{
                $aid = M('supplier')->data($data)->add();
                if($aid){
                    addlog('新增商家，AID：'.$aid);
                    $this->success('恭喜！商家新增成功！', U('index'));
                }else{
                    $this->error('抱歉，未知错误！');
                }

            }
        }
}