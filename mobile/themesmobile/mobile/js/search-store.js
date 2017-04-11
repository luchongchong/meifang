(function() {
	var sp = new SecondPage('.sidebar'), sh=[], 
		str,
		empStr = '<li class="no-histoty"><img src="../images/no.png" /><p>暂无历史记录</p></li>';
	
	// 打开搜索历史页面
	$('#searchStore').focus(function(){
		// 搜索历史
		if(Storage.get('SEARCH_HISTORY')){
			sh = Storage.get('SEARCH_HISTORY');
			str = '';
			for (var i = sh.length - 1; i >= 0; i--) {
				str += '<li class="items' + isLast(i) +'"><a href="javascript:void(0)">' + sh[i] + '</a></li>'
			};
			str += '<li class="options"><button class="btn_middle btn_white" id="emptySH">清空历史记录</button></li>'
			$('#searchHistory').html(str);

			$('#searchHistory > li > a').on('click',function(){
				$('#searchStore').val($(this).text());
			})
			$('#emptySH').on('click',function(){
				$('#searchHistory').html(empStr);
				Storage.remove('SEARCH_HISTORY');
			})
		}
		sp.openSidebar()
	})

	// 搜索表单提交
	$('.serach-store-form').submit(function(e){
		e.preventDefault();
		// 记录历史
		var search = $('#searchStore').val(),
			b = true;
		if(Storage.get('SEARCH_HISTORY')){
			for (var i = sh.length - 1; i >= 0; i--) {
				if(search == sh[i])
					b =false;
			}
		}
		if(b){
			sh.push(search);
			Storage.set('SEARCH_HISTORY',sh);			
		}
		
		// 搜索完成后
		sp.closeSidebar();
	})

	function isLast(b){
		return Boolean(b) ? ' ':' last-items';
	}
})()