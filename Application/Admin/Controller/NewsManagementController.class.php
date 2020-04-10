<?php
namespace Admin\Controller;
use Think\Controller;
class NewsManagementController extends CommController {

    /* 文章列表
     *
     */
    public function index(){
    	$hdong=M("huodong");
        $count= $hdong->where()->count();// 查询满足要求的总记录数
        $Page= new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show= $Page->show();// 分页显示输出
        $list=$hdong->where()->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('list',$list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
          
        //dump($list);die;
        $this->display();
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
    
    public function miaosha (){
    	 // dump("222112");
    	$this->display();
    }
    
    
     public function addcl_miaosha(){
    	$data=I("post.");
//  	dump($data);die;
    	 if(!empty($_FILES['art_img_url'])) {
            $config = array(
                'rootPath' => './Uploads/newsPicture/',
                'savePath' => '',
                'maxSize' => 19145728,
                'exts' => array('jpg', 'gif', 'png', 'jpeg')
            );

            $upload = new \Think\Upload($config);

            $newsPicture = $upload->uploadOne($_FILES['art_img_url']);

            $news = '/Uploads/newsPicture/' . $newsPicture['savepath'] . $newsPicture['savename'];
        }
        $arr=array(
           'img'=>$news, 
           'name'=>$data['art_title'],
           'jiage'=>$data['art_from'],
           'xiangqing'=>I('post.art_content')
        );
       if( M("miaosha")->add($arr)){
       	 $this->success('添加成功',U('NewsManagement/miaosha'),1);
       }
        
    }
    
    
    
    /*添加
     *
     */
    public function add(){
        //分类
        $where['cate_p_id']=24;
        $res = M('cate')->where($where)->order('cate_order asc')->select();
        //实例化分类树类
        $treeOb=new TreeController();
        $treeOb->createtree($res);
        $list2=$treeOb->tree;
        //dump($list2);
        $this->assign('list2',$list2);
        //活动供应商
        $gong=M("gongying")->where("cate_id=1")->select();
        $this->gong=$gong;
        $this->display();
    }

    /*编辑
   *
   */
    public function edit(){

        $obj=M('huodong')->where(array('id'=>I('get.id')))->find();
        $this->assign('obj',$obj);
        //活动供应商
        $gong=M("gongying")->where("cate_id=1")->select();
        $this->gong=$gong;


        $this->display();
    }
    /*添加处理
     *
     */
    public function addcl(){
    	   $data=I("post.");
//  	     dump($_FILES['art_img_url']);
//  	   dump($data);
    	   
        if(!empty($_FILES['art_img_url'])) {
            $config = array(
                'rootPath' => './Uploads/newsPicture/',
                'savePath' => '',
                'maxSize' => 100*1024*1024,
                'exts' => array('jpg', 'gif', 'png', 'jpeg')
            );

            $upload = new \Think\Upload($config);

            $newsPicture = $upload->uploadOne($_FILES['art_img_url']);

            $news = '/Uploads/newsPicture/' . $newsPicture['savepath'] . $newsPicture['savename'];
        }

        $model = M('huodong');
      
      
        $data['huodong_title'] = I('post.art_title');  //活动名称
        $data['huodong_img'] = $news; //活动图片
        $data['huodong_money'] = I('post.art_from'); //活动价格
        $data['huodong_ren']=I('post.art_ren'); //活动发布人
        $data['time']=I('post.time');  //活动时间
        $data['didian']=I('post.didian');  //活动地点
        $data['type']=I('post.type0'); //普通会员是否参加  参加为  1  不参加为   null
        $data['type1']=I('post.type1');
        $data['type2']=I('post.type2');
        $data['type3']=I('post.type3');
        $data['type4']=I('post.type4');
        $data['date'] =date('Y-m-d H:i:s',time()); //发布时间
        $data['text']=I('post.art_content');  //活动详情
        $data['renshu']=I('post.renshu');
         $data['gong']=I('post.gong_id'); 
        if($model->add($data)){
            //redirect(U('Cate/cateList'));
            $this->success('添加成功',U('NewsManagement/index'),1);
        }else{
            $this->error('添加失败',U('NewsManagement/index'),1);
        }

    }

    /*删除$this->success("添加成功")；
     * 
     * 
     *
     */
    public function delete(){
        $rs=M('huodong')->where(array('id'=>I('get.id')))->delete();
        if($rs){
            $this->success('删除成功',U('NewsManagement/index'));
        }else{
            $this->error();
        }
    }

    /*编辑处理
         *
         */
    public function update(){
    	$data=I("post.");
        if(!empty($_FILES['art_img_url'])) {
            $config = array(
                'rootPath' => './Uploads/newsPicture/',
                'savePath' => '',
                'maxSize' => 19145728,
                'exts' => array('jpg', 'gif', 'png', 'jpeg')
            );

            $upload = new \Think\Upload($config);

            $newsPicture = $upload->uploadOne($_FILES['art_img_url']);

            $news = '/Uploads/newsPicture/' . $newsPicture['savepath'] . $newsPicture['savename'];
        }
          if(strlen($news)<30){
          	$news=$data['huodong_img'];
          	
          }else{
          	$news=$news;
          }
          
          
        $_update['huodong_title'] = I('post.art_title');  //活动名称
        $_update['huodong_img'] = $news; //活动图片
        $_update['huodong_money'] = I('post.art_from'); //活动价格
        $_update['huodong_ren']=I('post.art_ren'); //活动发布人
        $_update['time']=I('post.time');  //活动时间
        $_update['didian']=I('post.didian');  //活动地点
        $_update['type']=I('post.type0'); //普通会员是否参加  参加为  1  不参加为   null
        $_update['type1']=I('post.type1');
        $_update['type2']=I('post.type2');
        $_update['type3']=I('post.type3');
        $_update['type4']=I('post.type4');
        $_update['date'] =date('Y-m-d H:i:s',time()); //发布时间
        $_update['text']=I('post.art_content');  //活动详情
        $_update['renshu']=I('post.renshu');
        $_update['gong']=I('post.gong_id');
       

        $where['id']=I('post.ids');
      
        $rs=M('huodong')->where($where)->save($_update);

        if($rs){
            $this->success('编辑成功',U('NewsManagement/index'),1);
        }else{
            $this->error('编辑失败',U('NewsManagement/index'),1);
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
            $this->success('批量删除成功',U('NewsManagement/index'));
        }else{
            $this->error('批量删除失败',U('NewsManagement/index'));
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