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
        //            dump($orderList);exit;
        $this->orderList = $orderList;
        $this->display();
    }


    public function detail() { 
        $d['booking_id'] = I('booking_id');         
        $d['mobile'] = I('mobile'); 

        $d['orderId'] = I('respid');  

        $list = $this->orderDetail($d); 
        $this->list = $list;            
        $this->d = $d;

        //        dump($list);exit;
        $this->display();

    }

    /**
    * 核单（向HLY查询真实订单）
    * 
    */
    public function query() { 
        //订单编码,这里用响应respid

        $d['booking_id'] = I('booking_id');         
        $d['mobile'] = I('mobile'); 

        $d['orderId'] = I('respid');       
        $d['$ret'] = 1;       

        //调用和力云查询接口
        $ret = D('Hly','Service')->query($d);

        foreach ($ret as $k => $v) {
            $list[strtolower($k)]  = $v;
        }
        // if ($list['status'] == 3 || $list['status'] == 4 || $list['status'] == 5){
        //
        $deliver['code'] = $list['deliveryno'];
        $deliver['express'] = $list['deliveryexpress'];
        $deliver['expstatus'] = $list['deliveryexpstatus'];
        $deliver['statusmsg'] = $list['statusmsg'];
        $deliver['expinfo'] = $list['deliveryexpinfo'];
        $deliver['status'] = $list['status'];
        //        }

        $filter['booking_id'] = $booking_id;

        // 0000：查询成功
        //1：订单不存在
        //其他：查询失败

        if ($deliver['retcode'] == '0000')  {
            $ret = M('hlydelivery')->where($filter)->data($deliver)->save();
        }
        $list = $this->orderDetail($d); 
        $this->list = $list;            
        $this->deliver = $deliver; 
        $this->d = $d;

        $this->display('detail');
    }

    /**
    * 内部方法，查询订单明细
    * 
    */
    private function orderDetail($d) {

        $sql = 'SELECT * FROM qw_hlyorder a, qw_hlydelivery b,qw_hlylockednum c WHERE c.id = b.uid AND c.id=a.uid AND c.STATUS=1 and a.booking_id='.$d['booking_id'];   

        $orderList = M('hlyorder')->query($sql);

        if ($orderList) {
            $list = $orderList[0];     
        }  else {
            $list = $orderList;
        }
        return $list;
    }



}