<?php
ini_set('date.timezone','Asia/Shanghai');
//error_reporting(E_ERROR);
require_once "../lib/WxPay.Api.php";
require_once 'log.php';

//初始化日志
$logHandler= new CLogFileHandler("../logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);

function printf_info($data)
{
    foreach($data as $key=>$value){
        echo "<font color='#f00;'>$key</font> : $value <br/>";
    }
}


 
$_REQUEST["out_trade_no"] = $_GET["sn"]; //订单号
$_REQUEST["transaction_id"]=$_GET["weixin"];//微信订单
$_REQUEST["total_fee"]= $_GET["money"]*100;  //金额
$_REQUEST["refund_fee"] = $_GET["money"]*100;  //总金额
dump($_REQUEST);
die; 
///****************修改退款状态****************/
////$mysql_conn = mysql_connect('localhost','tst666','Po7y5s3f',3306);
////mysql_select_db('tst666',$mysql_conn);
////mysql_query('set names utf8');
////$id = $_GET["id"];  //id
////
////$sql = "update ht_tuikuan set state = 1 where id = '$id' ";
////$res = mysql_query($sql,$mysql_conn);




/****************修改 end******微信交易号  **********/

if(isset($_REQUEST["transaction_id"]) && $_REQUEST["transaction_id"] != ""){   
	$transaction_id = $_REQUEST["transaction_id"];
	$total_fee = $_REQUEST["total_fee"];
	$refund_fee = $_REQUEST["refund_fee"];
	$input = new WxPayRefund();
	$input->SetTransaction_id($transaction_id);
	$input->SetTotal_fee($total_fee);
	$input->SetRefund_fee($refund_fee);
    $input->SetOut_refund_no(WxPayConfig::MCHID.date("YmdHis"));
    $input->SetOp_user_id(WxPayConfig::MCHID);
	printf_info(WxPayApi::refund($input));
	exit();
}


//if(isset($_REQUEST["out_trade_no"]) && $_REQUEST["out_trade_no"] != ""){
//	$out_trade_no = $_REQUEST["out_trade_no"];
//	$total_fee = $_REQUEST["total_fee"];
//	$refund_fee = $_REQUEST["refund_fee"];
//	$input = new WxPayRefund();
//	$input->SetOut_trade_no($out_trade_no);
//	$input->SetTotal_fee($total_fee);
//	$input->SetRefund_fee($refund_fee);
//  $input->SetOut_refund_no(WxPayConfig::MCHID.date("YmdHis"));
//  $input->SetOp_user_id(WxPayConfig::MCHID);
//	printf_info(WxPayApi::refund($input));
//	exit();
//}
?>
<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1" /> 
    <title>微信支付样例-退款</title>
</head>
<body>  
	<form action="#" method="post">
        <div style="margin-left:2%;color:#f00">微信订单号和商户订单号选少填一个，微信订单号优先：</div><br/>
        <div style="margin-left:2%;">微信订单号：</div><br/>
        <input type="text" style="width:96%;height:35px;margin-left:2%;" name="transaction_id" /><br /><br />
        <div style="margin-left:2%;">商户订单号：</div><br/>
        <input type="text" style="width:96%;height:35px;margin-left:2%;" name="out_trade_no" /><br /><br />
        <div style="margin-left:2%;">订单总金额(分)：</div><br/>
        <input type="text" style="width:96%;height:35px;margin-left:2%;" name="total_fee" /><br /><br />
        <div style="margin-left:2%;">退款金额(分)：</div><br/>
        <input type="text" style="width:96%;height:35px;margin-left:2%;" name="refund_fee" /><br /><br />
		<div align="center">
			<input type="submit" value="提交退款" style="width:210px; height:50px; border-radius: 15px;background-color:#FE6714; border:0px #FE6714 solid; cursor: pointer;  color:white;  font-size:16px;" type="button" onclick="callpay()" />
		</div>
	</form>
</body>
</html>