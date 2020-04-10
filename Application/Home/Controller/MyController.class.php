<?php
namespace Home\Controller;
use Think\Controller;
class MyController extends PublicController {
	
	public function _initialize(){
          	
        $user=$_SESSION['user'];
		  if(empty($user)){
		    $this->weixindeng();
		  }
		  $hezuo=$this->hezuo();
		  $this->hz=$hezuo;
    }
    
     //地址列表
    public function mapdemo(){
    	if (isset($_SERVER['HTTP_REFERER'])) {
    		$url=$_SERVER['HTTP_REFERER'];
            cookie('url',$url,3600); 
         } 
         
    	$uid=$_SESSION['user']['id'];
    	$address=M("address")->where("uid='".$uid."'")->select();
    	$this->address=$address;
    	$this->uid=$uid;
    	$this->display();
    }
     
     //新增地址
     public function addmap(){
     	$uid=$_SESSION['user']['id'];
     	$this->uid=$uid;
    	$this->display();
    }
    
    public function add(){
    	$data=I("post.");
    	$uid=$data['uid'];
    	$sa=M("address")->where("uid='".$uid."' and mo=1")->find();
    	if(!empty($sa)){
    		$save=M("address")->where("uid='".$uid."' and mo=1")->save(array('mo'=>'0'));
    	}
    	$arr['uid']=$data['uid'];
    	$arr['name']=$data['name'];
    	$arr['dian']=$data['dian'];
    	$arr['address']=$data['address'];
    	$arr['xiangqing']=$data['xiangqing'];
    	$arr['mo']='1';
    	$res=M("address")->add($arr);
    	if($res){
    		$url = $_COOKIE['url'];
    		
    		echo '<script>window.location.href="'.$url.'"; </script>';
    	}
    	
    }
    
    //设置默认
    public function mo(){
    	$id=I("ids");
    	$uid=I("uid");
    	$sa=M("address")->where("uid='".$uid."' and mo=1")->find();
    	if(!empty($sa)){
    		$save=M("address")->where("uid='".$uid."' and mo=1")->save(array('mo'=>'0'));
    	}
    	
    	
    	$tt=M("address")->where("id='".$id."'")->save(array('mo'=>'1'));
    	if($tt){
    		$this->ajaxReturn(array('state'=>'1','msg'=>'更改成功'));
    	}else{
    		$this->ajaxReturn(array('state'=>'0','msg'=>'更改失败'));
    	}
    	
    }
    
    public function edit(){
    	$id=I("id");
        $temp=M("address")->where("id='".$id."'")->find();
        $this->temp=$temp;
        $this->display();
    }
    
    public function xiu(){
    	$data=I("post.");
    	$id=$data['ids'];
    	$arr['name']=$data['name'];
    	$arr['dian']=$data['dian'];
    	$arr['address']=$data['address'];
    	$arr['xiangqing']=$data['xiangqing'];
    	$save=M("address")->where("id='".$id."'")->save($arr);
    	if($save){
    		$url = $_COOKIE['url'];
    		
    		echo '<script>window.location.href="'.$url.'"; </script>';
    	}
    }
     //删除地址
    public function shan(){
    	$id=I("ids");
    	$dele=M("address")->where("id='".$id."'")->delete();
    	if($dele){
    		$this->ajaxReturn(array('state'=>'1','msg'=>'删除成功'));
    	}else{
    		$this->ajaxReturn(array('state'=>'0','msg'=>'删除失败'));
    	}
    }
    
     //个人中心
    public function user(){
    	$uid=$_SESSION['user']['id'];
    	$user=M("user")->where("id='".$uid."'")->find();
    	$this->user=$user;
    	$this->display();
    }
    
    
    //常见问题
    public function longtime(){
    	$temp=M("wenti")->where()->order('time desc')->select();
    	$this->temp=$temp;
        
    	
    	$this->display();
    }
    
   
   
    
    
     //完善会员信息
    public function userindex(){
    	$this->display();
    }
    
     //完善个人信息
    public function useritem(){
    	$uid=$_SESSION['user']['id'];
    	$user=M("user")->where("id='".$uid."'")->find();
    	$this->user=$user;
    	if($user['type']==1){
    		 //是会员
    		 $this->display('My/userindex');
    	}else{ 
    		//不是会员
    		$this->display('My/useritem');
    	}
    	
    }
    
     //我的订单
    public function myoder(){
    	$uid=$_SESSION['user']['id'];
    	
    	$tyy=I("id");
    	$this->tyy=$tyy;
    	//代付款
    	
    	$where['uid']=$uid;
    	$where['ster']=0;
    	$where['tuikuan']=0;
      	$where['h_id']=array('eq','null');
      	$temp=M("dingdan")->where($where)->order('id desc')->select();
      	foreach($temp as $k=>$v){
      		$tt=M("article")->where("art_id='".$v['store_id']."'")->find();
      		$temp[$k]['shuoming']=$tt['guige'];
      	}
      	 
    	$this->temp=$temp;
    	
    	//代发货
    	$fa['uid']=$uid;
    	$fa['ster']=1;
    	$fa['tuikuan']=0;
    	$fa['h_id']=array('eq','null');
    	$tem=M("dingdan")->where($fa)->order('id desc')->select();
    	foreach($tem as $k=>$v){
      		$tt=M("article")->where("art_id='".$v['store_id']."'")->find();
      		$tem[$k]['shuoming']=$tt['guige'];
      	}
    	$this->tem=$tem;
    	//待收货
    	$shou['uid']=$uid;
    	$shou['ster']=2;
    	$shou['tuikuan']=0;
    	$shou['h_id']=array('eq','null');
    	$te=M("dingdan")->where($shou)->order('id desc')->select();
    	foreach($te as $k=>$v){
      		$tt=M("article")->where("art_id='".$v['store_id']."'")->find();
      		$te[$k]['shuoming']=$tt['guige'];
      	}
    	$this->te=$te;
    	
    	
    	//待评价
    	$sh['uid']=$uid;
    	$sh['ster']=3;
    	$sh['tuikuan']=0;
    	$sh['h_id']=array('eq','null');
    	$ping=M("dingdan")->where($sh)->order('id desc')->select();
    	foreach($ping as $k=>$v){
      		$tt=M("article")->where("art_id='".$v['store_id']."'")->find();
      		$ping[$k]['shuoming']=$tt['guige'];
      	}
    	$this->ping=$ping;
//  	dump($ping);die;
    	
    	
    	
    	
    	
    	//quanbu
    	
     	$tu['uid']=$uid;
       
  	    $tui=M("dingdan")->where($tu)->order('id desc')->select();
        $this->quanbu=$tui;
    	
    	
    
    	$this->display();
    }
    
    // 订单详情
    public function orderdet(){
        $id = I('id/d');
        empty($id) && die('参数错误！');

        $uid = $_SESSION['user']['id'];
        $map['id'] = ['eq', $id];
        $map['uid'] = ['eq', $uid];
        $res = M('Dingdan')->where($map)->find();
        $res['article'] = M('Article')->where(['art_id' => $res['store_id']])->find();
        if ($res['type'] != 1) {
            $res['address'] = M('Address')->where(['id' => $res['addressid']])->find();
        }
        $money_you = M('Guige')->where(['id' => $res['gid']])->getField('money_you');
        $res['money_you'] = $money_you ? $money_you : '0.00';
        $res['guige'] = !empty($res['guige']) ? $res['guige'] : '暂无规格';
        $res['zjg'] = $res['jiage'] * $res['num'];

        $flag = 0; // 未知状态
        if ($res['tuikuan'] == 0) {
            $res['ster'] == 0 && $flag = 1; // 代付款
            $res['ster'] == 1 && $flag = 2; // 代发货
            $res['ster'] == 2 && $flag = 3; // 待收货
            $res['ster'] == 3 && $flag = 4; // 待评价
            $res['ster'] == 4 && $flag = 5; // 已完成
        }

        $res['tuikuan'] == 1 && $flag = 6; // 退款中
        $res['tuikuan'] == 2 && $flag = 7; // 已退款

        $res['wuliu_time'] = !empty($res['wuliu_time']) ? $res['wuliu_time'] : '已发货';
        $res['wan_time'] = !empty($res['wan_time']) ? $res['wan_time'] : '已完成';

        $res['flag'] = $flag;
        $this->res = $res;
        $this->display();
    }
    
    public function myodertui(){
    	$uid=$_SESSION['user']['id'];
    	$tu['uid']=$uid;
        $tu['tuikuan']=array('neq','0');
  	    $tui=M("dingdan")->where($tu)->order('tuikuan_ktime desc')->select();
        $tui = arr_sort($tui, 'tuikuan');
        $this->tui = $tui;
        $this->display();
    }
    
    
    
     public function shangchuan(){
    	if(!IS_AJAX){
    		$this->error('页面不存在');
    	}
    	$info=uploadAJAX(10,'brand');
    	
    	$this->ajaxReturn($info);
    }
    
    public function item(){
    	$data=I("post.");
    	
    	if(!empty($data['img'])){
    		$arr['img']=$data['img'];
    	}
    	$uid=$data['uid'];
    	$arr['username']=$data['name'];
    	$arr['radio']=$data['radio'];
    	$arr['photo']=$data['photo'];
    	$arr['shenfen']=$data['shenfen'];
    	$arr['shengri']=$data['shengri'];
    	$res=M("user")->where("id='".$uid."'")->save($arr);
    	if($res){
    	  $this->redirect('Home/My/user');
    	}else{
    		$this->redirect('Home/My/user');
    	}
    }
    
    
    
    
    
    
    public function item_huiyuan(){
    	$data=I("post.");
    	 
    	
    	$uid=$data['uid'];
    	$arr['username']=$data['name'];
    	$arr['radio']=$data['radio'];
    	$arr['nian']=$data['nian'];
    	$arr['photo']=$data['photo'];
    	$arr['mabiao']=$data['mabiao'];
    	$arr['shengri']=$data['shengri'];
    	$arr['che']=$data['che'];
    	$arr['che_xing']=$data['che_xing'];
    	$arr['che_ling']=$data['che_ling'];
    	$arr['che_se']=$data['che_se'];
    	$res=M("user")->where("id='".$uid."'")->save($arr);
    	if($res){
    	  $this->redirect('Home/My/user');
    	}else{
    		 $this->redirect('Home/My/user');
    	}
    }
    
    
    public function quxiao(){
    	$id=I("id");
    	$res=M("dingdan")->where("id='".$id."'")->delete();
    	if($res){
    		$this->ajaxReturn(array('state'=>'1','msg'=>'取消成功'));
    	}else{
    		$this->ajaxReturn(array('state'=>'0','msg'=>'网络错误,稍后再试'));
    	}
    }

    // 退款页面
    public function tk(){
        $id=I("id");
        $res = M("dingdan")->where("id='".$id."'")->find();
        $this->res = $res;
        $this->gg = M('guige')->where(['id' => $res['gid']])->find();
        $this->display();
    }

    public function tuikuan(){
        $data = I('post.');

//         if (empty($data['yuanyin'])){
//             die("<script>alert('请选择退款原因！');history.go(-1); </script>");
//         }

        $upload = new \Think\Upload([
            'rootPath'  => './Uploads/tui/',
            'savePath'  => '',
            'maxSize'   => 19145728000,
            'exts'      => array('jpg', 'gif', 'png', 'jpeg')
        ]);

        $photo1 = $upload->uploadOne($_FILES['photo1']);
        if ($photo1) {
            $add_data['photo1'] = '/Uploads/tui/'. $photo1['savepath'] . $photo1['savename'];
        }
        $photo2 = $upload->uploadOne($_FILES['photo2']);
        if ($photo2) {
            $add_data['photo2'] = '/Uploads/tui/'. $photo2['savepath'] . $photo2['savename'];
        }
        $photo3 = $upload->uploadOne($_FILES['photo3']);
        if ($photo3) {
            $add_data['photo3'] = '/Uploads/tui/'. $photo3['savepath'] . $photo3['savename'];
        }

        $add_data['did'] = $data['id'];
        $add_data['uid'] = $_SESSION['user']['id'];
        $add_data['yuanyin'] = $data['yuanyin'];
        $add_data['shuoming'] = $data['shuoming'];
        $add_data['time'] = time();

        if (M("tuikuan")->add($add_data)) {
            $temp=M("dingdan")->where("id='".$data['id']."'")->find();
            if($temp['ster']==0){
                die("<script>alert('该订单未支付！');history.go(-1); </script>");
            }
            $arr['tuikuan']=1;
            $arr['tuikuan_ktime']=date('Y-m-d H:i:s',time());
            $res=M("dingdan")->where("id='".$data['id']."'")->save($arr);
            if($res){
                require './ThinkPHP/Library/Org/Util/Ucpaas.class.php';
                // 给管理员发送退款短信
                $ucpass =  new \Ucpaas([
                    'accountsid' => 'a2769b5baced349ab6c2833ef00a7f72',
                    'token' => '6b12b3514e6cb17794d0341c7b169ca2'
                ]);
                // 短信验证码（模板短信）
                $appId = "0441da62e64f47f6ae93d15ae54927e3";
                $users = M('users')->where(['user_id' => 1])->find();
                $ucpass->SendSms($appId, "397097", null, $users['phone'], '');

                die("<script>alert('申请成功，请等待审核');window.location.href='" . U('My/user') . "'; </script>");
            }else{
                 die("<script>alert('申请失败，请重试！');history.go(-1); </script>");
            }
        } else {
            die("<script>alert('请重试！');history.go(-1); </script>");
        }
    }
    
    // 撤销退款
    public function chexiao(){
    	$id=I("id");
    	$res=M("dingdan")->where("id='".$id."'")->save(array('tuikuan'=>0));
    	if($res){
             M('tuikuan')->where(['did' => $id])->delete();
    		$this->ajaxReturn(array('state'=>'1','msg'=>'撤销成功'));
    	}else{
    		$this->ajaxReturn(array('state'=>'0','msg'=>'撤销失败'));
    	}
    }
    
    public function shouhuo(){
    	$id=I("id");
    	$res=M("dingdan")->where("id='".$id."'")->save(array('ster'=>3));
    	if($res){
    		$this->ajaxReturn(array('state'=>'1','msg'=>'收货成功'));
    	}else{
    		$this->ajaxReturn(array('state'=>'0','msg'=>'收货失败'));
    	}
    }
    
    
    //物流
    public function wl(){
    	$id=I("id");
    	$temp=M("dingdan")->where("id='".$id."'")->find();
        if (empty($temp['wuliudan']) || empty($temp['wuliu_gongsi'])) {
            die("<script>alert('暂无物流公司和物流了单号信息！');history.go(-1); </script>");
        }
    	$this->temp=$temp;
     	$this->dan=$temp['wuliudan'];
     	$this->name=$temp['wuliu_gongsi'];
    	$this->display();
    }
    
    
    public function username(){
    	$temp=M("women")->where("id=1")->find();
    	$this->temp=$temp;
    	$this->display();
    }
	
   
    
}