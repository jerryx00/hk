<?php
namespace Qwadmin\Controller;

use Qwadmin\Controller\ComController;

class MsginboxController extends ComController
{

    protected $MODEL_NAME = 'msg_inbox';

    public function index()
    {


        $u = $this->USER;
        $p = isset($_GET['p']) ? intval($_GET['p']) : '1';
        $field = isset($_GET['field']) ? $_GET['field'] : '';
        $keyword = isset($_GET['keyword']) ? htmlentities($_GET['keyword']) : '';
        $order = isset($_GET['order']) ? $_GET['order'] : 'DESC';
        $where = '';
        $areaid = I('areaid');
        $s = I('keyword');
        if ($areaid != '') {
            $filter['areaid'] = ['eq', $areaid];
        }

        if ($s != '') {
            $filter['segment'] = ['like', '%' . $s . '%'];
        }

        $filter['sender'] = ['eq', '10086'];
        $filter['msgtitle'] = ['like', '您好%'];

        $UID = $u['uid'];

        $user = M($this->MODEL_NAME);
        $pagesize = 10;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量

        $count = $user->where($filter)->count();

        $list = $user->where($filter)->limit($offset . ',' . $pagesize)->order('id desc')->select();

//        echo  M()->getLastSql();exit;
        $page = new \Think\Page($count, $pagesize);
        $page = $page->show();
        $this->assign('list', $list);
        $this->assign('page', $page);

        $this->display();
    }


    public function del()
    {

        $uids = isset($_REQUEST['uids']) ? $_REQUEST['uids'] : false;
        //uid为1的禁止删除
        if ($uids == 1 or !$uids) {
            $this->error('参数错误！');
        }
        if (is_array($uids)) {
            foreach ($uids as $k => $v) {
                if ($v == 1) {//uid为1的禁止删除
                    unset($uids[$k]);
                }
                $uids[$k] = intval($v);
            }
            if (!$uids) {
                $this->error('参数错误！');
                $uids = implode(',', $uids);
            }
        }

        $map['id'] = array('in', $uids);
        if (M($this->MODEL_NAME)->where($map)->delete()) {
            addlog('删除号码段ID：' . $uids);
            $this->success('恭喜，删除号码段成功！');
        } else {
            $this->error('参数错误！');
        }
    }

    public function add()
    {
        $this->display('form');
    }

    public function edit()
    {
        $u = $this->USER;
        $id = isset($_GET['id']) ? intval($_GET['id']) : false;
        if ($id) {
            $vo = M($this->MODEL_NAME)->where("id='$id'")->find();

        }

        $this->assign('vo', $vo);
        $this->display('form');
    }

    public function update($ajax = '')
    {
        $id = I('id');
        $data = I('data');
        $data['code'] = '025';

        $t = time();
        //$cnt = M('mobile_segment')->where($filter)->count();
        if ($id == '') {
            //                $data['areaid'] = $areaid;
            $id = M($this->MODEL_NAME)->data($data)->add();
            addlog('新增号码段，areaid：' . $id);
        } else {
            $ret = M($this->MODEL_NAME)->data($data)->where("id='$id'")->save();
            addlog('编辑号码段，areaid：' . $id);
        }
        $this->success('操作成功！', 'index');
    }


    /**
     * 查看全部乱短信
     */
    public function allinbox()
    {
        $sender = I('sender');
        if ($sender != "" || empty($sender)) {
            $sender = '10086';
        }
        $filter['sender'] = ['eq', $sender];

        $u = $this->USER;
        $p = isset($_GET['p']) ? intval($_GET['p']) : '1';
        $field = isset($_GET['field']) ? $_GET['field'] : '';
        $keyword = isset($_GET['keyword']) ? htmlentities($_GET['keyword']) : '';
        $order = isset($_GET['order']) ? $_GET['order'] : 'DESC';
        $where = '';

        $UID = $u['uid'];

        $user = M($this->MODEL_NAME);
        $pagesize = 10;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量

        $count = $user->where($filter)->count();

        $list = $user->where($filter)->limit($offset . ',' . $pagesize)->order('id desc')->select();

//        echo  M()->getLastSql();exit;
        $page = new \Think\Page($count, $pagesize);
        $page = $page->show();
        $this->assign('list', $list);
        $this->assign('page', $page);

        $this->display('index');
    }

    /**
     * 显示全部收、发信息
     */
    public function msgall()
    {
        $mobile = I('mobile');
        $this->inboxlist = $this->inbox();
        $this->sentboxlist = $this->sentbox();
        $this->display();
    }

    /**
     * 查看某个端口短信
     */
    public function port()
    {
        $port = I('port');

        $created = I('created');
        if ($created != "" && !empty($created)) {
            $lastDate = [$created, $created + 60 * 60 * 24 * 2];
            $filter['created_at'] = ['between', $lastDate];
        }
        $sender = '10086';

        $filter['sender'] = ['eq', $sender];
        $filter['commport'] = ['eq', $port];
        $user = M($this->MODEL_NAME);

        $p = isset($_GET['p']) ? intval($_GET['p']) : '1';
        $pagesize = 10;#每页数量
        $offset = $pagesize * ($p - 1);//计算记录偏移量

        $count = $user->where($filter)->count();

        $list = $user->where($filter)->limit($offset . ',' . $pagesize)->order('id desc')->select();

//        echo  M()->getLastSql();exit;
        $page = new \Think\Page($count, $pagesize);
        $page = $page->show();
        $this->assign('list', $list);
        $this->assign('page', $page);

        $this->display('index');
    }

    /**
     * 查看某个号码短信
     */
    private function inbox()
    {
        $mobile = I('mobile');

        $created = I('created');
        if ($created != "" && !empty($created)) {
            $lastDate = [$created, $created + 60 * 60 * 24 * 2];
            $filter['created_at'] = ['between', $lastDate];
        }
        $filter['msgtitle'] = ['like', '%' . $mobile . '%'];
        $list = M('msg_inbox')->where($filter)->select();
        return $list;
    }

    /**
     *
     */
    private function sentbox()
    {
        $mobile = I('mobile');
        $created = I('created');
        if ($created != "" && !empty($created)) {
            $lastDate = [$created, $created + 60 * 60 * 24 * 2];
            $filter['created_at'] = ['between', $lastDate];
        }

//        $filter['msgtitle'] = ['like', '%{$mobile}%'];
        $filter['msgtitle'] = ['like', '%' . $mobile . '%'];
        $list = M('msg_sentbox')->where($filter)->select();
        return $list;
    }


}