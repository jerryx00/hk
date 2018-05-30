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

    class OrderController extends ComController {  

        public function index() {
            $this->ORDER_MODEL = 'order_v';
            parent::index();
        }

        public function history() {
            $this->ORDER_MODEL = 'order_history_v'; 
            parent::index();
        }
        public function mobile() {
            $this->ORDER_MODEL = 'order_v';

            $u = $this->USER; 
            $uid = $u['uid'];

            $mobile = trim(I('mobile'));


            if ($mobile != '') {
                if (strlen($mobile) != 11) {
                    $this->error('您输入的手机号码不正确,请输入正确的手机号码');    
                }

                //$filter['mobile'] = $mobile;
                $where = ' where 1=1';
                $where = $where. ' and mobile='.$mobile;

                $user = M($this->ORDER_MODEL);

                $sql = 'select * from qw_order_v '. $where;
               // ' union '.
//                'select * from qw_order_history_v'.$where;

                $list  = $user->query($sql);

                $this->assign('list',$list);    
            } 



            $this -> display();
        }


}