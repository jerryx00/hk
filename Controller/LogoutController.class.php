<?php
    /**
    *
    * 版权所有：金豌豆<>
    * 作    者：国平<8688041@qq.com>
    * 日    期：2016-01-17
    * 版    本：1.0.0
    * 功能说明：后台登出控制器。
    *
    **/

    namespace Qwadmin\Controller;
    use Qwadmin\Controller\ComController;
    class LogoutController extends ComController {
        public function index(){
            session('user', null);            
            cookie('user',null);
            $url = U("login/index");        
            header("Location: {$url}");
            exit(0);
        }
}