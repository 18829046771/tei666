<?php
namespace Admin\Controller;
use Think\Controller;
class UserController extends CommController {


    /* 列表
     *
     */
    public function index(){

        $User= M('users');
        $count= $User->count();// 查询满足要求的总记录数
        $Page= new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show= $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list=$User->order('user_id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('list',$list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
        $this->display(); // 输出模板
    }
    /*添加
     *
     */
    public function add(){
        $this->display();
    }
    /*添加处理
     *
     */

    public function addcl(){
        if(empty($_POST['user_name'])){
            $this->error('用户名不能为空',U('User/add'),1);
        }
        if($_POST['user_password'] != $_POST['user_password2']){
            $this->error('确认密码错误',U('User/add'),1);
        }
        $model = M('users');
        $data['user_name'] = I('post.user_name');
        $arr=new Md5Controller();
        $ss=$arr->to_md5(I('post.user_password'));
        $data['user_password'] = $ss;
        $data['user_nickname'] = I('post.user_nickname');
        $data['phone'] = I('post.phone');
        if($model->add($data)){
            //redirect(U('Cate/cateList'));
            $this->success('添加成功',U('User/index'),1);
        }else{
            $this->error('添加失败',U('User/index'),1);
        }

    }
    /*编辑
     *
     */
    public function edit(){
        $obj=M('users')->where(array('user_id'=>I('get.user_id')))->find();
        $this->assign('obj',$obj);
        $this->display();
    }
    /*修改
    *
    */
    public function update(){
        if(!empty($_POST['user_password']))
        {
            $arr=new Md5Controller();
            $ss=$arr->to_md5(I('post.user_password'));
            $_update['user_password']=$ss;
        }
        //数据库操作
        if(!empty($_POST['user_nickname'])){
            $_update['user_nickname'] = I('post.user_nickname');
            $where['user_id']=I('get.user_id');
            $rs=M('users')->where($where)->save($_update);
            if($rs===false){
                $this->error('编辑失败',U('User/index'),1);

            }else{
                $this->success('编辑成功',U('User/index'),1);
            }
        }



    }
    /*删除
     *
     */
    public function delete(){
        $rs=M('users')->where(array('user_id'=>I('get.user_id')))->delete();
        if($rs){
            $this->success('删除成功',U('User/index'),1);
        }else{
            $this->error('删除失败',U('User/index'),1);
        }
    }
    /*修改密码
     *
     */

    public function chenge(){

        $this->display();
    }
    /*修改密码处理
    *
    */

    public function chengecl(){
        if(!empty($_POST['pass_old'])) {
            if (empty($_POST['pass_old'])) {
                $this->error('请输入原始密码', U('User/chenge'), 1);
            }
            if (empty($_POST['pass_new'])) {
                $this->error('请输入原始密码', U('User/chenge'), 1);
            }
            if (empty($_POST['pass_new2'])) {
                $this->error('请输入原始密码', U('User/chenge'), 1);
            }
            if (I('post.pass_new') != I('post.pass_new2')) {
                $this->error('确认密码输入错误请重新输入', U('User/chenge'), 1);
            }
            $arr = $_SESSION['system_user_id'];
            $admin = M('users')->where(array('user_id' => $arr))->find();
            $my_md5 = new Md5Controller();
            if ($my_md5::md5_to2(I('post.pass_old')) != $my_md5::get_md5($admin['user_password'])) {
                $this->error('原始密码输入错误', U('User/chenge'), 1);
            }
            //更新密码
            $_uper['user_password'] = $my_md5::to_md5(I('post.pass_new'));
            $rs = M('users')->where(array('user_id' => $arr))->save($_uper);
            if ($rs) {
                $this->success('密码修改成功,请重新登陆', U('Login/out'), 1);
            } else {
                $this->error('密码修改失败', U('User/chenge'), 1);
            }
        }else{
            $this->error('密码修改失败', U('User/chenge'), 1);

        }
    }
    /*批量删除
     *
     */
    public function deleteAll(){
        //数据库操作
    }
}