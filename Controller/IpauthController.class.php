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


    class IpauthController extends ComController {

        /**
        * 查询接口
        */
        //http://139.224.59.94/llfx/index.php/ipauth/querystock
        //http://139.224.59.94/llfx/index.php/ipauth/querystock
        public function querystock(){             
            $qry = $_SERVER["QUERY_STRING"];
            $url = C('hostApiUrl')."/ipauth/querystock"; 
            $result = $this->getQueryString($url, $qry);

            $ret = json_decode($result);
            $ret = json_decode($ret);
                        
            $this->returnMsg =  $ret->TSR_MSG .'('. $ret->TSR_RESULT.')';
            
            if ($ret->result == "0") {                  
                
                $this->info = $ret->data ;
            } 
            
            
            
            $this->display('Personal/stock'); 
        }

        private function getQueryString($url, $qry){
            if (isset($qry) && !empty($qry)) {
                $url = $url.'&'. $qry;
            }
            $result = send_post_urlencoded($url);
            return $result;
        }


}