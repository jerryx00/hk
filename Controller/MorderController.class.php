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
* 和力云号卡订单类
* 
*/
class MorderController extends BasehlyController {

    public function index() {        
//        $orderList = M('hlyorder')->where(['status'=>'1'])->order('updated_at')->select();
        $sql = 'SELECT * FROM qw_hlyorder a, qw_hlydelivery b,qw_hlylockednum c WHERE c.id = b.uid AND c.id=a.uid AND c.STATUS=1';   
        $orderList = M('hlyorder')->query($sql);
        $this->orderList = $orderList;
        $this->display();
    }

    


}