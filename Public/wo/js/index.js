mui('.padding14-tb').on('tap','span' ,function(){
	$('.padding14-tb').find('span').removeClass('coGreen');
	var j = $(this).index()+1;
	for(var i=0;i<j;i++)
	$('.padding14-tb').find('span').eq(i).addClass('coGreen');
	$("#fen").attr('value',j);
});

//

