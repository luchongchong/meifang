<?php

/**
 *
 * ============================================================================ 
 * 版权所有 2005-2014 上海优辉商务，并保留所有权利。
 * 网站地址: http://www.j345.net
 * ----------------------------------------------------------------------------
 * 优辉网络,共创你我
 * ============================================================================
 * $Author: liubo $
 * $Id: cls_image.php 17217 2011-01-19 06:29:08Z liubo $
*/
defined('IN_ECTOUCH') or die('Deny Access');
class Cls_share
{
 //$title分享标题
 //$url分享链接
 //$img分享图片
 //$desc分享给朋友的简介
 //$back_url确认分享执行的回调函数链接
function cls_shares($title='',$url='',$img='',$desc='',$back_url=''){
	$ret_info = $GLOBALS['db'] -> getRow("SELECT * FROM `ecs_weixin` WHERE `id` = 1");
	$appid = $ret_info['appid'];
	$appsecret = $ret_info['appsecret']; 
	$re = @$this->getSignPackage($appid,$appsecret);
    $str = "<script src='http://res.wx.qq.com/open/js/jweixin-1.0.0.js' type='text/javascript'></script>";
    $str .= "\r\n<script type='text/javascript' language='javascript'>\r\n
wx.config({
    debug: false,
    appId: '".$re['appId']."',
    timestamp: ".$re['timestamp'].",
    nonceStr: '".$re['nonceStr']."',
    signature: '".$re['signature']."',
    jsApiList: [
		'onMenuShareTimeline',
		'onMenuShareAppMessage',
    ]
});

wx.ready(function () {
	//分享到朋友圈
	wx.onMenuShareTimeline({
    title: '".$title."',
    link: '".$url."',
    imgUrl: '".$img."',
    success: function () { 
		$.get('".$back_url."', function(result){	
  		});
    },
    cancel: function () { 
    }
	});
	
	//分享到朋友
	wx.onMenuShareAppMessage({
    title: '".$title."',
    desc: '".$desc."',
    link: '".$url."',
    imgUrl: '".$img."',
    type: '',
    dataUrl: '',
    success: function () { 
		$.get('".$back_url."', function(result){	
  		});
    },
    cancel: function () { 
    }
	});
});
</script>\r\n";
    dump($str);exit();
    return $str;
	
}
	
	
function getSignPackage($appid,$secret) {
    $jsapiTicket = $this->getJsApiTicket($appid,$secret);
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $arr=$this->mksign($url,$jsapiTicket );
    $signPackage = array(
      "appId"     => $appid,
      "nonceStr"  => $arr['noncestr'],
      "timestamp" => $arr['timestamp'],
      "url"       => $arr['url'],
      "signature" => $arr['sign']
    );
    
    return $signPackage; 
}

//获取随机字符串
function createNonceStr($length = 32) {
    $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
}

//获取微信jsapi_ticket
function getJsApiTicket($appid,$secret) {
    // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
    $data = json_decode(file_get_contents("./weixin/jsapi_ticket.json"));
    if ($data->expire_time < time()) {
      $accessToken = $this->getAccessToken($appid,$secret);
      // 如果是企业号用以下 URL 获取 ticket
      // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
      $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
      $res = json_decode($this->httpGet($url));
      $ticket = $res->ticket;
      if ($ticket) {
        $data->expire_time = time() + 7000;
        $data->jsapi_ticket = $ticket;
        $fp = fopen("./weixin/jsapi_ticket.json", "w");
        fwrite($fp, json_encode($data));
        fclose($fp);
      }
    } else {
      $ticket = $data->jsapi_ticket;
    }
    return $ticket;
}

 //获取微信AccessToken
function getAccessToken($appid,$secret) {
    $ret = $GLOBALS['db'] -> getRow("SELECT * FROM `ecs_weixin_config` WHERE `id` = 1");
	$appid = $ret['appid'];
	$appsecret = $ret['appsecret'];
	$dateline = $ret['dateline'];
	$time = time();
	if (($time - $dateline) > 6000) {
		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";
		$ret_json = $this->httpGet($url);
		$ret = json_decode($ret_json);
		if ($ret -> access_token) {
			$GLOBALS['db'] -> query("UPDATE `ecs_weixin_config` SET `access_token` = '$ret->access_token',`dateline` = '$time' WHERE `ecs_weixin_config`.`id` =1;");
		} 
	}
	$ret = $GLOBALS['db'] -> getOne("SELECT `access_token` FROM `ecs_weixin_config` WHERE `id` = 1");
	return $ret;
}





 function mksign($url,$jsapi_ticket){
    $data['url'] = $url;
    $data['jsapi_ticket'] = $jsapi_ticket;
    $data['noncestr'] = $this->getNonceStr();
    $data['timestamp'] = time();
    $data['sign'] = $this->myMakeSign($data);
    return $data;
}


/**
 *
 * 产生随机字符串，不长于32位
 * @param int $length
 * @return 产生的随机字符串
 */
function getNonceStr($length = 32)
{
    $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
    $str ="";
    for ( $i = 0; $i < $length; $i++ )  {
        $str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
    }
    return $str;
}

/**
 * 生成签名
 *  @return 签名
 */
 function myMakeSign($params){
    //签名步骤一：按字典序排序数组参数
    ksort($params);
    $string = $this->myToUrlParams($params);
    //        //签名步骤二：在string后加入KEY
    //        $string = $string . "&key=".WxPayConfig::KEY;
    //签名步骤三：sha1加密
    $string = sha1($string);
    //签名步骤四：所有字符转为大写
    //        $result = strtoupper($string);
    return $string;
}

/**
 * 将参数拼接为url: key=value&key=value
 * @param   $params
 * @return  string
 */
function myToUrlParams($params){
    $string = '';
    if( !empty($params) ){
        $array = array();
        foreach( $params as $key => $value ){
            $array[] = $key.'='.$value;
        } 
        
        $string = implode('&',$array);
    }
    return $string;
}


function httpGet($url) {
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_TIMEOUT, 500);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curl, CURLOPT_URL, $url);
	$res = curl_exec($curl);
	curl_close($curl);	
	return $res;
}
   
}

?>
