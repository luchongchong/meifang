(function(){
	$("#filter1").divselect("#input1");
	$("#filter2").divselect("#input2");
	$("#filter3").divselect("#input3");
	$("#filter4").divselect("#input4");
	$("#filter5").divselect("#input5");

	$(".yang_attr li").hover(function(){
		$(this).css('background-color','#eee');
	},function(){
		$(this).css('background-color','#fff');
	});
})()