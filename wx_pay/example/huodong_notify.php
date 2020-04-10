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
        
		$time = time();

        // 修改订单状态
		$sql = "update ht_baoming set pay_state=1,pay_time='$time' where paysn='$order_sn'"; 
		$res = mysql_query($sql, $mysql_conn);

        $appId = "0441da62e64f47f6ae93d15ae54927e3";
        $options['accountsid']='a2769b5baced349ab6c2833ef00a7f72';
        $options['token']='6b12b3514e6cb17794d0341c7b169ca2';
        $ucpass = new Ucpaas($options);

        $res1 = mysql_query("select money from ht_baoming where paysn='$order_sn' ", $mysql_conn);
        $res1 = mysql_fetch_array($res1);

        // 给平台管理员发送短信
        $rest1 = mysql_query("select phone  from ht_users where user_id = 1 ", $mysql_conn);
        $rest1 = mysql_fetch_array($rest1);
        // 短信验证码（模板短信）
        $ucpass->SendSms($appId, '387475',$param=null, $rest1['phone'], '');

        // 给财务发
        $rest2 = mysql_query("select phone  from ht_users where user_id = 3 ", $mysql_conn);
        $rest2 = mysql_fetch_array($rest2);
        //短信验证码（模板短信）
        $ucpass->SendSms($appId, '387480', $res1['money'], $rest2['phone'], '');

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
