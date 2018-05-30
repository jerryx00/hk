<?php
    /**
    *
    * 版权所有：金豌豆<>
    * 作    者：国平<8688041@qq.com>
    * 日    期：2016-09-20
    * 版    本：1.0.0
    * 功能说明：后台首页控制器。
    *
    **/

    namespace Qwadmin\Controller;
    use Qwadmin\Controller\ComController;
    use Think\Auth;
    class IndexController extends ComController {
        public function index(){

            $model = new \Think\Model();
            $mysql = $model ->query( "select VERSION() as mysql" );
            $p = isset($_GET['p'])?intval($_GET['p']):'1';

            $t = time()-3600*24*60;
            $log = M('log');

            //$log->where("t < $t")->delete();//删除60天前的日志
            $pagesize = 25;#每页数量
            $offset = $pagesize*($p-1);//计算记录偏移量
            $count = $log->count();
            $u = $this->USER;
            if ($u['uid'] > 2) {
                $where['uid'] = $u['uid'];
                $list = $log->order('id desc')->where($where)->limit($offset.','.$pagesize)->select();
            }   else {
                $list = $log->order('id desc')->limit($offset.','.$pagesize)->select();     
            }

            $page	=	new \Think\Page($count,$pagesize); 
            $page = $page->show();
            $this->assign('list',$list);	
            $this->assign('page',$page);

            $this->assign('mysql',$mysql[0]['mysql']);
            $this->assign('nav',array('','',''));//导航
            $this -> display();
        }
        
         public function getData(){
            //$list = M('order_yt')->field('user,price')->select();
//            foreach ($list as $k => $v) {
//                $data['name'][$k] = $v['user'];
//                $data[num][$k] = $v['price'];
//
//            }
//            $ret = json_encode($data);
            //            echo $ret;
//            $ret = '[[141,234,342,432,653,123,631,342,342,453],[5,2,7,3,1,2,7,6,5,3]]';
            echo $ret;
        }
}