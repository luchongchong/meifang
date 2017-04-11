<?php
if (! defined ( 'IN_ECS' )) {
	die ( 'Hacking attempt' );
}

$payment_lang = ROOT_PATH . 'languages/' . $GLOBALS ['_CFG'] ['lang'] . '/payment/weixin.php';

if (file_exists ( $payment_lang )) {
	global $_LANG;
	
	include_once ($payment_lang);
}

/* 模块的基本信息 */
if (isset ( $set_modules ) && $set_modules == TRUE) {
	$i = isset ( $modules ) ? count ( $modules ) : 0;
	
	/* 代码 */
	$modules [$i] ['code'] = basename ( __FILE__, '.php' );
	
	/* 描述对应的语言项 */
	$modules [$i] ['desc'] = 'weixin_desc';
	
	/* 是否支持货到付款 */
	$modules [$i] ['is_cod'] = '0';
	
	/* 是否支持在线支付 */
	$modules [$i] ['is_online'] = '1';
	
	/* 作者 */
	$modules [$i] ['author'] = '68ecshop';
	
	/* 网址 */
	$modules [$i] ['website'] = '';
	
	/* 版本号 */
	$modules [$i] ['version'] = '2.0.0';
	
	/* 配置信息 */
	$modules [$i] ['config'] = array (
			array (
					'name' => 'appId',
					'type' => 'text',
					'value' => '' 
			),
			array (
					'name' => 'appSecret',
					'type' => 'text',
					'value' => '' 
			),
			array (
					'name' => 'partnerId',
					'type' => 'text',
					'value' => '' 
			),
			array (
					'name' => 'partnerKey',
					'type' => 'text',
					'value' => '' 
			) 
		);
	
	return;
}

/**
 * 类
 */
class weixin {
	
	/**
	 * 构造函数
	 *
	 * @access public
	 * @param        	
	 *
	 *
	 * @return void
	 */
	function __construct()
    {
        
        $payment = get_payment('weixin');
       
        $payment=unserialize_config($payment['pay_config']);
        $root_url = 'http://' . $_SERVER ['HTTP_HOST'].'/mobile/';
        //$root_url = "http://test.mfmb58.com/mobile/";
       
        if(!defined('WXAPPID'))
        {
           //var_dump($payment);
            define("WXAPPID", $payment['appId']);
            define("WXMCHID", $payment['partnerId']);
            define("WXKEY", $payment['partnerKey']);
            define("WXAPPSECRET", $payment['appSecret']);
            define("WXCURL_TIMEOUT", 30);
            define('WXNOTIFY_URL',$root_url.'wx_native_callback.php');
        }
        require_once(dirname(__FILE__)."/WxPayPubHelper/WxPayPubHelper.php");
        require_once(dirname(__FILE__)."/WxpayAPI_php_v3/lib/WxPay.Config.php");
        require_once(dirname(__FILE__)."/WxpayAPI_php_v3/lib/WxPay.Api.php");
        require_once(dirname(__FILE__)."/WxpayAPI_php_v3/lib/WxPay.Notify.php");
        require_once(dirname(__FILE__)."/WxpayAPI_php_v3/native_notify.php");
    }
	
	 function get_code($order, $payment)
    {
        $jsApi = new JsApi_pub();
        
         
        if (!isset($_GET['code']))
        {
            $redirect = urlencode('http://' . $_SERVER ['HTTP_HOST'].'/mobile/flow.php?step=done&order_id='.$order['order_id']);
            $url = $jsApi->createOauthUrlForCode($redirect);
            Header("Location: $url");
        }else
        {
            $code = $_GET['code'];
            $jsApi->setCode($code);
            $openid = $jsApi->getOpenId();
        }

        if($openid)
        {
            $unifiedOrder = new NativeNotifyCallBack();
            $current_time = time();
            // 订单过期时间：10分钟
            $expire_time = strtotime("+10 minutes");
            
            // 订单开始时间
            $time_start = date('Y-m-d H:i:s', $current_time);
            $time_start_str = date('YmdHis', $current_time);
            // 订单过期时间
            $time_expire = date('Y-m-d H:i:s', $expire_time);
            $time_expire_str = date('YmdHis', $expire_time);
           
            $order_info=array(
                               'order_name'=>$order['order_sn'],
                               'order_no'=>$order['order_sn'],
                               'attach' =>$order['log_id'],
                               'online_pay_sum'=>strval(intval($order['order_amount']*100))
            );
            $order_info['time_start'] = $time_start_str;
            $order_info['time_expire'] = $time_expire_str;
            $order_info['current_time'] = $current_time;
            
            $prepay_id=$unifiedOrder->wxunifiedorder($order_info, WXNOTIFY_URL, $openid);
        
            $jsApiParameters = $prepay_id;
        
           
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
            $allow_use_wxPay = true;
        
            if(strpos($user_agent, 'MicroMessenger') === false)
            {
                $allow_use_wxPay = false;
            }
            else
            {
                preg_match('/.*?(MicroMessenger\/([0-9.]+))\s*/', $user_agent, $matches);
                if($matches[2] < 5.0)
                {
                    $allow_use_wxPay = false;
                }
            }
            $html .="<script src='http://res.wx.qq.com/open/js/jweixin-1.1.0.js' type='text/javascript'></script>";
            $html .= '<script language="javascript">';
            if($allow_use_wxPay)
            {
                
                $re = @$this->getSignPackage(WXAPPID,WXAPPSECRET);
               
                $html .="\r\n
wx.config({
    debug: false,
    appId: '".$re['appId']."',
    timestamp: ".$re['timestamp'].",
    nonceStr: '".$re['nonceStr']."',
    signature: '".$re['signature']."',
    jsApiList: [
		'chooseWXPay',
    ]
});";
                $html .= "function jsApiCall(){";
                $html .= "wx.ready(function(){";
                $html .="wx.chooseWXPay({";
                $html .="timestamp:'".$jsApiParameters['timeStamp']."',"; 
                $html .="nonceStr:'". $jsApiParameters['nonceStr']."',";
                $html .="package: '".$jsApiParameters['package']."'," ;
                $html .="signType: '".$jsApiParameters['signType']."',";
                $html .="paySign: '".$jsApiParameters['sign']."',"; 
                $html .="success:function (res) {
                      if(res.errMsg == 'chooseWXPay:ok'){
                            window.location.href='http://test.mfmb58.com/mobile/respond.php';
                        };
                    }";
                $html .="})";
                
               
                $html .= "});";
                $html .=     "}";
            }
            else
            {
                $html .= 'function callpay(){';
                $html .= 'alert("您的微信不支持支付功能,请更新您的微信版本")';
                $html .= "}";
        
            }
        
            $html .= '</script>';
            $html .= '<button  class="c-btn4"  type="button" onclick="jsApiCall()">微信支付</button>';
        
            return $html;
        
        }
        else
        {
            $html .= '<script language="javascript">';
            $html .= 'function callpay(){';
            $html .= 'alert("请在微信中使用微信支付")';
            $html .= "}";
            $html .= '</script>';
            $html .= '<button type="button" onclick="callpay()"       class="pay_bottom">微信支付</button>';
        
            return $html;
        }
        
        
    }
    function respond()
    {
        $xml = file_get_contents("php://input");
        $this->log(ROOT_PATH.'/data/wx_new_log.txt','2017-3-9 royallu'."\r\n");
        $this->log(ROOT_PATH.'/data/wx_new_log.txt',$xml."\r\n");
        $notify = new NativeNotifyCallBack();
        return $notify->Handle(true);		

    }

    public function log($file,$txt)
    {
        $fp =  fopen($file,'ab+');
        fwrite($fp,'-----------'.local_date('Y-m-d H:i:s').'-----------------');
        fwrite($fp,$txt);
        fwrite($fp,"\r\n\r\n\r\n");
        fclose($fp);
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
        $data = json_decode(file_get_contents("./jsapi_ticket.json"));
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
