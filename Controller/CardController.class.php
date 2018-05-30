<?php
namespace Qwadmin\Controller;
use Qwadmin\Controller\ComController;
class CardController extends AreaController {


    protected $GOODS_MODEL = 'goods_km'; 
    protected $CARD_MODEL = 'cards_km'; 



    public function index() {
        $u = $this->USER;
        $p = isset($_GET['p'])?intval($_GET['p']):'1';
        $field = isset($_GET['field'])?$_GET['field']:'';
        $keyword = isset($_GET['keyword'])?htmlentities($_GET['keyword']):'';
        $order = isset($_GET['order'])?$_GET['order']:'DESC';

        $filter['status'] = ['eq', '0'];//0:已经使用状态

        $UID = $u['uid'];

        if ($u['uid'] > 10) {
            $this->error("您无权查看");    
        }

        $model = M($this->CARD_MODEL);
        $pagesize = 10;#每页数量
        $offset = $pagesize*($p-1);//计算记录偏移量


        $list  = $model->where($filter)->limit($offset.','.$pagesize)->order('id desc')->select();
        //        echo  M()->getLastSql();exit;
        $count = $model->where($where)->count();   


        $page    =    new \Think\Page($count,$pagesize); 
        $page = $page->show();
        $this->assign('list',$list);    
        $this->assign('page',$page);

        $this -> display('index');
    }

    public function tongji() {
        $u = $this->USER;
        $p = isset($_GET['p'])?intval($_GET['p']):'1';


        $filter['status'] = ['eq', '0'];//0:已经使用状态

        $UID = $u['uid'];

        if ($u['uid'] > 10) {
            $this->error("您无权查看");    
        }

        $model = M($this->CARD_MODEL);
        $pagesize = 10;#每页数量
        $offset = $pagesize*($p-1);//计算记录偏移量

        //$sql = "select areaid,fluxnum,status,count(id) as cnt from qw_cards_km where status=1 group by areaid, fluxnum,status";
        $sql = "select a.areaid,a.fluxnum,a.status,count(a.id) as cnt,b.status as areastatus from qw_cards_km a,".
        " qw_goods_channel_km_price b where a.areaid=b.areaid and b.fluxnum = a.fluxnum and a.status=1 group by a.areaid, a.fluxnum,a.status";
        $list  = $model->query($sql);

        foreach ($list as $k => $v) {

        }



        $page    =    new \Think\Page($count,$pagesize); 
        $page = $page->show();
        $this->assign('list',$list);    
        $this->assign('page',$page);

        $this -> display();

    }

    /**
    *      当前剩余
    *      v=c表示重新统计
    * 
    */
    public function left() {
        $v = I('get.v');
        $u = $this->USER;
        $p = isset($_GET['p'])?intval($_GET['p']):'1';


        $filter['status'] = ['eq', '1'];//0:已经使用状态

        $UID = $u['uid'];

        if ($u['uid'] > 10) {
            $this->error("您无权查看");    
        }

        $model = M($this->CARD_MODEL);
        $pagesize = 10;#每页数量
        $offset = $pagesize*($p-1);//计算记录偏移量

        if ($v == 'c')  {
            $sql = "select a.areaid,a.fluxnum,a.status,count(a.id) as cnt, now() as created_at,a.discount,b.price from qw_cards_km a,qw_goods_yt b ".
            "  where a.status = 1 and a.fluxnum=b.fluxnum group by a.areaid, a.fluxnum,a.discount";
            $list  = $model->query($sql);

            $ins_flag = M('cards_left')->addAll($list);
        } else {
            //$sql = "select a.areaid,a.fluxnum,a.status, cnt,  created_at from qw_order_left a ".
            //            "  where a.status = 1 order by areaid,fluxnum";
            //            $list  = M('order_left')->query($sql)  ;

            unset($filter);
            $filter['status'] = ['eq',1];

            $count = M('cards_left')->where($filter)->count(); 

            $list  = M('cards_left')->where($filter)->limit($offset.','.$pagesize)->order('areaid,fluxnum')->select();
            //        echo  M()->getLastSql();exit;

        }

        $page    =    new \Think\Page($count,$pagesize); 
        $page = $page->show();
        $this->assign('list',$list);    
        $this->assign('page',$page);


        $this -> display('left');

    }

    public function indexu() {
        $u = $this->USER;
        $p = isset($_GET['p'])?intval($_GET['p']):'1';
        $field = isset($_GET['field'])?$_GET['field']:'';
        $keyword = isset($_GET['keyword'])?htmlentities($_GET['keyword']):'';
        $order = isset($_GET['order'])?$_GET['order']:'DESC';

        $filter['status'] = ['eq', '1'];//1:未使用状态

        $UID = $u['uid'];

        if ($u['uid'] > 10) {
            $this->error("您无权查看");    
        }

        $model = M($this->CARD_MODEL);
        $pagesize = 10;#每页数量
        $offset = $pagesize*($p-1);//计算记录偏移量


        $list  = $model->where($filter)->limit($offset.','.$pagesize)->order('start_date')->select();    
        $count = $model->where($where)->count();
        //        echo  M()->getLastSql();exit;
        $page    =    new \Think\Page($count,$pagesize); 
        $page = $page->show();
        $this->assign('list',$list);    
        $this->assign('page',$page);

        $this -> display('index');
    }
    public function add(){
        $fluxlist = M($this->GOODS_MODEL)->where(['status'=>1])->select();
        $this->fluxgroup = $fluxlist;


        $arealist = M('mobile_channel')->select();
        $this->arealist = $arealist;

        $this -> display();

    } 

    /** 
    * csv导入数据 
    * @param  String $ftype 文件类型 
    * @author cx qq-825844216 
    */  
    public function imp() { 
        $data = I('data'); 
        $action = I('action'); 
        $areaid= $data['areaid'] ;
        if ($action == '1') {
            $this->expd($areaid);
        }
        if ($data['fluxnum'] == '') {
            $this->error("流量大小参数为空");
        }
        if ($data['discount'] == '') {
            $this->error("折扣参数为空");
        }
        $discount = intval($data['discount'] ) ;
        if ($discount < 1 || $discount > 100)  {
            $this->error("折扣参数应该为1~100之间的数字");
        }
        $t = time();
        if (!empty($_FILES)) {
            $upload = new \Think\Upload();// 实例化上传类
            $filepath='./Public/Csv/'; 
            $upload->exts = array('csv','xlxs','xls');
            // 设置附件上传类型
            $upload->rootPath  =  $filepath; // 设置附件上传根目录
            $upload->saveName  =     'time';
            $upload->autoSub   =     false;
            if (!$info=$upload->upload()) {
                $this->error($upload->getError());
            }
            foreach ($info as $key => $value) {
                unset($info);
                $info[0]=$value;
                $info[0]['savepath']=$filepath;
            }
            $filename=$info[0]['savepath'].$info[0]['savename'];
            $fileName = iconv("utf-8", "gb2312", $fileName);

            $handle = fopen ( $filename, 'r' );  
            $result = $this->input_csv ( $handle ); // 解析csv  
            $len_result = count ( $result );  
            if ($len_result == 0) {  
                $this->error('没有任何数据！', U('index'));  
            }  
            $data_values = '';  
            // array_values($result);
            $data_card = [];
            $j = 0;

            for($i = 1; $i < $len_result + 1; $i ++) { // 循环获取各字段值  
                $rr = array_values ( $result [$i] ); 
                //                $data_card[$j]['areaname'] = (String) iconv("UTF-8", "GB2312", $rr [0]); ; // 中文转码  
                $data_card[$j]['areaname'] =    mb_convert_encoding($rr[0], "UTF-8", "GBK"); // 中文转码  
                $data_card[$j]['seqno'] =  mb_convert_encoding($rr[1], "UTF-8", "GBK") ;  
                $data_card[$j]['identify_code'] =  mb_convert_encoding(NumToStr($rr[2]), "UTF-8", "GBK");
                $data_card[$j]['card_type'] = mb_convert_encoding($rr[3], "UTF-8", "GBK")  ;
                $data_card[$j]['taocha_level'] = mb_convert_encoding($rr[4], "UTF-8", "GBK")  ;
                //                $data_card[$j]['taocha_name'] = (String) iconv("UTF-8", "GB2312", $rr[5]);
                $data_card[$j]['taocha_name'] =  mb_convert_encoding($rr[5], "UTF-8", "GBK");
                $data_card[$j]['start_date'] =   mb_convert_encoding($rr[7], "UTF-8", "GBK")  ;
                $data_card[$j]['end_date'] =  mb_convert_encoding($rr[6], "UTF-8", "GBK")  ;
                $data_card[$j]['operater'] =  mb_convert_encoding($rr[8], "UTF-8", "GBK") ;
                $data_card[$j]['fluxnum'] = $data['fluxnum'];
                $data_card[$j]['areaid'] = $data['areaid'];
                $data_card[$j]['created_at'] = $t;
                $data_card[$j]['updated_at'] = $t;

                $data_card = replace_for_csv($data_card); 

                $filter = $filter.$data_card[$j]['identify_code'].',';                   

                $filter_w[$j] = $data_card[$j]['identify_code'];  
                $j++; 
            }

            $filters = substr($filter,0,-1);

            fclose ( $handle ); // 关闭指针 
            $f_w['identify_code']  = ['in', $filter_w];
            $card_f = M($this->CARD_MODEL)->field('identify_code')->where($f_w)->select();


            if ($card_f) {
                foreach ($card_f as $key => $value) {
                    $cards_info = $cards_info.$value.','; 
                }
                $this->error('有'.count($card_f).'条记录重复,\n'.$cards_info, 'index', 15);
                //echo '有'.count($card_f).'条记录重复';
            }
            //foreach ($card_f as $key => $value) {
            //                    foreach ($data_card as $k => $v) {
            //                      if ($value['identify_code'] == $v['identify_code']){
            //                          
            //                      }
            //                    }
            //                }

            //            $ret = M($this->CARD_MODEL)->addAll($data_card);
            foreach ($data_card as $k => $v) {
                $ret = M($this->CARD_MODEL)->data($v)->add();
                if (!$ret) {
                    $this->success('导入失败'.$v['identify_code']);
                } 
            }
            if ($ret) {
                $this->success('成功导入'.count($data_card).'条记录');
            } 
        }   
    }  
    // csv导入  
    public function input_csv($csv_file) {  
        $result_arr = array ();  
        $i = 0;  
        while ( $data_line = fgetcsv ( $csv_file, 10000 ) ) {  
            if ($i == 0) {  
                $GLOBALS ['csv_key_name_arr'] = $data_line;  
                $i ++;  
                continue;  
            }  

            foreach ( $GLOBALS ['csv_key_name_arr'] as $csv_key_num => $csv_key_name ) {  
                $result_arr [$i] [$csv_key_name] = $data_line [$csv_key_num];  
            }  
            $i ++;  
        }  
        return $result_arr;  
    }  


    protected function expd($areaid){//导出Excel
        //        班级    学号    学生姓名    家长姓名    性别    联系电话    家庭住址


        //            $xlsName  = "User";
        $expCellName  = array(
            array('areaname','卡归属地市'),
            array('seqno','序列号'),
            array('identify_code','验证码'),
            array('taocha_name','套餐名称'),    
            array('fluxnum','流量包大小'),
        );


        //$xlsData  = $xlsModel->Field('id,classid,ucode,uname,parname,sex,tel,addr')->select();
        $expTableData  = M('cards_km')->field('areaname,seqno,identify_code,seqno,taocha_name,fluxnum')->where(['areaid'=>$areaid, 'status'=>1])->select();
        $filename = $areaid;
        $this->getExcel($expCellName,$expTableData,$filename);

    }

    private  function getExcel($expCellName,$data, $fileName){
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