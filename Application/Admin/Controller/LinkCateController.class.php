<?php
namespace Admin\Controller;
use Think\Controller;
class LinkCateController extends CommController {
    //分类列表页面
    function Linklist(){
        $list=M('link_c')->select();
        $this->assign('list',$list);

        //链接分类显示

//链接分类
        $where['l_cate_p_id']=0;
        $res = M('link_c')->where($where)->order('l_cate_order asc')->select();
        //实例化分类树类
        $treeOb=new TreeController();
        $treeOb->createtree2($res);
        $list2=$treeOb->tree;
        //dump($list2);
        $this->assign('list',$list2);


        $this->display();
    }
    //分类增加函数
    function linkcl(){
        $model = M('link_c');
        $data['l_cate_p_id'] = I('post.l_cate_p_id');
        $data['l_cate_name'] = I('post.l_cate_name');
        $data['l_cate_order'] = I('post.l_cate_order');
       if($model->add($data)){
           //redirect(U('Cate/cateList'));
           $this->success('添加成功',U('LinkCate/Linklist'),1);
       }else{
           $this->error('编辑失败',U('LinkCate/Linklist'),1);
       }



    }
    //增加分类页面
    function Linkadd(){
        //链接分类
        $where['l_cate_p_id']=0;
        $res = M('link_c')->where($where)->order('l_cate_order asc')->select();
        //实例化分类树类
        $treeOb=new TreeController();
        $treeOb->createtree2($res);
        $list2=$treeOb->tree;
        //dump($list2);
        $this->assign('list2',$list2);
            $this->display();
        }

    //删除分类
    function delete(){
        $rs=M('link_c')->where(array('l_cate_id'=>I('get.l_cate_id')))->delete();
        if($rs) {
            $this->error('删除成功', U('LinkCate/Linklist'), 1);
        }
    }


    /*编辑页面
     *
     */
    public function Linkedit(){
        //dump(I('get.cate_id'));
        $obj=M('link_c')->where(array('l_cate_id'=>I('get.l_cate_id')))->find();
        $this->assign('obj',$obj);
        //dump($obj);
        //链接分类
        $where['l_cate_p_id']=0;
        $res = M('link_c')->where($where)->order('l_cate_order asc')->select();
        //实例化分类树类
        $treeOb=new TreeController();
        $treeOb->createtree2($res);
        $list2=$treeOb->tree;
        //dump($list2);
        $this->assign('list2',$list2);

        $this->display();
    }
    /*编辑数据
    *
    */
    public function update(){
        $_update['l_cate_p_id'] = I('post.l_cate_p_id');
        $_update['l_cate_name'] = I('post.l_cate_name');
        $_update['l_cate_order'] = I('post.l_cate_order');

        $where['l_cate_id']=I('get.l_cate_id');
        $rs=M('link_c')->where($where)->save($_update);

        if($rs){
            $this->success('编辑成功',U('LinkCate/Linklist'),1);
        }else{
            $this->error('编辑失败',U('LinkCate/Linklist'),1);
        }
    }
     


   



}