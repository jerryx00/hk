<?php

namespace Qwadmin\Model;

/**
 * OrderYt
 * 模型
 */
class OrderYtModel extends CommonModel {

    // realtions
    protected $_link = array(
        
    );

    protected $_validate = array(
       
    );

    protected $_auto = array(
        // password
        array('password', 'encryptPassword', 3, 'callback'),
        // remark
        array('remark', 'htmlspecialchars', 3, 'function'),
        // 创建时间
        array('created_at', 'time', 1, 'function'),
        // 更新时间
        array('updated_at', 'time', 3, 'function'),
        // 最后登录时间
        array('last_login_at', 'time', 1, 'function')
    );

    /**
     * 加密密码
     * @param  string $password 需要被加密的密码
     * @return string
     */
    protected function encryptPassword($password) {
        if ('' == $password) {
            return null;
        }

        return D('Admin', 'Service')->encrypt($password);
    }
}
