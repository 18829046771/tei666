<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1" /> 
    <title>微信支付样例-退款</title>
</head>
<?php
ini_set('date.timezone','Asia/Shanghai');
error_reporting(E_ERROR);
require_once "../lib/WxPay.Api.php";
require_once '../lib/Ucpaas.class.php';
require_once 'log.php';
//echo WxPayConfig::MCHID.date("YmdHis");exit;
//初始化日志
$logHandler= new CLogFileHandler("../logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);


function printf_info($data){
    foreach($data as $key=>$value){
        echo "<font color='#f00;'>$key</font> : $value <br/>";
    }
}
/***********/

if(isset($_REQUEST["out_trade_no"]) && $_REQUEST["out_trade_no"] != ""){
	$out_trade_no = $_REQUEST["out_trade_no"];
	$total_fee = $_REQUEST["money"] * 100;
	$refund_fee = $_REQUEST["money"] * 100;
	$input = new WxPayRefund();
	$input->SetOut_trade_no($out_trade_no);
	$input->SetTotal_fee($total_fee);
	$input->SetRefund_fee($refund_fee);
    $input->SetOut_refund_no(WxPayConfig::MCHID.date("YmdHis"));
    $input->SetOp_user_id(WxPayConfig::MCHID);

	$restout=WxPayApi::refund($input);
	 if($restout['result_code']=="SUCCESS"){
	 	     /**** 更改退款状态*******/
	 	        $time=time();
                $mysql_conn = mysql_connect('localhost','root','tei666@2019',3306);
				mysql_select_db('tei666',$mysql_conn);
				mysql_query('set names utf8');
				$id = $_GET["id"];  //id
				$sql_tui = "update ht_baoming set  tuikuan = 2,tuikuan_etime='$time' where id = '$id' "; 
                $res_cha = mysql_query($sql_tui, $mysql_conn);

		        $options['accountsid'] = 'a2769b5baced349ab6c2833ef00a7f72';
		        $options['token'] = '6b12b3514e6cb17794d0341c7b169ca2';
		        $ucpass = new Ucpaas($options);
		        // 短信验证码（模板短信）
		        $appId = "0441da62e64f47f6ae93d15ae54927e3";

		       $rest=mysql_query("select money from ht_baoming where id = {$id} ", $mysql_conn);
		       $rest=mysql_fetch_array($rest);
		       $money = $rest['money'];
				$arr = $ucpass->SendSms($appId, "397096", "$money", '13669246999', '');
				$arr_n = json_decode(json_encode($arr), true);
              
				
            /***************/
	 		echo '<script>alert("退款成功");window.location.href ="http://www.xatei666.com/index.php/Admin/Wan/tuikuan.html"</script>';
	 }else{
	 	echo '<script>alert("退款失败");history.back(); </script>';
	 }
	exit();
}
?>
<body>  
	<!--<form action="#" method="post">
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
	</form>-->
</body>
</html>

<?php
 
 
// /***************/
// $_REQUEST["transaction_id"]=$_GET['weixin']; 
// $_REQUEST["total_fee"]= $_GET['money']*100;
// $_REQUEST["refund_fee"] = $_GET['money']*100;

// if(isset($_REQUEST["transaction_id"]) && $_REQUEST["transaction_id"] != ""){
// 	$transaction_id = $_REQUEST["transaction_id"];
// 	$total_fee = $_REQUEST["total_fee"];
// 	$refund_fee = $_REQUEST["refund_fee"];
// 	$input = new WxPayRefund();

// 	$input->SetTransaction_id($transaction_id);
// 	$input->SetTotal_fee($total_fee);
// 	$input->SetRefund_fee($refund_fee);
//     $input->SetOut_refund_no(WxPayConfig::MCHID.date("YmdHis"));
//     $input->SetOp_user_id(WxPayConfig::MCHID);
// //  print_r($_REQUEST);
// //  print_r($input);exit;
//  //  print_r($input);exit;
// 	exit();
// }
?>
