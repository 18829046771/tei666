<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 16-2-22
 * Time: 上午10:30
 */

// 二维数组排序
function arr_sort($arrays, $sort_key, $sort_order = SORT_ASC, $sort_type = SORT_NUMERIC ){
    if(is_array($arrays)){
        foreach ($arrays as $array){
            if(is_array($array)){
                $key_arrays[] = $array[$sort_key];
            }else{
                return false;
            }
        }
    }else{
        return false;
    }
    array_multisort($key_arrays,$sort_order,$sort_type,$arrays);
    return $arrays;
}

function huodongType($val){
    $arr = [
        1 => '免费仅限会员参与',
        2 => '会员非会员均可参加',
        3 => '仅会员可参加'
    ];
    return $arr[$val];
}

// 根据cate_id获取分类
function getCate($cate_id){
    return M('cate')->where(['cate_id' => $cate_id])->find();
}

function getPage($count, $pagesize = 10) {
    $p = new \Think\Page($count, $pagesize);
    $p -> setConfig('prev', '上一页');
    $p -> setConfig('next', '下一页');
    return $p;
}

/**
 * 获取用户信息
 * @param $user_id_or_name  用户id 邮箱 手机 第三方id
 * @param int $type  类型 0 user_id查找 1 邮箱查找 2 手机查找 3 第三方唯一标识查找
 * @param string $oauth  第三方来源
 * @return mixed
 */
function get_user_info($user_id_or_name,$type = 0,$oauth=''){
	$map = array();
	if($type == 0)
		$map['user_id'] = $user_id_or_name;
	if($type == 1)
		$map['email'] = $user_id_or_name;
	if($type == 2)
		$map['mobile'] = $user_id_or_name;
	if($type == 3){
		$map['openid'] = $user_id_or_name;
		$map['oauth'] = $oauth;
	}
	$user = M('user')->where($map)->find();
	return $user;
}


    function makePaySn($member_id) {
		return mt_rand(10,99)
		. sprintf('%010d',time() - 946656000)
		. sprintf('%03d', (float) microtime() * 1000)
		. sprintf('%03d', (int) $member_id % 1000);
    }
    
    function bianhao($number){
    	    $tt=$number+1;
    	 if(strlen($tt)==1){
    	 	
    	 	    $bian='0000'.$tt;
    	 }else if(strlen($tt)==2){
    	 		$bian='000'.$tt;
    	 }else if(strlen($tt)==3){
    	 		$bian='00'.$tt;
    	 }else if(strlen($tt)==4){
    	 		$bian='0'.$tt;
    	 }else if(strlen($tt)==5){
    	 		$bian=$tt;
    	 }
    	 
    	 return $bian;
    }
    
    
    
    /**
 *
 * @param number $length 字符长度
 * @return string
 * 生成随机字符串
 */
function createRandomStr($length=6){
    $str = '1qQaWzExRsTwY2U3IeOdPcAvSfDrF4G5HtJgKbLnZhXyC6V7BuNjMmki89olp0';//62个字符
    $strlen = 62;
    while($length > $strlen){
        $str .= $str;
        $strlen += 62;
    }
    for($i=1;$i<=$length;$i++){
        $str = str_shuffle($str);
        $string .= $str[0];
    }

    return $string;
}


function uploadAJAX($size,$url='file',$type=array('jpg', 'gif', 'png', 'jpeg')){

        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize  = $size*1024*1024;
        $upload->rootPath = './Uploads/'.$url.'/'; // 设置附件上传根目录
        $upload->savePath = ''; // 设置附件上传（子）目录
        $upload->saveName = time().createRandomStr(8);
        $upload->exts = $type;
        $upload->autoSub = true;
        $upload->subName = array('date','Ymd');
        //上传文件
        $info = $upload->upload();
       
         
        if(!$info) {// 上传错误提示错误信息
           return array('message'=>$upload->getError());
             
        }else{// 上传成功
            return array('ster'=>0,'url'=>'/Uploads/'.$url.'/'.$info['0']['savepath'].$info['0']['savename']);
        }
    }





function createQRcode($url,$flag=0){ 
	vendor("phpqrcode.phpqrcode"); 
	// 纠错级别：L、M、Q、H 
	$level = 'L'; 
	// 点的大小：1到10,用于手机端4就可以了 
	$size = 4; 
	if($flag){ 
				$path = "Public/erwei/"; 
			    if(!file_exists($path)){
				 mkdir($path, 0700); 
				}
				 // 生成的文件名 
				$fileName = $path.time().'.png';
				//时间戳命名 
				QRcode::png($url, $fileName, $level, $size);
				
			    return $fileName; 
	    	}else{ 
	    		
	    	 	QRcode::png($url, false, $level, $size);//不保存，直接显示二维码 
	    	 	exit; 
	    	 	
	    	}
	    	 	 
	    	 	 
}

		

