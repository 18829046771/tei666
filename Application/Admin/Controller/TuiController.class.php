<?php
namespace Admin\Controller;
use Think\Controller;
class TuiController extends CommController {

    /* 
     *
     */
    public function index(){
    	
        $Article=M("tuijian");
		$products = $Article -> where() ->select();
       
        
        $this->assign('list',$products);

        $this->assign('page',$show);

        $this->display();
    }
      
      
      
      
      
      
      
      
    
    /*添加热卖推荐
     *
     */
    public function bian(){
        $id=I("id");
        $this->ids=$id;
       $this->display();
    }
    
    public function editHandle(){
    	$data=I("post.");
    	$id=$data['id'];
    	if (!empty($_FILES['img'])){
                $config = array(
                    'rootPath'  => './Uploads/picture/',
                    'savePath'  => '',
                    'maxSize'   => 19145728000,
                    'exts'      => array('jpg', 'gif', 'png', 'jpeg')
                );

                $upload = new \Think\Upload($config);

                $picture = $upload -> uploadOne($_FILES['img']);

                $img =  '/Uploads/picture/'.$picture['savepath'].$picture['savename'];

            }
    	
    	
    	$arr['store_name']=$data['store_name'];
    	$arr['img']=$img;
    	$arr['num']=$data['num'];
    	$arr['member']=$data['member'];
    	$arr['url']=$data['url'];
    	$arr['time']=time();
    	$res=M("tuijian")->where("id='".$id."'")->save($arr);
    	if($res){
    		 $this -> success('编辑成功',U('Tui/index'));
    	}else{
    		 $this -> success('编辑失败',U('Tui/index'));
    	}
    	 
    }
   
   
   
    
}











