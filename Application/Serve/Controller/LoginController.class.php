<?php
namespace Serve\Controller;
use Think\Controller;

class LoginController extends Controller {

    public function index(){
        $this->display();
    }

    // 登陆
    public function doLogin(){
        $data = I("post.");
        $rules = [
            ['username', 'require', '用户名不能为空！'],
            ['password', 'require', '密码不能为空！']
        ];
        $db = M('Fuwu');
        if (!$db->validate($rules)->create()){
            // 如果创建失败 表示验证没有通过 输出错误提示信息
            $this->error($db->getError());
        }else{
            // 验证通过 可以进行其他数据操作
            if(empty($_COOKIE['login_err'])){
                setcookie('login_err', 0, time() + 1200);
                $_COOKIE['login_err'] = 0;
            }else{
                if($_COOKIE['login_err'] >= 20){
                    $this->error('您登陆错误已超过三次，请您20分钟后再次登陆！');die();
                }
            }

            $res = $db->where(['username' => $data['username']])->find();
            if(!is_array($res)){
                setcookie('login_err', $_COOKIE['login_err'] + 1, time() + 1200);
                $this->error('用户名错误！');die();
            }
            // 检查密码
            if(md5($data['password']) != $res['password']){
                setcookie('login_err', $_COOKIE['login_err'] + 1, time() + 1200);
                $this->error('密码错误！');die();
            }

            // 设置session
            $_SESSION['serve_id'] = $res['id'];
            $this->success('恭喜您登陆成功！', U('Index/index'), 1);
        }
    }

    // 退出登陆
    public function out(){
        unset($_SESSION['serve_id']);
        $this->success('退出成功', U('Login/index'), 1);
    }

}