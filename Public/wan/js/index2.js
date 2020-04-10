//收藏状态切换
$('.index_list_sc').click(function(){
	var self = this;
	var jdata = $(this).data();

    $.ajax({
        type : "POST",
        url: jdata.url,
        data : {hid: jdata.hid},
        success: function(data){
		    mui.toast(data.msg);
		    if (data.state == 1) {
				$(self).toggleClass('index_list_sc_active');
				location.reload();
		    }
        }
    }); 

})

$('#seach').click(function(){
	var kw = $("#kw").val();
	if (kw == '') {
		alert('请输入搜索关键词！');
		return false;
	}
	$('#seachForm').submit();
})

//back
$('.seach_back').click(function(){
	window.history.back(-1);
});

//重置
$('.seach_rese').click(function(){
	$('.seach_oklist').removeClass('active')
});

function selectNav(val) {
	var src = '';
	switch (val){
		case 1:
			src = '/Public/wan/images/indexico2.png';
		break;
		case 2:
			src = '/Public/wan/images/mystarico2.png';
			break;
		case 3:
			src = '/Public/wan/images/myicoa.png';
			break;
		default:
			src = '/Public/wan/images/indexico2.png';
			break;
	}
	$('#nav' + val + ' img').attr('src', src);
	$('#nav' + val + ' div').addClass('navs_onec');
}

function selectTop(val) {
	$('#top' + val).addClass('active');
	$('#top' + val + ' img').show();
}
