/*老的加载代码
$(window).scroll(function () {
		if ($(window).scrollTop() == $(document).height() - $(window).height()) {
			$('.get_more').click();
		}
});
*///
//   var upScrollTop;
//   $headerFixed = $("._pre");
//   _head_height = $headerFixed.height();
//   
//   $touchweb_com_searchListBox=$(".touchweb-com_searchListBox");
//			var docH;
//			var winH;
//			var scrollH;
//			var productH;
//		
//  $(window).scroll(function() {
//           $top为scroll后位置的高度，upScrollTop为scroll前位置的高度
//            var $top = $(window).scrollTop();
//            upScrollTop = upScrollTop || $top;
//
//            计算scroll高度
//	        docH = $(document).height();
//			winH = $(window).height();
//			productH = $touchweb_com_searchListBox.find('li').height();
//			scrollH = docH - winH - productH * 2 - 50;
//
//            if (upScrollTop > _head_height) { // 向下滚动
//
//                if($top >= scrollH){  // 向下滚动到底部加载下一页
//				   
//	            	$('.get_more').click();
//				}
//            }
//
//});
/*var is_stop = true;
$(window).scroll(function () {
	if(is_stop){
		if ($(window).scrollTop()== $(document).height() - $(window).height()) {
		is_stop = false;
		$('.get_more').click();
		is_stop = true;
		}
	}
});*/
/*
$(function(){  
var is_show = true;
$(window).scroll(function() {  
if ($(this).scrollTop() + $(window).height() + 50 >= $(document).height() && $(this).scrollTop() > 50 && is_show==true) {  
is_show = false;
			$('.get_more').click();
			 is_show = true;
};
});
});*/

/*
var stop=true; 
var id='{dede:field.typeid/}';
if(id==''){
id=0;
}else{
id=parseInt(id);
}
var page=2;
$(window).scroll(function(){ 
    totalheight = parseFloat($(window).height()) + parseFloat($(window).scrollTop())+100; 
    if($(document).height() <= totalheight){ 
        if(stop==true){ 
            stop=false; 
$.post("/member/ajax_wapload.php",
 	 {
id:id,
p:page
  },
  function(data,status){
  $(".wrap").html($(".wrap").html()+data); 
page++;
stop=true; 
  });
               
        } 
    }
});
*/