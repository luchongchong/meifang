<html>
<head>
<meta name="Generator" content="ECSHOP v2.7.3" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>美房美邦</title>
<meta name="viewport" content="width=device-width,height=device-height,inital-scale=1.0,maximum-scale=1.0,user-scalable=no;">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="format-detection" content="telephone=no">
<link href="themesmobile/mobile/css/style1.css"  rel="stylesheet" type="text/css">
<link href="themesmobile/mobile/css/ectouch.css" rel="stylesheet" type="text/css" />
<script src="themesmobile/js/lib/jquery-1.9.1.min.js"></script>

<style type="text/css">
.window {
width:240px;
position:absolute;
display:none;
margin:-50px auto 0 -120px;
padding:2px;
top:0;
left:50%;
border-radius:0.6em;
-webkit-border-radius:0.6em;
-moz-border-radius:0.6em;
background-color: rgba(255, 0, 0, 0.5);
-webkit-box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
-moz-box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
-o-box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
font:14px/1.5 Microsoft YaHei,Helvitica,Verdana,Arial,san-serif;
z-index:10;
bottom: auto;
}
.window .content {
overflow:auto;
padding:10px;
    color: #222222;
    text-shadow: 0 1px 0 #FFFFFF;
border-radius: 0 0 0.6em 0.6em;
-webkit-border-radius: 0 0 0.6em 0.6em;
-moz-border-radius: 0 0 0.6em 0.6em;
}
.window #txt {
min-height:30px;font-size:20px; line-height:22px; color:#FFF; text-align:center;
}
</style>

</head>

<body>
<div id="page">
  <header id="header">
    <div class="header_l"> <a class="ico_10" onClick="javascript:history.back();"> 返回 </a> </div>
    <h1> 我的分店 </h1>
  </header>
</div>

<div class="clr"></div>


<div class="jifen-box header_highlight" style=" margin:20px 10px 0px 10px;">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="integral_table">
    <thead>
    <tr>
    <th>会员名称</th>
    <th>昵称</th>
    <th>粉丝数量</th>
    <th>销量</th>
    </tr>
    </thead>
    <tbody id="weixin_list">
    
    <?php $_from = $this->_var['arr']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'user');if (count($_from)):
    foreach ($_from AS $this->_var['user']):
?>
    <tr>
  	<td ><?php echo $this->_var['user']['user_name']; ?></td>
  	<td ><?php echo $this->_var['user']['nickname']; ?></td>
  	<td ><?php echo $this->_var['user']['fans_num']; ?></td>
  	<td ><?php echo $this->_var['user']['sell_num']; ?></td>
    </tr>
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    </tbody>
    </table>
</div>
<script type="text/javascript" language="javascript">

}

</script>

<script>
$(document).ready(function () {
//$(window).load(function () {
	var page = 1;
	var is_none = false;
	
	
	$(window).scroll(function () {
		//alert($(window).scrollTop());
		//alert($(document).height());
		//alert($(window).height())
	
		if ($(document).scrollTop() >= $(document).height() - $(window).height()) {
		   
			if(is_none)
			{
				return false;
			}
			page++;
			$.get('user.php?act=intro_fans&page='+page,function(res){
				//alert(res);
				$('.sjjzz').hide();
				if(res == false){
					$('#weixin_list').append('<tr class="sjjzz"><td>数据全部加载完毕!</td></tr>');
					is_none = true;
					$('.sjjzz').hide();
					return false;
				}else{
					$('#weixin_list').append(res);
					$('#weixin_list').append('<tr class="sjjzz"><td>数据加载中...</td></tr>');
					$('.sjjzz').hide();
				}
		   })
	   }	

   });
});

</script>

</body>
</html>
