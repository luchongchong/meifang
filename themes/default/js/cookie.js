//cookie读写
(function() {
    var Cookie = {
        get: function(sname) {
            var sre = "(?:;)?" + sname + "=([^;]*);?";
            var ore = new RegExp(sre);
            if (ore.test(document.cookie)) {
                try {
                    return unescape(RegExp["$1"]); // decodeURIComponent(RegExp["$1"]);
                } catch (e) {
                    return null;
                }
            } else {
                return null;
            }
        },

        //expires默认7天，0不设置，-1移除
        set: function(c_name, value, expires) {
            if (typeof expires == 'number') {
                if (expires == 0) {
                    expires = null;
                } else {
                    expires = this.getExpDate(expires, 0, 0);
                }
            } else {
                expires = expires || this.getExpDate(7, 0, 0);
            }
            document.cookie = c_name + "=" + escape(value) + ((expires == null) ? "" : ";expires=" + expires) + "; path=/";
        },

        remove: function(key) {
            this.set(key, '', -1);
        },

        getExpDate: function(e, t, n) {
            var r = new Date;
            if (typeof e == "number" && typeof t == "number" && typeof t == "number")
                return r.setDate(r.getDate() + parseInt(e)), r.setHours(r
                        .getHours() + parseInt(t)), r.setMinutes(r.getMinutes() + parseInt(n)),
                    r.toGMTString()
        }
    };
    window.Cookie = Cookie;
})();
