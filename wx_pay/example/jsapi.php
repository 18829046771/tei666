<?php

ini_set('date.timezone','Asia/Shanghai');
//error_reporting(E_ERROR);
require_once "../lib/WxPay.Api.php";
require_once "WxPay.JsApiPay.php";
require_once 'log.php';

//初始化日志
$logHandler= new CLogFileHandler("../logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);
 
//打印输出数组信息
function printf_info($data)
{
    foreach($data as $key=>$value){
        echo "<font color='#00ff55;'>$key</font> : $value <br/>";
    }
}
 
//printf_info($input);exit;
/****************获取商品信息 start****************/
$mysql_conn = mysql_connect('localhost','root','tei666@2019',3306);
mysql_select_db('tei666',$mysql_conn);
mysql_query('set names utf8');
$order_id = $_GET["order_id"];  //产品id

$sql = "select product_title  from ht_article where art_id = '$order_id' ";
$res = mysql_query($sql,$mysql_conn);
$res=mysql_fetch_array($res);//函数2
 
$yuan = $_COOKIE['yuan'];
 
/****************获取商品信息 end****************/


/****************车产品修改订单地址 end****************/
//①、获取用户openid
$tools = new JsApiPay();
session_start();
$openId = $_SESSION['openid'];//$tools->GetOpenid();




     
$myFee = $_GET["order_amount"]*100; //支付金额



 
//②、统一下单
$input = new WxPayUnifiedOrder();
$input->SetBody($res['product_title']);  // 产品名称
$input->SetAttach("weixin");
$input->SetOut_trade_no($_GET["order_sn"]);  //订单编号
$input->SetTotal_fee($myFee);
$input->SetTime_start(date("YmdHis"));
$input->SetTime_expire(date("YmdHis", time() + 600));
$input->SetGoods_tag($res['product_title']);  // 产品名称
$input->SetNotify_url("http://www.xatei666.com/wx_pay/example/notify.php");  //回调地址
$input->SetTrade_type("JSAPI");
//printf_info($input);exit;

$input->SetOpenid($openId);


$order = WxPayApi::unifiedOrder($input);



$jsApiParameters = $tools->GetJsApiParameters($order);


//获取共享收货地址js函数参数
$editAddress = $tools->GetEditAddressParameters();

 //echo $editAddress;die;
//③、在支持成功回调通知中处理成功之后的事宜，见 notify.php
/**
 * 注意：
 * 1、当你的回调地址不可访问的时候，回调通知会失败，可以通过查询订单来确认支付是否成功
 * 2、jsapi支付时需要填入用户openid，WxPay.JsApiPay.php中有获取openid流程 （文档可以参考微信公众平台“网页授权接口”，
 * 参考http://mp.weixin.qq.com/wiki/17/c0f37d5704f0b64713d5d2c37b468d75.html）
 */
?>

<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/> 
    <title>微信支付</title>  
    <script src="http://www.xatei666.com/Public/zhifu/jquery.min.js"></script>
    <script  src="http://www.xatei666.com/Public/zhifu/layer/layer.js"></script>
    <link rel="stylesheet" href="http://www.xatei666.com/Public/zhifu/layer/skin/layer.css" />

    <script type="text/javascript">
	// 返回事件监听
	pushHistory();
	var blok = true
	window.addEventListener("popstate", function(e) {
		if(blok){
			blok = false;
			window.location.href = "http://www.xatei666.com/index.php/Home/Xin/index.html";
			
		}
    }, false);
    function pushHistory() {
        var state = {
            title: "title",
            url: "#"
        };
        window.history.pushState(state, "title", "#");
    }

    $(document).ready(function () {
        callpay();
    });
	//调用微信JS api 支付
	function jsApiCall(){   
		WeixinJSBridge.invoke(
			'getBrandWCPayRequest',
			<?php echo $jsApiParameters; ?>,
			
			function(res){
				     
				WeixinJSBridge.log(res.err_msg);

				 
				if(res.err_msg == 'get_brand_wcpay_request:cancel'){
				    alert('取消支付！');
				    location.href = "http://www.xatei666.com/index.php/Home/Xin/index.html";
                }
                if(res.err_msg == 'get_brand_wcpay_request:ok'){
                	
                    layer.open({
                        content: '订单支付成功,此刻您的选择？',
                        style: 'border:none; width:250px;',
                        btn: ['继续浏览'],
                        yes: function(){
                               location.href ="<?php echo $yuan; ?>";

                           // location.href = "http://www.xatei666.com/index.php/Home/Xin/index.html";
                        }
                        
                    });

                }

			}
		);
	}

	function callpay()
	{
		if (typeof WeixinJSBridge == "undefined"){
		    if( document.addEventListener ){
				 
		        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
		    }else if (document.attachEvent){
				
		        document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
		        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
		    }
		}else{
			
		    jsApiCall();
		}
	}
	</script>
	<script type="text/javascript">
	//获取共享地址
	function editAddress()
	{
		WeixinJSBridge.invoke(
			'editAddress',
			<?php echo $editAddress; ?>,
			function(res){
				var value1 = res.proviceFirstStageName;
				var value2 = res.addressCitySecondStageName;
				var value3 = res.addressCountiesThirdStageName;
				var value4 = res.addressDetailInfo;
				var tel = res.telNumber;
			}
		);
	}
	
	window.onload = function(){
		if (typeof WeixinJSBridge == "undefined"){
		    if( document.addEventListener ){
		        document.addEventListener('WeixinJSBridgeReady', editAddress, false);
		    }else if (document.attachEvent){
		        document.attachEvent('WeixinJSBridgeReady', editAddress); 
		        document.attachEvent('onWeixinJSBridgeReady', editAddress);
		    }
		}else{
			editAddress();
		}
	};
	</script>
</head>
<body>
	<div style="text-align: center;color: #666;margin-top: 100px;">请耐心等待，正在唤起微信支付</div>

</body>

</html>