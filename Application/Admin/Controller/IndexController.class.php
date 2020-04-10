<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends CommController {
    public function index(){
//        dump($_SESSION);
//        exit;
        $this->display('Login/index');
    }
    public function add(){
        $this->display();
    }
}