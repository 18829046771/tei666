<?php
namespace Admin\Controller;
use Think\Controller;
class JcszController extends CommController {

    /* 文章列表
     *
     */
    public function index(){
        $obj=M('keys')->find();
        $this->assign('obj',$obj);
        $this->display();
    }

    /* 标题
    *
    */
    public function bt(){
        $obj=M('keys')->where(array('key_id'=>I('get.key_id')))->find();
        $this->assign('obj',$obj);
        $this->display();
    }
    /* 标题处理
    *
    */
    public function btcl(){
        $_update['key_title'] = htmlspecialchars_decode(I('post.key_title'));
        $where['key_id']=I('get.key_id');
        $rs=M('keys')->where($where)->save($_update);
        if($rs){
            $this->success('编辑成功',U('Jcsz/index'),1);
        }else{
            $this->error('编辑失败',U('Jcsz/index'),1);
        }
    }
    /* 关键字
    *
    */
    public function gjz(){
        $obj=M('keys')->where(array('key_id'=>I('get.key_id')))->find();
        $this->assign('obj',$obj);
        $this->display();
    }
    /* 关键字处理
    *
    */
    public function gjzcl(){
        $_update['key_content'] = htmlspecialchars_decode(I('post.key_content'));
        $where['key_id']=I('get.key_id');
        $rs=M('keys')->where($where)->save($_update);
        if($rs){
            $this->success('编辑成功',U('Jcsz/index'),1);
        }else{
            $this->error('编辑失败',U('Jcsz/index'),1);
        }
    }
    /* 描述
    *
    */
    public function ms(){
        $obj=M('keys')->where(array('key_id'=>I('get.key_id')))->find();
        $this->assign('obj',$obj);
        $this->display();
    }
    /* 描述处理
    *
    */
    public function mscl(){
        $_update['desc_content'] = htmlspecialchars_decode(I('post.desc_content'));
        $where['key_id']=I('get.key_id');
        $rs=M('keys')->where($where)->save($_update);
        if($rs){
            $this->success('编辑成功',U('Jcsz/index'),1);
        }else{
            $this->error('编辑失败',U('Jcsz/index'),1);
        }
    }
    /* 联系方式
    *
    */
    public function lxfs(){
        $obj=M('keys')->where(array('key_id'=>I('get.key_id')))->find();
        $this->assign('obj',$obj);
        $this->display();
    }
    /* 联系方式处理
    *
    */
    public function lxfscl(){
        $_update['con_content'] = htmlspecialchars_decode(I('post.con_content'));
        $where['key_id']=I('get.key_id');
        $rs=M('keys')->where($where)->save($_update);
        if($rs){
            $this->success('编辑成功',U('Jcsz/index'),1);
        }else{
            $this->error('编辑失败',U('Jcsz/index'),1);
        }
    }
    /* 版权
   *
   */
    public function bq(){
        $obj=M('keys')->where(array('key_id'=>I('get.key_id')))->find();
        $this->assign('obj',$obj);
        $this->display();
    }
    /* 版权处理
    *
    */
    public function bqcl(){
        $_update['footer_content'] = htmlspecialchars_decode(I('post.footer_content'));
        $where['key_id']=I('get.key_id');
        $rs=M('keys')->where($where)->save($_update);
        if($rs){
            $this->success('编辑成功',U('Jcsz/index'),1);
        }else{
            $this->error('编辑失败',U('Jcsz/index'),1);
        }
    }


}