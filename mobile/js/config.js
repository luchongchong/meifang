var config = {
    beginTime: undefined, //第一条数据的时间戳
    endTime: undefined, //最后一条数据的时间戳
    skip: 0,
    page: 1, //当前第几页，从1开始
    pageSize: 10, //默认分页大小
    server: location.protocol + '//' + location.host,
    //server: 'http://42.192.0.11:4001', //测试接口用
    // server: "https://www.ilaw66.com", //测试接口用
    image: location.protocol + '//' + location.host + '/',
    // image: 'http://42.192.0.11:4001'
};

//account
config.api_login = config.server + '/user/quickLogin';//免验证码登陆
config.api_logout = config.server + '/user/logout';//登出
config.api_reg_app = config.server + '/app/user/reg';//手机端用户注册
config.api_reg_resendSMS = config.server + '/app/user/sms';//获取短信验证码

//forget
config.api_forget_resendSMS = config.server + '/app/user/sms';//忘记密码功能重新发送手机验证码
config.api_forget = config.server + '/app/user/forget';//手机端忘记密码

