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
$myFee = $_GET["order_amount"]*100; //支付金额



   
//②、统一下单
$input = new WxPayUnifiedOrder();
$input->SetBody('开通会员');  // 产品名称
$input->SetAttach("weixin");
$input->SetOut_trade_no($_GET["order_sn"]);  //订单编号
$input->SetTotal_fee($myFee);
$input->SetTime_start(date("YmdHis"));
$input->SetTime_expire(date("YmdHis", time() + 600));
$input->SetGoods_tag('开通会员');  // 产品名称
$input->SetNotify_url("http://www.xatei666.com/wx_pay/example/not.php");  //回调地址
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
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/> 
    <title>微信支付</title>  
    <script src="http://www.xatei666.com/Public/zhifu/jquery.min.js"></script>
    <script  src="http://www.xatei666.com/Public/zhifu/layer/layer.js"></script>
    <link rel="stylesheet" href="http://www.xatei666.com/Public/zhifu/layer/skin/layer.css" />

    <script type="text/javascript">
        $(document).ready(function () {
            callpay();
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
				    location.href = "http://www.xatei666.com/index.php/Home/Xin/index.html";
                }
                if(res.err_msg == 'get_brand_wcpay_request:ok'){
                    layer.open({
                        content: '会员开通成功,此刻您的选择？',
                        style: 'border:none; width:250px;',
                        btn: ['继续浏览'],
                        yes: function(){
                            location.href = "http://www.xatei666.com/index.php/Home/Huiyuan/vipindex.html";
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
	
</head>
<body>
	<div style="text-align: center;color: #666;margin-top: 100px;">请耐心等待，正在唤起微信支付</div>

</body>

</html>
