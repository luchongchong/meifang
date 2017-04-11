<?php
/**
 * JS_API支付demo
 * ====================================================
 * 在微信浏览器里面打开H5网页中执行JS调起支付。接口输入输出数据格式为JSON。
 * 成功调起支付需要三个步骤：
 * 步骤1：网页授权获取用户openid
 * 步骤2：使用统一支付接口，获取prepay_id
 * 步骤3：使用jsapi调起支付
*/	

	define('IN_ECS', true);
	require(dirname(__FILE__) . './../includes/init.php');

	include_once("WxPayPubHelper/WxPayPubHelper.php");
	//使用jsapi接口
	$jsApi = new JsApi_pub();
	//=========步骤1：网页授权获取用户openid============
	//通过code获得openid
	if (!isset($_GET['code']))
	{
		//触发微信返回code码
		$out_trade_no = $_GET['order_id'];
		$call_url = WxPayConf_pub::JS_API_CALL_URL."?order_id=".$out_trade_no;
		//需要url_rewrite支持
		$url = $jsApi->createOauthUrlForCode($call_url);
		Header("Location: $url"); 
	}else
	{
		//获取code码，以获取openid
	    $code = $_GET['code'];
		$jsApi->setCode($code);
		$openid = $jsApi->getOpenId();
		$out_trade_no = $_GET['order_id'];
	}

	//订单金额
	$sql = "select order_amount from ".$ecs->table('order_info')." where order_id=".$out_trade_no;

	//echo $sql;
	$order_amount = $db->getOne($sql);
	//$order_amount = 0.01;
	$total_fee = intval(floatval($order_amount)*100);
	
	//订单号
	$sql_sn = "select order_sn from ".$ecs->table('order_info')." where order_id=".$out_trade_no;
	$out_trade_no_two = $db->getOne($sql_sn);
	
	//=========步骤2：使用统一支付接口，获取prepay_id============
	//使用统一支付接口
	$unifiedOrder = new UnifiedOrder_pub();
	
	//设置统一支付接口参数
	//设置必填参数
	//appid已填,商户无需重复填写
	//mch_id已填,商户无需重复填写
	//noncestr已填,商户无需重复填写
	//spbill_create_ip已填,商户无需重复填写
	//sign已填,商户无需重复填写
	$unifiedOrder->setParameter("openid","$openid");//
	//自定义订单号，此处仅作举例
	
	$timeStamp = time();
	//$out_trade_no = WxPayConf_pub::APPID."$timeStamp";	
	$unifiedOrder->setParameter("out_trade_no", $out_trade_no_two);//商户订单号
	$return_url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx2326ecf73474cfaa&redirect_uri=http://www.mfmb58.com/mobile/weixinlogo.php&response_type=code&scope=snsapi_base&state=2#wechat_redirect";
	
	$unifiedOrder->setParameter("body",'美房美邦墙纸');//商品描述
	//$unifiedOrder->setParameter("total_fee", $total_fee);//总金额
	$unifiedOrder->setParameter("total_fee", $total_fee);//总金额
	$unifiedOrder->setParameter("notify_url",WxPayConf_pub::NOTIFY_URL);//通知地址 , 更新支付状态
	$unifiedOrder->setParameter("trade_type","JSAPI");//交易类型
	
	//非必填参数，商户可根据实际情况选填
	//$unifiedOrder->setParameter("sub_mch_id","XXXX");//子商户号  
	//$unifiedOrder->setParameter("device_info","XXXX");//设备号 
	//$unifiedOrder->setParameter("attach","XXXX");//附加数据 
	//$unifiedOrder->setParameter("time_start","XXXX");//交易起始时间
	//$unifiedOrder->setParameter("time_expire","XXXX");//交易结束时间 
	//$unifiedOrder->setParameter("goods_tag","XXXX");//商品标记 
	//$unifiedOrder->setParameter("openid","XXXX");//用户标识
	//$unifiedOrder->setParameter("product_id","XXXX");//商品ID

	$prepay_id = $unifiedOrder->getPrepayId();
	//=========步骤3：使用jsapi调起支付============
	$jsApi->setPrepayId($prepay_id);

	$jsApiParameters = $jsApi->getParameters();
	//echo $jsApiParameters;
?>

<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="author" content="ecy.cc">
	<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=0">
	<meta name="apple-touch-fullscreen" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">

    <title>微信安全支付</title>

	<script type="text/javascript">
		//调用微信JS api 支付
		function jsApiCall()
		{
			WeixinJSBridge.invoke(
				'getBrandWCPayRequest',
				<?php echo $jsApiParameters; ?>,
				function(res){
					WeixinJSBridge.log(res.err_msg);
					if(res.err_msg=='get_brand_wcpay_request:ok'){
						document.getElementById('payDom').style.display='none';
						document.getElementById('successDom').style.display='';
					
							setTimeout("window.location.href='<?php echo $return_url; ?>' ",2000);
						
					}else{
						document.getElementById('payDom').style.display='none';
						document.getElementById('failDom').style.display='';
						document.getElementById('failRt').innerHTML=res.err_code+'|'+res.err_desc+'|'+res.err_msg;
					}
				}
			);
		}

		function callpay()
		{
			if (typeof WeixinJSBridge == "undefined"){
			    if( document.addEventListener ){
			        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
			    }else if (document.attachEvent){
			        document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
			        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
			    }
			}else{
			    jsApiCall();
			}
		}
	</script>
</head>

<body>
<style>
body {padding-top:10px;}
.cardexplain ul {list-style:none; line-height:30px;}
.cardexplain input { margin:0 auto;width:100%; font-size:16px; padding:5px !important; text-align:center; border:solid 0px red; color:white; background:url("../images/loginbtn.png") no-repeat scroll center center #f00; background-color:#f00 !important;}
</style>

<div id="payDom" class="cardexplain">
	<ul class="round">
		<li class="title mb"><span class="none">支付信息</span></li>
		<li class="nob">金额 : <?php echo $order_amount;?>元</li>
	</ul>

	<div>
		<input type="button"
		onclick="javascript:callpay();"  value="点击进行微信支付" />	
	</div>
	
</div>
<div id="failDom" style="display:none" class="cardexplain">
	<ul class="round">
	<li class="title mb"><span class="none">支付结果</span></li>
	<li class="nob">
		<div>支付失败</div>
		<div id="failRt"></div>
	</li>
	</ul>
	<div class="footReturn"style="text-align:center">
		<input type="button" 
			style="margin:0 auto 20px auto;width:100%" 
			onclick="callpay()" class="submit" value="重新进行支付"/>
	</div>
</div>

<div id="successDom" style="display:none" class="cardexplain">
	<ul class="round">
		<li class="title mb"><span class="none">支付成功</span></li>
		<li class="nob">
			<div>您已支付成功，页面正在跳转...</div>
			<div id="failRt"></div>
		</li>
	</ul>
</div>

</body>


</html>
