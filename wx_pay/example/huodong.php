<?php

ini_set('date.timezone','Asia/Shanghai');
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
   
//①、获取用户openid
$tools = new JsApiPay();
$openId = $tools->GetOpenid();
$myFee = $_GET["order_amount"] * 100; //支付金额



   
//②、统一下单
$input = new WxPayUnifiedOrder();
$input->SetBody('忒惠玩活动');  // 产品名称
$input->SetAttach("weixin");
$input->SetOut_trade_no($_GET["order_sn"]);  //订单编号
$input->SetTotal_fee($myFee);
$input->SetTime_start(date("YmdHis"));
$input->SetTime_expire(date("YmdHis", time() + 600));
$input->SetGoods_tag('忒惠玩活动');  // 产品名称
$input->SetNotify_url("http://www.xatei666.com/wx_pay/example/huodong_notify.php");  //回调地址
$input->SetTrade_type("JSAPI");
$input->SetOpenid($openId);

$order = WxPayApi::unifiedOrder($input);

$jsApiParameters = $tools->GetJsApiParameters($order);
//var_dump($jsApiParameters);die;
//
//
////获取共享收货地址js函数参数 oreder  ORDERPAID   orderpaid
//$editAddress = $tools->GetEditAddressParameters();

 
?>
<html>
<head>
    <meta charset="utf-8">
    <title>微信支付</title>  
	<meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1,user-scalable=no">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<link rel="stylesheet" href="http://www.xatei666.com/Public/wan/css/mui.min.css">
	<link rel="stylesheet" href="http://www.xatei666.com/Public/wan/css/index.css" />
	<script src="http://www.xatei666.com/Public/wan/js/rem.js"></script>
	<script src="http://www.xatei666.com/Public/wan/js/jquery.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="http://www.xatei666.com/Public/wan/js/mui.min.js" type="text/javascript" charset="utf-8"></script>
	<style>
		.modalbox{width: 100%;height:100%;position: fixed;top:0;left:0;z-index: 9999;display: none;}
		.zz-box{width: 100%;height:100%;background: rgba(44,44,44,.7);position: absolute;left:0;top:0;z-index: 2;}
		.modal-title{padding:0rem;height:0.8rem}
		.modal-icon{font-size: .4rem;float: right;color: #777}
		.modal-cen{width:6rem;height:3.55rem;background: #fff;border-radius: 5px;z-index: 10;left:0;right:0;top:0;bottom:0;margin:auto;position: absolute;overflow: hidden;}
		.modal-show{padding:.3rem 0 .5rem 0;text-align: center;font-size: .28rem;}
		.modal-btnbox{width: 100%;background: #f3f3f3;margin-top:0.55rem}
		.modal-btn{font-size: .28rpx;color:#666;text-align: center;height:1rem;width:50%;line-height: 1rem;}
		.modal-btnbox .active{background: #ff9600;color:#fff}
	</style>

    <script type="text/javascript">
        $(document).ready(function () {
            callpay();

			// 返回首页
			$('#sy').click(function(){
				$('.modalbox').hide();
				window.location.href = "http://www.xatei666.com/index.php/Wan/Index/index.html";
			});

			$('.zz-box,.cloes-btn').click(function(){
				$('.modalbox').hide();
				window.location.href = "http://www.xatei666.com/index.php/Wan/Index/index.html";
			});

			// 查看订单
			$('#dd').click(function(){
				$('.modalbox').hide();
				location.href = "http://www.xatei666.com/index.php/Wan/Bm/orderdet.html?bid=" + <?php echo $_GET["bid"]; ?>;
			});


        });
	//调用微信JS api 支付
	function jsApiCall()
	{
		WeixinJSBridge.invoke(
			'getBrandWCPayRequest',
			<?php echo $jsApiParameters; ?>,
			function(res){   
				WeixinJSBridge.log(res.err_msg);

				if(res.err_msg == 'get_brand_wcpay_request:cancel'){
				    alert('取消支付！');
				    location.href = "http://www.xatei666.com/index.php/Wan/index/index.html";
                }
                if(res.err_msg == 'get_brand_wcpay_request:ok'){
                	$('.modalbox').show();
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
	
</head>
	<body>
		<div style="text-align: center;color: #666;margin-top: 100px;">请耐心等待，正在唤起微信支付</div>

		<!--新增弹窗  其他地方的弹窗都一样-->
		<div class="modalbox">
			<div class="zz-box"></div>
			<div class="modal-cen">
				<div class="modal-title">
					<div class="modal-icon">
						<span class="cloes-btn mui-icon mui-icon-closeempty" style="font-size: 0.8rem"></span>
					</div>
				</div>
				<div class="modal-show">
					<div class="modal-text">购买成功！</div>
				</div>
				<div class="modal-btnbox flex-s">
					<div class="modal-btn" id="dd">查看订单</div>
					<div class="modal-btn active" id="sy">返回首页</div>
				</div>
			</div>
		</div>
	</body>

</html>
