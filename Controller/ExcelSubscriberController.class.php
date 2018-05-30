<?php

    namespace Qwadmin\Controller;
    use Qwadmin\Controller\ComController;

    class ExcelSubscriberController extends ComController {

        public function add(){
            $where['province']="1";//省内
            $where['goodtype']="3";//电信
            $goodgroup = M('goods_group')->where($where)->select();

            $this->assign('goodgroup',$goodgroup);
            $this->cc = "省内";
            $this-> province = 1;
            $this -> display('add');

        }


        /**
        *
        * 导出Excel
        */
        function export(){//导出Excel

            $xlsName  = "User";
            $xlsCell  = array(
                //array('id','账号序列'),
                array('classid','班级ID'),
                array('ucode','学号'),
                array('uname','学生姓名'),
                array('parname','学生家长'),
                array('sex','性别'),
                array('tel','电话'),
                array('addr','家庭地址'),                  
                //array('email','邮箱'),
                //                array('remark','备注')    
            );
            $xlsModel = M('user');

            $xlsData  = $xlsModel->where('usertype=2 and classid='.$classid)->Field('id,classid,ucode,uname,parname,sex,tel,addr')->order(" ucode ")->select();
            //echo $xlsModel->getLastSql();exit;
            foreach ($xlsData as $k => $v)
            {
                $xlsData[$k]['sex']=$v['sex']==1?'男':'女';
            }

            $filename=$classid;             
            $this->exportExcel($xlsName,$xlsCell,$xlsData,$filename);

        }
        function imp2(){
            if (!empty($_FILES)) {
                $upload = new \Think\Upload();// 实例化上传类
                $filepath='./Public/Excle/'; 
                $upload->exts = array('xlsx','xls');// 设置附件上传类型
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
                vendor("PHPExcel.PHPExcel");
                $file_name=$info[0]['savepath'].$info[0]['savename'];
                ;
                $objPHPExcel = \PHPExcel_IOFactory::load($file_name,$encode='utf-8');

                $sheet = $objPHPExcel->getSheet(0);
                $highestRow = $sheet->getHighestRow(); // 取得总行数
                $highestColumn = $sheet->getHighestColumn(); // 取得总列数
            }
        }

        function imp(){ 
            
            //文件导入，主要读取手机号码 =============================== 
            if (!empty($_FILES)) {
                if($ajax=='yes'){
                    $uid = I('get.uid',0,'intval');
                    $gid = I('get.gid',0,'intval');
                    die('1');
                } 
                $uid = isset($_POST['uid'])?intval($_POST['uid']):false;
                $goodsid = I('post.good_id');
                //$goods_name = I('post.goods_name');
                $goodList = M('goods_group')->where(array('goodid'=>$goodsid))->find();
                $goodsname = $goodList['goodname'];
                if(!$goodsid){
                    $this->error('请选择流量包！');
                }
                $data['goodsid'] = $goodsid;
                $data['goodsname'] = $goodsname;
                $data['province'] = I('post.province');
                $data['description'] = I('post.description');
                $data['createdtype'] = '2';
                
                if ($data['province']=="1") {
                    $url = 'Ship/index';
                } else {
                    $url = 'Shipc/index';
                }
                
                //add by xugp 2016-04-23  begin
                $u = $this->USER;
                //这里暂时固定，后续可以考虑从用户个人信息中获取
                $staffValue = $u['phone'];
                $intersource = $u['intersource']; //渠道号  

                if($u['uid'] == 1 ){
                    $data['pid'] = 0;
                }else{
                    $data['pid'] = $u['uid'];
                } 
                $data['uid'] = $u['uid'];
                $data['result'] = C('DEFAULT_DD_RESULT');
                $data['create_time'] = time();
                $data['update_time'] = time();
                //$data['user'] = $user; 

                $upload = new \Think\Upload();// 实例化上传类
                $filepath='./Public/Excle/'; 
                $upload->exts = array('xlsx','xls');// 设置附件上传类型
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
                vendor("PHPExcel.PHPExcel");
                $file_name=$info[0]['savepath'].$info[0]['savename'];

                $objPHPExcel = \PHPExcel_IOFactory::load($file_name,$encode='utf-8');
                $sheet = $objPHPExcel->getSheet(0);
                $highestRow = $sheet->getHighestRow(); // 取得总行数
                $highestColumn = $sheet->getHighestColumn(); // 取得总列数
                $cnt = 0;
                $fail = 0;
                for($i=1;$i<=$highestRow;$i++)
                {   
                    $phone = (string)$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
                    if (empty($phone) || $phone==0){
                        continue;
                    } else {
                        
                        //$d = preg_match('/^\\d+$', $phone);
                    }

                    $users[$i] = $data;
                    $users[$i]['phone'] = $phone;
                    //$rnd = rand(100000,999999);
                    $rnd = 100000 + intval($i);
                    $users[$i]['reqid'] = $intersource. date('YmdHis') .$rnd; 

                    $uid = M('subscriber')->data($users[$i])->add(); 
                    if ($uid !== false)  {
                        $cnt ++;
                        addlog('导入会员成功，用户手机号码：'.$phone.";reqid:".$data['reqid'] .";商品ID." .$goodsid . "商量名称:".$goodsname);    
                    } else {
                        $fail ++;
                        addlog('导入会员失败，用户手机号码：'.$phone.";reqid:".$data['reqid'] .";商品ID." .$goodsid . "商量名称:".$goodsname);    
                    }


                } 

                if($cnt < 1){
                    $this->error('没有获取到手机号码,请重新导入');
                } else { 
                    $this->success('成功导入'.$cnt .'条,失败'.$fail."条.", U($url));

                } 
            }

        }




    }
?>