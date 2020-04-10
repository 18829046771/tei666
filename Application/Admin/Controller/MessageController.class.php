<?php
namespace Admin\Controller;
use Think\Controller;
class MessageController extends CommController {

    /* 文章列表
     *
     */
    public function index(){
         $where=array();
         $sename=I("sename");
         $this->sename = $sename;
         if(!empty($sename)){
         	$user=M("user")->where("sename='".$sename."'")->find();
         	$where['uid'] = $user['id'];
         }
         $storename=I("storname");
         $this->storename = $storename;
         if(!empty($storename)){
         	$store=M("article")->where("product_title='".$storename."'")->find();
         	$where['art_id']=$store['art_id'];
         }
        $User= M('ping');
        $count= $User->where($where)->count();// 查询满足要求的总记录数
        $Page= new \Think\Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show= $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list=$User->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach($list as $k=>$v){
        	
        	$store=M("article")->where("art_id='".$v['art_id']."'")->find();
        	$user=M("user")->where("id='".$v['uid']."'")->find();
        	$list[$k]['user']=$user['sename'];
        	$list[$k]['storename']=$store['product_title'];
        	$list[$k]['textt']=mb_substr($v['text'], 0, 7,'utf-8');
        } 
          
        $this->assign('list',$list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出

        $this->display(); // 输出模板
    }

    /*删除
     *
     */
    public function delete(){
        $rs=M('message')->where(array('messbo_id'=>I('get.messbo_id')))->delete();
        if($rs){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }
     public function xianshi(){
     	 $id=I("id");
	     $re=M("ping")->where("id='".$id."'")->save(array('state'=>1));
	     if($re){
	     	$this->success('操作成功',U('Message/index'));
	     }
     }
     
     
     public function yin(){
     	 $id=I("id");
	     $re=M("ping")->where("id='".$id."'")->save(array('state'=>0));
	     if($re){
	     	$this->success('操作成功',U('Message/index'));
	     }
     }
    /*批量删除
         *
         */
    public function deleteAll(){
    	
    	  
        foreach($_POST as $rows)
        {
            $rs=M('ping')->where(array('id'=>$rows))->delete();

        }
        if($rs){
            $this->success('批量删除成功',U('Message/index'));
        }else{
            $this->error('批量删除失败',U('Message/index'));
        }
    }
    
    
    
    //关于我们
    public function guanyu(){
    	$temp=M("women")->where("id=1")->find();
    	$this->temp=$temp;
    	$this->display();
    }
    
    //关于我们编辑处理
    public function guanyu_add(){
    	$data=I("post.");
    	
    	$arr=array(
    	 'content'=>$data['art_content']
    	 
    	);
    	$res=M("women")->where("id=1")->save($arr);
    	
    	
    	if($res){
    		 $this->success('编辑成功',U('Message/guanyu'));
    	}else{
    		 $this->success('编辑失败',U('Message/guanyu'));
    	}
    }

}