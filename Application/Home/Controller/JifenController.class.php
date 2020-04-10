<?php
namespace Home\Controller;
use Think\Controller;
class JifenController extends PublicController {
	
	public function _initialize(){
          
        $user=$_SESSION['user'];
		  if(empty($user)){
		     	$this->weixindeng();
		  }
		  
		$hezuo=$this->hezuo();
		$this->hz=$hezuo;  
    }

	//积分商城
	public function jifenshop(){
        $uid=$_SESSION['user']['id'];
        $user=M("user")->where("id='".$uid."'")->find();
        //积分商品
        $pointGoods = M('point_goods')->order('art_id desc')->select();
        foreach ($pointGoods as $k => $v){
            if($v['has_spec'] == 0){ //无规格
                $pointGoods[$k]['price'] = $v['huiyuan'];
                $pointGoods[$k]['price_shop'] = $v['jiage'];
            }else{ //有规格
                $map['cid'] = $v['art_id'];
                $map['flag'] = 'point';
                $specList = M('guige_point')->where($map)->order('id')->select();
                $pointGoods[$k]['price'] = $specList[0]['point'];
                $pointGoods[$k]['price_shop'] = $specList[0]['point_shop'];
            }
        }
        $this->pointGoods = $pointGoods;
        $this->user=$user;
        $this->display();
	}

	public function commit_pj(){
	    if(!IS_AJAX){
	        echo '非法请求!';exit;
        }
	    $input = I('post.');
//        print_r($input);exit;
	    if(empty($input['content'])){
            $this->ajaxReturn(array('st'=>0,'info'=>'请输入评价内容!'));
        }

	    $input['add_time'] = time();
	    $rs = M('comment_point')->add($input);
	    $rs2 = M('dingdan_point')->where(array('id'=>$input['order_id']))->setField('ster',4);
	    if($rs && $rs2){
	        $this->ajaxReturn(array('st'=>1,'info'=>'评价成功'));
        }else{
            $this->ajaxReturn(array('st'=>0,'info'=>'评价失败'));
        }
    }

	//积分商品详情
    public function jifenInfo($id){
	    $info = M('point_goods')->where(array('art_id'=>$id))->find();
	    $banner = array();
	    if(!empty($info['art_img_url_s'])){
	        array_push($banner,$info['art_img_url_s']);
        }
        if(!empty($info['art_title'])){
            array_push($banner,$info['art_title']);
        }
        if(!empty($info['art_keywords'])){
            array_push($banner,$info['art_keywords']);
        }
        if(!empty($info['art_description'])){
            array_push($banner,$info['art_description']);
        }
        //规格
        $map['flag'] = 'point';
	    $map['cid'] = $info['art_id'];
        $guige = M('guige_point')->where($map)->order('id')->select();

        if($info['has_spec'] == 0){ //无规格
            $info['price'] = $info['huiyuan'];
            $info['price_shop'] = $info['jiage'];
        }else{ //有规格
            $map['cid'] = $info['art_id'];
            $map['flag'] = 'point';
            $specList = M('guige_point')->where($map)->order('id')->select();
            $info['price'] = $specList[0]['point'];
            $info['price_shop'] = $specList[0]['point_shop'];
        }

        $this->guige = $guige;
	    $this->info = $info;
        $this->banner = $banner;
	    $this->display();
    }
    //确认订单
    public function submitOrder(){
//	    print_r($_SESSION);exit;
        if(isset($_GET['spec_id'])){ //有规格
            $specInfo = M('guige_point')->where(array('id'=>$_GET['spec_id']))->find();
            $goodsInfo = M('point_goods')->where(array('art_id'=>$specInfo['cid']))->find();
            $goodsInfo['sel_price'] = $specInfo['point'];
            $goodsInfo['sel_spec'] = $specInfo['guige'];
        }
        if(isset($_GET['goods_id'])){ //无规格
            $goodsInfo = M('point_goods')->where(array('art_id'=>$_GET['goods_id']))->find();
            $goodsInfo['sel_price'] = $goodsInfo['huiyuan'];
            $goodsInfo['sel_spec'] = '无';
        }
        $map['mo'] = 1;
        $map['uid'] = $_SESSION['user']['id'];
        $address = M('address')->where($map)->find();
        $this->address = $address;
        $this->goodsInfo = $goodsInfo;
        $this->display();
    }
    //兑换处理
    public function dhDo(){
//        $this->ajaxReturn(array('state'=>0,'info'=>'您的积分不足！'));
	    $post = $_POST;
	    $goodsInfo = M('point_goods')->where(array('art_id'=>$post['goods_id']))->find();
	    $total_point = $post['sel_num']*$post['sel_price'];
	    if(empty($_SESSION['user']['id'])){
	        $this->ajaxReturn(array('state'=>0,'info'=>'您还没有登陆！'));
        }
        $userInfo = M('user')->where(array('id'=>$_SESSION['user']['id']))->find();
	    if($total_point > $userInfo['jifen']){
            $this->ajaxReturn(array('state'=>0,'info'=>'您的积分不足！','zong'=>$total_point,'jifen'=>$userInfo['jifen']));
        }

        $mapAddr['uid'] = $_SESSION['user']['id'];
	    $mapAddr['mo'] = 1;
        $findAddr = M('address')->where($mapAddr)->find();
        if(!$findAddr){
            $this->ajaxReturn(array('state'=>0,'info'=>'您还没有选择收货地址！'));
        }

        $paysn=makePaySn($post['goods_id']);
//        $array['store_id']=$id;
        $array['paysn']=$paysn;
//        $array['store_id']=$store['art_id'];  //产品ID
        $array['name']=$goodsInfo['product_title'];  //产品名称
        $array['img']=$goodsInfo['art_img_url'];  //产品图片
        $array['time']=date('Y-m-d H:i:s',time());  //订单生成时间
        $array['zhifu_time']=date('Y-m-d H:i:s',time());  //订单生成时间
        $array['uid']=$_SESSION['user']['id'];    //userid
        $array['ster']='1';   //订单状态
        $array['zong']=$post['sel_num']*$post['sel_price'];
        $array['num']=$post['sel_num'];
        $array['spec'] = $post['sel_spec'];//商品规格
        $array['goods_id'] = $post['goods_id'];
        $array['consignee'] = $findAddr['name'];
        $array['mobile'] = $findAddr['dian'];
        $array['address'] = $findAddr['address'].$findAddr['xiangqing'];
        $rs=M("dingdan_point")->add($array);

        $rs2 = M('user')->where(array('id'=>$_SESSION['user']['id']))->setDec('jifen',$post['sel_price']);
        if($rs && $rs2){
            $this->ajaxReturn(array('state'=>1,'info'=>'恭喜您，兑换成功'));
        }

    }

    //兑换成功页面
    public function success(){
	    $this->display();
    }
		
		public function jifenbuzu(){
			$zong=I("zong");
			$jifen=I("jifen");
			if(!empty($zong)){
				$this->zong=$zong;
			}else{
				$this->zong='0';
			}
			if(!empty($jifen)){
				$this->jifen=$jifen;
			}else{
				$this->jifen='0';
			}
			$this->display();

		}

    //我的订单
    public function myOrder(){
        $this->display();
    }

    //我的订单
    public function myOrderAjax(){

	    $post = $_POST;

	    if($post['os'] != -1){
            $map['ster'] = $post['os'];
        }

        $map['uid'] = $_SESSION['user']['id'];
        $list = M('dingdan_point')->where($map)->select();
        $this->list = $list;
        $this->display();
    }

    //确认收货
    public function commitReceive($id){
	    $rs = M('dingdan_point')->where(array('id'=>$id))->setField('ster',3);
	    if($rs){
	        $this->ajaxReturn(array('state'=>1,'info'=>'操作成功'));
        }else{
            $this->ajaxReturn(array('state'=>0,'info'=>'操作失败'));
        }
    }
	
	
   
    
}