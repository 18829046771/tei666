<?php
namespace Admin\Controller;
use Think\Controller;
class LoginController extends Controller {

    /* 文章列表
     *
     */
    public function index(){

   

        $this->display();
    }

    /* 登陆
        *
        */
    public function doLogin(){

        if(empty($_POST['user_name']))
        {
            $this->error('用户名不能为空',U('Login/index'),1);
        }
        if(empty($_POST['user_password']))
        {
            $this->error('密码不能为空',U('Login/index'),1);
        }
        if(empty($_COOKIE['login_err']))
        {
            setcookie('login_err',0,time()+1200);
            $_COOKIE['login_err']=0;
        }else
        {
            if($_COOKIE['login_err'] >= 20)
            {
                $this->error('您登陆错误已超过三次,请您20分钟后再次登陆',U('Login/index'),1);
            }
        }



        //检查用户名唯一性
        $arr=new Md5Controller();
        $username=I('post.user_name');
        $admin=M('users')->where(array('user_name'=>$username))->find();
        if(!is_array($admin))
        {
            setcookie('login_err',$_COOKIE['login_err']+1,time()+1200);
            $this->error('用户名错误',U('Login/index'),1);
        }

        //检查密码
        if(md5(md5(I('post.user_password'))) != $arr->get_md5($admin['user_password']))
        {
            setcookie('login_err',$_COOKIE['login_err']+1,time()+1200);
            //登陆日志
            $_insert['log_user_name']=I('post.user_name');
            $_insert['log_status']   =2;
            $_insert['log_password'] =I('post.user_password');
            $_insert['log_ip']       =$_SERVER['REMOTE_ADDR'];
            $_insert['log_time']     =date('Y-m-d H:i:s');
            $model=M('login_logs');
            $model->add($_insert);
            $this->error('密码错误',U('Login/index'),1);
        }



        //设置session
        $_SESSION['system_user_id']=$admin['user_id'];
        $_SESSION['system_user_nickname']=$admin['user_nickname'];
        //登陆日志
        $_insert['log_user_name']=I('post.user_name');
        $_insert['log_status']   =1;
        $_insert['log_ip']       =$_SERVER['REMOTE_ADDR'];
        $_insert['log_time']     =date('Y-m-d H:i:s');
        $model=M('login_logs');

        if($model->add($_insert)){
            //redirect(U('Cate/cateList'));
            //$this->success('恭喜您登陆成功',U('ProductManagement/index'),1);
            $this->success('恭喜您登陆成功',U('Yonghu/shouye'),1);
        }else{
            $this->error('登录失败',U('Login/index'),1);
        }


    }
    /* 退出登陆
         *
         */
    public function out(){
        unset($_SESSION['system_user_id']);
        unset($_SESSION['system_user_nickname']);
        $this->success('退出成功',U('Login/index'),1);
    }

}