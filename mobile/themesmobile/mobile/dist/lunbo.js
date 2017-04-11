$(function(){
	var swiper1 = new Swiper('.swiper1', {
		pagination : '.pagination1',
		loop:true,
		autoplay:3000,
		speed:600,
		grabCursor: true
	});
    $('.pagination1 .swiper-pagination-switch').click(function(){
    	swiper1.swipeTo($(this).index())
    });
})

