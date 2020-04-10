<?php
namespace Wan\Controller;
use Think\Controller;

require_once ROOT_PATH . "/wx_pay/lib/WxPay.Api.php";
require_once ROOT_PATH . "/wx_pay/lib/WxPay.Config.php";

class PublicController extends Controller {

    // 获取微信头像，openid,  获取微信号
    protected  $weixinConfig;
    protected  $user_id=0;

	public function weixindeng(){
    	//微信浏览器
    	if(strstr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')){
 	    	// 获取公众号微信配置
            $wechat_list = M('wx_user')->select();
	    	$weixinConfig = $wechat_list[0];
	    	$this->weixinConfig=$weixinConfig; // 存入属性中
	    	$this->assign('wechat_config', $weixinConfig); // 微信配置
	    	if(!empty($weixinConfig) ){
 	    		$user_info=$this->getUserInfo2(); // 获取微信用户信息
				//dump($user_info);
	    		$data = [
    				'openid'=>$user_info['openid'], // 微信用户号
    				'oauth'=>'weixin',
    				'nickname'=>trim($user_info['nickname']) ? trim($user_info['nickname']) : '微信用户',
    				'sex'=>$user_info['sex'],
    				'head_pic'=>$user_info['headimgurl'],
	    		];
	    		$data = $this->thirdLogin($data); // 登录
	    		if($data['status'] == 1){
	    			session('user',$data['result']); // 结果
	    		}
 	    	}
    	}
    }

    // 第三方登录
    public function thirdLogin($data=array()){
    	$openid = $data['openid']; //第三方返回唯一标识
    	$oauth = $data['oauth']; //来源
    	if(!$openid || !$oauth)
    	return array('status'=>-1,'msg'=>'参数有误','result'=>'');
    	//获取用户信息 
    	$user = get_user_info($openid,3,$oauth);
    	if(!$user){
    		//账户不存在 注册一个
    		$map['password'] = '';
    		$map['openid'] = $openid;
    		$map['sename'] = $data['nickname'];
    		$map['time'] = time();
    		$map['oauth'] = $oauth;
    		$map['img'] = $data['head_pic'];
    		$map['radio'] = $data['sex'];
    		$map['last_login']=time();
    		$row = M('user')->add($map);
    		$user = get_user_info($openid,3,$oauth);	
    	}
    	return array('status'=>1,'msg'=>'登陆成功','result'=>$user);
    }
 
    //获取用户基本信息
    public function getUserInfo2(){
    	$AppID = $this->weixinConfig['appid'];
    	$AppSecret = $this->weixinConfig['appsecret'];
    	if(!isset($_GET['code'])){
    		//触发微信返回code码
    		$baseUrl = urlencode($this->get_url());
    		$url = $this->__CreateOauthUrlForCode($baseUrl); // 获取 code地址
    		Header("Location: $url"); // 跳转到微信授权页面 需要用户确认登录的页面  //又跳转回当前方法执行下面的else  zhai
    		exit();
    	}else{
    		$url='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$AppID.'&secret='.$AppSecret.'&code='.$_GET['code'].'&grant_type=authorization_code';
    		$ch = curl_init();
    		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    		curl_setopt($ch, CURLOPT_URL, $url);
    		$json =  curl_exec($ch);
    		curl_close($ch);
    		$arr=json_decode($json,1);
    	}
		if(!isset($arr['access_token']) || empty($arr['access_token'])){
			dump($arr);
			die();
		}
    	$url2="https://api.weixin.qq.com/sns/userinfo?access_token=".$arr['access_token']."&openid=".$arr['openid'];
    	$ch2 = curl_init();
    	curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, FALSE);
    	curl_setopt($ch2, CURLOPT_RETURNTRANSFER, TRUE);
    	curl_setopt($ch2, CURLOPT_URL, $url2);
    	$json2 =  curl_exec($ch2);
    	curl_close($ch2);
    	$arr2 = json_decode($json2,1);

    	//将access_token存入用户信息数组中
    	$arr2['access_token']=$arr['access_token'];
    	$_SESSION['openid'] = $arr2['openid'];//将openid存入session  zhai
     	return $arr2;
    }

    public function getAccessToken($AppID,$AppSecret) {
    	// access_token 应该全局存储与更新，以下代码以写入到文件中做示例
    	$data = json_decode(file_get_contents(getcwd().'/access_token.json'));
    	if ($data->expire_time < time()) {//过期
    		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$AppID."&secret=".$AppSecret;
    		$res = json_decode($this->httpGet($url));
    		$access_token = $res->access_token;
    		if ($access_token) {
    			$data->expire_time = time() + 7000;
    			$data->access_token = $access_token;
    			$fp = fopen(getcwd().'/access_token.json', "w");
    			fwrite($fp, json_encode($data));
    			fclose($fp);
    		}
    	} else {//未过期
    		$access_token = $data->access_token;
    	}
    	return $access_token;
    }

    public function httpGet($url) {
    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	$res = curl_exec($ch);
    	curl_close($ch);
    	return $res;
    }

    // 获取微信签名
    public function getSignPackage(){
    	require getcwd().'/Public/weixin-jssdk/php/jssdk.php'; //引入jssdk文件
    	$wechat_list = M('wx_user')->select();
	    $weixinConfig = $wechat_list[0];
	   
    	$jssdk = new \JSSDK($weixinConfig['appid'],$weixinConfig['appsecret']);
    	
    	$signPackage = $jssdk->GetSignPackage();//获取
    	
    	return $signPackage;
    }
    
    /**
     * （第一步：获取code,拼接链接  zhai）
     * 构造获取code的url连接
     * @param string $redirectUrl 微信服务器回跳的url，需要url编码
     * @return 返回构造好的url
     */
    private function __CreateOauthUrlForCode($redirectUrl){
    	$urlObj["appid"] = $this->weixinConfig['appid'];
    	$urlObj["redirect_uri"] = "$redirectUrl";
    	$urlObj["response_type"] = "code";
    	$urlObj["scope"] = "snsapi_userinfo";
    	$urlObj["state"] = "STATE"."#wechat_redirect";
    	$bizString = $this->ToUrlParams($urlObj);
    	return "https://open.weixin.qq.com/connect/oauth2/authorize?".$bizString;
    }
    
    /**
     * 获取当前的url 地址
     * @return type
     */
    private function get_url() {
    	$sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
    	$php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
    	$path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
    	$relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
    	return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
    }

    /**
     * 拼接签名字符串
     * @param array $urlObj
     * @return 返回已经拼接好的字符串
     */
    private function ToUrlParams($urlObj){
    	$buff = "";
    	foreach ($urlObj as $k => $v){
    		if($k != "sign"){
    			$buff .= $k . "=" . $v . "&";
    		}
    	}
    	$buff = trim($buff, "&");
    	return $buff;
    }

    //------------------------------分享------------------------------//
    // 生成微信JSAPI签名 signature
    public function getSignature($timestamp, $noncestr){
        $access_token = $this->getShareAccessToken();
        $jsapi_ticket = $this->getJsapiTicket($access_token);
        // 签名算法
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $arrdata =[
            "timestamp" => $timestamp, 
            "noncestr" => $noncestr, 
            "url" => $url, 
            "jsapi_ticket" => $jsapi_ticket
        ];
        ksort($arrdata);
        $paramstring = "";
        foreach($arrdata as $key => $value){
            if(strlen($paramstring) == 0)
                $paramstring .= $key . "=" . $value;
            else
                $paramstring .= "&" . $key . "=" . $value;
        }
        $signature = sha1($paramstring);
        return $signature;
    }

    // 获取 access_token
    public function getShareAccessToken(){
        $appid = \WxPayConfig::APPID;
        $appsecret = \WxPayConfig::APPSECRET;
        $url_token = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url_token);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        $jsoninfo = json_decode($output, true);
        if(isset($jsoninfo['errcode'])){
            die($jsoninfo['errmsg']);
        }
        return $jsoninfo["access_token"];
    }

    // 获取 jsapi_ticket
    public function getJsapiTicket($access_token){
        $url_jsapi_ticket = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token={$access_token}&type=jsapi";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url_jsapi_ticket);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        $jsoninfo = json_decode($output, true);
        return $jsoninfo["ticket"];
    }

    // 获取毫秒级别的时间戳
    public function getMillisecond(){
        //获取毫秒的时间戳
        $time = explode ( " ", microtime () );
        $time = $time[1] . ($time[0] * 1000);
        $time2 = explode( ".", $time );
        $time = $time2[0];
        return $time;
    }

}