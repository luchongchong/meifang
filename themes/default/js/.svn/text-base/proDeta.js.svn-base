// JavaScript Document
//获得焦点
$(function(){
	$("input.text").focus(function(){
		if($(this).val()==this.defaultValue){
			$(this).val("");	
		}	
	}).blur(function(){
		if($(this).val()==""){
			$(this).val(this.defaultValue);
		}	 
	});	
//切换商品（商品详情）
$(".jq .jqzoom:gt(0)").hide();
$(".imgSmall img:first").addClass("imgBor")
$(".imgSmall img").mouseover(function(){
	$(this).addClass("imgBor").siblings("img").removeClass("imgBor");
	var index = $(".imgSmall img").index(this);
	$(".jq .jqzoom").eq(index).show().siblings().hide();
	})
//切换商品介绍（商品详情）
$("#inList li:first").addClass("inse");
$("#inCont .intro:gt(0)").hide();
$("#inList li").mouseover(function(){
	$(this).addClass("inse").siblings().removeClass("inse");
	var index = $("#inList li").index(this);
	$("#inCont .intro").eq(index).show().siblings().hide();
	})
//切换小商品
$(".title li.limg img").click(function(){
	$(this).addClass("imgBor").siblings().removeClass();
	})
$(".title li.limg span").click(function(){
	$(this).addClass("imgBor").siblings().removeClass();
	})	
//选购买数量
var total = parseInt($("b.ku a").text());
$("input.payJian").click(function(){
	var now = parseInt($("input.payText").val());
	if(now>1){
		var next = now-1;
		}
	else{
		var next = 1;
		}
	$("input.payText").val(next);
	})
$("input.payJia").click(function(){
	var now = parseInt($("input.payText").val());
	if(now<total){
		var next =now+1;
		}
	else{
		var next = total;
		}
	$("input.payText").val(next);
	});
		
/*//返回顶部	
	var $backToTopTxt = "返回顶部", $backToTopEle = $('<div class="backToTop"></div>').appendTo($("body"))
        .text($backToTopTxt).attr("title", $backToTopTxt).click(function() {
            $("html, body").animate({ scrollTop: 0 }, 120);
    }), $backToTopFun = function() {
        var st = $(document).scrollTop(), winh = $(window).height();
        (st > 0)? $backToTopEle.show(): $backToTopEle.hide();
        //IE6下的定位
        if (!window.XMLHttpRequest) {
            $backToTopEle.css("top", st + winh - 166);
        }
    };
    $(window).bind("scroll", $backToTopFun);
    $(function() { $backToTopFun(); });*/

});














