// JavaScript Document
$(function(){
	//搜索框默认值清除/还原
	$(".sousuo").focus(function(){
	if(this.value==this.defaultValue)
	{
		this.value="";
	}
	}).blur(function(){
	if(this.value=="")
	{
		this.value=this.defaultValue;
	}
	})
	})
//这是首页banner自动切换效果
	//实现初始化效果
	$(function(){
	$("#imgList .imgs a:not(:first)").hide();
	//实现页码效果
	var nums=$(".imgs img").length;//获取图片数量
	$(".page span").eq(0).addClass("current");
	//鼠标经过页码进行图片切换
	$(".page span").mouseover(function(){
		$(this).addClass("current").siblings().removeClass("current");
		var index=$(this).index();
		$(".imgs a").eq(index).stop().fadeIn(1000).siblings().hide();
	})
	//图片进行自动切换
	aut=setInterval(function(){
		var nexts;//下一页索引值
		var nows=$(".current").index();//当前索引值
		if((nows+1)<nums)
		{
			nexts=nows+1;
		}else
		{
			nexts=0;
		}
		$(".page span").eq(nexts).trigger("mouseover");
	},4000)
	//鼠标经过图片停止切换
	$(".imgs img").mouseover(function(){
		    clearInterval(aut);
		  }).mouseout(function(){
			  aut = setInterval(function(){
		 var nexts//下一页索引值
		 var nows = $(".current").index(); //当前页索引值
		 if((nows+1)<nums)
		 {
			 nexts=nows+1;
		 }
		 else
		 {
			nexts=0; 
		 }
		 $(".page span").eq(nexts).trigger("mouseover");
		},4000);
	});	
})
	//选项卡
$(function(){
	$("#tabList li img:gt(0)").hide();
	$("#tabCont .conts:not(:first)").hide();
	$("#tabList li:eq(0)").addClass("current1");
	$("#tabList li").mouseover(function(){
		$(this).addClass("current1").siblings("li").removeClass("current1");
		
		$(this).find("img").show();
		$(this).siblings("li:not(:first)").find("img").hide();
		var index=$(this).index();
		$("#tabCont .conts").eq(index).show().siblings().hide();
		})
})
//模块左侧特效
	$(function(){
	$("dl.pin1 .hide").hide();
	/*$("dl.pin1 .hide").css("opacity",0.5);
	if(!$(this).is(":animated")){
	$("dl.pin1").mouseover(function(){
		$(this).find(".hide").show();
		$(this).find(".hide").stop().animate({"top":"0"},100);
		}).mouseout(function(){
		$(this).find(".hide").hide();
		$(this).find(".hide").stop().animate({"top":"305px"},1);
	})
	}*/
	$("dl.pin1 dt a").mouseover(function(){
		$(this).find("img").css("border","2px solid #ccc");
	}).mouseout(function(){
		$(this).find("img").css("border","0");	
	});
})
	//第三个产品列表特效
$(function(){
	if(!$(this).is(":animated")){
	$("#module1 .box").mouseover(function(){
		$(this).find("dl.small dd a").css("color","#f00");
		$(this).find("dl.small").stop().animate({"bottom":"23px","left":"8px","width":"170px","height":"260px"},100)}).mouseout(function(){
			$(this).find("dl.small dd a").css("color","#666");
			$(this).find("dl.small").stop().animate({"bottom":"0","left":"0","width":"100%","height":"100%"},10);
			})
		}	
	})
	//这是首页内容（wrapper）部分鼠标经过图片半透明并添加红色边框的效果
	$(function(){
		
	$("#module1 dl.imgShow").mouseover(function(){
		$(this).find(".redBorder").css("border","5px solid #ccc")
		$(this).find("dt").css("opacity",0.5);
		$(this).css("background-color","#ccc")
		}).mouseout(function(){
		$(this).find(".redBorder").css("border","5px solid #fff")
		$(this).find("dt").css("opacity",1);
		$(this).css("background-color","#fff")
		})
		
		})