<?php

/**
 * ECTouch Open Source Project
 * ============================================================================
 * Copyright (c) 2012-2014 http://ectouch.cn All rights reserved.
 * ----------------------------------------------------------------------------
 * 文件名称：wechat.php
 * ----------------------------------------------------------------------------
 * 功能描述：微信登录插件
 * ----------------------------------------------------------------------------
 * Licensed ( http://www.ectouch.cn/docs/license.txt )
 * ----------------------------------------------------------------------------
 */

/* 访问控制 */
defined('IN_ECTOUCH') or die('Deny Access');

$payment_lang = ROOT_PATH . 'plugins/connect/language/' . C('lang') . '/' . basename(__FILE__);

if (file_exists($payment_lang)) {
    include_once ($payment_lang);
    L($_LANG);
}
/* 模块的基本信息 */
if (isset($set_modules) && $set_modules == TRUE) {
    $i = isset($modules) ? count($modules) : 0;
    /* 类名 */
    $modules[$i]['name'] = '微信登录插件';
    // 文件名，不包含后缀
    $modules[$i]['type'] = 'weixin';

    $modules[$i]['className'] = 'weixin';
    // 作者信息
    $modules[$i]['author'] = 'ECTouch Team';

    // 作者QQ
    $modules[$i]['qq'] = '10000';

    // 作者邮箱
    $modules[$i]['email'] = 'support@ectouch.cn';

    // 申请网址
    $modules[$i]['website'] = 'http://mp.wexin.qq.com';

    // 版本号
    $modules[$i]['version'] = '1.0';

    // 更新日期
    $modules[$i]['date'] = '2014-8-19';
    /* 配置信息 */
    $modules[$i]['config'] = array(
        array('type' => 'text', 'name' => 'app_id', 'value' => ''),
        array('type' => 'text', 'name' => 'app_secret', 'value' => ''),
        array('type' => 'text', 'name' => 'token', 'value' => ''),
    );
    return;
}

/**
 * WECHAT API client
 */
class weixin {

    private $token = '';
    private $appid = '';
    private $appkey = '';
    private $weObj = '';

    /**
     * 构造函数
     *
     * @param unknown $app            
     * @param string $access_token            
     */
    public function __construct($conf) {
        $this->token = $conf['token'];
        $this->appid = $conf['app_id'];
        $this->appsecret = $conf['app_secret'];

        $config['token'] = $this->token;
        $config['appid'] = $this->appid;
        $config['appsecret'] = $this->appsecret;

        $this->weObj = new Wechat($config);
    }

    /**
     * 授权登录地址
     */
    public function act_login($info, $url){
        // 微信浏览器浏览
        if (is_wechat_browser() && ($_SESSION['user_id'] === 0 || empty($_SESSION['openid']))) {
            return $this->weObj->getOauthRedirect($url, 1);
        }
        else{
            show_message("请在微信内访问或者已经登录。", L('relogin_lnk'), url('login', array(
                'referer' => urlencode($this->back_act)
                    )), 'error');
        }
    }

    /**
     * 登录处理
     */
    public function call_back($info, $url, $code, $type){
        if (!empty($code)) {
            $token = $this->weObj->getOauthAccessToken();
            if($token === false){
                logResult('wechat call_back $token ' . $this->weObj->errMsg);
                if(isset($_SESSION['repeat'])){
                    //删除避免重复跳转验证
//                    unset($_SESSION['repeat']);
                }
                return false;
            }
            $userinfo = $this->weObj->getOauthUserinfo($token['access_token'], $token['openid']);
            $_SESSION['wechat_user'] = empty($userinfo) ? array() : $userinfo;
            //公众号信息
            $wechat = model('Base')->model->table('wechat')->field('id, oauth_status')->where(array('type'=>2, 'status'=>1, 'default_wx'=>1))->find();
            $this->update_weixin_user($userinfo, $wechat['id'], $this->weObj);
            if(isset($_SESSION['repeat'])){
                //删除避免重复跳转验证
                unset($_SESSION['repeat']);
            }
            if(!empty($_SESSION['redirect_url'])){
                return array('url'=>$_SESSION['redirect_url']);
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * 更新微信用户信息
     *
     * @param unknown $userinfo          
     * @param unknown $weObj            
     */
    public function update_weixin_user($userinfo, $wechat_id = 0, $weObj)
    {
        $time = time();
        $ret = model('Base')->model->table('wechat_user')->field('openid, ect_uid')->where('openid = "' . $userinfo['openid'] . '"')->find();
        if (empty($ret)) {
            //微信用户绑定会员id
            $ect_uid = 0;
            //查看公众号是否绑定
            if($userinfo['unionid']){
                $ect_uid = model('Base')->model->table('wechat_user')->field('ect_uid')->where(array('unionid'=>$userinfo['unionid']))->getOne();
            }

            //未绑定
            if(empty($ect_uid)){
                // 设置的用户注册信息
                $register = model('Base')->model->table('wechat_extend')
                    ->field('config')
                    ->where('enable = 1 and command = "register_remind" and wechat_id = '.$wechat_id)
                    ->find();
                if (! empty($register)) {
                    $reg_config = unserialize($register['config']);
                    $username = msubstr($reg_config['user_pre'], 3, 0, 'utf-8', false) . time().mt_rand(1, 99);
                    // 密码随机数
                    $rs = array();
                    $arr = range(0, 9);
                    $reg_config['pwd_rand'] = $reg_config['pwd_rand'] ? $reg_config['pwd_rand'] : 3;
                    for ($i = 0; $i < $reg_config['pwd_rand']; $i ++) {
                        $rs[] = array_rand($arr);
                    }
                    $pwd_rand = implode('', $rs);
                    // 密码
                    $password = $reg_config['pwd_pre'] . $pwd_rand;
                    // 通知模版
                    $template = str_replace(array(
                        '[$username]',
                        '[$password]'
                    ), array(
                        $username,
                        $password
                    ), $reg_config['template']);
                } else {
                    $username = 'wx_' . time().mt_rand(1, 99);
                    $password = 'ecmoban';
                    // 通知模版
                    $template = '默认用户名：' . $username . "\r\n" . '默认密码：' . $password;
                }
                    //add by jxy start 2016/7/13                
                    $scene_id = 164;  //默认徽商(164);
                    $scene_user =model('Base')->model->table('users')->field()->where(array('user_id'=>$scene_id))->find();
                    $other_data = array('parent_id'=>$scene_user['user_id'],
                                            'agent_id'=>$scene_user['agent_id'],
                                            'agent_name'=>$scene_user['agent_name'],//agent_name
                                            'store_uid'=>$scene_user['store_uid'],
                                            'guide_uid'=>$scene_user['guide_uid'],
                                            'wshop_uid'=>$scene_user['wshop_uid'],
                                            'nick_name'=>$userinfo['nickname'],
                                            'stage'    =>$scene_user['stage']+1
                                            );
                    //add by jxy end  
                                            
                // 会员注册
                $domain = get_top_domain();
                //update by kin
                //if (model('Users')->register($username, $password, $username . '@' . $domain) !== false) {
                if (model('Users')->register($username, $password, $username . '@' . $domain,$other_data) !== false) {
                    model('Users')->update_user_info();
                } else {
                    die('授权失败，如重试一次还未解决问题请联系管理员');
                }
                $data1['ect_uid'] = $_SESSION['user_id'];
            }
            else{
                //已绑定
                $username = model('Base')->model->table('users')->field('user_name')->where(array('user_id'=>$ect_uid))->getOne();
                $template = '您已拥有帐号，用户名为'.$username;
                $data1['ect_uid'] = $ect_uid;
            }
            
            // 获取用户所在分组ID
            $group_id = $weObj->getUserGroup($userinfo['openid']);
            $group_id = $group_id ? $group_id : 0;

            $data1['wechat_id'] = $wechat_id;
            $data1['subscribe'] = 0;
            $data1['openid'] = $userinfo['openid'];
            $data1['nickname'] = $userinfo['nickname'];
            $data1['sex'] = $userinfo['sex'];
            $data1['city'] = $userinfo['city'];
            $data1['country'] = $userinfo['country'];
            $data1['province'] = $userinfo['province'];
            $data1['language'] = $userinfo['country'];
            $data1['headimgurl'] = $userinfo['headimgurl'];
            $data1['subscribe_time'] = $time;
            $data1['group_id'] = $group_id;
            $data1['unionid'] = $userinfo['unionid'];
            
            model('Base')->model->table('wechat_user')->data($data1)->insert();
        } else {
            //开放平台有privilege字段,公众平台没有
            unset($userinfo['privilege']);
            model('Base')->model->table('wechat_user')->data($userinfo)->where(array('openid'=> $userinfo['openid']))->update();
            $new_user_name = model('Base')->model->table('users')->field('user_name')->where(array('user_id'=>$ret['ect_uid']))->getOne();
            ECTouch::user()->set_session($new_user_name);
            ECTouch::user()->set_cookie($new_user_name);
            model('Users')->update_user_info();
        }
        session('openid', $userinfo['openid']);
    }

    private function create_weixin_qrcode($user_id){

    }

}
