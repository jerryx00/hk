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
use Think\Controller\RestController;
use Org\Util\DES; 
use Org\Util\Mycrypt3des;
use Org\Util\ArrayHelper;


/**
* 和力云号卡接口
* 身份校验接口
*/
class IdentityController extends BasehlyController {

    public function index() {

        $d['telnum'] = I('telnum');
        $d['region'] = I('region');
        $d['price'] = I('price');
        $d['offer_id'] = I('offer_id');

        // //身份校验
        //       if ($d['idcard'] != '' && !empty($d['idcard'])) {
        //           list($result, $returnContent) = http_jwd('http://localhost/api.php/HlyInterface/idenCheck', $d);
        //       }
        $this->u = $d;
        $this->display();
    }

    /**
    * 检验身份
    * 
    */
    public function check() {
        $d = I('data');
//        $d['idcard'] = I('idcard');
//        $d['mobile'] = I('telnum');
//        $d['username'] = I('username');



        $list = [];
        //身份校验
        if ($d['idcard'] != '' && !empty($d['idcard'])) {
            //           list($result, $returnContent) = http_jwd('http://localhost/api.php/Hly/idenCheck', $d, 'POST');
            $retList = D('Hly','Service')->idenCheck($d);
        }
        $t = time();
        $d['created_at'] = $t;
        $d['updated_at'] = $t;
        $d['returncode'] = $retList['ReturnCode'];
        $d['returnmessage'] = $retList['ReturnMessage'];

        $idcard = $d['idcard'];

        $list[] = array_merge($retList, $d);
//        $vo = M('hlylockednum')->where(['telnum'=>$d['telnum'],'idcard'=>$d['idcard']])->find();
//
//        if ($vo) {            
//            $ret = M('hlylockednum')->where(['telnum'=>$d['telnum'],'idcard'=>$d['idcard']])->data($d)->save();
//            $d['lockid'] = $vo['id']; 
//        } else {
//            $ret = M('hlylockednum')->data($d)->add(); 
//            $d['lockid'] = $ret;  
//        }        

        $this->list = $list;
        $this->u = $d;
        $this->idcard = $idcard;
        $this->display('index');
    }

    /**
    * 写下面这个控制器，再加一个显示界面，界面名称 token.index
    *  访问方式 http://localhost/admin.php/Identity/token.html
    */
    public function token() {
        //功能，把当前qw_hlytoken表中token显示出来，显示三个字段
        //Token值 创建时间、过期时间
        //如果当前的token过期了，则更新这个表中的token值
        //如下命令获取则更新
        $retList = D('Hly','Service')->getToken();
        $ret = json_encode($retList);
        return $ret;          
    }


}