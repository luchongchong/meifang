// JavaScript Document
	$(function(){
		$("#hobby .con:gt(0)").hide();
		$(".title_num p").mouseover(function(){
			var index =$(this).index();
			$(".con").eq(index).show().siblings(".con").hide();
		})
		/**/
		$("#cnav ul li").hover(function(){
			$(this).stop().animate({left:"-5px"},300);
			$(this).css("border-right","20px solid #f90")
		},function(){
			$(this).stop().animate({left:"-110px"},300);
			$(this).css("border-right","20px solid #666")
		})
		/**/
		$("#cl p").hover(function(){
			$(this).stop().animate({left:"-80px"},300);
		},function(){
			$(this).stop().animate({left:"-170px"},300);
		})
		/*三级城市下拉菜单*/
		$("#tcbox").hide()
		$("#dbg").hide()
		$("form.index input").focus(function(){
			$(this).val("")	
		}).blur(function(){
			var a = $(this).val()
			if(a==""){
				$(this).val("杰克琼斯品牌特惠99元封顶！")	
			}else{
				$(this).val(a)
			}
		})
		/**/
		$("#tcbox form:gt(0)").hide()
		$("#tcbox .drzc p span").click(function(){
			var tcboxindex = $(this).index()
			$(this).addClass("current").siblings().removeClass()	
			$("#tcbox form").eq(tcboxindex).show().siblings("form").hide()
		})
		
		$("#top .T_top .r").click(function(){
			var yscroll=document.documentElement.scrollTop;//等价于$(window).scrollTop() 
			$("#tcbox").show().css("top",yscroll+200+"px");	
			$("#dbg").show().css("height",$(document).height());	
		});
		$(".close").click(function(){
			$("#dbg").hide();
			$("#tcbox").hide();
			return 0;
		})
		$("#sub").click(function(){
			$("#dbg").hide();
			$("#tcbox").hide();
			return 0;	
		});	
		$("#yhm").focus(function(){
			$(this).val("")	
		}).blur(function(){
			var a = $(this).val()
			if(a==""){
				$(this).val("邮箱/手机号/QQ号")	
			}else{
				$(this).val(a)
			}
		})
		$("#pas").focus(function(){
			$(this).val("")
			$(this).attr("type","text")
		}).blur(function(){
			var a = $(this).val()
			if(a==""){
				$(this).val("密码")	
			}else{
				$(this).val(a)
			}
		})
		$("#zck").focus(function(){
			$(this).val("")	
		}).blur(function(){
			var a = $(this).val()
			if(a==""){
				$(this).val("请输入手机号")	
			}else{
				$(this).val(a)
			}
		})
		$("#zcpas").focus(function(){
			$(this).val("")
			$(this).attr("type","text")
		}).blur(function(){
			var a = $(this).val()
			if(a==""){
				$(this).val("密码")	
			}else{
				$(this).val(a)
			}
		})
		$(".zck2").focus(function(){
			$(this).val("")
			$(this).attr("type","text")
		}).blur(function(){
			var a = $(this).val()
			if(a==""){
				$(this).val("请再次输入上面的密码")	
			}else{
				$(this).val(a)
			}
		})
		$("#dxyzm").focus(function(){
			$(this).val("")	
		}).blur(function(){
			var a = $(this).val()
			if(a==""){
				$(this).val("短信验证码")	
			}else{
				$(this).val(a)
			}
		})
		/*登录*/
		$("#top .T_top .zc").click(function(){
			var yscroll=document.documentElement.scrollTop;//等价于$(window).scrollTop() 
			$("#tcbox").show().css("top",yscroll+200+"px");	
			$("#dbg").show().css("height",$(document).height());
		})
		/*注册*/
		/**/
		$("#top .nav ul li").hover(function(){
			$(this).animate({color:"#f00"},500)
		},function(){})
		/**/
		$("#top .banner dl dt:eq(0)").siblings("dt").hide();
		$("#top .banner ul li").mouseover(function(){
			var index = $(this).index()
			$(this).addClass("current").siblings("li").removeClass();
			$("#top .banner dl dt").stop().eq(index).fadeIn(600).siblings("dt").hide();
		})
		var num = $("#top .banner ul li").size();
		auto = setInterval(function(){
			var index = $("#top .banner ul li.current").index();
			if((index+1)<num){
				var next = index+1; 
			}else{
				next = 0;
			}
			$("#top .banner ul li").eq(next).trigger("mouseover");
		},3000)
		$("#top .banner dl dt").hover(function(){
			clearInterval(auto);
		},function(){
		auto = setInterval(function(){
			var index = $("#top .banner ul li.current").index();
			if((index+1)<num){
				var next = index+1; 
			}else{
				next = 0;
			}
			$("#top .banner ul li").eq(next).trigger("mouseover");
		},3000)
		})
		/**/
		$("#index_left .kdn dl").hover(function(){
			$(this).css("border","2px solid #f06")		
		},function(){
			$(this).css("border","2px solid #c1c1c1")			
		})
		/**/
		$("#index_right dl").hover(function(){
			$(this).css("border","1px solid #f06")		
		},function(){
			$(this).css("border","1px solid #c1c1c1")			
		})
		/**/
		$("#ttdj dl dt").css("position","relative")
		$("#ttdj dl dt").hover(function(){
			$(this).stop().animate({right:"10px"},500)		
		},function(){
			$(this).stop().animate({right:"0px"},500)			
		})
		/**/
	})
	/**/
	$(function(){	
/*兴趣切换*/
    	$("#hobby .con:gt(0)").hide();
	    $(".title_num p").mouseover(function(){
		var proindex =$(this).index();
		$(".con").eq(proindex).show().siblings(".con").hide();
		})
/*详情页加减*/
	var kucun=$(".kucun").text();	
		var kucuns=parseInt(kucun);
		var value=$(".wenben").val;
		$(".wenben").keyup(function(){
			var value=$(".wenben").val();
			var values=parseInt(value);
			if(values>kucuns){
				$(".wenben").val(1000);
				}
			})
			
		$(".jia").click(function(){
			var value=$(".wenben").val();
			var values=parseInt(value);
			if(values<kucuns){
				var jias=values+1;
				$(".wenben").val(jias);
				}
			})
		$(".jian").click(function(){
			var value=$(".wenben").val();
			var values=parseInt(value);
			if(values>1){
				var jians=values-1;
				$(".wenben").val(jians);
				}
			})
			
    /*放大镜效果*/
	 var num=$("#smallList img").length;
   $("div.zoomdiv").css("opacity",0.5);
     $("#midList .jqzoom:not(:first)").hide();
	 $("#smallList img").click(function(){
	    $(this).addClass("redBorder").siblings().removeClass("redBorder");
		var fdjndex=$(this).index();
		$(".jqzoom").eq(fdjndex).show().siblings().hide();
	 });
	 setInterval(function(){
		 var next;
		 var now=$(".redBorder").index()
		 if(now<num-1){
			 next=num+1;
			 }
		 else{
			 next=0;
			 }
		 $("#smallList img").eq(next).mouseover();
		 },2000)
		 /*颜色选中效果*/	 
	$(".s_p1 a img").click(function(){
	    $(this).addClass("redBorder").siblings().removeClass("redBorder");
		
		})
	/*尺码选中效果*/	 
	$(".s_p2 span").click(function(){
	    $(this).addClass("redBorder").siblings().removeClass("redBorder");
		
		})	 


})
	$(function(){
		$("#Three #jia").click(function(){
			var now=parseInt($("#write").val());
			var total=parseInt($("#total").text());
			if(now<total){
				 next=now+1;	
			}else{
				 next=total	
			};
			$("#write").val(next);
		});
		
		$("#Three #jian").click(function(){
			var now=parseInt($("#write").val());
			if(now>1){
				 next=now-1;	
			}else{
				 next=1;	
			};
			$("#write").val(next);
		});
		$("#write").keyup(function(){
			var total=parseInt($("#write").text());
			var now=parseInt($("#write").val());
			if(now>total){
				var now=total;	
			};
			if(now<1){
				now=1;	
			}
			$("#write").val();
		});
	})
	