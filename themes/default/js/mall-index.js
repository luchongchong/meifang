(function(){
	// 右侧服务
	$(window).scroll(function(){
		if($(document).scrollTop() > 200){
			// $('#rightServer').css('display','block');
		}else{
			// $('#rightServer').css('display','none')
		}
	});

	$('#toTop').on('click',function(){
		// Opera 的补丁, 少了它 Opera 是直接用跳的而且画面闪烁 
		$body = (window.opera) ? (document.compatMode == 'CSS1Compat' ? $('html') : $('body')) : $('html,body');
		$body.animate({scrollTop: 0}, 500);
	});
    // 首页滚屏
	$('#J_slide').slideBox({
		duration : 0.3,//滚动持续时间，单位：秒
		easing : 'linear',//swing,linear//滚动特效
		delay : 4,//滚动延迟时间，单位：秒
		hideClickBar : false,//不自动隐藏点选按键
		// hideBottomBar : true,//隐藏底栏
		clickBarRadius : 10
	});
	// 爆款
	$('#baoKuan .bao_pro').hover(function(){
		$(this).find('.info').animate({'height':'70px'},100);
	},function(){
		$(this).find('.info').animate({'height':'33px'},100);
	})
})()