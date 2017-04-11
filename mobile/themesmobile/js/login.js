(function(){
	$('#loginFormTit span').on('click', function(){
		var that = $(this), line_w,
			line = $('#loginFormTit .line');
			index = that.index();
		index == 0 ? line_w = '0': line_w = '50%';
		line.animate({'left':line_w},100);
		
		that.addClass('on').siblings('span').removeClass('on');

		$('.login-box').find('.logType').eq(index).show()
			.siblings('.logType').hide();
	})

	var btnsubmit = $('input[type="submit"]');
	
	$('#login-form').submit(function(e){
		e.preventDefault();
		
		var phone = $('input[name="name"]').val(),
			password = $('input[name="password"]').val();
		if(phone.isEmpty()){
			Tools.showAlert('手机号不能为空',5000);
			return;
		}
		if(!phone.isPhone()){
			Tools.showAlert('手机号格式不正确',5000);
			return;
		}
		if(password.isEmpty()){
			Tools.showAlert('密码不能为空',5000);
			return;
		}
		if(!password.isValidPwd()){
			Tools.showAlert('密码格式不正确',5000);
			return;
		}
		
		Ajax.submit({
			url: config.api_login,
			data: $(this)
		}, function(data){
			if(data.error){
				Tools.showAlert('用户名或密码错误',5000);
				return;
			}
			
			//记住我，若记住则记录用户手机号以便下次登录
			if($('input[name="remember"]:checked').length > 0){
				Storage.set(Storage.REMEMBER, phone);
				Storage.set('FLV-PASSWORD', password);
			}else{
				Storage.remove(Storage.REMEMBER);
			}

			Cookie.set(Storage.AUTH,data.data._id);
			Storage.set(Storage.AUTH, data.data._id);
			Storage.set(Storage.ACCOUNT,data.data);
			
			var from = Tools.getQueryValue('from');
			if(from){
				location.href = decodeURIComponent(from);
			}else{
				location.href = "../user/index.html";
			}
		});
	});
})();