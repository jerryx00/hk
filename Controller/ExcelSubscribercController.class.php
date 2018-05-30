<?php

    namespace Qwadmin\Controller;
    use Qwadmin\Controller\ComController;

    class ExcelSubscribercController extends ExcelSubscriberController {

        public function add(){
            $where['province']="2";//全国
            $where['goodtype']="3";//电信
            $goodgroup = M('goods_group')->where($where)->select();

            $this->assign('goodgroup',$goodgroup);
            $this->cc = "全国";
            $this-> province = 2;
            $this -> display('ExcelSubscriber/add');

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
            

    }
?>