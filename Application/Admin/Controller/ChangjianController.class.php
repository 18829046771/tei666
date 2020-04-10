<?php
namespace Admin\Controller;

class ChangjianController extends CommController {

    /* 文章列表
     *
     */
    public function index(){
        $Article = M('article');
        $Cate = M('wenti');

        $cate_id = $Cate -> where() -> order('time desc') ->select();
   
       

        $this->assign('list',$cate_id);
       
       
        $this->display();
    }
    
    
    public function addcl(){
    	$data=I("post.");
    	
    	$arr['wen']=$data['wen'];
    	$arr['da']=$data['da'];
    	$arr['time']=date("Y-m-d H:i:s",time());
    	$res=M("wenti")->add($arr);
    	if($res){
    		    $this->success('添加成功',U('Changjian/index'),1);
    	}else{
    		    $this->success('添加失败',U('Changjian/index'),1);
    	}
    }
    
  

    /*删除
     *
     */
    public function delete(){
        $rs=M('wenti')->where(array('id'=>I('get.id')))->delete();
        if($rs){
            $this->success('删除成功',U('Changjian/index'));
        }else{
            $this->error();
        }
    }

   

  

    /*批量删除
 *
 */
    public function deleteAll(){
        foreach($_POST as $rows)
        {
            $rs=M('wenti')->where(array("id"=>$rows))->delete();

        }

        if($rs){
            $this->success('批量删除成功',U('Changjian/index'));
        }else{
            $this->error('批量删除失败',U('Changjian/index'));
        }
    }
    
    
    public function edit(){
    	$id=I("id");
    	$temp=M("wenti")->where("id='".$id."'")->find();
    	$this->temp=$temp;
    	$this->display();
    }
    
    public function cledr(){
    	$data=I("post.");
    	$id=$data['ids'];
        $arr['wen']=$data['wen'];
    	$arr['da']=$data['da'];
        $res=M("wenti")->where("id='".$id."'")->save($arr);
    	if($res){
    		    $this->success('编辑成功',U('Changjian/index'),1);
    	}else{
    		    $this->success('编辑失败',U('Changjian/index'),1);
    	}
    }
    
    
    
    public function huiyuan(){
    	$temp=M('quanyi')->where()->select();
    	$this->temp=$temp;
    	$this->display();
    }
    
    public function edithuiyuan(){
    	$id=I("id");
    	$temp=M("quanyi")->where("id='".$id."'")->find();
    	$this->temp=$temp;
    	$this->display();
    }
    
    
    public function quanyi(){
    	$data=I("post.");
    	$text=$data['wen'];
    	$ids=$data['ids'];
    	$res=M("quanyi")->where("id='".$ids."'")->save(array('text'=>$text));
    	if($res){
    		 $this->success('编辑成功',U('Changjian/huiyuan'),1);
    	}else{
    		 $this->success('编辑失败',U('Changjian/huiyuan'),1);
    	}
    }
}