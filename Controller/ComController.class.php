<?php
    /**
    *
    * 版权所有：金豌豆<>
    * 作    者：国平<8688041@qq.com>
    * 日    期：2015-09-17
    * 版    本：1.0.0
    * 功能说明：后台公用控制器。
    *
    **/

    namespace Qwadmin\Controller;

    use Common\Controller\BaseController;
    use Think\Auth;

    class ComController extends BaseController
    {
        public $USER;

        public function _initialize()
        {
            C(setting());
            //$user = cookie('user');
            $user = session('user');
            //var_dump($user);exit;
            //add by xugp 2016-04-27  临时
            //if ($user['phone'] == "") {
//                $user['phone'] = "15251823848";
//            }
//            if ($user['intersource'] == "") {
//                $user['intersource'] = "200305";
//            }
//            if ($user['intersource'] == "") {
//                $user['intersource'] = "200305";
//            }

            $this->USER = $user;
            $url = U("login/index");
            if (!$user) {
                header("Location: {$url}");
                exit(0);
            }
            $m = M();
            $prefix = C('DB_PREFIX');
            $UID = $this->USER['uid'];
            $userinfo = $m->query("SELECT * FROM {$prefix}auth_group g left join {$prefix}auth_group_access a on g.id=a.group_id where a.uid=$UID");
           
            $Auth = new Auth();
            $allow_controller_name = array('Upload');//放行控制器名称
            $allow_action_name = array();//放行函数名称
            if ($userinfo[0]['group_id'] != 1 && !$Auth->check(CONTROLLER_NAME . '/' . ACTION_NAME, $UID) && !in_array(CONTROLLER_NAME, $allow_controller_name) && !in_array(ACTION_NAME, $allow_action_name)) {
                if (CONTROLLER_NAME . '/' . ACTION_NAME == "Login/index" || CONTROLLER_NAME . '/' . ACTION_NAME == "Logout/index") {
                    session('user', null);
                }
                $this->error('没有权限访问本页面!');
            }

            $user = member(intval($UID));
            $this->assign('user', $user);


            $current_action_name = ACTION_NAME == 'edit' ? "index" : ACTION_NAME;
            $current = $m->query("SELECT s.id,s.title,s.name,s.tips,s.pid,p.pid as ppid,p.title as ptitle FROM {$prefix}auth_rule s left join {$prefix}auth_rule p on p.id=s.pid where s.isback=1 and s.name='" . CONTROLLER_NAME . '/' . $current_action_name . "'");
            $this->assign('current', $current[0]);


            $menu_access_id = $userinfo[0]['rules'];

            if ($userinfo[0]['group_id'] != 1) {

                $menu_where = "AND id in ($menu_access_id)";

            } else {

                $menu_where = '';
            }
            $menu = M('auth_rule')->field('id,title,pid,name,icon')->where("islink=1 $menu_where ")->order('o ASC')->select();
            $menu = $this->getMenu($menu);
            $this->assign('menu', $menu);

        }


        protected function getMenu($items, $id = 'id', $pid = 'pid', $son = 'children')
        {
            $tree = array();
            $tmpMap = array();

            foreach ($items as $item) {
                $tmpMap[$item[$id]] = $item;
            }

            foreach ($items as $item) {
                if (isset($tmpMap[$item[$pid]])) {
                    $tmpMap[$item[$pid]][$son][] = &$tmpMap[$item[$id]];
                } else {
                    $tree[] = &$tmpMap[$item[$id]];
                }
            }
            return $tree;
        }
        
         /**
        * { status : true, info: $info}
        * @param  string $info
        * @param  string $url
        * @return
        */
        protected function successReturn($info, $url) {
            $this->resultReturn(true, $info, $url);
        }

        
        /**
        * { status : false, info: $info}
        * @param  string $info
        * @param  string $url
        * @return
        */
        protected function errorReturn($info, $url) {
            $this->resultReturn(false, $info, $url);
        }
        
         /**
        * 返回带有status、info键值的json数据
        * @param  boolean $status
        * @param  string $info
        * @param  string $url
        * @return
        */
        protected function resultReturn($status, $info, $url) {
            $json['status'] = $status;
            $json['info'] = $info;
            $json['url'] = isset($url) ? $url : '';

            return $this->ajaxReturn($json);
        }
        
        public function index() {
            $u = $this->USER;
            $p = isset($_GET['p'])?intval($_GET['p']):'1';
            $field = isset($_GET['field'])?$_GET['field']:'';
            $keyword = isset($_GET['keyword'])?htmlentities($_GET['keyword']):'';
            $order = isset($_GET['order'])?$_GET['order']:'DESC';
            $where = '';
            $uid = $u['uid'];

            if ($uid < '10') {
                $w = " 1=1 " ;    
            }   else {
                $w = " 1=1 and uid=".$uid ;
            }


            $prefix = C('DB_PREFIX');
            if($order == 'asc'){
                $order = $prefix.$this->ORDER_MODEL.".created_at asc";
            }elseif(($order == 'desc')){
                $order = $prefix.$this->ORDER_MODEL.".created_at desc";
            }else{
                $order = $prefix.$this->ORDER_MODEL.".created_at desc";
            }
            if($keyword <>''){
               if($field=='user'){
                    $where = $prefix.$this->ORDER_MODEL.".user LIKE '%$keyword%'";
                }
                if($field=='phone'){
                    $where = $prefix.$this->ORDER_MODEL.".mobile LIKE '%$keyword%'";
                }
                if($field=='flownum'){
                    $where = $prefix.$this->ORDER_MODEL.".fluxnum =".$keyword;
                }
                if($field=='order_ret'){
                    $where = $prefix.$this->ORDER_MODEL.".order_ret = '$keyword'";
                } 
                if($field=='notify_ret'){
                    $where = $prefix.$this->ORDER_MODEL.".notify_ret = '$keyword'";
                }




                $where = $where. " and ". $w;
            }   else 
            {
                $where = $where. $w;
            }

            //$s = D('OrderYt', 'Service')->encryptPassword($w);



            $user = M($this->ORDER_MODEL);
            $pagesize = 10;#每页数量
            $offset = $pagesize*($p-1);//计算记录偏移量
            $count = $user->where($where)->count();

            $list  = $user->field($prefix.$this->ORDER_MODEL.".*")->order($order)->where($where)->limit($offset.','.$pagesize)->select();


            //echo $user->getLastSql();exit;
            $page    =    new \Think\Page($count,$pagesize); 
            $page = $page->show();
            $this->assign('list',$list);    
            $this->assign('page',$page);          

            $this -> display('index');
        }
}