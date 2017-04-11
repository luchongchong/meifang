<?php

// 第一种  ==== 直接出来图片
define('IN_ECS', true);
include "./phpqrcode_modify_saveimg/phpqrcode.php";


require(dirname(__FILE__) . '/includes/init.php');
$res=$GLOBALS['db']->getOne('SELECT is_sample FROM '.$GLOBALS['ecs']->table('goods').' WHERE goods_id='.$_REQUEST['id']);
//只取路径
$url='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]; 
//echo dirname($url).'<br>';
//$value = $_POST['value'];
if($res == true){
    $value = dirname($url).'/mobile/sample_info.php?id='.$_REQUEST['id'];
}else{
    $value = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx2326ecf73474cfaa&redirect_uri=http://www.mfmb58.com/mobile/weixinlogo.php&response_type=code&scope=snsapi_base&state='.$_REQUEST['id'].'#wechat_redirect';
//    $value = dirname($url).'/mobile/goods.php?id='.$_REQUEST['id'];
}
//echo $value.'<br>';
//$value='http://www.baidu.com';
$errorCorrectionLevel = 'L';
$matrixPointSize = 4;
QRcode::png($value, false, $errorCorrectionLevel, $matrixPointSize);
//echo 3;
exit;

// 第二种
//
//   include('./phpqrcode_modify_saveimg/phpqrcode.php'); 
//   // 二维码数据 
//   $data = 'http://gz.altmi.com'; 
//   // 纠错级别：L、M、Q、H 
//   $errorCorrectionLevel = 'L';  
//   // 点的大小：1到10 
//   $matrixPointSize = 4;  
//   // 生成的文件名 
//   $filename = $errorCorrectionLevel.'|'.$matrixPointSize.'.png'; 
//   QRcode::png($data, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
//exit;
//var_dump($_REQUEST['id']);


// 第三种  ajax 请求图片
?>
<html>
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<script src="./js/jquery-1.9.1.min.js"></script>
<script>
	$(function(){
		$('input[type="button"]').click(function(){
			$data = $('input[name="data"]').val();
			$level = $('input[name="level"]').val();
			$size = $('input[name="size"]').val();
			$.post('./phpqrcode_modify_saveimg/longfei.php',{'data':$data,'level':$level,'size':$size},function(res){
				alert(res)
			});
		});
		// alert(3);
	});
</script>
<head></head>
<body>
<form action="./phpqrcode_modify_saveimg/longfei.php" method="post">
	字符:(网址, 文字都可以)
	<br /><input type="text" name="data" value="123456" /><br />
	质量:(L,M,Q,H 依次提高)
	<br /><input type="text" name="level" value="M" /><br />
	大小:(1-10不等)
	<br /><input type="text" name="size" value="10" /><br />
	<input type="button" value="提交" />
</form>
</body>
</html>
