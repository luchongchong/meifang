(function($){
	$.fn.dropdown = function(settings){
		var defaults = {
			dropdownEl: '.dropdown-menu',
			openedClass: 'dropdown-open',
			delayTime: 100
		}
		var opts = $.extend(defaults, settings);
		return this.each(function(){
			var curObj = null;
			var preObj = null;
			var openedTimer = null;
			var closedTimer = null;
			$(this).hover(function(){
				if(openedTimer)
				clearTimeout(openedTimer);
				curObj = $(this);
				openedTimer = setTimeout(function(){
					curObj.addClass(opts.openedClass).find(opts.dropdownEl).show();
				},opts.delayTime);
				preObj = curObj;
			},function(){
				if(openedTimer)
				clearTimeout(openedTimer);
				openedTimer = setTimeout(function(){
					preObj.removeClass(opts.openedClass).find(opts.dropdownEl).hide();
				},opts.delayTime);
			});
			//点击事件关闭菜单
			$(this).bind('click', function(){
				if(openedTimer)
				clearTimeout(openedTimer);
				openedTimer = setTimeout(function(){
					preObj.removeClass(opts.openedClass).find(opts.dropdownEl).hide();
				},opts.delayTime);
			});
		});
	};

	// 图片轮播
	$.fn.slideBox = function(options) {
        //默认参数
        var defaults = {
            direction : 'left',//left,top
            duration : 0.6,//unit:seconds
            easing : 'swing',//swing,linear
            delay : 3,//unit:seconds
            startIndex : 0,
            hideClickBar : true,
            clickBarRadius : 5,//unit:px
            hideBottomBar : false,
            width : null,
            height : null
        };
        var settings = $.extend(defaults, options || {});
        //计算相关数据
        var wrapper = $(this), ul = wrapper.children('ul.items'), lis = ul.find('li'), firstPic = lis.first().find('img');
        var li_num = lis.size(), li_height = 0, li_width = 0;
        //定义滚动顺序:ASC/DESC.ADD.JENA.201208081718
        var order_by = 'ASC';
        //初始化
        var init = function(){
            if(!wrapper.size()) return false;
            //手动设定值优先.ADD.JENA.201303141309
            li_height = settings.height ? settings.height : lis.first().height();
            li_width = settings.width ? settings.width : lis.first().width();
             
            wrapper.css({width: li_width+'px', height:li_height+'px'});
            lis.css({width: li_width+'px', height:li_height+'px'});//ADD.JENA.201207051027
             
            if (settings.direction == 'left') {
                ul.css('width', li_num * li_width + 'px');
            } else {
                ul.css('height', li_num * li_height + 'px');
            }           
            ul.find('li:eq('+settings.startIndex+')').addClass('active');
             
            if(!settings.hideBottomBar){//ADD.JENA.201208090859
                var tips = $('<div class="tips"></div>').css('opacity', 0.6).appendTo(wrapper);
                var title = $('<div class="title"></div>').html(function(){
                    var active = ul.find('li.active').find('a'), text = active.attr('title'), href = active.attr('href');
                    return $('<a>').attr('href', href).text(text);
                }).appendTo(tips);
                var nums = $('<div class="nums"></div>').hide().appendTo(tips);
                lis.each(function(i, n) {
                    var a = $(n).find('a'), text = a.attr('title'), href = a.attr('href'), css = '';
                    i == settings.startIndex && (css = 'active');
                    $('<a>').attr('href', href).text(text).addClass(css).css('borderRadius', settings.clickBarRadius+'px').mouseover(function(){
                        $(this).addClass('active').siblings().removeClass('active');
                        ul.find('li:eq('+$(this).index()+')').addClass('active').siblings().removeClass('active');
                        start();
                        stop();
                    }).appendTo(nums);
                });
             
                if(settings.hideClickBar){//ADD.JENA.201206300847
                    tips.hover(function(){
                        nums.animate({top: '0px'}, 'fast');
                    }, function(){
                        nums.animate({top: tips.height()+'px'}, 'fast');
                    });
                    nums.show().delay(2000).animate({top: tips.height()+'px'}, 'fast');
                }else{
                    nums.show();
                }
            }
             
            lis.size()>1 && start();
        }
        //开始轮播
        var linear_num = 0;
        var start = function() {
            var active = ul.find('li.active'), active_a = active.find('a');
            var index = active.index();

            if(settings.easing == "linear"){
                var first_li = ul.find('li:first');
                var mt = -parseInt(li_height),
                    ml = -parseInt(li_width);

                if(settings.direction == 'left'){
                    param = {'left':ml + 'px'};
                }else{
                    param = {'top':mt + 'px'};
                }
                if(linear_num<1){
                    setTimeout(function(){
                        ul.stop().animate(param, settings.duration*1000, settings.easing, function(i){
                            ul.css('left', '0');
                        });
                    }, settings.delay*1000);
                    linear_num++;
                    console.log(index)
                }else{
                    var len = ul.find('li').length;
                    index = len + 1 - index;
                    if(index == len) index=0;
                    if(index == len+1) index=1;

                    wrapper.find('.nums').find('a:eq('+index+')').addClass('active').siblings().removeClass('active');
                    wrapper.find('.title').find('a').attr('href', active_a.attr('href')).text(active_a.attr('title'));

                    ul.find('li:last').after(first_li.clone());
                    ul.stop().animate(param, settings.duration*1000, settings.easing, function(i){
                        first_li.remove();
                        ul.css('left', '0');
                    });                    
                }
                
            }else{
                wrapper.find('.nums').find('a:eq('+index+')').addClass('active').siblings().removeClass('active');
                wrapper.find('.title').find('a').attr('href', active_a.attr('href')).text(active_a.attr('title'));
                if(settings.direction == 'left'){
                    offset = index * li_width * -1;
                    param = {'left':offset + 'px' };
                }else{
                    offset = index * li_height * -1;
                    param = {'top':offset + 'px' };
                }
             
                ul.stop().animate(param, settings.duration*1000, settings.easing, function() {
                    active.removeClass('active');
                    if(order_by=='ASC'){
                        if (active.next().size()){
                            active.next().addClass('active');
                        }else{
                            order_by = 'DESC';
                            active.prev().addClass('active');
                        }
                    }else if(order_by=='DESC'){
                        if (active.prev().size()){
                            active.prev().addClass('active');
                        }else{
                            order_by = 'ASC';
                            active.next().addClass('active');
                        }
                    }
                });

            }
            wrapper.data('timeid', window.setTimeout(start, settings.delay*1000));
        };
        //停止轮播
        var stop = function() {
            window.clearTimeout(wrapper.data('timeid'));
        };
        //鼠标经过事件
        wrapper.hover(function(){
            stop();
        }, function(){
            wrapper.data('timeid', window.setTimeout(start, settings.delay*1000))
        }); 
        //首张图片加载完毕后执行初始化
        var imgLoader = new Image();
        imgLoader.onload = function(){
            imgLoader.onload = null;
            init();
        }
        imgLoader.src = firstPic.attr('src');
             
    };

    // hover延迟
    $.fn.hoverDelay = function(options){
        var defaults = {
            hoverDuring: 200,
            outDuring: 200,
            hoverEvent: function(){
                $.noop();
            },
            outEvent: function(){
                $.noop();    
            }
        };
        var sets = $.extend(defaults,options || {});
        var hoverTimer, outTimer, that = this;
        return $(this).each(function(){
            $(this).hover(function(){
                clearTimeout(outTimer);
                hoverTimer = setTimeout(function(){sets.hoverEvent.apply(that)}, sets.hoverDuring);
            },function(){
                clearTimeout(hoverTimer);
                outTimer = setTimeout(function(){sets.outEvent.apply(that)}, sets.outDuring);
            });    
        });
    }

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

    // 按钮小轮播
    $.fn.btnScroll = function(showCount){
        var wrap = $(this),
            prev = wrap.find('.prev'),
            next = wrap.find('.next'),
            ul   = wrap.find('ul'),
            unit = wrap.find('li');
            unit_length =  unit.width() + parseInt(unit.css('margin-left'));
            ul.css('left', '0');

        prev.on('click', function(){
            var left = parseInt(ul.css('left'));
            if (left < 0) {
                ul.animate({'left': (left+unit_length)+'px'},100);
            };
        })
        next.on('click', function(){
            var left = parseInt(ul.css('left'));
            if( showCount*unit_length - left  < unit_length*unit.length){
                ul.animate({'left': left-unit_length +'px'},100);
            }
        })

    }
})(jQuery);


//工具类
(function() {
    var that = this,
        preventDefault, panel, panelBg, delay, count = 0,
        toastPanel, temp;

    //自定义提示框，依赖jquery
    var TipPanel = function(el, options) {
        var that = this;

        that.panel = el || $('#ti-panel');
        that.panelBg = panelBg || $('#ti-panel-bg');
        that.panelTitle = that.panel.find('.panel-title');
        that.panelTips = that.panel.find('.panel-tips');
        that.close = that.panel.find('.panel-close');
        that.btnOk = that.panel.find('.btn-ok');
        that.btnCancel = that.panel.find('.btn-cancel');
        that.panelText = that.panel.find('.panel-text');
        that.panelTick = that.panel.find('.panel-tick');


        that.options = {
            type: 'error',
            tick: 0,
            okText: '确定',
            cancelText: '取消',
            showTitle: true,
            showTips: false
        };


        //确认关闭
        that.panel.on('click', '.btn-ok', function(e) {
            e.preventDefault();
            that.hide(true);
        });

        //取消
        that.panel.on('click', '.btn-cancel', function(e) {
            e.preventDefault();
            that.hide();
        });
    };

    TipPanel.prototype = {
        delay: undefined,
        count: 0,
        setOptions: function(options) {
            var that = this;

            for (i in options) that.options[i] = options[i];

            if (that.options.showTitle) {
                that.panelTitle.show();
            } else {
                that.panelTitle.hide();
            }
            if (that.options.showTips) {
                that.panelTips.show();
            } else {
                that.panelTips.hide();
            }
            if (that.options.okText) {
                that.btnOk.text(that.options.okText);
            }
            if (that.options.cancelText) {
                that.btnCancel.text(that.options.cancelText);
            }
            if (that.options.tipsText) {
                that.panelTips.html(that.options.tipsText);
            }
            if (that.options.type == 'confirm') {
                that.btnOk.show();
                that.btnCancel.show();
            } else {
                that.btnOk.show();
                that.btnCancel.hide();
            }
            that.panelText.html(that.options.message);
            that.panel.css('margin-top', -(that.panel.height() / 2)).show();
            that.panelBg.show();

            if (that.options.tick > 1000) {
                that.panelTick.text(that.options.tick / 1000);
                that.delay = setInterval(function() {
                    if (that.count < that.options.tick - 1000) {
                        that.count = count + 1000;
                        that.panelTick.text((that.options.tick - count) / 1000);
                    } else {
                        that._end();
                        that.count = 0;
                        clearInterval(that.delay);
                    }
                }, 1000);
            } else if (that.options.tick <= 1000 && that.options.tick > 0) {
                that.delay = setTimeout(function() {
                    that._end();
                }, that.options.tick);
            }
        },
        _end: function() {
            var that = this;

            that.panel.hide();
            that.panelBg.hide();

            if (typeof that.options.tipsCallback == 'function') {
                that.options.tipsCallback();
                that.options.tipsCallback = undefined;
            } else if (typeof that.options.yesCallback == 'function') {
                that.options.yesCallback();
                that.options.yesCallback = undefined;
            }
        },
        show: function() {

        },
        hide: function(yesClick) {
            var that = this;

            if (that.delay) {
                clearTimeout(that.delay);
            }
            if (!that.panel) {
                return;
            }
            that.panel.hide();
            that.panelBg.hide();

            if (yesClick) {
                typeof that.options.yesCallback == 'function' && that.options.yesCallback();
            } else {
                typeof that.options.noCallback == 'function' && that.options.noCallback();
            }
            that.options.yesCallback = undefined;
            that.options.noCallback = undefined;
        }
    }

    //按指定格式格式化日期
    function format(date, pattern) {
        var that = date;
        var o = {
            "M+": that.getMonth() + 1,
            "d+": that.getDate(),
            "h+": that.getHours(),
            "m+": that.getMinutes(),
            "s+": that.getSeconds(),
            "q+": Math.floor((that.getMonth() + 3) / 3),
            "S": that.getMilliseconds()
        };
        if (/(y+)/.test(pattern)) {
            pattern = pattern.replace(RegExp.$1, (that.getFullYear() + "")
                .substr(4 - RegExp.$1.length));
        }
        for (var k in o) {
            if (new RegExp("(" + k + ")").test(pattern)) {
                pattern = pattern.replace(RegExp.$1, RegExp.$1.length == 1 ? o[k] : ("00" + o[k]).substr(("" + o[k]).length));
            }
        }
        return pattern;
    };

    var Tools = {
        // 货币格式化，2050.5=>2,050.5
        formatCurrency1: function(content, defaultValue, unit) {
            if (!content) {
                return defaultValue || '--';
            }

            content = content + ''; //转字符串

            var prefix, subfix, idx = content.indexOf('.');
            if (idx > 0) {
                prefix = content.substring(0, idx);
                subfix = content.substring(idx, content.length);
            } else {
                prefix = content;
                subfix = '';
            }

            var mod = prefix.toString().length % 3;
            var sup = '';
            if (mod == 1) {
                sup = '00';
            } else if (mod == 2) {
                sup = '0';
            }

            prefix = sup + prefix;
            prefix = prefix.replace(/(\d{3})/g, '$1,');
            prefix = prefix.substring(0, prefix.length - 1);
            if (sup.length > 0) {
                prefix = prefix.replace(sup, '');
            }
            if (subfix) {
                if (subfix.length == 2) {
                    subfix += '0';
                } else if (subfix.length == 1) {
                    subfix += '00';
                }
                subfix = subfix.substring(0, 3);
            }
            return prefix + subfix;
        },
        strToDate: function(str) { //字符串转日期，yyyy-MM-dd hh:mm:ss
            var tempStrs = str.split(" ");
            var dateStrs = tempStrs[0].split("-");
            var year = parseInt(dateStrs[0], 10);
            var month = parseInt(dateStrs[1], 10) - 1;
            var day = parseInt(dateStrs[2], 10);

            var timeStrs = tempStrs[1].split(":");
            var hour = parseInt(timeStrs[0], 10);
            var minute = parseInt(timeStrs[1], 10) - 1;
            var second = parseInt(timeStrs[2], 10);
            var date = new Date(year, month, day, hour, minute, second);
            return date;
        },
        //获取URL参数
        getQueryValue: function(key) {
            var q = location.search,
                keyValuePairs = new Array();

            if (q.length > 1) {
                var idx = q.indexOf('?');
                q = q.substring(idx + 1, q.length);
            } else {
                q = null;
            }

            if (q) {
                for (var i = 0; i < q.split("&").length; i++) {
                    keyValuePairs[i] = q.split("&")[i];
                }
            }

            for (var j = 0; j < keyValuePairs.length; j++) {
                if (keyValuePairs[j].split("=")[0] == key) {
                    // 这里需要解码，url传递中文时location.href获取的是编码后的值
                    // 但FireFox下的url编码有问题
                    return decodeURI(keyValuePairs[j].split("=")[1]);

                }
            }
            return '';
        },
        //时间戳格式化
        formatDate: function(content, type) {
            var pattern = type || "yyyy-MM-dd hh:mm";
            if (isNaN(content) || content == null) {
                return content;
            } else if (typeof(content) == 'object') {
                var y = dd.getFullYear(),
                    m = dd.getMonth() + 1,
                    d = dd.getDate();
                if (m < 10) {
                    m = '0' + m;
                }
                var yearMonthDay = y + "-" + m + "-" + d;
                var parts = yearMonthDay.match(/(\d+)/g);
                var date = new Date(parts[0], parts[1] - 1, parts[2]);
                return format(date, pattern);
            } else {
                var date = new Date(parseInt(content));
                return format(date, pattern);
            }
        },
        // 获取窗口尺寸，包括滚动条
        getWindow: function() {
            return {
                width: window.innerWidth,
                height: window.innerHeight
            };
        },
        // 获取文档尺寸，不包括滚动条但是高度是文档的高度
        getDocument: function() {
            var doc = document.documentElement || document.body;
            return {
                width: doc.clientWidth,
                height: doc.clientHeight
            };
        },
        // 获取屏幕尺寸
        getScreen: function() {
            return {
                width: screen.width,
                height: screen.height
            };
        },
        // 显示、禁用滚动条
        showOrHideScrollBar: function(isShow) {
            preventDefault = preventDefault || function(e) {
                e.preventDefault();
            };
            (document.documentElement || document.body).style.overflow = isShow ? 'auto' : 'hidden';
            // 手机浏览器中滚动条禁用取消默认touchmove事件
            if (isShow) {
                // 注意这里remove的事件必须和add的是同一个
                document.removeEventListener('touchmove', preventDefault, false);
            } else {
                document.addEventListener('touchmove', preventDefault, false);
            }
        },
        // 显示对话框
        showDialog: function() {},
        // 显示着遮罩层
        showOverlay: function() {},
        // 显示确认框
        showConfirm: function(msg, yesCallback, noCallback) {
            var opt = {};
            if (typeof msg == 'object') {
                opt = msg;
            } else {
                opt.message = msg;
                opt.yesCallback = yesCallback;
                opt.noCallback = noCallback;
            }
            opt.type = 'confirm';
            opt.showTitle = true;
            opt.showTip = false;

            panel = panel || new TipPanel();
            panel.setOptions(opt);
        },
        // 显示提示
        showAlert: function(msg, tick, callback) {
            var opt = {};
            if (typeof msg == 'object') {
                opt = msg;
            } else {
                opt.message = msg;
                opt.tick = tick;
                opt.yesCallback = callback;
            }
            opt.type = 'alert';

            panel = panel || new TipPanel();
            panel.setOptions(opt);
        },
        // 显示加载框
        showLoading: function() {
            $('#ti-loading').show();
        },
        hideLoading: function() {
            $('#ti-loading').hide();
        },
        hidePanel: function(yesClick) {
            panel && panel.hide(yesClick);
        },
        showToast: function(msg, tick) {
            toastPanel = toastPanel || $('#ti-toast');
            tick = tick || 600;

            if (delay) {
                clearTimeout(delay);
            }

            toastPanel.find('span').text(msg);
            toastPanel.show();
            delay = setTimeout(function() {
                toastPanel.hide();
            }, tick);
        },
        isIPad: function() {
            //5.0 (iPad; CPU OS 8_1_1 like Mac OS X) AppleWebKit/600.1.4 (KHTML, like Gecko) Mobile/12B435
            return (/iPad/gi).test(navigator.appVersion);
        },
        // 将form中的值转换为键值对
        formJson: function(form) {
            var o = {};
            var a = $(form).serializeArray();
            $.each(a, function() {
                if (o[this.name] !== undefined) {
                    if (!o[this.name].push) {
                        o[this.name] = [o[this.name]];
                    }
                    o[this.name].push(this.value || '');
                } else {
                    o[this.name] = this.value || '';
                }
            });
            return o;
        }
    };

    window.Tools = Tools;

    // 关闭
    $('.panel-close').on('click',function(){
        $(this).parent().hide()
        $('#ti-panel-bg').hide()
    })
})();

// 扩展String的方法
(function() {
    String.prototype.isSpaces = function() {
        for (var i = 0; i < this.length; i += 1) {
            var ch = this.charAt(i);
            if (ch != ' ' && ch != "\n" && ch != "\t" && ch != "\r") {
                return false;
            }
        }
        return true;
    };

    String.prototype.isUserName = function() {
        return (new RegExp(/^[\x{4e00}(-)\x{9fa5}\w]|[(-)_]|[a-zA-Z0-9@]{4,20}$/).test(this));
    };
    String.prototype.isValidMail = function() {
        return (new RegExp(
                /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/)
            .test(this));
    };

    String.prototype.isPhone = function() {
        return (new RegExp(/^1\d{10}?$/).test(this));
    };

    String.prototype.isEmpty = function() {
        return (/^\s*$/.test(this));
    };

    String.prototype.isValidPwd = function() {
        return (new RegExp(/^([a-zA-Z0-9@]|[(-)_!@#$%^&*]){6,20}$/).test(this));
    };

    String.prototype.isPostCode = function() {
        return (new RegExp(/^\d{6}?$/).test(this));
    };

})();
