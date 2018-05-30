<?php
    /**
    *
    * 版权所有：金豌豆<>
    * 作    者：国平<8688041@qq.com>
    * 日    期：2016-01-17
    * 版    本：1.0.0
    * 功能说明：后台登录控制器。
    *
    **/

    namespace Qwadmin\Controller;
    use Common\Controller\BaseController;
    use Think\Auth;
    class LoginController extends BaseController {
        public function index(){
            $user=session('user');
            if(!empty($user['uid'])){
                $this -> error('您已经登录,正在跳转到主页',U("index/index"));   
            }
            $this -> display();
        }
        public function login(){
            $username = I('post.user');
            $password = I('post.password');
        
            $verify = isset($_POST['verify'])?trim($_POST['verify']):'';
             //$remember = isset($_POST['remember'])?$_POST['remember']:0;
            if ($username=='') {
                addlog('登录失败.用户名为空',"unkown");
                session('user', null);    
                $this -> error('用户名不能为空！',U("login/index"));
            } elseif ($password=='') {
                addlog('登录失败.密码为空 ',$username);  
                session('user', null);  
                $this -> error('密码必须！',U("login/index"));
            }  else if (!$this->check_verify($verify,'login')) {
                //add by xup 2016-04-27   如果用户验证码错误，则要把session清空
                session('user', null);
                addlog('登录失败.验证码错误 ',$username);    

                $this -> error('验证码错误！',U("login/index"));
            }

            
           

            $model = M("Member");
            $password = password($password);
            $user = $model ->field('uid,user,phone,actioncode,intersource,pid,path,level,accesstoken,accesscode')-> where(array('user'=>$username,'password'=>$password)) -> find();
            if($user != false) {
                //if($remember){
                //				cookie('user',$user,3600*24*365);//记住我;
                //			}else{
                //				session('user',$user);
                //			}
                session('user',$user);

                // session("uid", $user['uid']);
                //                session("phone", $user['phone']);
                //                session("user", $user['phone']);
                addlog('登录成功.');				
                $url=U('index/index');
                $this->redirect('Index/index');

            }else{
                addlog('登录失败。用户名或密码错误',$username);
                $this -> error('登录失败，用户名或密码错误. 请重试！',U("login/index"));
            }
        }

        public function verify() {
            $config = array(
                'fontSize' => 14, // 验证码字体大小
                'length' => 4, // 验证码位数
                'useNoise' => false, // 关闭验证码杂点
                'imageW'=>100,
                'imageH'=>30,
                'codeSet'   =>  '0123456789',  
            );
            $verify = new \Think\Verify($config);
            $verify -> entry('login');
        }

        function check_verify($code, $id = '') {
            $verify = new \Think\Verify();
            return $verify -> check($code, $id);
        }
}