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

	        })
	        .fail(function() {
	        	console.log("error");
	        })  
	    })
	}
})
