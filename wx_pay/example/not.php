<?php

ini_set('date.timezone','Asia/Shanghai');
error_reporting(E_ERROR);

require_once "../lib/WxPay.Api.php";
require_once '../lib/WxPay.Notify.php';
require_once '../lib/Ucpaas.class.php';
require_once 'log.php';

//初始化日志
$logHandler= new CLogFileHandler("../logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);

class PayNotifyCallBack extends WxPayNotify
{
	//查询订单
	public function Queryorder($transaction_id)
	{
		$input = new WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = WxPayApi::orderQuery($input);
		Log::DEBUG("query:" . json_encode($result));
		if(array_key_exists("return_code", $result)
			&& array_key_exists("result_code", $result)
			&& $result["return_code"] == "SUCCESS"
			&& $result["result_code"] == "SUCCESS")
		{
			return true;
		}
		return false;
	}

    //重写回调处理函数
    public function NotifyProcess($data, &$msg)
    {

        $mysql_conn = mysql_connect('localhost','root','tei666@2019',3306);
        mysql_select_db('tei666',$mysql_conn);
        mysql_query('set names utf8');
        $order_sn = $data['out_trade_no'];
        $tt=$data['transaction_id'];  
        
		$time=date('Y-m-d H:i:s',time());
		$endtime=date("Y-m-d H:i:s",strtotime("+1years",strtotime($time)));
		
	    //1.修改用户会员等级
       $sql = "select uid  from ht_huiyuan_paysn where paysn = '$order_sn' ";
       $res = mysql_query($sql,$mysql_conn);
       $res=mysql_fetch_array($res);//函数2
       $uid=$res['uid'];
       $count="select *from ht_user where type=1 order by number desc limit 1";
       $count_tr = mysql_query($count,$mysql_conn);
       $count_ty=mysql_fetch_array($count_tr);//函数2
       $number=$this->bianhao($count_ty['number']);
       $sql_cha = "update ht_user set type_time ='$time',type='1',type_endtime='$endtime',number='$number' where id = '$uid' "; 
	   $res_cha = mysql_query($sql_cha,$mysql_conn);
        //修改订单状态
		$sql2 = "update ht_huiyuan_paysn set weixin='$tt',ster='1'  where paysn = '$order_sn' "; 
		$res_2 = mysql_query($sql2,$mysql_conn);

		  

        /**********************************/
//          $info=array(
//            'appid' => 'wx59f2613e4a0248c7',
//            'appsecret' => '32cb474042f7c2cce8ec4393b1f31a3e',
//
//          );
//          $url_token="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$info['appid']}&secret={$info['appsecret']}";
//          $res = $this->http_request($url_token);
//          $result = json_decode($res,true);
//          $access_token = $result['access_token'];
//
//          $order_money = $data['total_fee']/100;
//          $order_money2 = $order_money.' 元';
//          $template_data = '
//        {
//         "touser":"'.$data['openid'].'",
//         "template_id":"bLQ-mP-WJFJnSSYjrO2g_hh4vLntMCNCXJLf02gYHto",
//         "url":"http://weixin.qq.com/download",
//         "data":{
//                 "orderMoneySum":{
//                     "value":"'.$order_money2.'",
//                     "color":"#173177"
//                 },
//                 "orderProductName":{
//                     "value":"'.$data['attach'].'",
//                     "color":"#173177"
//                 },
//                 "Remark":{
//                     "value":"您的订单已经支付成功，感谢您的光临",
//                     "color":"#888"
//                 }
//         }
//     }';
//          $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
//          $res = $this->http_request($url,urldecode($template_data));  // 发送模板消息
        /**********************************/

        Log::DEBUG("call back:" . json_encode($data));
        $notfiyOutput = array();

        if(!array_key_exists("transaction_id", $data)){
            $msg = "输入参数不正确";
            return false;
        }
        //查询订单，判断订单真实性
        if(!$this->Queryorder($data["transaction_id"])){
            $msg = "订单查询失败";
            return false;
        }
        return true;
    }
    
    
     public function bianhao($number){
    	   $num=(int)$number;
    	   $ht=$num+1;
    	   $tt=(string)$ht;
    	  
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
    
    
    
	public function http_request($url,$data=null){
	    $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,FALSE);
        if(!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
}

Log::DEBUG("begin notify");
$notify = new PayNotifyCallBack();
$notify->Handle(false);
