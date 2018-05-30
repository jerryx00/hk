<?php
    namespace Qwadmin\Controller;
    use Qwadmin\Controller\ComController;
    class ChannelController extends AreaController {

        public function index(){
            $this->MODEL_NAME= 'mobile_channel' ;
            parent::index();
        }

        public function del(){  
            $this->MODEL_NAME= 'mobile_channel';
            parent::del();
        }
        public function add(){
            $this->MODEL_NAME= 'mobile_channel';
            parent::add();
        }
        public function edit(){
            $this->MODEL_NAME= 'mobile_channel';
            parent::edit();;
        }

        public function update($ajax=''){
            $this->MODEL_NAME= 'mobile_channel';
            parent::update();
        }

}