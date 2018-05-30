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
* CZ接口
*/
class BaseorderController extends ComController {

    protected $ORDER_POST_URL = '/Ytauth/order';
    protected $ORDER_NOTIFY_URL = '/Ytauth/notify';

    protected $ORDER_MODEL = 'order_yt'; 
    protected $SP_TYPE = '0';

    protected $arr = [
        '10'=>3,
        '30'=>5,
        '70'=>10,
        '100'=>10,
        '150'=>20,
        '300'=>20,
        '500'=>30,

        '700'=>40,
        '1024'=>50,
        '2048'=>70,
        '3072'=>100,
        '4096'=>130,
        '6144'=>180,
        '11264'=>280,
    ] ;

    public function index($page_v='index') {
        $u = $this->USER;
        $p = isset($_GET['p'])?intval($_GET['p']):'1';
        $field = isset($_GET['field'])?$_GET['field']:'';
        $keyword = isset($_GET['keyword'])?htmlentities($_GET['keyword']):'';
        $order = isset($_GET['order'])?$_GET['order']:'DESC';
        $where = '';

        $uid = $u['uid'];

        if ($uid < '10') {
            $w = " status=1 " ;    
        }   else {
            $w = " status=1 and uid=".$uid ;
        }
        if ($this->ORDER_AREAID !='') {
            $w = $w. " and areaid=".$this->ORDER_AREAID ;
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
                $where = $prefix.$this->ORDER_MODEL.".fluxnum = ".$keyword;
            }
            if($field=='order_ret'){
                $where = $prefix.$this->ORDER_MODEL.".order_ret = ".$keyword;
            } 
            if($field=='notify_ret'){
                $where = $prefix.$this->ORDER_MODEL.".notify_ret = ".$keyword;
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

        $this->$n_type = '';
        $this->assign('list',$list);    
        $this->assign('page',$page);          
        $this->m=$this->ORDER_MODEL;
        $this -> display($page_v);
    }


    public function add() { 
        $fluxlist = M($this->ORDER_MODEL)->where(['status'=>1])->select();
        $this->fluxgroup = $fluxlist;
        $this->display();

    }
    public function insert() { 
        $u = $this->USER;

        //            $d = $_POST['data'];
        $ajax = I('post.ajax');
        if ($ajax == '1') {
            $d = $_POST;    
        }  else {
            $d = $_POST['data'];
        }


        $d['scope'] =  intval($d['scope']);
        $d['activeflag'] =  intval($d['activeflag']);
        $d['expiration'] =  intval($d['expiration']);
        $d['fluxnum'] =  intval($d['fluxnum']);
        $d['operator'] =  intval($d['operator']);


        foreach ($d as $k => $v) {
            if (!isset($v)) {
                if ($ajax == '1') {
                    $ret['header']['errcode'] = '1000999';
                    $ret['header']['errmsg'] = '输入错误,请重新提交';
                    $this->ajaxReturn($ret);

                }  else {
                    return $this->errorReturn('输入错误,请重新提交！', $reloadUrl); 
                }  
            }
        }

        // for($i=0; $i<=count($d); $i++){
        //                if (empty($d[$i])) {
        //                    return $this->errorReturn('输入错误,请重新提交！', $reloadUrl);
        //                }
        //            }
        $h['timestamp']= get_date(); 


        $goodList = M($this->ORDER_MODEL)->where(['fluxnum' => $d['fluxnum'], 'status'=>'1'])->find();

        if ($goodList) {
            $d['fluxid'] = $goodList['fluxid'];
        } else {
            return $this->errorReturn('该商品不存在,请重新提交！', $reloadUrl);
        }
        $d['orderno']=  $h['timestamp'] .$u['uid'].'0'.rand(1, 100000); //客户系统的订单号 
        $d['iftype'] = '0';
        $d['created_at'] = time();


        $ret = M($this->ORDER_MODEL.'_log')->data($d)->add();
        if ($ret === false) {
            return $this->errorReturn('输入错误,请重新提交！', $reloadUrl);
        }



        $d['backurl'] = C('hostApiUrl').$this->AUTH_NOTIFY;  //回调 URL  ,可以考虑用配置文件来实现，以便服务器切换  

        $p['appid'] = $u['actioncode'];      //平台分配的客户标识
        $p['access_token'] = $u['accesstoken']; 
        $p['appsecret'] = $u['intersource']; 

        $d['timestamp']= get_date(); 
        $d['user']= $u['user'];;

        $h['type']= 'json'; 

        $h['identity']=  $d['orderno'];  //一次请求的唯一标识码，建议调用方生成唯一标识


        $sign = md5_yingtong($h, $d, $p);

        $h['sign']= $sign; 

        $req['header'] = $h;
        $req['payload']['data'] = $d;

        $hostUrl = C('hostApiUrl');
        //            $hostUrl = 'http://139.224.59.94/llfx/index.php';
        $url = $hostUrl. $this->$ORDER_POST_URL;
        $url = $url . 'appid='.$u['actioncode'] .'&';
        $url = $url . 'access_token=' .$u['accesstoken'];

        $jsonStr = json_encode($req);
        //var_dump($url);
        //            var_dump($jsonStr);exit;
        list($result, $returnContent) = http_post($url, $jsonStr);

        //var_dump($jsonStr);exit;

        if ($ajax == '1') {                 
            $retContent = json_decode($returnContent);
            $this->ajaxReturn($retContent);
        }  else {
            $ret = json_decode($returnContent);
        }



    }

    public function view() { 
        $id = I('get.id');
        $vo = M($this->ORDER_MODEL)->where(['id'=>$id])->find();
        $this->vo = $vo;
        $this->display();

    }

    /**
    * 查看回调信息
    * 
    */
    public function notifyinfo() { 
        $orderid = I('get.orderid');
        $list = M('json_notify')->where(['orderid'=>$orderid])->select();
        $this->list = $list;
        $this->display();

    }

    /**
    * 查看某条回信息的明细
    * 
    */
    public function notitydetail() { 
        $id = I('get.id');
        $list = M('json_notify')->where(['id'=>$id])->find();
        $this->vo = $list;
        $this->display();

    }

    public function manua(){
        $id = I('get.id');
        $sql = 'select * from '.$prefix.$this->ORDER_MODEL.' a, qw_json_notify b where a.orderno = b.orderno and b.id='.$id;
        //$list = M('json_notify')->where(['id'=>$id])->find();
        $list = M('json_notify')->query($sql);
        $vo = $list[0];
        $data = $vo['log'];  
        $url = $vo['backurl'];              
        list($result, $returnContent) = http_post($url, $data);
        $ret =  $returnContent;  
        if ($ret == 'ok') {
            $this->success('回调成功('. $ret.')');
        } else {
            $this->error('回调失败('. $ret.')');
        }     

    }  


    /**
    * 测试加密，通过 2016-11-27
    * 
    */
    public function d() { 
        $str = "15251823848";  
        $key = "0Gn1b2uHgj9lnA5qlef4yE7x"; 
        $crypt = new Mycrypt3des();
        $crypt->Mycrypt3des($key);
        $mstr = $crypt->encrypt($str);  
        echo $mstr.'<br/>';         

        $dstr = $crypt->decrypt($mstr);
        echo $dstr.'<br/>';    
    }  

    public function mobileinfo() {
        $mobile = I('get.mobile');
        $m = substr($mobile,0,7);
        $filter['status']='1';
        $filter['segment'] = $m;

        $sql = 'select b.segment as prefix, a.areaid, a.areaname as city,a.status as status1,b.status as status2 from qw_mobile_area a,qw_mobile_segment b '.
        ' where a.areaid = b.areaid '.
        ' and b.segment='.$m;
        $list = M('mobile_segment')->query($sql);


        $vo['suit'] = '无匹配规则';
        if($list) {
            $result['retMsg'] = '查询成功';
            $vo = $list[0];

            if ($vo['status1']  == '0')  {
                $vo['suit'] = '未启用规则';    
            }  else {
                if ($vo['status2'] == '0') {
                    $vo['suit'] = '未启用规则';   
                } else {
                    $vo['suit'] = '已启用规则';      
                }

            }

        }  else {
            $result['retMsg'] = '查询失败';    
        }
        $vo['prefix'] = $m;
        $vo['phone'] = $mobile;




        $this->result = $result;             
        $this->vo = $vo;
        $this->display(); 

    }
    /**
    * put your comment there...
    * 
    */
    public  function mobileinfo_baidu()
    {
        $mobile = I('mobile');;
        list($result, $returnContent) = get_baidu_MobileInfo($mobile);
        $result = json_decode($returnContent,true);
        $retData =  $result['retData'];
        if ( $result['errNum']  == '0') {                

        } else {

        }
        $this->result = $result;
        $this->vo = $retData;
        $this->display();
    } 

    public function km($model, $notify_ret, $map, $list_n, $batchid) {
        
        if ($notify_ret == '5') {  //批量重发(充值短信)
            $upd_f = M($model)->where($map)->data(['sms_ret'=>2])->save();
                
            if ($upd_f) {
                $this->success("批量重发(充值短信)修改完毕");
            }
            $this->success("批量重发(充值短信)修改,没有发现要修改记录");

        }
        
        //else if ($notify_ret == '1') {  //批量重发(充值短信)
        //                $notify_ret = 1;
        //                $this->success("批量重发(充值短信)修改完毕");
        //            }
    }
    public function batch_notify(){
        $notify_ret = I('ntype');
//        echo $notify_ret;exit;

        $model = I('data_model');
        $uids = isset($_REQUEST['uids'])?$_REQUEST['uids']:false;
        //uid为1的禁止删除
        if(!$uids && $notify_ret <> '20'){
            $this->error('请先选择记录');
        }
        if(is_array($uids)) 
        { 
            foreach($uids as $k=>$v){                   
                $uids[$k] = intval($v);
            }
            if(!$uids && $notify_ret <> '20'){
                $this->error('请先选择记录');
                $uids = implode(',',$uids);
            }
        }
        $batch = count($map);
        if($uids){
            $map['id']  = array('in',$uids);
            $list_n = M($model)->where($map)->order('created_at desc')->select();
        }
        $batchid = date('YmdHis');

        if ($notify_ret == '5') {
            $this->km($model, $notify_ret, $map, $list_n, $batchid);
            return;
        }
        if ($notify_ret == '20') { //导出
            $this->expOrder($batchid, $uids);
        }else if ($notify_ret == '10') { //批量copy
            if ($model == 'order_hb') {
                $content = "";

                //                $bhtime = date('Ymd', $bhtime);
                //YmdHi,8,00
                $handtemplate = explode(',', C('HONGBAO_FLUX_TEMPLATE'));
                if (count($handtemplate) < 3) {
                    $bhtime = time() + (10*60);
                    $bhtime = date('YmdHi', $bhtime).'0000';
                } else {
                    $bhtime = time() + (intval($handtemplate[1])*60);
                    $bhtime = date($handtemplate[0], $bhtime).$handtemplate[2];
                }

                foreach ($list_n as $k => $v) {
                    $content = $content.$v['mobile'].'|'.$v['fluxnum'].'|'.$bhtime."<br>";
                }
                $upd_f = M($model)->where($map)->data(['batchid'=>$batchid])->save();
            } else {
                $content = "copytime : ".$batchid."<br>";
                $content = $content.'-----------------------------'."<br>";

                $con = "copytime :   ".$batchid."\n";
                $con = $con.'------------------'."\n";

                foreach ($list_n as $k => $v) {
                    $price = ArrayHelper::element($v['fluxnum'], $this->arr);
                    $flux = $v['fluxnum'];
                    if ($v['fluxnum'] < 1000) {
                        $flux = $flux."M";
                    } else {
                        $flux = intval($flux)/1024;
                        $flux = $flux."G";
                    }
                    $content = $content.$v['mobile'].'        '.$flux.'        '.$price."<br>";
                    $con = $con.$v['mobile'].'        '.$flux."\n";
                }
                $upd_f = M($model)->where($map)->data(['batchid'=>$batchid])->save();
            }

            //echo $content;
            $this->cnt = count($list_n);
            $this->list = $con;
            $this->display('copy');
        } else { //批量回调
            foreach ($list_n as $k => $v) {
                $id = $v['id'];
                $orderid= $v['orderid'];
                $orderno = $v['orderno'];
                $mobile = $v['mobile'];
                $data_back[$k] = $this->manuafix_logic($id, $orderid,$orderno ,$mobile, $notify_ret, $model);
            }
            $this->success("回调完成".count($list_n)."条订单");
        }


    }

    public function manuafix(){
        $mobile = i('get.mobile');
        $orderid = i('get.orderid');
        $orderno = i('get.orderno');
        $id = I('get.id');
        $notify_ret = I('get.notify');
        $notify_ret1 = I('get.notify_ret');
        $data_model = I('m');

        $upd_notify = true;
        if ($notify_ret == $notify_ret1) { //说明状态不用更新了，直接回调就可以了
            $upd_notify = false;
        } 
      //  if ($notify_ret == '255' || $notify_ret == '') { 
//            $this->success("本订单还在处理中,无须回调"); 
//        }  else {
            $data_ret = $this->manuafix_logic($id, $orderid,$orderno ,$mobile, $notify_ret,$data_model, $upd_notify);
            $this->success("回调完成 ".$data_ret['ret']);
//        }

    }

    /**
    * 手工回调 
    * 
    */
    public function manuafix_logic($id, $orderid,$orderno ,$mobile, $notify_ret, $data_model, $upd_notify){

        //            $orderid_prefix = substr($orderid,0,3);
        //            dump($orderid_prefix);exit;
        //            if ($orderid_prefix == 'SPO'){
        //               $this->ORDER_MODEL = 'order_sp';
        //            }
        //        if ($upd_notify){ // 需要更新状态
        $t = time();
        if ($notify_ret != '' && $id !='' && $id != 0) {   
            if ($notify_ret == '11')  {                     
                $data_save['notify_ret']='1';    
            } else {
                $data_save['notify_ret']=$notify_ret;    
            }   
            $data_save['trans_time']=$t;
            $data_save['updated_at']=$t; 
            $data_save['notify_time']=$t;
            $data_save['callbacktimes']= ['exp', 'callbacktimes+1'];
            $upd_f = M($data_model)->where(['id'=>$id])->data($data_save)->save();
             
            unset($data_save);
        } 
        //        }
        //        echo M()->getLastSql();exit;

        $list = M($data_model)->where(['orderid'=>$orderid,'orderno'=>$orderno ,'mobile'=>$mobile])->find();
        if ($data_model == 'order_km'){
            $f['identify_code'] = $list['identify_code'];
            if ($notify_ret == '11')  {
                $d['status'] = '0'; //0:已使用,1:未使用(使用失败)
                $d['info'] = "卡密无效或已经被使用"; 
            }else   {
                 $d['status'] = $list['notify_ret'];  //1:卡密设置为未使用
            }
            $d['orderid'] = $list['orderid'];

            $fetKmFlag = M('cards_km')->where($f)->data($d)->save();
//            dump( M($model)->getlastSql());exit;
            unset($f);
            unset($d);
        }

        $url = $list['backurl'];
        //==================================
        $timestamp = toDate($list['notify_time'],'YmdHis');
        $uid = $list['uid'];

        $user  = M('member')->where(['uid'=>$uid])->cache(true)->find();
        $username = $user['user'];
        $appsecret = $user['intersource'];

        $sign = MD5($timestamp.$username.appsecret);

        //=========================================
        $header['timestamp'] = $timestamp;
        $header['type'] = 'json';
        $header['identity'] = $list['orderno'];
        $header['sign'] = $sign;

        //==================================
        $data['orderid'] = $list['orderid']  ;
        $data['orderno'] = $list['orderno']  ;
        $data['mobile'] = $list['mobile']  ;
        //            $data['result'] = $list['notify_ret']  ;
        $data['result'] = intval($list['notify_ret'])  ;
        $data['desc'] = $list['desc'];
        $data['transtime'] = $timestamp;

        $notify_data['header'] = $header;
        $notify_data['payload']['data'] = $data;
        $jsonStr = json_encode($notify_data);

        //        if ($notify_ret != $list['notify_ret']) {
        $this->updFund_AfterNofity($list, $list, $list);
        //        }

        $self_url = '/llfx/api.php';                
        $pos = strpos($list['backurl'], $self_url);    
        //\Think\Log::record('suffix:'.$suffix.'; self_url:'.$self_url. ';orderno:'.$d['orderno']);  
        if($pos > 0){              
            $ret = 'ok,not to notify back';           
        } else {
            list($result, $returnContent) = http_post($url, $jsonStr);
            $ret =  $returnContent; 
            $data['ret'] = $ret;            
        }
        return $data;


    }

    public function updFund_AfterNofity($data, $existList, $data_u){
        $data_u1 = $data_u;
        unset($data_u);
        $data_u['notify_ret'] = $data_u1['notify_ret'] ;

        $data = object_array($data);
        $data_w['orderno'] =  $existList['orderno'];
        $data_w['mobile'] =  $existList['mobile'];
        $data_w['orderid'] =  $existList['orderid'];

        if ($data_u['notify_ret'] == 0){
            $feeflag1 = M('order_fee_detail')->where($data_w)->data(['notify_ret'=>'0'])->save();
            // $feeflag2 = M('order_fee_detail')->where($data_w)->setInc('callbacktimes'); // 加1
            $feeflag3 = M('order_fee_detail_fail')->where($data_w)->delete();
        } else { 
            //返还该订单费用, 注意不要重复返还
            $vo_fail = M('order_fee_detail_fail')->where($data_w)->find();
            if(!$vo_fail )  {   //记录不存在
                if ($existList['notify_ret'] !='255'){  //是否明确失败了      
                    $data_back['orderno'] =  $existList['orderno'];
                    $data_back['uid'] =  $existList['uid'];
                    $data_back['user'] =  $existList['user'];
                    $data_back['mobile'] =  $existList['mobile'];
                    $data_back['fluxnum'] =  $existList['fluxnum'];
                    $data_back['orderid'] =  $existList['orderid'];
                    $data_back['price'] =  $existList['price'];
                    $data_back['order_ret'] = $existList['order_ret'] ;

                    $data_back['orderid'] =  $existList['orderid'];

                    $data_back['created_at'] =  time(); 
                    $data_back['iftype'] =  1; //返还标志
                    if ($vo_fail['callbacktimes'] == ''){
                        $vo_fail['callbacktimes'] = 0;
                    }                    
                    //                    if ($vo_fail['callbacktimes'] == '0'){
                    $data_back['unused'] =  $existList['unused'] + $existList['price']; //返还费用

                    $data_back['notify_ret'] = $data_u['notify_ret'] ; 

                    $feeflag = M('order_fee_detail_fail')->data($data_back)->add();
                }
            } else {


                $data_back['callbacktimes'] =  $vo_fail['callbacktimes'] + 1; //回调次数
                $data_back['unused'] =  $existList['unused'] + $existList['price']; //返还费用

                $feeflag = M('order_fee_detail_fail')->where($data_w)->data($data_back)->save();
            }

        }
    }

    public function expOrder($batchid, $uids){
        //        function export(){//导出Excel
        $u = $this->USER;
        $_uid = I("get.uid");
        $_fid = I("get.fid");
        if ($_uid !="" && $_fid=="xgp"){
            $uid = $_uid;
        } else {
            $uid = $u['uid'];    
        }


        if ($uid == ''){
            $this->error("对不起,您无法执行导出操作!");
        }     

        $xlsName  = "订单明细";
        $xlsCell  = array(
            '订单号',
            '订单ID',
            '号码',
            '流量', 
            '价格',
            '导出批次',
            '订单日期',
        );

        $xlsModel = M('order_nj'); 


        //        $filter['created_at'] =  array('gt',$ym);

        if($uids){      
            $filter['id']  = array('in',$uids);
        }else {
            $filter['batchid']  = array('exp',' IS NULL'); 
        }

        $xlsData  = $xlsModel->field("orderno,orderid,mobile,fluxnum,price,batchid,FROM_UNIXTIME(created_at, '%Y-%m-%d %H:%i:%s') ")
        ->where($filter)->order("created_at")->select();
        //        echo M()->getLastSql();exit;
        if (count($xlsData) == 0) {     

            if (count($xlsData) == 0) {
                $this->error("当前待导出的订单记录为空");
            }
        }
        //                        echo $xlsModel->getLastSql();exit;
        foreach ($xlsData as $k => $v)
        {

            $flux = $v['fluxnum'];
            if ($v['fluxnum'] < 1000) {
                $flux = $flux."M";
            } else {
                $flux = intval($flux)/1024;
                $flux = $flux."G";
            }
            //$content = $content.$v['mobile'].'        '.$flux.'        '.$price."<br>";

        }

        //            $filename = $be[0].'-'.$be[1];
        $upd_f = $xlsModel->where($filter)->data(['batchid'=>$batchid])->save();
        $filename1 = $batchid;
        $this->getExcel($xlsCell, $xlsData, $filename1);

    } 


    private  function getExcel($headArr,$data, $fileName){
        //对数据进行检验
        if(empty($data) || !is_array($data)){
            die("data must be a array");
        }
        //检查文件名
        if(empty($fileName)){
            exit;
        }

        $date = date("Y_m_d",time());
        $fileName .= "_{$date}.xls";


        //导入PHPExcel类库，因为PHPExcel没有用命名空间，只能inport导入
        vendor("PHPExcel.PHPExcel");
        vendor("PHPExcel.PHPExcel.Writer.Excel5");
        vendor("PHPExcel.PHPExcel.IOFactory.php");

        //创建PHPExcel对象，注意，不能少了\
        $objPHPExcel = new \PHPExcel();
        $objProps = $objPHPExcel->getProperties();

        //设置表头
        $key = ord("A");
        foreach($headArr as $v){
            $colum = chr($key);
            $objPHPExcel->setActiveSheetIndex(0) ->setCellValue($colum.'1', $v);
            $key += 1;
        }

        $column = 2;
        $objActSheet = $objPHPExcel->getActiveSheet();
        foreach($data as $key => $rows){ //行写入
            $span = ord("A");
            foreach($rows as $keyName=>$value){// 列写入
                $j = chr($span);
                $objActSheet->setCellValue($j.$column, $value);
                $span++;
            }
            $column++;
        }

        $fileName = iconv("utf-8", "gb2312", $fileName);
        //重命名表
        // $objPHPExcel->getActiveSheet()->setTitle('test');
        //设置活动单指数到第一个表,所以Excel打开这是第一个表
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"$fileName\"");
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output'); //文件通过浏览器下载

        $this->success("导出成功", U('index'));
    }
}