<?php
namespace Admin\Controller;
use Think\Controller;

class PointController extends CommController {

	//商品列表
    public function goodsList(){

        $Article = M('point_goods');
        $where['cate_p_id']=18;
        $catep=M("cate")->where($where)->getField('cate_id',true);
        $cate=M("cate")->where($where)->select();
        $this->cate=$cate;
        $select1=I("select1");
        $this->select1 = $select1;
        $select2=I("select2");
        $this->select2 = $select2;
        $ming=I("ming");
        $this->ming = $ming;
        $tt = array();
        if(empty($select1)&& empty($ming)){
            $tt['art_cate_id'] = array('in',$catep);
        }else if(!empty($select1)){
            $tt['art_cate_id'] = $select1;
        }elseif(!empty($ming)){
            $tt['product_title']=array('like','%'.$ming.'%');
        }
        if(!empty($select2)){
            if($select2=='2'){
                $tt['type'] = 0;
            }else{
                $tt['type'] = 1;
            }

        }



        $count = $Article -> where($tt) -> count();
        $Page = new \Think\Page($count,10);
        $show = $Page -> show();
        //第一页
        $products = $Article -> where($tt) -> order('art_date desc') -> limit($Page->firstRow.','.$Page->listRows) -> select();
        foreach($products as $k=>$v){
            $tt=M("cate")->where("cate_id= '".$v['art_cate_id']."'")->find();
            $products[$k]['fen']=$tt['cate_name'];
        }

        $this->assign('list',$products);

        $this->assign('page',$show);

        $this->display();

    }
    //订单列表
    public function orderList(){
        $Brand = M('dingdan_point');
        $tt = array();

        if(isset($_GET['consignee'])){
            $tt['consignee'] = array('like','%'.$_GET['consignee'].'%');
            $this->consignee = $_GET['consignee'];
        }

        if(isset($_GET['mobile'])){
            $tt['mobile'] = array('like','%'.$_GET['mobile'].'%');
            $this->mobile = $_GET['mobile'];
        }

        if(isset($_GET['paysn'])){
            $tt['paysn'] = array('like','%'.$_GET['paysn'].'%');
            $this->paysn = $_GET['paysn'];
        }

        $count = $Brand ->where($tt)-> count();
        $Page = new \Think\Page($count, 20);
        $show = $Page -> show();

        $brandList = $Brand->where($tt) ->order("time desc")-> limit($Page->firstRow.','.$Page->listRows) -> select();
        foreach($brandList as $k=>$v){
            $name=M("address")->where("id='".$v['addressid']."'")->find();
            $brandList[$k]['dianhua']=$name['dian'];
            $brandList[$k]['username']=$name['name'];
            $mober=M("user")->where("id='".$v['uid']."'")->find();
            $brandList[$k]['huiyuan']=$mober['type'];
//            $store=M("article")->where("art_id='".$v['store_id']."'")->find();
//            $brandList[$k]['fuwu']=$store['fu_id'];
        }


        $this -> assign('brand',$brandList);
        $this -> assign('page',$show);
        $this -> display();

    }

    //物流发货
    public function orderList_fh(){
        $Brand = M('dingdan_point');
        $catep=M("cate")->where("cate_p_id=18")->getField('cate_id',true);
        $tt = array();
        $tt['ster'] = 1;

        if(isset($_GET['consignee'])){
            $tt['consignee'] = array('like','%'.$_GET['consignee'].'%');
            $this->consignee = $_GET['consignee'];
        }

        if(isset($_GET['mobile'])){
            $tt['mobile'] = array('like','%'.$_GET['mobile'].'%');
            $this->mobile = $_GET['mobile'];
        }

        if(isset($_GET['paysn'])){
            $tt['paysn'] = array('like','%'.$_GET['paysn'].'%');
            $this->paysn = $_GET['paysn'];
        }

        $count = $Brand ->where($tt)-> count();
        $Page = new \Think\Page($count,20);
        $show = $Page -> show();

        $brandList = $Brand->where($tt) ->order("time desc")-> limit($Page->firstRow.','.$Page->listRows) -> select();
        foreach($brandList as $k=>$v){
            $name=M("address")->where("id='".$v['addressid']."'")->find();
            $brandList[$k]['dianhua']=$name['dian'];
            $brandList[$k]['username']=$name['name'];
            $mober=M("user")->where("id='".$v['uid']."'")->find();
            $brandList[$k]['huiyuan']=$mober['type'];
        }


        $this -> assign('brand',$brandList);
        $this -> assign('page',$show);
        $this -> display();

    }

    //物流发货
    public function deliver(){
        $id=I("id");
        $info=M("dingdan_point")->where("id='".$id."'")->find();
        $this->info=$info;

        $this->display();
    }

    public function deliverDo(){
        $post = $_POST;
        $post['ster'] = 2;
        $res=M("dingdan_point")->where(array('id'=>$post['id']))->save($post);
        if($res){
            $this->ajaxReturn(array('state'=>1,'info'=>'发货成功'));
        }else{
            $this->ajaxReturn(array('state'=>0,'info'=>'发货失败'));
        }
    }


    /*
   *  添加商品
   */
    public function add_che(){

        //分类
        $where['cate_p_id']=18;
        $res = M('cate')->where($where)->order('cate_order asc')->select();
        //实例化分类树类
        $treeOb=new TreeController();
        $treeOb->createtree($res);
        $list2=$treeOb->tree;
        //dump($list2);
        $this->assign('list2',$list2);

        //车产品供应商
        $gong=M("gongying")->where("cate_id=18")->select();
        $this->gong=$gong;
        $this->display();
    }

    public function cheDetVideo(){
        $this->display();
    }

    // 阿里云上传视频
    public function ossVideo(){
        set_time_limit(0);
        ini_set('memory_limit', '512M');
        ini_set('magic_quotes_runtime', 0);

        import("Org.Util.aliyunOss.samples.Common");
        $bucket = \Common::getBucketName(); // 获取存储空间名称
        $ossClient = \Common::getOssClient();
        if (is_null($ossClient)) exit(1);

        $file = $_FILES['file'];
        if (!isset($file) || empty($file)) {
            die('非法上传操作！');
        }

        $ext = substr($file['name'], stripos($file['name'], '.'));

        if (!in_array($ext, ['.mp4', '.swf', '.flv', '.wav', '.ram', '.wma'])) {
            die('视频文件格式不正确！');
        }

        $object = uniqid() . $ext; // 想要保存文件的名称
        $filePath = $file['tmp_name']; // 文件路径，必须是本地的。

        $res = $ossClient->uploadFile($bucket, $object, $filePath, []); // 上传文件
        if ($res) {
            $ossClient->putObjectAcl($bucket, $object, 'public-read'); // 文件的权限设置成public-read
            $url = $res['info']['url'];
            echo '<script>opener.document.getElementById("' . I('action') . '").value="' . $url . '";window.opener=null;self.close(); </script>';
            die();
        }else{
            die('抱歉，上传失败，请重新上传！');
        }
    }

    //添加车
    public function addcl_che(){
        $data=I("post.");
// 	    dump($data);die;
//        print_r($data);exit;
        $model = M('point_goods');
        $products=I("images");
        $banns1=I("image");
        $banns2=I("imag");
        $banns3=I("imagester");
        $banns4=I("imagestu");
        $data['art_img_url'] = $products;
        $data['art_img_url_s']=$banns1;
        $data['art_title']=$banns2;
        $data['art_keywords']=$banns3;
        $data['art_description']=$banns4;
        $data['product_title'] = I('post.art_title');//产品名称
        $data['has_spec'] = I('post.has_spec');
        $data['jiage']=I("art_jiage");  //产品商城价格
        $data['huiyuan']=I("huiyuan");  //产品会员价格  111
        $data['num']=I("art_kucun");  //产品库存
        $data['type']=I("type"); // 产品是否上下架  1 上架  0  下架
        $data['guige']=I("art_shuo"); //产品说明
        $data['youdi']=I("art_you"); //邮费
        $data['art_cate_id'] = I('post.art_cate_id'); //所属
        $data['art_content'] = I('post.art_content');//纵览
        $data['content'] = I('post.content'); //费用说明
        $data['cont'] = I('post.cont'); //行程
        $data['shipin'] = I('post.shipin'); //Banner视频
        $data['xiang_shipin'] = I('post.xiang_shipin'); //详情视频111
//        $data['gong'] = I('post.gong_id'); //供应商
//        $data['jinjia'] = I('post.jinjia'); //进价 111
//        $data['fuwu'] = I('post.fuwu'); //产品是否有服务商  1是  0 不是 111
//        $data['fu_id'] = I('post.fu_id'); //服务商 111
//        $data['f_money'] = I('post.f_money'); //服务商费用111
        $data['art_date'] =date('Y-m-d H:i:s',time());
        $res=$model->add($data);


        if($res){

            /****处理产品规格值*****/
            if(!empty($data['gui1']) && !empty($data['point1']) && !empty($data['point_shop1']) && !empty($data['num1'])){
                $arr=array(
                    'guige'=>$data['gui1'],
                    'num'=>$data['num1'],
                    'point' => $data['point1'],
                    'point_shop' => $data['point_shop1'],
                    'flag' => 'point',
                    'cid'=>$res
                );

                M("guige_point")->add($arr);
            }

            if(!empty($data['gui2']) && !empty($data['point2']) && !empty($data['point_shop2']) && !empty($data['num2'])){
                $arr=array(
                    'guige'=>$data['gui2'],
                    'num'=>$data['num2'],
                    'point' => $data['point2'],
                    'point_shop' => $data['point_shop2'],
                    'flag' => 'point',
                    'cid'=>$res
                );

                M("guige_point")->add($arr);
            }


            if(!empty($data['gui3']) && !empty($data['point3']) && !empty($data['point_shop3']) && !empty($data['num3'])){
                $arr=array(
                    'guige'=>$data['gui3'],
                    'num'=>$data['num3'],
                    'point' => $data['point3'],
                    'point_shop' => $data['point_shop3'],
                    'flag' => 'point',
                    'cid'=>$res
                );

                M("guige_point")->add($arr);
            }


            if(!empty($data['gui4']) && !empty($data['point4']) && !empty($data['point_shop4']) && !empty($data['num4'])){
                $arr=array(
                    'guige'=>$data['gui4'],
                    'num'=>$data['num4'],
                    'point' => $data['point4'],
                    'point_shop' => $data['point_shop4'],
                    'flag' => 'point',
                    'cid'=>$res
                );

                M("guige_point")->add($arr);
            }


            if(!empty($data['gui5']) && !empty($data['point5']) && !empty($data['point_shop5']) && !empty($data['num5'])){
                $arr=array(
                    'guige'=>$data['gui5'],
                    'num'=>$data['num5'],
                    'point' => $data['point5'],
                    'point_shop' => $data['point_shop5'],
                    'flag' => 'point',
                    'cid'=>$res
                );

                M("guige_point")->add($arr);
            }

            if(!empty($data['gui6']) && !empty($data['point6']) && !empty($data['point_shop6']) && !empty($data['num6'])){
                $arr=array(
                    'guige'=>$data['gui6'],
                    'num'=>$data['num6'],
                    'point' => $data['point6'],
                    'point_shop' => $data['point_shop6'],
                    'flag' => 'point',
                    'cid'=>$res
                );

                M("guige_point")->add($arr);
            }

            if(!empty($data['gui7']) && !empty($data['point7']) && !empty($data['point_shop7']) && !empty($data['num7'])){
                $arr=array(
                    'guige'=>$data['gui7'],
                    'num'=>$data['num7'],
                    'point' => $data['point7'],
                    'point_shop' => $data['point_shop7'],
                    'flag' => 'point',
                    'cid'=>$res
                );

                M("guige_point")->add($arr);
            }


            if(!empty($data['gui8']) && !empty($data['point8']) && !empty($data['point_shop8']) && !empty($data['num8'])){
                $arr=array(
                    'guige'=>$data['gui8'],
                    'num'=>$data['num8'],
                    'point' => $data['point8'],
                    'point_shop' => $data['point_shop8'],
                    'flag' => 'point',
                    'cid'=>$res
                );

                M("guige_point")->add($arr);
            }


            $this->success('添加成功',U('Point/goodsList'),1);
        }else{
            $this->error('添加失败',U('Point/goodsList'),1);
        }


    }

    public function edit_che(){


        $obj=M('point_goods')->where(array('art_id'=>I('get.art_id')))->find();

        $this->assign('obj',$obj);

        //分类显示
        $where['cate_p_id']=18;
        $res = M('cate')->where($where)->order('cate_order asc')->select();
        //实例化分类树类
        $treeOb=new TreeController();
        $treeOb->createtree($res);
        $list2=$treeOb->tree;
        //dump($list2);
        $this->assign('list2',$list2);

        //规格
        $mygui=M("guige_point")->where(array('cid'=>I('get.art_id'),'flag'=>'point'))->select();
        $conter=count($mygui);
        $this->chang=$conter;
        $this->assign('guige',$mygui);

        //车供应商
        $gong=M("gongying")->where("cate_id=18")->select();
        $this->gong=$gong;
        $this->display();

    }

    //车编辑
    public function update_che(){
        $data=I("post.");


        $model = M('article');
        $products=I("images");
        $banns1=I("image");
        $banns2=I("imag");
        $banns3=I("imagester");
        $banns4=I("imagestu");
        $data['art_img_url'] = $products;
        $data['art_img_url_s']=$banns1;
        $data['art_title']=$banns2;
        $data['art_keywords']=$banns3;
        $data['art_description']=$banns4;
        $data['product_title'] = I('post.art_title');//产品名称
        $data['has_spec'] = I('post.has_spec');
        $data['jiage']=I("art_jiage");  //产品价格
        $data['huiyuan']=I("huiyuan");  //产品会员价格  111
        $data['num']=I("art_kucun");  //产品库存
        $data['guige']=I("art_shuo"); //产品说明
        $data['youdi']=I("art_you"); //邮费
        $data['type']=I("type"); // 产品是否上下架  1 上架  0  下架
        $data['art_cate_id'] = I('post.art_cate_id'); //所属
        $data['art_content'] = I('post.art_content');//纵览
        $data['content'] = I('post.content'); //费用说明
        $data['cont'] = I('post.cont'); //行程
        $data['shipin'] = I('post.shipin'); //视频
        $data['gong']=I('post.gong_id'); //供应商
        $data['art_date']      =date('Y-m-d H:i:s',time());
//        $data['jinjia'] = I('post.jinjia'); //进价 111
//        $data['fuwu'] = I('post.fuwu'); //产品是否有服务商  1是  0 不是 111
//        $data['fu_id'] = I('post.fu_id'); //服务商 111
//        $data['f_money'] = I('post.f_money'); //服务商费用111
        $data['xiang_shipin'] = I('post.xiang_shipin'); //详情视频111
        $where['art_id']=I('post.art_id');

        $res=I('post.art_id');
        $rs=M('point_goods')->where($where)->save($data);

        if($rs){
            /****处理产品规格值*****/

            $guige=M("guige_point")->where("cid='".$res."'")->delete();
            if(!empty($data['gui1']) && !empty($data['point1']) && !empty($data['point_shop1']) && !empty($data['num1'])){
                $arr=array(
                    'guige'=>$data['gui1'],
                    'num'=>$data['num1'],
                    'point' => $data['point1'],
                    'point_shop' => $data['point_shop1'],
                    'flag' => 'point',
                    'cid'=>$res
                );

                M("guige_point")->add($arr);
            }

            if(!empty($data['gui2']) && !empty($data['point2']) && !empty($data['point_shop2']) && !empty($data['num2'])){
                $arr=array(
                    'guige'=>$data['gui2'],
                    'num'=>$data['num2'],
                    'point' => $data['point2'],
                    'point_shop' => $data['point_shop2'],
                    'flag' => 'point',
                    'cid'=>$res
                );

                M("guige_point")->add($arr);
            }


            if(!empty($data['gui3']) && !empty($data['point3']) && !empty($data['point_shop3']) && !empty($data['num3'])){
                $arr=array(
                    'guige'=>$data['gui3'],
                    'num'=>$data['num3'],
                    'point' => $data['point3'],
                    'point_shop' => $data['point_shop3'],
                    'flag' => 'point',
                    'cid'=>$res
                );

                M("guige_point")->add($arr);
            }


            if(!empty($data['gui4']) && !empty($data['point4']) && !empty($data['point_shop4']) && !empty($data['num4'])){
                $arr=array(
                    'guige'=>$data['gui4'],
                    'num'=>$data['num4'],
                    'point' => $data['point4'],
                    'point_shop' => $data['point_shop4'],
                    'flag' => 'point',
                    'cid'=>$res
                );

                M("guige_point")->add($arr);
            }


            if(!empty($data['gui5']) && !empty($data['point5']) && !empty($data['point_shop5']) && !empty($data['num5'])){
                $arr=array(
                    'guige'=>$data['gui5'],
                    'num'=>$data['num5'],
                    'point' => $data['point5'],
                    'point_shop' => $data['point_shop5'],
                    'flag' => 'point',
                    'cid'=>$res
                );

                M("guige_point")->add($arr);
            }

            if(!empty($data['gui6']) && !empty($data['point6']) && !empty($data['point_shop6']) && !empty($data['num6'])){
                $arr=array(
                    'guige'=>$data['gui6'],
                    'num'=>$data['num6'],
                    'point' => $data['point6'],
                    'point_shop' => $data['point_shop6'],
                    'flag' => 'point',
                    'cid'=>$res
                );

                M("guige_point")->add($arr);
            }

            if(!empty($data['gui7']) && !empty($data['point7']) && !empty($data['point_shop7']) && !empty($data['num7'])){
                $arr=array(
                    'guige'=>$data['gui7'],
                    'num'=>$data['num7'],
                    'point' => $data['point7'],
                    'point_shop' => $data['point_shop7'],
                    'flag' => 'point',
                    'cid'=>$res
                );

                M("guige_point")->add($arr);
            }


            if(!empty($data['gui8']) && !empty($data['point8']) && !empty($data['point_shop8']) && !empty($data['num8'])){
                $arr=array(
                    'guige'=>$data['gui8'],
                    'num'=>$data['num8'],
                    'point' => $data['point8'],
                    'point_shop' => $data['point_shop8'],
                    'flag' => 'point',
                    'cid'=>$res
                );

                M("guige_point")->add($arr);
            }


            $this->success('编辑成功',U('Point/goodsList'),1);

        }else{
            $this->error('编辑失败',U('Point/goodsList'),1);
        }


    }
    /*
    *删除
    */
    public function delete(){
        $rs=M('point_goods')->where(array('art_id'=>I('get.art_id')))->delete();
        if($rs){
            $this->success('删除成功',U('Point/goodsList'));
        }else{
            $this->error();
        }
    }
}