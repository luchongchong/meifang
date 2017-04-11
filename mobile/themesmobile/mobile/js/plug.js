(function($,window) {
    $.count = function(e){
        this.el = $(e);
        return this.el.each(this.init);
    }
    $.extend($.count.prototype, {
        init: function() { 
            var that=$(this);
            that.on('click', function(){
                var par=that.parent();
                var txtInput=par.find("input"),
                    sub=par.find(".subtract"),
                    add=par.find(".add");
                var num=txtInput.val(),
                    minN=txtInput.attr("minNum"),
                    maxN=txtInput.attr("maxNum");
                if(that.hasClass("edit")){
                    switch(that.attr("btn-role")){
                        case "add":
                            num=parseInt(num)+1;
                            if(!sub.hasClass("edit")) sub.addClass("edit");
                            if(num==maxN) that.removeClass("edit");//最大数量控制
                        break;
                        case "subtract":
                            if(num > 1){
                                num=parseInt(num)-1;
                                if(!add.hasClass("edit")) add.addClass("edit");
                                if(num==minN) that.removeClass("edit");//最小数量控制
                            }
                        break
                    }
                    txtInput.val(num);
                }
            })
        }
    });
    // 数量加减
    new $.count(".change-num");

    // 清空功能
    $.emptyTxt = function(e) {
        this.el = $(e);
        this.el.append("<i class='empty'></i>");
        return this.el.each(this.init)
    };
    $.extend($.emptyTxt.prototype, {
        init: function() { 
            var el = $(this).find("input"), emptyEl = $(this).find(".empty");
            // console.log(emptyEl);
            !el.val() && (emptyEl[0].className = "empty");
            var emp = function() {
                emptyEl[0].className = "empty show";
                !el.val() && (emptyEl[0].className = "empty")
            };
            el.focus(function(){
                $("i.empty").each(function(){
                    this.className = "empty"
                });
                emptyEl[0].className = el.val() ? "empty show" : "empty" ;
            })
            el.keyup(emp);
            emptyEl.click(function(e) {
                el.val("").focus();
                emptyEl[0].className = "empty";
                e.preventDefault()
            })
        }
    })

    // 模拟select
    $.fn.divselect = function(inputselect){
        var val = $(inputselect);
        var ul = $(this).find('ul');
        var label = $(this).find('label');
        label.on('click',function(){
            $('.sel > ul').hide();
            if(ul.css('display') == 'none')
                ul.slideDown('fast');
            else
                ul.slideUp('fast');

            return false;
        })
        ul.on('click', 'li', function(){
            var txt = $(this).html();
            label.html(txt);
            val.val($(this).attr('selectid'));
            ul.hide();
            $(this).addClass('on').siblings().removeClass('on');
        });

        $(document).click(function(){
            ul.hide();
        });
    };

    // 展开隐藏功能
    $.fold = function(e) {
        this.el = $(e);
        return this.el.each(this.init)

    };
    $.extend($.fold.prototype, {
        init: function() { 
            var that = $(this),
                conEl = that.parents('section').find('ul'),
                fH = conEl.height(),
                txtEl = that.find('span'),
                fEl = that.find('i');

            that.on('click',function(){
                if(that.find('i').hasClass('unfold')){
                    conEl.height(fH);
                    fEl.removeClass('unfold');
                    txtEl.text('显示全部商品');
                }else{
                    conEl.height('auto');
                    fEl.addClass('unfold');
                    txtEl.text('隐藏部分商品');
                }
            })
            
        }
    });

})(jQuery,window)
