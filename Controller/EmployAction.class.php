<?php

namespace Qwadmin\Controller;
use Think\Db;
use Vendor\Database;
use Qwadmin\Controller\ComController;

class EmployAction extends BaseAction{
   
	//套餐查询
	public function taocan(){
		$openid = $_GET['wecha_id'];
        $phone = $this->getphone($openid);
		if($phone == 0){
			header("Location: http://weixin.poxixiaoshi.com/index.php?g=Wap&m=Employ&a=index&token=xrsozr1400680309&wecha_id=".$openid);
		}
		//$url = "http://42.51.1.154/jiekou/queryFlow.php";
		
		$url = "http://139.224.59.94/System/jiekou/wx_function.php?r=ipcumulation&accNbr=".$phone;
		$reslut = file_get_contents($url);
        //$reslut = '{"TSR_RESULT":"0","TSR_CODE":"0","TSR_MSG":"成功！","catalogs":[{"catalogName":"天翼乐享3G上网版49元套餐","items":[{"accuName":"天翼语音时长","cumulationAlready":"100.00","cumulationLeft":"0.00","cumulationTotal":"100.00","unitName":"分钟","endTime":"2014-04-01 00:00:00","offerName":"天翼乐享3G上网版49元套餐","startTime":"2014-03-01 00:00:00","state":"0"},{"accuName":"天翼点对点短信条数","cumulationAlready":"5.00","cumulationLeft":"25.00","cumulationTotal":"30.00","unitName":"条","endTime":"2014-04-01 00:00:00","offerName":"天翼乐享3G上网版49元套餐","startTime":"2014-03-01 00:00:00","state":"0"},{"accuName":"天翼彩信条数","cumulationAlready":"0.00","cumulationLeft":"6.00","cumulationTotal":"6.00","unitName":"条","endTime":"2014-04-01 00:00:00","offerName":"天翼乐享3G上网版49元套餐","startTime":"2014-03-01 00:00:00","state":"0"},{"accuName":"WLAN上网时长","cumulationAlready":"0.00","cumulationLeft":"1800.00","cumulationTotal":"1800.00","unitName":"分钟","endTime":"2014-04-01 00:00:00","offerName":"天翼乐享3G上网版49元套餐","startTime":"2014-03-01 00:00:00","state":"0"},{"accuName":"手机上网国内流量","cumulationAlready":"0.00","cumulationLeft":"200.00","cumulationTotal":"200.00","unitName":"MB","endTime":"2014-04-01 00:00:00","offerName":"天翼乐享3G上网版49元套餐","startTime":"2014-03-01 00:00:00","state":"0"}]},{"catalogName":"综合VPN","items":[{"accuName":"主叫时长","cumulationAlready":"8.00","cumulationLeft":"1492.00","cumulationTotal":"1500.00","unitName":"分钟","endTime":"2014-04-01 00:00:00","offerName":"综合VPN","startTime":"2014-03-01 00:00:00","state":"0"}]},{"catalogName":"院线通赠送300M手机上网流量","items":[{"accuName":"手机上网全国流量","cumulationAlready":"10.11","cumulationLeft":"289.89","cumulationTotal":"300.00","unitName":"MB","endTime":"2014-04-01 00:00:00","offerName":"院线通赠送300M手机上网流量","startTime":"2014-03-01 00:00:00","state":"0"}]},{"catalogName":"流量优惠/流量赠送/30M/当月-JS2012","items":[{"accuName":"手机上网全国流量","cumulationAlready":"11.23","cumulationLeft":"18.77","cumulationTotal":"30.00","unitName":"MB","endTime":"2014-04-01 00:00:00","offerName":"流量优惠/流量赠送/30M/当月-JS2012","startTime":"2014-03-03 00:00:00","state":"0"}]},{"catalogName":"每月赠送手机上网流量300M，连送2个月","items":[{"accuName":"手机上网全国流量","cumulationAlready":"0.00","cumulationLeft":"300.00","cumulationTotal":"300.00","unitName":"MB","endTime":"2014-04-01 00:00:00","offerName":"每月赠送手机上网流量300M，连送2个月","startTime":"2014-03-03 00:00:00","state":"0"}]},{"catalogName":"流量优惠/流量券/200M省内流量/当月","items":[{"accuName":"手机上网省内流量","cumulationAlready":"18.57","cumulationLeft":"181.43","cumulationTotal":"200.00","unitName":"MB","endTime":"2014-04-01 00:00:00","offerName":"流量优惠/流量券/200M省内流量/当月","startTime":"2014-03-03 00:00:00","state":"0"}]},{"catalogName":"流量优惠/流量券/100M省内流量/当月","items":[{"accuName":"手机上网省内流量","cumulationAlready":"44.67","cumulationLeft":"55.33","cumulationTotal":"100.00","unitName":"MB","endTime":"2014-04-01 00:00:00","offerName":"流量优惠/流量券/100M省内流量/当月","startTime":"2014-03-03 00:00:00","state":"0"}]},{"catalogName":"流量优惠/流量券/30M省内流量/当月","items":[{"accuName":"手机上网省内流量","cumulationAlready":"30.00","cumulationLeft":"0.00","cumulationTotal":"30.00","unitName":"MB","endTime":"2014-04-01 00:00:00","offerName":"流量优惠/流量券/30M省内流量/当月","startTime":"2014-03-03 00:00:00","state":"0"}]}]}';
        $asd = json_decode($reslut);
		//print_r($asd[0]->catalogs);exit();
		//print_r($asd->catalogs);exit();
		$this->assign('info',$asd[0]->catalogs);
		//$this->assign('info',$asd->catalogs[0]);
		$this->display('taocan');
	}
	
	//消耗查询
	public function xiaohao(){
		$openid = $_GET['wecha_id'];
        $phone = $this->getphone($openid);
		if($phone == 0){
			header("Location: http://weixin.poxixiaoshi.com/index.php?g=Wap&m=Employ&a=index&token=xrsozr1400680309&wecha_id=".$openid);
		}
		//$url = "http://42.51.1.154/jiekou/queryFlow.php";
		$url = "http://139.224.59.94/System/jiekou/wx_function.php?r=ipcumulation&accNbr=".$phone;
		$reslut = file_get_contents($url);
		//$reslut = '{"result":"0","resultMsg":"查询成功","CumUlationResp":[{"accuName":"手机上网国内流量","cumulationAlready":"5037.00","cumulationLeft":"404563.00","cumulationTotal":"409600.00","endTime":"20131201000000","offerName":"天翼乐享3G上网版89元套餐","startTime":"20131101000000","unitName":"KB"}]}';
		$asd = json_decode($reslut);
		//print_r($asd->CumUlationResp[0]);exit();
		//$this->assign('info',$asd->CumUlationResp[0]);
		/*$asd[0]->catalogs[0] = $asd[0]->catalogs[2];
		$asd[0]->catalogs[1] = $asd[0]->catalogs[3];
		$asd[0]->catalogs[2] = $asd[0]->catalogs[4];
		$asd[0]->catalogs[3] = $asd[0]->catalogs[5];
		$asd[0]->catalogs[4] = $asd[0]->catalogs[6];*/
		$asd[0]->catalogs[0] = "";
		$asd[0]->catalogs[1] = "";
		$asd[0]->catalogs[3] = "";
		$asd[0]->catalogs[4] = "";
		$asd[0]->catalogs[6] = "";
		
		//print_r($asd[0]->catalogs);exit();
		
		$this->assign('info',$asd[0]->catalogs);
		$this->display('xiaohao');
	}

	
	//显示推荐
	public function xianshi(){
	
        $openid = $_GET['wecha_id'];
        $phone = $this->getphone($openid);
		if($phone == 0){
			header("Location: http://weixin.poxixiaoshi.com/index.php?g=Wap&m=Employ&a=index&token=xrsozr1400680309&wecha_id=".$openid);
		}
         $token = $_GET['token'];
		 $model = M('liuliang');
		$model1 = $model->where(array('type'=>1))->select();
		$model2 = $model->where(array('type'=>2))->select();
		$model3 = $model->where(array('type'=>3))->select();
		$model4 = $model->where(array('type'=>4))->select();
		$model5 = $model->where(array('type'=>5))->select();
		
		//var_dump($model1);exit();
		$this->assign('info1',$model1);
		$this->assign('info2',$model2);
		$this->assign('info3',$model3);
		$this->assign('info4',$model4);
		$this->assign('info5',$model5);
        
		$this->display('xianshi');
	}
   
	//流量包列表
	public function liuliangbao(){
	
        $openid = $_GET['wecha_id'];
        $phone = $this->getphone($openid);
		if($phone == 0){
			header("Location: http://weixin.poxixiaoshi.com/index.php?g=Wap&m=Employ&a=index&token=xrsozr1400680309&wecha_id=".$openid);
		}
        $token = $_GET['token'];
		$model = M('liuliang');
		$model1 = $model->where(array('type'=>1))->select();
		$model2 = $model->where(array('type'=>2))->select();
		$model3 = $model->where(array('type'=>3))->select();
		$model4 = $model->where(array('type'=>4))->select();
		$model5 = $model->where(array('type'=>5))->select();
		
		//var_dump($model1);exit();
		$this->assign('info1',$model1);
		$this->assign('info2',$model2);
		$this->assign('info3',$model3);
		$this->assign('info4',$model4);
		$this->assign('info5',$model5);
        
		$this->display('liuliangbao');
	}
	
	//订购流量
	public function dinggou(){
	
        $openid = $_GET['wecha_id'];
        $phone = $this->getphone($openid);
		if($phone == 0){
			header("Location: http://weixin.poxixiaoshi.com/index.php?g=Wap&m=Employ&a=index&token=xrsozr1400680309&wecha_id=".$openid);
		}
        $token = $_GET['token'];
        $model = M('liuliang');
		$model1 = $model->where(array('type'=>1))->select();
		$model2 = $model->where(array('type'=>2))->select();
		$model3 = $model->where(array('type'=>3))->select();
		$model4 = $model->where(array('type'=>4))->select();
		$model5 = $model->where(array('type'=>5))->select();
		
		//var_dump($model1);exit();
		$this->assign('info1',$model1);
		$this->assign('info2',$model2);
		$this->assign('info3',$model3);
		$this->assign('info4',$model4);
		$this->assign('info5',$model5);
		$this->display('dinggou');
	}
	//提交订购
	public function subdingg(){
		$openid = $_GET['wecha_id'];
        $phone = $this->getphone($openid);
		if($phone == 0){
			header("Location: http://weixin.poxixiaoshi.com/index.php?g=Wap&m=Employ&a=index&token=xrsozr1400680309&wecha_id=".$openid);
		}
        $phone = $this->getphone($openid);
		$info = $_POST['info'];
		$info = explode("*",$info);
		foreach ($info as $key => $value) {
			$asd = explode("#",$value);
			$accNbr = $phone;
			$offerSpecl = $asd[0];
			$goodName = $asd[1];
			//echo $goodName."#".$offerSpecl."#".$accNbr;exit();
			$url = "http://139.224.59.94/System/jiekou/wx_function.php?r=ipordermore3&accNbr=".$accNbr."&offerSpecl=".$offerSpecl."&goodName=".$goodName;
			$reslut = file_get_contents($url);
			//var_dump(json_decode($reslut));//exit();
			//echo $url;exit();
			echo $reslut;
		}
		
	}
	
   
 
}  
?>