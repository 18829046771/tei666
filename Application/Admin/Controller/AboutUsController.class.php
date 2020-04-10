<?php
namespace Admin\Controller;

class AboutUsController extends CommController {

    /* 文章列表
     *
     */
    public function index(){
        $Article = M('article');
        $Cate = M('cate');

        $cate_id = $Cate -> where('cate_p_id = 18 AND (cate_id=19 || cate_id=21)') -> order('cate_id desc') ->select();
   
       

        $this->assign('list',$cate_id);
       
       
        $this->display();
    }
    
    
    /* 荣誉证书列表
     *
     */
    public function zhengshu(){
    	
    	
  	 
  	  $ter=M('zhengshu')->where()->select();
   	  $this->tt=$ter;
    	  $this->display();
    }
    
    
    
    //增加证书
    public function addzhengshu(){
    	
      $this->display();	
    }
    //编辑证书
    public function zhengshu_bian(){
    	$id=I("id");
    	$tt=M("zhengshu")->where("id='".$id."'")->find();
    	$this->tt=$tt;
       $this->display();	
    }
    
    public function zhengshu_xiu(){
    	 if (!empty($_FILES['art_img_url']['name'])) {
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize = 3145728 ;// 设置附件上传大小
            $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->rootPath = './Public/Uploads/Article/'; // 设置附件上传根目录
            $upload->savePath = ''; // 设置附件上传（子）目录
            $upload->autoSub = false;
            $upload->saveName=time().rand(1000,9999);
            // 上传文件
            $info = $upload->upload();
            //dump($info);
            if(!$info) {// 上传错误提示错误信息
                $this->error($upload->getError());
            }else{// 上传成功
                // $this->success('上传成功！');
                $str=getcwd();
                $str=str_replace('\\','/',$str);
                //dump($str);
                $filepath=$str.'/Public/Uploads/Article/'.$info['art_img_url']['savepath'].$info['art_img_url']['savename'];
                $image = new \Think\Image();
                $re=file_exists($filepath);
                $image->open($filepath);
                // 生成一个居中裁剪为150*150的缩略图并保存为thumb.jpg
                //dump($str.'/Public/Uploads/Article_s/'.$info['art_img_url']['savepath'].$info['art_img_url']['savename']);
                //exit();
                $re= $image->thumb(200, 200,\Think\Image::IMAGE_THUMB_SCALE)->save($str.'/Public/Uploads/Article_s/'.$info['art_img_url']['savename']);
            }



        }
    	
    	$model = M('zhengshu');


        $data['img'] = '/Public/Uploads/Article/'.$info['art_img_url']['savepath'].$info['art_img_url']['savename'];
        $data['title'] = I('post.art_title');
        
        if(I('post.art_date') != '')
        {
            $data['art_date'] =I('post.art_date');
        }   
           $id=I("ids");
          $xiu=M("zhengshu")->where("id='".$id."'")->save($data);
        if($xiu){
            $this->success('修改成功',U('AboutUs/zhengshu'),1);
        }else{
            $this->error('修改失败',U('AboutUs/zhengshu'),1);
        }

    	
    }
    public function addclzhengshu(){
    	

       
        if (!empty($_FILES['art_img_url']['name'])) {
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize = 3145728 ;// 设置附件上传大小
            $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->rootPath = './Public/Uploads/Article/'; // 设置附件上传根目录
            $upload->savePath = ''; // 设置附件上传（子）目录
            $upload->autoSub = false;
            $upload->saveName=time().rand(1000,9999);
            // 上传文件
            $info = $upload->upload();
            //dump($info);
            if(!$info) {// 上传错误提示错误信息
                $this->error($upload->getError());
            }else{// 上传成功
                // $this->success('上传成功！');
                $str=getcwd();
                $str=str_replace('\\','/',$str);
                //dump($str);
                $filepath=$str.'/Public/Uploads/Article/'.$info['art_img_url']['savepath'].$info['art_img_url']['savename'];
                $image = new \Think\Image();
                $re=file_exists($filepath);
                $image->open($filepath);
                // 生成一个居中裁剪为150*150的缩略图并保存为thumb.jpg
                //dump($str.'/Public/Uploads/Article_s/'.$info['art_img_url']['savepath'].$info['art_img_url']['savename']);
                //exit();
                $re= $image->thumb(200, 200,\Think\Image::IMAGE_THUMB_SCALE)->save($str.'/Public/Uploads/Article_s/'.$info['art_img_url']['savename']);
            }



        }
        $model = M('zhengshu');


        $data['img'] = '/Public/Uploads/Article/'.$info['art_img_url']['savepath'].$info['art_img_url']['savename'];
        $data['title'] = I('art_title');
          
        
         
        if( $model->add($data)){
            $this->success('添加成功',U('AboutUs/zhengshu'),1);
        }else{
            $this->error('添加失败',U('AboutUs/zhengshu'),1);
        }

    
    }
    
    
    
    //删除证书
    public function zhengshu_del(){
    	$id=I("id");
    	$ter=M("zhengshu")->where("id='".$id."'")->delete();
    	if($ter){
    		 $this->success('删除成功',U('AboutUs/zhengshu'),1);
    	}else{
    		 $this->reeor('删除失败',U('AboutUs/zhengshu'),1);
    	}
    }
   
    
    /* 文章列表
     *
     */
    public function artlist(){

        //分类名称获取
        $keyword=isset($_POST['search'])?$_POST['search']:'';
        //dump($keyword);
        $User= M('article');
        $map['ht_article.art_title']=array('like','%'.$keyword.'%');
        $map['ht_article.art_cate_id']=I('get.cate_id');
        $count= $User->where($map)->count();// 查询满足要求的总记录数
        $Page= new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show= $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性

        $list=$User->join('ht_cate on ht_article.art_cate_id = ht_cate.cate_id')->
        where($map)->order('ht_article.art_date desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('list',$list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
        //dump($count);
        $this->display('Article/index'); // 输出模板
    }
    /*添加
     *
     */
    public function add(){
        //分类
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

    /*编辑
   *
   */
    public function edit(){

        $obj=M('cate')->where(array('cate_id'=>I('get.art_id')))->find();
        $this->assign('obj',$obj);
         // dump($obj);
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
    /*添加处理
     *
     */
    public function addcl(){

        if(empty($_POST['art_title'])){
            $this->error('栏目名不能为空',U('Article/add'),1);
        }
        if (!empty($_FILES['art_img_url']['name'])) {
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize = 3145728 ;// 设置附件上传大小
            $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->rootPath = './Public/Uploads/Article/'; // 设置附件上传根目录
            $upload->savePath = ''; // 设置附件上传（子）目录
            $upload->autoSub = false;
            $upload->saveName=time().rand(1000,9999);
            // 上传文件
            $info = $upload->upload();
            //dump($info);
            if(!$info) {// 上传错误提示错误信息
                $this->error($upload->getError());
            }else{// 上传成功
                // $this->success('上传成功！');
                $str=getcwd();
                $str=str_replace('\\','/',$str);
                //dump($str);
                $filepath=$str.'/Public/Uploads/Article/'.$info['art_img_url']['savepath'].$info['art_img_url']['savename'];
                $image = new \Think\Image();
                $re=file_exists($filepath);
                $image->open($filepath);
                // 生成一个居中裁剪为150*150的缩略图并保存为thumb.jpg
                //dump($str.'/Public/Uploads/Article_s/'.$info['art_img_url']['savepath'].$info['art_img_url']['savename']);
                //exit();
                $re= $image->thumb(200, 200,\Think\Image::IMAGE_THUMB_SCALE)->save($str.'/Public/Uploads/Article_s/'.$info['art_img_url']['savename']);
            }



        }
        $model = M('article');


        $data['art_img_url'] = $info['art_img_url']['savename'];
        $data['art_img_url_s'] = $info['art_img_url']['savename'];

        $data['art_title'] = I('post.art_title');
        $data['art_cate_id'] = I('post.art_cate_id');
        $data['art_keywords'] = I('post.art_keywords');
        $data['art_description'] = I('post.art_description');
        $data['art_author'] = I('post.art_author');
        $data['art_from'] = I('post.art_from');
        $data['art_content'] = str_replace('\\','',html_entity_decode(I('post.art_content')));
        if(I('post.art_date') != '')
        {
            $data['art_date'] =I('post.art_date');
        }
        $data['art_date']      =date('Y-m-d H:i:s');
        if($model->add($data)){
            $this->success('添加成功',U('AboutUs/index'),1);
        }else{
            $this->error('添加失败',U('AboutUs/index'),1);
        }

    }

    /*删除
     *
     */
    public function delete(){
        $rs=M('article')->where(array('art_id'=>I('get.art_id')))->delete();
        if($rs){
            $this->success('删除成功',U('Article/index'));
        }else{
            $this->error();
        }
    }

    /*编辑处理
         *
         */
    public function update(){

        if (!empty($_FILES['art_img_url']['name'])) {
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize = 3145728 ;// 设置附件上传大小
            $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->rootPath = './Public/Uploads/Article/'; // 设置附件上传根目录
            $upload->savePath = ''; // 设置附件上传（子）目录
            $upload->autoSub = false;
            $upload->saveName=time().rand(1000,9999);
            // 上传文件
            $info = $upload->upload();
            //dump($info);
            if(!$info) {// 上传错误提示错误信息
                $this->error($upload->getError());
            }else{// 上传成功
                // $this->success('上传成功！');
                $str=getcwd();
                $str=str_replace('\\','/',$str);
                //dump($str);
                $filepath=$str.'/Public/Uploads/Article/'.$info['art_img_url']['savepath'].$info['art_img_url']['savename'];
                $image = new \Think\Image();
                $re=file_exists($filepath);
                $image->open($filepath);
                // 生成一个居中裁剪为150*150的缩略图并保存为thumb.jpg
                //dump($str.'/Public/Uploads/Article_s/'.$info['art_img_url']['savepath'].$info['art_img_url']['savename']);
                //exit();
                $re= $image->thumb(200, 200,\Think\Image::IMAGE_THUMB_SCALE)->save($str.'/Public/Uploads/Article_s/'.$info['art_img_url']['savename']);
            }


            // 保存当前数据对象
            $_update['cate_img_url'] = '/Public/Uploads/Article/'.$info['art_img_url']['savepath'].$info['art_img_url']['savename'];
          //  $_update['art_img_url_s'] = $info['art_img_url']['savename'];

        }

            
        $_update['cate_title'] = I('post.art_title');
       
        $_update['content'] =str_replace('\\','',html_entity_decode(I('post.art_content')));
        if(I('post.art_date') != '')
        {
            $data['art_date']      =I('post.art_date');
        }
    

        $where['cate_id']=I('get.cate_id');
               
//      if($where['cate_id']=='20'){
//      	  $t='荣誉证书';
//      	 $arr=array(
//      	    'cate_img_url'=>'/Public/Uploads/Article/'.$info['art_img_url']['savepath'].$info['art_img_url']['savename'],
//      	    'cate_title'=>I('post.art_title'),
//      	    'content'=>'0',
//      	    'cate_name'=>$t,
//      	   
//      	 );
//      	
//         $rs=M('cate')->where()->add($arr);
//       }else{
         $rs=M('cate')->where($where)->save($_update);
         
      

        if($rs){
            $this->success('编辑成功',U('AboutUs/index'),1);
        }else{
            $this->error('编辑失败',U('AboutUs/index'),1);
        }

    }
    /*批量删除
     *
     */
    public function deleteAll(){
        foreach($_POST as $rows)
        {
            $rs=M('article')->where(array("art_id"=>$rows))->delete();

        }

        if($rs){
            $this->success('批量删除成功',U('Article/index'));
        }else{
            $this->error('批量删除失败',U('Article/index'));
        }
    }


    public function imgs(){
        $this->assign('pid',I('get.art_id'));
        $this->display('Article/imgs');
    }

    //通过webuploader上传的图片
    public function webuploader() {

        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =  3*1024*1024 ;// 设置附件上传大小
        $upload->exts      =  array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath = './Public/Upload/'; // 设置附件上传根目录
        $upload->savePath = './Upload/'; // 设置附件上传（子）目录
        //创建文件夹
        if(!is_dir($upload->savePath)){
            mkdir($upload->savePath,0777,true);
        }
        // 上传文件,返回上次结果
        // dump(1);
        $info   =  $upload->upload();
        //dump($photos);
        // 			exit();
        if(!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
        }else{// 上传成功 获取上传文件信息
            echo json_encode($info);
        }

    }
    public function imgadd(){

        $model = M('img_url');
        foreach($_POST['img_url'] as $row) {
            $data['img_p_id'] = I('post.pid');
            $data['img_url'] = $row;
            $arr=$model->add($data);
        }
        if ($arr) {
            $this->success('添加成功', U('Article/imgall',array('art_id'=>I('post.pid'))), 1);
        } else {
            $this->error('添加失败', U('Article/imgall',array('art_id'=>I('post.pid'))), 1);
        }


    }
    public function imgall(){
        $model = M('img_url');
        $pid=I('get.art_id');
        $this->assign('pid',$pid);
        $list=$model->table(array('ht_article'=>'a','ht_img_url'=>'b'))->where('b.img_p_id=a.art_id and b.img_p_id = '.$pid)->order('img_id desc')->select();
        //dump($list);
        $this->assign('list',$list);

        $this->display('Article/imgall');
    }

    /*删除
        *
        */
    public function imgdelete(){
        $rs=M('img_url')->where(array('img_id'=>I('get.img_id')))->delete();
        if($rs){
            $this->success('删除成功',U('Article/imgall',array('art_id'=>I('get.pid'))));
        }else{
            $this->error();
        }
    }

    /*批量删除
 *
 */
    public function imgdeleteAll(){
        foreach($_POST as $rows)
        {
            $rs=M('img_url')->where(array("img_id"=>$rows))->delete();

        }

        if($rs){
            $this->success('批量删除成功',U('Article/imgall',array('art_id'=>I('get.pid'))));
        }else{
            $this->error('批量删除失败',U('Article/imgall',array('art_id'=>I('get.pid'))));
        }
    }
}