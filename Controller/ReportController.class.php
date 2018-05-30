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
    class ReportController extends ComController {

        public function tongji(){
            $sid = I('sid');
            $sid = 3;
            $d = I('d');
            if ($d == '1') {
                M('order_tongji')->delete(['sid'=>$operator]) ;
            }  else {
                $ret = M('order_tongji')->max('order_date')->where(['sid'=>$sid]);
                $where = " and order_date > " ;
            }

            $table = M('supplier')->getFieldBySupplierid($operator,'table');
            $sql = "SELECT FROM_UNIXTIME(created_at,'%Y-%m-%d') AS d,user,count(uid) as cnt, notify_ret FROM ".C('DB_PREFIX').$table."  WHERE status=1".
            $where.
            " GROUP BY uid,d,notify_ret order by d desc";
            $list = M()->query($sql);
            foreach ($list as $k => $v) {
                $data[$k]['sid'] = $operator;
                $data[$k]['order_date'] = $v['d'];
                $data[$k]['cnt'] = $v['cnt'];
                $data[$k]['notify_ret'] = $v['notify_ret'];
                $data[$k]['created_at'] = time();
                $data[$k]['status'] = '1';
            }
            $ret = M('order_tongji')->addAll($data);
            echo $ret;
        }
        /**
        * 订单个数
        *
        */
        public function ordernum(){
            $sid = I('ids');
            $sid = 3;
            $filter['status'] = '1';
            $filter['sid'] = '3';

            $p = isset($_GET['p'])?intval($_GET['p']):'1';
            //$log->where("t < $t")->delete();//删除60天前的日志
            $pagesize = 10;#每页数量
            $offset = $pagesize*($p-1);//计算记录偏移量

            $list =  M('order_tongji') ->where($filter)->order('order_date desc')->limit($offset.','.$pagesize)->group('sid')->select();
            $count = count($list);

            $page	=	new \Think\Page($count,$pagesize);
            $page = $page->show();
            $this->assign('list',$list);
            $this->assign('page',$page);
            $this -> display();
        }

        /**
        * 按商家统计
        *
        */
        public function jsyd(){

            $model = M('order_km');

            $filter['status'] = '1';
            $filter['notify_ret'] = '0';

            $p = isset($_GET['p'])?intval($_GET['p']):'1';
            //$log->where("t < $t")->delete();//删除60天前的日志
            $pagesize = 10;#每页数量
            $offset = $pagesize*($p-1);//计算记录偏移量


            $count = $model->where($filter)->count();
            /**
            * SELECT uid, USER, SUM(price) AS total, ROUND(SUM(price),2), FROM_UNIXTIME(created_at,'%Y-%m-%d') AS d
            FROM qw_order_yt WHERE notify_ret=1
            GROUP BY uid,d
            ORDER BY d
            */
            $list =  $model ->field("uid, USER, ROUND(SUM(price),2) as  total, FROM_UNIXTIME(created_at,'%Y-%m-%d') AS d, notify_ret ")
            ->where($filter)->order('d desc')->limit($offset.','.$pagesize)->group('uid, d')->select();


            $page    =    new \Think\Page($count,$pagesize);
            $page = $page->show();
            $this->assign('list',$list);

            $this->assign('page',$page);
            $this -> display('index');

        }

        /**
         * 江苏卡密按地区统计
         */
        public function jskmbyarea(){
            $order=I('order');
            if ($order == 'd') {
                $order = ' d desc';
            }
            if ($order == 'areaid') {
                $order = ' areaid,d desc';
            }
            $model = M('order_km');

            $filter['order_ret'] = '0';
            $filter['notify_ret'] = '0';

            $yd = toDate(time(), 'Ym');
            $p = isset($_GET['p'])?intval($_GET['p']):'1';
//            $yd = isset($_GET['d'])?$_GET['d']:$yd;

//            $filter["FROM_UNIXTIME(created_at,'%Y%m')"] = $yd;

            //$log->where("t < $t")->delete();//删除60天前的日志
            $pagesize = 10;#每页数量
            $offset = $pagesize*($p-1);//计算记录偏移量


            $count = $model->where($filter)->count();

            $list =  $model ->field("uid, areaid, ROUND(SUM(price),2) as  total, FROM_UNIXTIME(created_at,'%Y%m') AS d, notify_ret ")
                ->where($filter)->order($order)->limit($offset.','.$pagesize)->group('d,areaid')->select();

//            echo M()->getlastSql();exit;
            $page    =    new \Think\Page($count,$pagesize);
            $page = $page->show();
            $this->assign('list',$list);

            $this->assign('page',$page);
            $this -> display('indexkm');

        }

          /**
          *话费
          *
          */
         public function jshfbyarea(){
            $order=I('order');
            if ($order == 'd') {
                $order = ' d desc';
            }
            if ($order == 'areaid') {
                $order = ' areaid,d desc';
            }
            $model = M('order_hf');

            $filter['order_ret'] = ['eq','0'];
            $filter['notify_ret'] = ['eq', '0'];
            $filter['created_at'] = ['gt', '1525104000'];


            $yd = toDate(time(), 'Ym');
            $p = isset($_GET['p'])?intval($_GET['p']):'1';
//            $yd = isset($_GET['d'])?$_GET['d']:$yd;

//            $filter["FROM_UNIXTIME(created_at,'%Y%m')"] = $yd;

            //$log->where("t < $t")->delete();//删除60天前的日志
            $pagesize = 10;#每页数量
            $offset = $pagesize*($p-1);//计算记录偏移量


            //$count = $model->where($filter)->count();

            $list =  $model ->field("uid, areaid, ROUND(SUM(price),2) as  total, FROM_UNIXTIME(created_at,'%Y%m') AS d, ROUND(SUM(fluxnum),2) as  mz, notify_ret ")
                ->where($filter)->order($order)->limit($offset.','.$pagesize)->group('d,areaid')->select();

//            echo M()->getlastSql();exit;
$count = count($list) ;
            $page    =    new \Think\Page($count,$pagesize);
            $page = $page->show();
            $this->assign('list',$list);

            $this->assign('page',$page);
            $this -> display('indexhf');

        }


        /**
         * 江苏卡密
         */
        public function jskm(){

            $model = M('order_km');

            $filter['order_ret'] = '0';
            $filter['notify_ret'] = '0';

            $yd = toDate(time(), 'Ym');
            $p = isset($_GET['p'])?intval($_GET['p']):'1';
//            $yd = isset($_GET['d'])?$_GET['d']:$yd;

//            $filter["FROM_UNIXTIME(created_at,'%Y%m')"] = $yd;

            //$log->where("t < $t")->delete();//删除60天前的日志
            $pagesize = 10;#每页数量
            $offset = $pagesize*($p-1);//计算记录偏移量


            //$count = $model->where($filter)->count();

            //$list =  $model ->field("uid, areaid , ROUND(SUM(price),2) as  total, FROM_UNIXTIME(created_at,'%Y%m') AS d, notify_ret ")
           //     ->where($filter)->order('d desc')->limit($offset.','.$pagesize)->group(' d')->select();
           $sql = "select a.uid, a.areaid , ROUND(SUM(a.price),2) as  total,  ROUND(SUM(b.price),2) as  mz,FROM_UNIXTIME(a.created_at,'%Y%m') AS d, a.notify_ret
from qw_order_km a,qw_goods_yt b where a.fluxnum=b.fluxnum and order_ret=0 and notify_ret=0 group by d" ;
$list = $model->query($sql);

//            echo M()->getlastSql();exit;
$count = count($list) ;
            $page    =    new \Think\Page($count,$pagesize);
            $page = $page->show();
            $this->assign('list',$list);

            $this->assign('page',$page);
            $this -> display('indexkm1');

        }



         /**
         * 江苏话费
         */
        public function jshf(){

            $model = M('order_km');

            $filter['order_ret'] = ['eq','0'];
            $filter['notify_ret'] = ['eq', '0'];
            $filter['created_at'] = ['gt', '1525104000'];

            $yd = toDate(time(), 'Ym');
            $p = isset($_GET['p'])?intval($_GET['p']):'1';

            $pagesize = 10;#每页数量
            $offset = $pagesize*($p-1);//计算记录偏移量




            //$list =  $model ->field("uid, areaid , ROUND(SUM(price),2) as  total, FROM_UNIXTIME(created_at,'%Y%m') AS d, notify_ret ")
           //     ->where($filter)->order('d desc')->limit($offset.','.$pagesize)->group(' d')->select();
           $sql = "select a.uid, a.areaid , ROUND(SUM(a.price),2) as  total,  ROUND(SUM(a.fluxnum),2) as  mz,FROM_UNIXTIME(a.created_at,'%Y%m') AS d, a.notify_ret
from qw_order_hf a where  a.order_ret=0 and a.notify_ret=0 and created_at >1525104000 group by d" ;
$list = $model->query($sql);

            $count = count($list);
//            echo M()->getlastSql();exit;
            $page    =    new \Think\Page($count,$pagesize);
            $page = $page->show();
            $this->assign('list',$list);

            $this->assign('page',$page);
            $this -> display('indexhf1');

        }
        /**
         * 江苏电信
         */
        public function jsdx(){
            $order=I('order');
            $model = M('order_tx');

            $filter['status'] = '1';
            $filter['notify_ret'] = '0';

            $p = isset($_GET['p'])?intval($_GET['p']):'1';
            //$log->where("t < $t")->delete();//删除60天前的日志
            $pagesize = 10;#每页数量
            $offset = $pagesize*($p-1);//计算记录偏移量


            $count = $model->where($filter)->count();
            /**
            * SELECT uid, USER, SUM(price) AS total, ROUND(SUM(price),2), FROM_UNIXTIME(created_at,'%Y-%m-%d') AS d
            FROM qw_order_yt WHERE notify_ret=1
            GROUP BY uid,d
            ORDER BY d
            */
            $list =  $model ->field("uid, USER, ROUND(SUM(price),2) as  total, FROM_UNIXTIME(created_at,'%Y-%m-%d') AS d, notify_ret ")
            ->where($filter)->order('d desc')->limit($offset.','.$pagesize)->group('uid, d')->select();


            $page    =    new \Think\Page($count,$pagesize);
            $page = $page->show();
            $this->assign('list',$list);

            $this->assign('page',$page);
            $this -> display('index');

        }

        /**
        * 汇总
        *
        */
          public function hz(){

            $model = M('order_sale_v');

            $p = isset($_GET['p'])?intval($_GET['p']):'1';
            //$log->where("t < $t")->delete();//删除60天前的日志
            $pagesize = 10;#每页数量
            $offset = $pagesize*($p-1);//计算记录偏移量


            $count = $model->order('user desc')->group('uid, otype')->count();
            /**
            * SELECT uid, USER, SUM(price) AS total, ROUND(SUM(price),2), FROM_UNIXTIME(created_at,'%Y-%m-%d') AS d
            FROM qw_order_yt WHERE notify_ret=1
            GROUP BY uid,d
            ORDER BY d
            */
           $list = $model->where($filter)->order('user desc')->group('uid, otype')->select();


            $page    =    new \Think\Page($count,$pagesize);
            $page = $page->show();
            $this->assign('list',$list);
            $this->assign('page',$page);

            $this -> display('hz');

        }

         public function card(){

            $model = M('cards_km');

            $p = isset($_GET['p'])?intval($_GET['p']):'1';
            //$log->where("t < $t")->delete();//删除60天前的日志
            $pagesize = 10;#每页数量
            $offset = $pagesize*($p-1);//计算记录偏移量


            $count = $model->order('id desc')->group('taocha_level,areaname,status')->count();
            /**
            * SELECT uid, USER, SUM(price) AS total, ROUND(SUM(price),2), FROM_UNIXTIME(created_at,'%Y-%m-%d') AS d
            FROM qw_order_yt WHERE notify_ret=1
            GROUP BY uid,d
            ORDER BY d
            */
           $list = $model->field('areaname,taocha_level,taocha_name,status,count(taocha_level) as cnt')->where($filter)
           ->order('taocha_level desc')->group('taocha_level,areaname,status')->select();

//             dump(M()->getLastSql());exit;
            $i = 0;
            $data_new = [];
            foreach ($list as $k => $v) {
                $taocha_level = $v['taocha_level'];
                $data_new[$taocha_level] = $v;
                $data_new[$taocha_level]['cnt_left']='0';
                $data_new[$taocha_level]['cnt_used']='0';
                if ($v['status'] == 1){
                    $data_new[$taocha_level]['cnt_left'] = $v['cnt'] ;
                } else {
                    $data_new[$taocha_level]['cnt_used'] = $v['cnt'] ;
                }
            }
            $page    =    new \Think\Page($count, $pagesize);
            $page = $page->show();
            $this->assign('list',$data_new);
            $this->assign('page',$page);

            $this -> display();

        }
}