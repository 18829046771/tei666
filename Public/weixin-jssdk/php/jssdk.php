<?php
class JSSDK {
  private $appId;
  private $appSecret;

  public function __construct($appId, $appSecret) {
    $this->appId = $appId;
    $this->appSecret = $appSecret;
  }

  public function getSignPackage() {
    $jsapiTicket = $this->getJsApiTicket();
   // echo $jsapiTicket;exit;
    $url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $timestamp = time();
    $nonceStr = $this->createNonceStr();

    // 这里参数的顺序要按照 key 值 ASCII 码升序排序
    $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

    $signature = sha1($string);

    $signPackage = array(
      "appId"     => $this->appId,
      "nonceStr"  => $nonceStr,
      /* "timestamp" => $timestamp, */
    		"timestamp" => strval($timestamp),
      "url"       => $url,
      "signature" => $signature,
      "rawString" => $string
    );
    return $signPackage; 
  }

  private function createNonceStr($length = 16) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
  }

  private function getJsApiTicket() {
    // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
//     $data = json_decode(file_get_contents("jsapi_ticket.json"));
    $data = json_decode(file_get_contents(getcwd().'/Public/weixin-jssdk/php/access_token.json'));
  //  print_r($data);exit;
    if ($data->expire_time < time()) {
        //echo 'small';exit;
      $accessToken = $this->getAccessToken();
     // echo $accessToken;exit;
      $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
      $res = json_decode($this->httpGet($url));
      $ticket = $res->ticket;
      if ($ticket) {
        $data->expire_time = time() + 7000;
        $data->jsapi_ticket = $ticket;
        $fp = fopen(getcwd().'/Public/weixin-jssdk/php/access_token.json', "w");
//         $fp = fopen("jsapi_ticket.json", "w");
        fwrite($fp, json_encode($data));
        fclose($fp);
      }
    } else {
      $ticket = $data->jsapi_ticket;
    }

    return $ticket;
  }

  private function getAccessToken() {
    // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
    $data = json_decode(file_get_contents(getcwd().'/Public/weixin-jssdk/php/access_token.json'));
    //print_r($data);exit;
//     $data = json_decode(file_get_contents("access_token.json"));
    if ($data->expire_time < time()) {
       // echo 'token small';exit;
      $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
      $res = json_decode($this->httpGet($url));
      $access_token = $res->access_token;
//      echo '333<br/>';
//      print_r($res);exit;

      if ($access_token) {
        $data->expire_time = time() + 7000;
        $data->access_token = $access_token;
//         $fp = fopen("access_token.json", "w");
        $fp = fopen(getcwd().'/Public/weixin-jssdk/php/access_token.json', "w");
        fwrite($fp, json_encode($data));
        fclose($fp);
      }
    } else {
      $access_token = $data->access_token;
    }
    return $access_token;
  }

  private function httpGet($url) {
   		$ch = curl_init();
  		curl_setopt($ch, CURLOPT_URL, $url);
 		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
  		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  		$res = curl_exec($ch);
  		curl_close($ch);
    	return $res;
  }
}

