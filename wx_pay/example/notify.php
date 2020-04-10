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
		//修改订单状态
		$sql_cha = "update ht_dingdan set ster = 1,zhifu_time ='$time',text='$tt' where paysn = '$order_sn' "; 
	    $res_cha = mysql_query($sql_cha,$mysql_conn);

        //给用户发短信
        //1.找到userid
        $sql = "select uid,store_id,name,lianxiphoto,addressid,zong from ht_dingdan where paysn = '$order_sn' ";
        $res = mysql_query($sql,$mysql_conn);
       $res=mysql_fetch_array($res);//函数2
       $uid=$res['uid'];
       $name=$res['name'];
       $zong=$res['zong'];
       $pjone=$res['lianxiphoto'];
       $addressid=$res['addressid'];
       if(isset($pjone) && !empty($pjone)){
       	  $to=$pjone;
       }else{
       	  $address="select dian from ht_address where id = '$addressid' ";
       	  $address1 = mysql_query($address,$mysql_conn);
          $address2=mysql_fetch_array($address1);//函数2
       	  $to=$address2['dian'];
       }

       //3.发送短信
        $options['accountsid']='a2769b5baced349ab6c2833ef00a7f72';
        $options['token']='6b12b3514e6cb17794d0341c7b169ca2';
        $ucpass = new Ucpaas($options);
        $appId = "0441da62e64f47f6ae93d15ae54927e3";
        //短信验证码（模板短信）
		    $arr = $ucpass->SendSms($appId, "387461",$param="$order_sn,$name",$to,$uid);
		    $arr_n = json_decode(json_encode($arr),true);
		   
        //给平台管理员发送短信
        
        //2.找到平台管理员手机号码
       $sql2="select phone  from ht_users where user_id = 1 ";
       $rest2=mysql_query($sql2,$mysql_conn);
       $rest2=mysql_fetch_array($rest2);
       $photo2=$rest2['phone'];
       //3.发送短信
        //短信验证码（模板短信）
		    $arr2 = $ucpass->SendSms($appId, "387475",$param=null, $photo2, $uid);
		    $arr_n2 = json_decode(json_encode($arr2),true);
    	
         //给财务发
        //2.找到财务手机号码
       $sql3="select phone  from ht_users where user_id = 3 ";
       $rest3=mysql_query($sql3,$mysql_conn);
       $rest3=mysql_fetch_array($rest3);
       $photo3=$rest3['phone'];
       //3.发送短信
        //短信验证码（模板短信）
		    $arr3 = $ucpass->SendSms($appId, "387480",$zong, $photo3,$uid);
		    $arr_n3 = json_decode(json_encode($arr3),true);
    

        // 给供应商
        // 1.找商品
        $goods = mysql_query("select gong from ht_article where art_id = " . $res['store_id'], $mysql_conn);
        $goods = mysql_fetch_array($goods);
        // 2.找到供应商手机号码
        $rest4 = mysql_query("select photo from ht_gongying where id = " . $goods['gong'], $mysql_conn);
        $rest4 = mysql_fetch_array($rest4);
        // 3.发送短信
        // 短信验证码（模板短信）
        $arr4 = $ucpass->SendSms($appId, "387482", null, $rest4['photo'], $uid);
        $arr_n4 = json_decode(json_encode($arr4), true);

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
