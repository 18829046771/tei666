//mui的侧滑
mui.init({
	swipeBack: false,
});
//侧滑容器父节点
var offCanvasWrapper = mui('#offCanvasWrapper');
//主界面容器
var offCanvasInner = offCanvasWrapper[0].querySelector('.mui-inner-wrap');
//菜单容器
var offCanvasSide = document.getElementById("offCanvasSide");
//Android暂不支持整体移动动画
if(!mui.os.android) {
	var spans = document.querySelectorAll('.android-only');
	for(var i = 0, len = spans.length; i < len; i++) {
		spans[i].style.display = "none";
	}
}
//移动效果是否为整体移动
var moveTogether = false;
//侧滑容器的class列表，增加.mui-slide-in即可实现菜单移动、主界面不动的效果；
var classList = offCanvasWrapper[0].classList;

//变换侧滑动画移动效果；
document.getElementById('offCanvasHide').addEventListener('tap', function() {
	offCanvasWrapper.offCanvas('close');
});
//主界面和侧滑菜单界面均支持区域滚动；
mui('#offCanvasSideScroll').scroll();
mui('#offCanvasContentScroll').scroll();
//实现ios平台的侧滑关闭页面；
if(mui.os.plus && mui.os.ios) {
	offCanvasWrapper[0].addEventListener('shown', function(e) { //菜单显示完成事件
		plus.webview.currentWebview().setStyle({
			'popGesture': 'none'
		});
	});
	offCanvasWrapper[0].addEventListener('hidden', function(e) { //菜单关闭完成事件
		plus.webview.currentWebview().setStyle({
			'popGesture': 'close'
		});
	});
}

//跳转
mui('body').on('tap', 'a', function() {
	document.location.href = this.href;
});

//搜索显示隐藏
mui('body').on('tap', '.ss-togbtn', function() {
	$('.zt-header').toggleClass('zt-header-active');
	$('#offCanvasWrapper .mui-content').toggleClass('mui-content-active');
	$('.header-ssbox').slideToggle(100);
});

//btn 透明度变化
mui('body').on('tap', '.btn-op', function() {
	$(this).stop();
	$(this).animate({'opacity':.7},100).animate({'opacity':1},100)
});

//购物车结算
	// +
	mui('body').on('tap', '.number-j', function() {
		var num=$('.number-sl').val();
		num++;
		Number(num);
	});
	//-
	mui('body').on('tap', '.number-s', function() {
		var num=$('.number-sl').val();
		num--;
		Number(num);
	});
	function Number(num){
		if(num>-1){
			$('.number-sl').val(num);
		}
	};

////发布评论 星星
//mui('body').on('tap', '.fbpl-img', function() {
//	var num=$(this).index();
//	$('.fbpl-img').attr('src','__PUBLIC__/xin/images/fbpl-xx2.png');
//	for(var i=0;i<=num;i++){
//		$('.fbpl-img').eq(i).attr('src','__PUBLIC__/xin/images/fbpl-xx2.png');
//	}
//});