<?php
namespace Qwadmin\Controller;
use Qwadmin\Controller\ComController;
class ToolsController extends ComController {

    protected $MODEL_NAME='mobile_area';

    public function index(){

        $this -> display();
    }

    
}