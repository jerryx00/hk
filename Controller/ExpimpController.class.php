<?php
namespace Qwadmin\Controller;
use Qwadmin\Controller\ComController;
    
    class ExpimpController extends ComController {


        public function exportExcel($expTitle,$expCellName,$expTableData,$filename=""){
            $xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//文件名称
            if ($filename==""){
                $filename = date('_YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
            } else {
                $filename = $filename.date('_YmdHi');//or $xlsTitle 文件名称可根据自己情况设定    
            }

            $cellNum = count($expCellName);
            $dataNum = count($expTableData);
            vendor("PHPExcel.PHPExcel");

            $objPHPExcel = new PHPExcel();
            $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');

            //$objPHPExcel->getActiveSheet(0)->mergeCells('A1:'.$cellName[$cellNum-1].'1');//合并单元格

            for($i=0;$i<$cellNum;$i++){
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'1', $expCellName[$i][1]); 
            } 
            // Miscellaneous glyphs, UTF-8   
            for($i=0;$i<$dataNum;$i++){
                for($j=0;$j<$cellNum;$j++){
                    $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+2), $expTableData[$i][$expCellName[$j][0]]);
                }             
            }  

            header('pragma:public');
            header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
            header("Content-Disposition:attachment;filename=$filename.xls");//attachment新窗口打印inline本窗口打印
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');  
            $objWriter->save('php://output'); 
            exit;   
        }
        /**
        *
        * 导出Excel
        */
        function expport(){//导出Excel
            //        班级    学号    学生姓名    家长姓名    性别    联系电话    家庭住址
            $classid=$_REQUEST['classid'];

            //            $xlsName  = "User";
            $expCellName  = array(
                array('id','编号'),
                array('ucode','学号'),
                array('uname','学生姓名'),
                array('evalute','评语'),    
            );

            $xlsModel = new UserModel();

            //$xlsData  = $xlsModel->Field('id,classid,ucode,uname,parname,sex,tel,addr')->select();
            $expTableData  = $xlsModel->getUsers($classid);
            // foreach ($xlsData as $k => $v)
            //            {
            //                $xlsData[$k]['sex']=$v['sex']==1?'男':'女';
            //            }
            $this->exportExcel($xlsName,$expCellName,$expTableData,$filename);

        }

        /**
        * 评语导入
        * 
        */
        function import(){
            if (empty($_FILES)) {
                $this->error("请选择上传的文件");
            }  else {
                import("@.ORG.UploadFile");
                $dir = getSaveFileDir();
                $config=array(
                    'allowExts'=>array('xlsx','xls'),
                    //'savePath'=>'../../upload/',
                    'savePath'=>getSaveFileDir(),
                    'saveRule'=>'time',
                );
                $upload = new UploadFile($config);               
                if (!$upload->upload()) {
                    $this->error($upload->getErrorMsg());
                } else {
                    $info = $upload->getUploadFileInfo();

                }

                vendor("PHPExcel.PHPExcel");
                $file_name=$info[0]['savepath'].$info[0]['savename'];
                $objReader = PHPExcel_IOFactory::createReader('Excel5');
                $objPHPExcel = $objReader->load($file_name,$encode='utf-8');
                $sheet = $objPHPExcel->getSheet(0);
                $highestRow = $sheet->getHighestRow(); // 取得总行数
                $highestColumn = $sheet->getHighestColumn(); // 取得总列数




                if (empty($_POST['send_time']))
                {
                    $etime = time();
                }
                else
                {
                    $etime = strtotime($_POST['send_time']);
                }
                //if (false === $model->create()) {
                //                $this->error($model->getError());
                //            }

                //保存当前数据对象
                //            $msg_id = $model->add();
                //            $data['notify_id']=$msg_id;
                $tgt_users = array();
                $notify = D('notify');
                $m = D('notify_rcver');

                $data['sender'] = $_SESSION [C('USER_AUTH_KEY')];
                $data['create_time'] = $etime;
                $data['schid']=$_SESSION['schid'];
                $data['classid']=$_POST['classid'];
                $data['status']=1;

                //开始事务 
                // $notify->startTrans();                
                for($i=2;$i<=$highestRow;$i++)
                {     
                    //接收人id
                    $data['rcver'] = $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
                    //$data['ucode'] = $objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();
                    //                    $data['nickname'] = $data['uname'] = $objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue();                      
                    $data['content'] = $objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();  
                    // echo  $data['evaluation'];exit; 
                    $data['type'] = 2;//评语
                    $msg_id=$notify->add($data);
                    //                    echo $notify->getlastSql(); 
                    $tgt_users[$i]['notify_id']  = $msg_id;
                    $tgt_users[$i]['rcver'] = $data['rcver'];
                    $tgt_users[$i]['read_flag'] = 0;
                    $tgt_users[$i]['from_group'] = 0;
                    $tgt_users[$i]['status'] = 1;                    
                    $ret = $m->add($tgt_users[$i]);
                }
                //echo $notify->getlastSql(); 


                // $ret = $m->addAll($tgt_users);
                //dump($tgt_users); exit;
                // if ($ret == false) {
                //                    //失败，rollback事务 
                //                    $notify->rollback();
                //                    $this->error('导入失败！');
                //                    throw new ThinkException($notify->getError());
                //                } else { 
                //                    //成功，提交事务
                //                     $notify->commit();
                $this->success('导入成功！');
                //                }
                //echo M('evaluation')->getLastSql();

            }


        }

       

    }
?>