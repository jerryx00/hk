<?php 

namespace Qwadmin\Controller;
use Think\Controller;
use Think\Upload;

class FileController extends Controller {
    public function index(){		
        $this -> display();
    }

    public function upload(){
        $upload = new \Think\Upload();// 实例化上传类
        $info   =   $upload->upload();

        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'txt');// 设置附件上传类型
        $upload->rootPath  =     './Uploads/'; // 设置附件上传根目录
        $upload->savePath  =     ''; // 设置附件上传（子）目录
        // 上传文件 
        $info   =   $upload->upload();

        if(!$info) {// 上传错误提示错误信息
            $info = $upload->getError();
            $status = '1';
        }else{// 上传成功
            $info = '上传成功';
            $status = '0';
        }
        $data['info'] = $info;
        $data['status'] = $status;        
        $this->ajaxReturn($data);

    }

    public function txt(){
        //$f = ['0511','0512','0513','0515','0519']; 
        //$f = ['0510','0518','0527','0516','0517']; 
        $f = ['000','0514','0523']; 
        foreach ($f as $k => $v) {
            $this->readTxt($v);
        }
    }
    private function readTxt($f){ 

        $file = fopen($f.'.txt', "r"); 

        $areaid = $f;
        $code=[]; 
        $i=0; 


        //            dump($list);
        //输出文本中所有的行，直到文件结束为止。 
        while(! feof($file)) 
        {  
            $code[$i]= fgets($file);//fgets()函数从文件指针中读取一行 
            $i++;  
        }
        fclose($file);
        $code=repEnter($code); 

        $ret = M('mobile_segment')->where(['areaid'=>$areaid])->delete();

        foreach ($code as $k => $v) {
            $arr = explode(',', $v);
            $i = 0;
            foreach ($arr as $key => $value) {
                if ($value != '') {
                    $d[$key]['areaid'] = $areaid;
                    $d[$key]['segment'] = $value;
                }
                $i++;
            }
            $ret = M('mobile_segment')->addAll($d);
        }
        echo $f .' import success!<br>';

    }

    public function t() {
        $file = 'nantong.txt';
        $fp = fopen($file, "r");
        $num = 10;
        $chunk = 4096;
        $fs = sprintf("%u", filesize($file));
        $max = (intval($fs) == PHP_INT_MAX) ? PHP_INT_MAX : filesize($file);
        for ($len = 0; $len < $max; $len += $chunk) {
            $seekSize = ($max - $len > $chunk) ? $chunk : $max - $len;
            fseek($fp, ($len + $seekSize) * -1, SEEK_END);
            $readData = fread($fp, $seekSize) . $readData;

            if (substr_count($readData, "n") >= $num + 1) {
                preg_match("!(.*?n){".($num)."}$!", $readData, $match);
                $data = $match[0];
                break;
            }
        }
        fclose($fp);
        echo $data;
    }
}