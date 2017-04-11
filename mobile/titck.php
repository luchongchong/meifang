<?php
require(dirname(__FILE__) . '/weixin/wechat.class.php');
$data=array('appid'=>'wxc24ea9799e1bac9a','appsecret'=>'f5034abd4b9c0882b238bd604f8891b8');
$core_lib_wechat= new core_lib_wechat($data);
$titck=$core_lib_wechat->getQRCode('1',1);
