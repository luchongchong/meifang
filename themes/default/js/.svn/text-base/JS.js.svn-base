// JavaScript Document
   $(function(){
 //  $("div.zoomdiv").css("opacity",0.5);
     $(".jqzoom").eq(0).show().siblings().hide(); 
	 $(".smallList img").eq(0).addClass("redBorder");
	 $(".smallList img").mouseover(function(){
	    $(this).addClass("redBorder").siblings().removeClass("redBorder");
		var indexs=$(this).index();
		$(".jqzoom").eq(indexs).show().siblings(".jqzoom").hide();
	 });
	 $("#jia").click(function(){
		var now=parseInt($("#num").val());
		var total=parseInt($(".total").text());
			if(now<total)
			{var next=now+1;}else{var next=total;}
			$("#num").val(next);
	});
	$("#jian").click(function(){
			var total=parseInt($(".total").text());
			if(now>1){
					var next=now-1;
				}else{var next=1};
			$("#num").val(next);
		});
		var $bottomTools = $('.bottom_tools');
	    var $qrTools = $('.qr_tool');
	    var qrImg = $('.qr_img');
	
	$(window).scroll(function () {
		var scrollHeight = $(document).height();
		var scrollTop = $(window).scrollTop();
		var $windowHeight = $(window).innerHeight();
		scrollTop > 50 ? $("#scrollUp").fadeIn(200).css("display","block") : $("#scrollUp").fadeOut(200);			
		$bottomTools.css("bottom", scrollHeight - scrollTop > $windowHeight ? 40 : $windowHeight + scrollTop + 40 - scrollHeight);
	});
	
	$('#scrollUp').click(function (e) {
		e.preventDefault();
		$('html,body').animate({ scrollTop:0});
	});
	
	$qrTools.hover(function () {
		qrImg.fadeIn();
	}, function(){
		 qrImg.fadeOut();
	});
	
   })