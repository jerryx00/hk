<?php

namespace Qwadmin\Logic;

/**
 * DataLogic
 */
class OrderYtLogic extends CommonLogic { 

    /**
     * 返回结果值
     * @param  int $status
     * @param  fixed $data
     * @return array
     */
    private function resultReturn($status, $data) {
        return array('status' => $status,
                     'data' => $data);
    }
}
