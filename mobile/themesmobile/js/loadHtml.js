$(function(){
	if($(".JQ_include").length>0){
	    $(".JQ_include").each(function(){
	        var _self = $(this);
	        $.ajax({
	        	url: $(this).attr("src"),
	        	async:false
	        })
	        .done(function(data) {
	        	_self.prop('outerHTML',data);
				// top
			    $('#top .JQ_tags').dropdown({
			        dropdownEl:'.top_panel',
			        openedClass:'hover'
			    });

				// 所有分类
			    $('#nav .JQ_tags').dropdown({
			        dropdownEl:'.nav_panel',
			        openedClass:'hover'
			    });

	        })
	        .fail(function() {
	        	console.log("error");
	        })  
	    })
	}
})
