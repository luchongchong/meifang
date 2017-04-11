(function(){

    // supplier list
    $("#supplierList .del").on("click",function(){
        if(confirm("确定要删除数据吗？"))
            $(this).parent().remove();
    })

    //add supplier 2
    $("#supplierAddList > li").on("click",function(){
        var _this=$(this);
        if( _this.hasClass("select")){
            _this.removeClass("select");
        }else{            
            _this.addClass("select");
        }
    })
})()