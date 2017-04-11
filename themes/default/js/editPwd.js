(function(){
	$("#countrySelect").divselect("#country");
	$("#countrySelect li").hover(function(){
		$(this).css('background-color','#eee');
	},function(){
		$(this).css('background-color','#fff');
	});
	// 
	$('#nextStep').on('click', function(){
		$('#step1Form').hide();
		$('#step2Form').show();
		$('#stepLine').animate({'width':'508px'},100)
			.parent().find('.s2').addClass('on');

	});
	// 
	$('#nextnextStep').on('click', function(){
		$('#step2Form').hide();
		$('#overEdit').show();
		$('#stepLine').animate({'width':'762px'},100)
			.parent().find('.s3').addClass('on');
	});
})()