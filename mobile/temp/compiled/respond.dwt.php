<!DOCTYPE html >
<html>
<head>
<meta name="Generator" content="ECSHOP v2.7.3" />
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width">
  <title><?php echo $this->_var['page_title']; ?></title>
  <meta name="Keywords" content="<?php echo $this->_var['keywords']; ?>" />
  <meta name="Description" content="<?php echo $this->_var['description']; ?>" />
  <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
  <link rel="stylesheet" href="themesmobile/68ecshopcom_mobile/css/public.css">
  <link href="themesmobile/mobile/css/ectouch.css" rel="stylesheet" type="text/css" />

<?php echo $this->smarty_insert_scripts(array('files'=>'common.js,utils.js')); ?>

	</head>
<body >
<div >
<div class="tab_nav" >
    <div class="header" style="background: #b0251e;">
      <div class="h-left">
        <a class="sb-back" href="index.php" title="回首页"></a>
      </div>
      <div class="h-mid">
订单提交成功
      </div>

    </div>
  </div>
  
  <div class="screen-wrap fullscreen login">
<div id="main"><div class="wrapper">

    <div class="content ptop0">
		
		<div class="con-ct radius shadow fo-con">
		<div class="cart-step" id="J_cartTab">
          <ul>
            <li>1.购物车列表</li>
            <li>2.确认订单</li>
            <li class="cur">3.购买成功</li>
          </ul>
        </div>
			<ul class="ct-list">
            <li style="text-align:center"><img src="themesmobile/mobile/img/ok.png" class="ok" alt=""></li>
			
            <li style="text-align:center;"><a href="<?php echo $this->_var['shop_url']; ?>"><?php echo $this->_var['lang']['back_home']; ?></a></li>
			</ul>
       
      
		</div>
	</div>
    

	
</div></div>

</div>
</div>
</body>

</html>