(function(){
	/*左侧电梯*/
	var d_top=$(document).scrollTop();
	var f1_top=$('.floor').eq(0).offset().top;
	// var flast_top=$('.floor:last').offset().top;
	$(window).scroll(function(){
		if($(document).scrollTop() > f1_top){
			// $('#lift dl').css('display','block');
			$('#lift').css('position','fixed');
			// if($(document).scrollTop() > flast_top){
			// 	// $('#lift dl').css('display','none');
			// 	$('#lift dd').removeClass('on');
			// }
		}else{
			$('#lift').css('position','absolute');
		}
		effect();
	});

	function myfun(){
		var dh=$(document).scrollTop();
		if(dh > f1_top){
			$('#lift').css('position','fixed');
			// $('#lift dl').css('display','block');
		}

		effect();
	}	
	window.onload=myfun;
})()

function effect(){
	$('.floor').each(function(i){
		if($(document).scrollTop()+5 > $(this).offset().top){
			$('#lift dd').removeClass('on').eq(i).addClass('on')
		}
	})
	//绑定楼层的滑动切换效果
	// Opera 的补丁, 少了它 Opera 是直接用跳的而且画面闪烁 
	$body = (window.opera) ? (document.compatMode == 'CSS1Compat' ? $('html') : $('body')) : $('html,body');
	$('#lift dd').unbind('click').on('click',function(){
		var i = $(this).index()-1;
		 $body.animate({scrollTop: $('.floor').eq(i).offset().top}, 500);
		 return false;// 返回false可以避免在原链接后加上
	});
}