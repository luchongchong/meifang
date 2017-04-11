(function(){
	// 大图小图
	$('#smallPicItem > li').hover(function(){
		$(this).addClass('active').siblings().removeClass('active');
		$('#bigPic img').attr('src',$(this).find('img').attr('src'))
	});

	$('#min-scroll').btnScroll(4);
})()