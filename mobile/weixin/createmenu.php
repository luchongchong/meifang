<?php
require(dirname(__FILE__) . '/api.class.php');
require(dirname(__FILE__) . '/wechat.class.php');
$weixinconfig = $GLOBALS['db']->getRow ( "SELECT * FROM " . $GLOBALS['ecs']->table('weixin_config') . " WHERE `id` = 1" );
$core_lib_wechat= new core_lib_wechat($weixinconfig);
$data=' {
                  "button": [{
                        "type": "view",
                        "name": "美美商城",
                        "url": "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx12fe2a76e0305e26&redirect_uri=http://test.mfmb58.com/mobile/weixinlogo.php&response_type=code&scope=snsapi_base&state=1#wechat_redirect"
                    }, {
                        "name": "服务中心",
                        "sub_button": [{
                            "type": "view",
                            "name": "美美客服",
                            "url": "http://test.mfmb58.com/mobile/index.php"
                        },{
                            "type": "view",
                            "name": "样本系列",
                            "url": "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx12fe2a76e0305e26&redirect_uri=http://test.mfmb58.com/mobile/weixinlogo.php&response_type=code&scope=snsapi_base&state=4#wechat_redirect"
                        },{
                            "type": "view",
                            "name": "附近体验店",
                            "url": "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx12fe2a76e0305e26&redirect_uri=http://test.mfmb58.com/mobile/weixinlogo.php&response_type=code&scope=snsapi_base&state=6#wechat_redirect"
                        },{
                            "type": "view",
                            "name": "我要加盟",
                            "url": "http://test.mfmb58.com/mobile/index.php"
                        }]
                    }, {
                        "name": "会员中心",
                        "sub_button": [{
                            "type": "view",
                            "name": "会员中心",
                            "url": "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx12fe2a76e0305e26&redirect_uri=http://test.mfmb58.com/mobile/weixinlogo.php&response_type=code&scope=snsapi_base&state=2#wechat_redirect"
                        }, {
                            "type": "view",
                            "name": "美美社区",
                            "url": "http://test.mfmb58.com/mobile/index.php"
                        }, {
                            "type":"click",
                        	"name":" 签到有礼",
                        	"key":"qd"
                        },{
                            "type": "view",
                            "name": "积分有礼",
                            "url": "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx12fe2a76e0305e26&redirect_uri=http://test.mfmb58.com/mobile/weixinlogo.php&response_type=code&scope=snsapi_base&state=7#wechat_redirect"
                        }]
                    }]
                }
        ';
var_dump($core_lib_wechat->createMenu($data));