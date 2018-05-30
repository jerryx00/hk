<?php

    namespace Qwadmin\Controller;
    use Qwadmin\Controller\ComController;

    //Excel/export?uid=31&d=201703
    class ExcelController extends ExcelSubscriberController {

        function export(){//导出Excel

            $xlsName  = "订单明细";
            $xlsCell  = array(
                '订单号',
                '订单ID',
                '订单日期',
                '号码',
                '地区',
                '流量包',
                '价格',
                '状态',
//                '说明',
            );
            $xlsModel = M('order_yt');
            $uid  = I('uid');

            $d = I('d');              


            //参数 格式 d=20180101
          //  if($d == '')  {
//                $d = date("Y-m-d");                
//            } 
//
            $be = getthemonthS($d);
            $ym[0] = strtotime($be[0]);
            $ym[1] = strtotime($be[1]);
            $filter['created_at'] =  array('between',$ym);  
            $filter['uid']=['eq', $uid] ;

            $xlsData  = $xlsModel
            ->Field("orderno,orderid,FROM_UNIXTIME(created_at, '%Y-%m-%d %H:%i') as created_at,mobile,areaid,fluxnum,price,notify_ret,desc")
            ->where($filter)->order("created_at")->select();
            //echo M()->getLastSql();exit;
           // if (count($xlsData) == 0) {     
//                $xlsData  = M('order_history_v')
//                ->Field("orderno,orderid,FROM_UNIXTIME(created_at, '%Y-%m-%d %H:%i') as created_at,mobile,price,notify_ret,desc")
//                ->where($filter)->order("created_at")->select();
//                if (count($xlsData) == 0) {
//                    $this->error("没有查到记录");
//                }
//            }
            //                        echo $xlsModel->getLastSql();exit;
//            $xlsData  = M('order_ym_u')->field("orderno,orderid,FROM_UNIXTIME(created_at, '%Y-%m-%d %H:%i') as created_at,mobile,price,notify_ret")
//            ->select();
            
            
            
            foreach ($xlsData as $k => $v)
            {
                $xlsData[$k]['notify_ret']=$v['notify_ret']==0?'成功':'失败';
            }

            //            $filename = $be[0].'-'.$be[1];
            $filename = $d;
            $this->getExcel($xlsCell, $xlsData, $filename);

        }




        public function out(){
            $data=array(
                array('username'=>'zhangsan','password'=>"123456"),
                array('username'=>'lisi','password'=>"abcdefg"),
                array('username'=>'wangwu','password'=>"111111"),
            );


            $filename="test_excel";
            $headArr=array("用户名","密码");
            $this->getExcel($filename,$headArr,$data);
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
            exit;
        }


    }
?>