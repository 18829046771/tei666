<?php
namespace Serve\Controller;
use Think\Controller;

class UserController extends CommController {

    // 修改密码
    public function chenge(){
        $this->display();
    }

    // 修改密码处理
    public function chengecl(){
        $pass_old = I('post.pass_old', 0);
        if (empty($pass_old)) {
            $this->error('请输入原始密码');die();
        }
        $pass_new = I('post.pass_new', 0);
        if (empty($pass_new)) {
            $this->error('请输入新密码');die();
        }
        $pass_new2 = I('post.pass_new2', 0);
        if (empty($pass_new2)) {
            $this->error('请输入确认密码');die();
        }
        if ($pass_new != $pass_new2) {
            $this->error('确认密码输入错误请重新输入');die();
        }
        $serve_id = $_SESSION['serve_id'];
        $db = M('fuwu');
        $res = $db->where(['id' => $serve_id])->find();
        if (md5($pass_old) != $res['password']) {
            $this->error('原始密码输入错误');die();
        }
        // 更新密码
        $_uper['password'] = md5($pass_new);
        $rs = $db->where(['id' => $serve_id])->save($_uper);
        if ($rs) {
            $this->success('密码修改成功，请重新登陆', U('Login/out'), 1);
        } else {
            $this->error('密码修改失败');
        }
    }

}