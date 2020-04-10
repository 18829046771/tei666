<?php
namespace Admin\Controller;
use Think\Controller;
class CateController extends CommController {
    //分类列表页面

   public  function cateList(){
        $where['cate_p_id']=0;
        $res = M('cate')->where($where)->order('cate_order asc')->select();
        //实例化分类树类
        $treeOb=new TreeController();
        $treeOb->createtree($res);
        $list2=$treeOb->tree;
        $this->assign('list',$list2);
        $this->display();
    }

   public function haoli(){
 	// $temp=M("haoli")->where()->select();
   	 $count = M("haoli") -> count();   
     $Page = new \Think\Page($count,10);
     $show = $Page -> show();
     $products = M("haoli") -> order('id desc') -> limit($Page->firstRow.','.$Page->listRows) -> select();
     foreach($products as $k=>$v){
     	$name=M("user")->where("id='".$v['uid']."'")->find();
     	$products[$k]['sename']=$name['sename'];
     	$products[$k]['dianhua']=$name['username'];
     }
     $this->assign('list',$products);
      $this->assign('page',$show);
   	 $this->display();
   }


    //分类增加函数
  public  function catecl(){
                
        if(empty($_POST['cate_name'])){
            $this->error('栏目名不能为空',U('Cate/cateAdd'),1);
        }


        if (!empty($_FILES['cate_img_url']['name'])) {
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize = 3145728;// 设置附件上传大小
            $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->rootPath = './Public/Uploads/Cate/'; // 设置附件上传根目录
            $upload->savePath = ''; // 设置附件上传（子）目录
            $upload->autoSub = false;
            $upload->saveName=time().rand(1000,9999);
            // 上传文件
            $info = $upload->uploadOne($_FILES['cate_img_url']);
           if (!$info) {// 上传错误提示错误信息
                $this->error($upload->getError());
            } /* else {// 上传成功
                $this->success('上传成功！');
            }*/
            $data['cate_img_url'] = $info['savename'];
        }

        $model = M('cate');
        // 保存当前数据对象
             $tt=I('post.cate_name');
          $ter=M("cate")->where("cate_p_id= '".$tt."'")->find();
            $tsr=$ter['cate_description']+1;
        $data['cate_name'] = I('post.cate_name');
        $data['cate_p_id'] = I('post.cate_p_id');
        $data['cate_order'] = I('post.cate_order');
        $data['cate_url'] = I('post.cate_url');
        $data['cate_description'] = $tsr;
       if($model->add($data)){
           //redirect(U('Cate/cateList'));
           $this->success('添加成功',U('Cate/cateList'),1);
       }else{
           $this->error('编辑失败',U('Cate/cateList'),1);
       }



    }
    //增加分类页面
   public  function cateAdd(){
        $where['cate_p_id']=0;
        $res = M('cate')->where($where)->order('cate_order asc')->select();
        //实例化分类树类
        $treeOb=new TreeController();
        $treeOb->createtree($res);
        $list2=$treeOb->tree;
        //dump($list2);
        $this->assign('list2',$list2);
            $this->display();
        }

    //删除分类
   public  function catedelete(){
        $rs=M('cate')->where(array('cate_id'=>I('get.cate_id')))->find();
        $rs2=M('cate')->where(array('cate_p_id'=>$rs['cate_id']))->select();
        if(empty($rs2))
        {
            $rs=M('cate')->where(array('cate_id'=>I('get.cate_id')))->delete();
            if($rs) {
                $this->success('删除成功', U('Cate/cateList'), 1);
            }
        }else{
            $this->error('删除失败因有二级', U('Cate/cateList'), 1);
        }


    }


    /*编辑页面
     *
     */
    public function cateedit(){
        $obj=M('cate')->where(array('cate_id'=>I('get.cate_id')))->find();
        $this->assign('obj',$obj);
        //dump($obj);
        //分类显示
        $where['cate_p_id']=0;
        $res = M('cate')->where($where)->order('cate_order asc')->select();
        //实例化分类树类
        $treeOb=new TreeController();
        $treeOb->createtree($res);
        $list2=$treeOb->tree;
        //dump($list2);
        $this->assign('list2',$list2);

        $this->display();
    }
    /*编辑数据
    *
    */
    public function update(){
        //数据库操作

        if (!empty($_FILES['cate_img_url']['name'])) {
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize = 3145728;// 设置附件上传大小
            $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->rootPath = './Public/Uploads/Cate/'; // 设置附件上传根目录
            $upload->savePath = ''; // 设置附件上传（子）目录
            $upload->autoSub = false;
            $upload->saveName=time().rand(1000,9999);
            // 上传文件
            $info = $upload->uploadOne($_FILES['cate_img_url']);
            if (!$info) {// 上传错误提示错误信息
                $this->error($upload->getError());
            } /* else {// 上传成功
                $this->success('上传成功！');
            }*/
            $_update['cate_img_url'] = $info['savename'];
        }

        // 保存当前数据对象

        $_update['cate_name'] = I('post.cate_name');
        $_update['cate_p_id'] = I('post.cate_p_id');
        $_update['cate_order'] = I('post.cate_order');
        $_update['cate_url'] = I('post.cate_url');
        $_update['cate_description'] = I('post.cate_description');

        $where['cate_id']=I('get.cate_id');
        $rs=M('cate')->where($where)->save($_update);

        if($rs){
            $this->success('编辑成功',U('Cate/cateList'),1);
        }else{
            $this->error('编辑失败',U('Cate/cateList'),1);
        }
    }
    
    
    
    public function miao(){
//      $count = M("miaoshadan") -> count();      -> limit($Page->firstRow.','.$Page->listRows)
//      $Page = new \Think\Page($count,10);
//      $show = $Page -> show();
        $products = M("miaoshadan") -> order('id desc') -> select();
     foreach($products as $k=>$v){
     	$name=M("user")->where("id='".$v['uid']."'")->find();
     	$products[$k]['sename']=$name['sename'];
     	$products[$k]['dianhua']=$name['username'];
     	$products[$k]['shenfen']=$name['shenfen'];
     }
     
     $this->assign('list',$products);
   	 $this->display();
       
    	
    }

   public function miao_xiangqing(){
   		$hid=I("hid");
   		//前20个中将的人
     	$temp=M("miaoshadan")->where( array('stater'=>1,'hid'=>$hid))->select();
     	
	   	foreach($temp as $k=>$v){
	   		$user=M("user")->where("id='".$v['uid']."'")->find();
	   		$temp[$k]['xingming']=$user['sename'];
	   		$temp[$k]['dianhua']=$user['username'];
	   		$temp[$k]['shenfen']=$user['shenfen'];
	   	}
	   	
   	    //本次活动没重将
   	    $tt=M("miaoshadan")->where( array('stater'=>0,'hid'=>$hid))->select();
   	    
   	    foreach($tt as $k=>$v){
	   		$user=M("user")->where("id='".$v['uid']."'")->find();
	   		$tt[$k]['xingming']=$user['sename'];
	   		$tt[$k]['dianhua']=$user['username'];
	   		$tt[$k]['shenfen']=$user['shenfen'];
	   	}
//	   	 dump($tt);die;
          $this->assign('list',$temp);
          $this->assign('temp',$tt);
   	
   	$this->display();
   }
   
   
   public function youxi(){
   	    $map['lid'] = array('neq',0);
   	    $count = M("liwu_add")->where($map) -> count();      
        $Page = new \Think\Page($count,10);
        $show = $Page -> show();
        $products = M("liwu_add")->where($map)-> order('id desc')-> limit($Page->firstRow.','.$Page->listRows) -> select();
         foreach($products as $k=>$v){
         	   $photo=M("user")->where("id='".$v['uid']."'")->find();
         	   $products[$k]['dianhua']=$photo['username'];
         	   $li=M("liwu")->where("id='".$v['lid']."'")->find();
         	   $products[$k]['li']=$li['name'];
         }
         // dump($products);die;  liwu_add
        $this->assign('page',$show);
        $this->assign('list',$products);
        $this->display();
   }

}