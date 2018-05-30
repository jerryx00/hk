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
* 和力云号卡查询
*/
class McardController extends BasehlyController {

	public function index() {
		$u['idcard'] = I('idcard');
		$u['mobile'] = I('mobile');
		$u['username'] = I('username');
		$u['authStatus'] = I('authStatus');

		$offers = M('hlyoffer')->where(['status'=>1])->order('id')->select();
		$this->offers = $offers;

		$city = M('mobile_area')->field('areaname')->order('id')->select();
		$list = [];
		$this->list = $list;
		$this->city = $city;
		$this->u = $u;
		$this->display();
	}

	public function token() {
		$time = getMillisecond();
		$appkey = C('AppKey');
		$SecretKey = C('SecretKey');

		$sign = get_hly_sign($time);

		$xmldata['Datetime'] = $time;
		$xmldata['Authorization']['AppKey'] = $appkey;
		$xmldata['Authorization']['sign'] = $sign;
		$url = C('HLY_AUTH_URL');

		$paras = xml_encode($xmldata, 'Request');

		echo 'getNewToken url ==> '.$url."<br>";
		dump($paras);
		echo "<br>" ;
		list($result, $returnContent) = http_post_hly($url, $paras, '', '');
		dump($result);
		echo "<br>" ;
		dump($returnContent);
	}

	//    号码查询
	public function search() {
		$info = I('info');

		$city = M('mobile_area')->field('areaname')->order('id')->select();
		$this->city = $city;

		//        $d['begin'] = I('begin');
		//        $d['mid'] = I('mid');
		//        $d['end'] = I('end');
		//        if ($d['begin'] == '' && $d['end']=='' && $d['mid'] ==' '){
		//            $fitmod = '%';
		//        }
		$MaxPrice= I('maxprice');
		if ($MaxPrice == '') {
			$MaxPrice = 1000;
		}
		$MinPrice = I('minprice');
		if ($MinPrice == '') {
			$MinPrice = 0;
		}
		$Fitmod = I('mobile');
		if ($Fitmod == '') {
			$Fitmod = '%';
		}
		$d['fitmod'] = $Fitmod;
		$d['maxcount'] = C('MaxCount');
		$d['pageindex'] = I('pageindex');
		if ($pageindex == '') {
			$d['pageindex'] = 1;
		}
		$d['maxprice'] = $MaxPrice;
		$d['minprice'] = $MinPrice;
		$d = array_merge($info, $d);



		//调用和力云查号接口
		$retList = D('Hly','Service')->getNum($d);
        

		$this->region = $d['region'];
		$this->u = $info;
		$this->list = $retList;
        
        $offers = M('hlyoffer')->where(['status'=>1])->order('id')->select();
        $this->offers = $offers;
        
		$this->display('index');
	}
	/**
	*
	*
	*/
	public function getNum() {

	}

	/**
	* 锁定号码接口
	*
	*/
	public function lockNum() {
		$d['region'] = I('region');
		$d['telnum'] = I('telnum');
		$d['price'] = I('price');
		$d['offer_id'] = I('offer_id');
		$d['username'] = I('username');
		//调用和力云锁号接口
		$retList = D('Hly','Service')->lockNum($d);


		//锁定成功，写入锁号表
		$d['idcard'] = I('idcard');
		$d['created_at'] = $retList['Datetime'];
		$d['updated_at'] = $retList['Datetime'];
		$d['returncode'] = $retList['ReturnCode'];
        $d['returnmessage'] = $retList['ReturnMessage'];
		$d['uid'] = session('user.uid');
        
		//此处需要 改进，如果锁号失败，则要看本地库中是否是由该 用户锁定，且在有效期内
		$filter['idcard'] = ['eq', $d['idcard']];
		$filter['telnum'] = ['eq', $d['telnum']];
		$filter['uid'] = ['eq', session('user.uid')];

		if ($d['returncode'] =='0' ) {
			$vo = M('hlylockednum')->where(['idcard'=>$d['idcard'],'telnum'=>$d['telnum']])->find();
			if ($vo) {

				$ret = M('hlylockednum')->where($filter)->save();
			} else {
				$ret = M('hlylockednum')->data($d)->add();
			}

			$this->list = $d;
			//unset($d);
			$this->success($d['telnum'].' 号码锁定成功', "add?lockid={$ret}&idcard={$d['idcard']}&telnum={$d['telnum']}");
		} else {
			$t = time() - 24*60*58;
			$filter['updated_at'] = ['gt',$t] ;
			$vo = M('hlylockednum')->where($filter)->find();
			if ($vo) {
				$this->success($d['telnum'].' 号码锁定成功', "add?lockid={$vo['id']}&idcard={$d['idcard']}&telnum={$d['telnum']}");
			}
			$this->list = $d;
			$ret = M('hlylockednum')->where(['idcard'=>$d['idcard'],'telnum' =>$d['telnum']])->delete();
			$this->error($d['telnum'].' 号码锁定失败,'.$d['returnmessage']."(".$d['returncode'].")" );

		}

	}

	public function lockNumAjax() {
		$d['region'] = I('region');
		$d['telnum'] = I('telnum');
		$d['price'] = I('price');
		//调用和力云锁号接口
		$retList = D('Hly','Service')->lockNum($d);


		//锁定成功，写入锁号表
		$d['idcard'] = I('idcard');
		$d['created_at'] = $retList['Datetime'];
		$d['updated_at'] = $retList['Datetime'];
		$d['returncode'] = $retList['ReturnCode'];
		$d['uid'] =  session('user.uid');
		//        $d['returnmessage'] = $retList['ReturnMessage'];
		if ($retList['ReturnCode'] =='0' ) {
			$vo = M('hlylockednum')->where(['idcard'=>$d['idcard']])->find();
			if ($vo) {
				$ret = M('hlylockednum')->where(['idcard'=>$d['idcard']])->data($d)->save();
			} else {
				$ret = M('hlylockednum')->data($d)->add();
			}
		}

	}

	/**
	* 查询锁定号码
	*
	*/
	public function lockmobile() {
		$list = M('hlylockednum')->where($filter)->select();
		$this->list = $list;
		$this->display();
	}
	/**
	* 用户信息录入界面
	*
	*/
	public function add(){
		$idcard = I('idcard');
		$telnum = I('telnum');
		// $filter['status'] = '1';
		//        if ($idcard != '') {
		//            $filter['idcard'] = $idcard;
		//        }
		//        $list = M('hlylockednum')->where($filter)->select();
		//        $this->list = $list;
		$this->telnum = $telnum;
		$this->lockid = I('lockid');

		$this->display();
	}

	/**
	* 提交订单，写入数据库
	*
	*/
	public function insert(){
		$delivery = I('data');
		$booking_id = date('YmdHis').get_millisecond();

		$lockid = $delivery['lockid'];
		$vo = M('hlylockednum')->where(['id'=>$lockid])->find();
		$delivery['offer_id'] = $vo['offer_id'];
		$delivery['market_sale_id'] = $vo['offer_id'];
		$delivery['type'] = '1';

		//cust_name    biz_info_dt    1    string    v200    客户姓名
		//    ic_no    biz_info_dt    1    string    v30    证件号码
		//    contact_name    biz_info_dt    1    string    v20    联系人姓名
		//    contact_phone    biz_info_dt    1    string    v20    联系人电话
		//    acc_nbr    biz_info_dt    1    string    v30    业务号码
		$filter['idcard'] = $vo['idcard'];
		$filter['telnum'] = $vo['telnum'];
		// //获取用户信息
		//        $cust_info = M('hlyuser')->where(['idcard' => $delivery['idcard']])->find();
		$cust_name = $vo['username'];
		$ic_no = $vo['idcard'];
		//联系人姓名
		$contact_phone = $delivery['delivery_phone'];
		$acc_nbr = $vo['telnum']; //所办业务号码


		//获取号码信息
		//    $list = M('hlylockednum')->where($filter)->find();

		//

		$order_info_dt['booking_id'] = $booking_id;
		$order_info_dt['type'] = $delivery['type'];
		$order_info_dt['offer_id'] = $delivery['offer_id'];
		$order_info_dt['market_sale_id'] = $delivery['market_sale_id'];
		$order_info_dt['delivery_type'] = $delivery['delivery_type'];
		$order_info_dt['Remark'] = $delivery['remark'];


		$delivery_info_dt['delivery_addr'] = $delivery['delivery_addr'];
		$delivery_info_dt['delivery_period'] = $delivery['delivery_period'];
		$delivery_info_dt['delivery_name'] = $delivery['delivery_name'];
		$delivery_info_dt['delivery_phone'] = $delivery['delivery_phone'];


		//        $biz_info_dt = $cust_info;
		unset($biz_info_dt['id']);
		$biz_info_dt['ic_no'] = $ic_no;

		$contact_name =  $delivery['delivery_name'];
		//联系人姓名    没有填客户姓名
		$biz_info_dt['contact_name'] = $contact_name;
		$biz_info_dt['contact_phone'] = $contact_phone;
		$biz_info_dt['acc_nbr'] = $acc_nbr;
		$biz_info_dt['cust_name'] = $cust_name;

		//qw_hlydelivery 表记录入库
		$hlydelivery_t = $delivery_info_dt;
		$hlydelivery_t = array_merge($hlydelivery_t, $biz_info_dt);
		$hlydelivery_t['uid'] = $lockid;
		$hlydelivery_t['remark'] = $delivery['remark'];
		$hlydelivery_t['status'] = '1';
		$hlydelivery_t['uid'] = session('user.uid');


		$t = time();

		$vo1 = M('hlydelivery')->where(['acc_nbr'=> $acc_nbr,'status'=>'1'])->find();
		if (!$vo1) {
			$hlydelivery_t['created_at'] = $t;
			$hlydelivery_t['updated_at'] = $t;

			$ret1 = M('hlydelivery')->data($hlydelivery_t)->add();
		}  else {
			$ret1 = $vo1['id'];
		}

		$order_t = $order_info_dt;
		$order_t['svrtype'] = $order_info_dt['type'];
		$order_t['uid'] = $lockid;
		$order_t['deliveryid'] = $ret1;   //物流ID
		$order_t['acc_nbr'] = $acc_nbr;


		$vo1 = M('hlyorder')->where(['acc_nbr'=> $acc_nbr,'status'=>'1'])->find();

		if (!$vo1) {
			$order_t['created_at'] = $t;
			$order_t['updated_at'] = $t;
			$ret1 = M('hlyorder')->data($order_t)->add();
		}

		$retList = D('Hly','Service')->order($order_info_dt,$delivery_info_dt, $biz_info_dt);
		$retList['updated_at'] = strtotime($retList['datetime']);
		$retList['uid'] = session('user.uid');

		$ret2 = M('hlyorder')->where(['acc_nbr'=> $acc_nbr,'uid'=>$lockid,'status'=>'1'])->data($retList)->save();

		$result = $this->ajaxReturn($retList);


	}

    /**
    * 业务介绍
    * 
    */
	public function introduce(){
		$list = M('hlyoffer')->where(['status'=>1])->order('id')->select();
		$this->list = $list;
		$this->display();

	}


	/**
	* 订单取消 入口
	*
	*/
	public function cancel(){
		$this->list = $list;
		$this->display();

	}


	/**
	* 取消订单业务逻辑，调用和力云接口
	*
	*/
	public function cancelOrder(){
		$d = I('info');
		//调用和力云锁号接口
		$retList = D('Hly','Service')->orderCancle($d);

		//更新本地数据库中的订单信息
		$orderId = $d['orderId'];
		$data['ReturnCode'] = $Content['retCode'];
		$data['ReturnMessage'] = $Content['retMsg'];
		$data['Datetime'] = $xml['Datetime'];
		$this->list = $list;
		$this->display();

	}
    
    
    /**
    * 取消锁定业务逻辑，调用和力云接口
    *
    */
    public function unLockNum(){
        $d = I('info');
        //调用和力云取消锁号接口
        $retList = D('Hly','Service')->unLockNum($d);

        //更新本地数据库中的订单信息
        $orderId = $d['orderId'];
        $data['ReturnCode'] = $Content['retCode'];
        $data['ReturnMessage'] = $Content['retMsg'];
        $data['Datetime'] = $xml['Datetime'];
        
        $this->list = $list;
        $this->display();  
    }



}