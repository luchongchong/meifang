(function(){
	new myAddress("province");

	$('#confirmOrder').on('click', function(){
		$('#paymentWrap').show();

		$('#panelBg').show()
	});


	$('#payment-form').submit(function(e){
		e.preventDefault();

		var payPwd = $('input[name="payPwd"]').val();
		if(payPwd.isEmpty()){
			Tools.showToast('支付密码不能为空',900);
			return;
		}
		if(!payPwd.isPayPwd()){
			Tools.showToast('支付密码为6位数字',900);
			return;
		}

		location.href = "../mall/orderOK.html";
	})
})()