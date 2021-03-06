<!DOCTYPE html>
<html>
<head>
<meta name="Generator" content="ECSHOP v2.7.3" />
<meta charset="utf-8" />
<title>美房美邦</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta name="format-detection" content="telephone=no" />
<link href="themesmobile/mobile/img/touch-icon.png" rel="apple-touch-icon-precomposed" />
<link href="themesmobile/mobile/img/favicon.ico" rel="shortcut icon" type="image/x-icon" />
<link href="themesmobile/mobile/css/ectouch.css" rel="stylesheet" type="text/css" />
<link href="themesmobile/mobile/css/style1.css"  rel="stylesheet" type="text/css">

<?php echo $this->smarty_insert_scripts(array('files'=>'transport.js,common.js,user.js')); ?>
<script type="text/javascript" src="themesmobile/mobile/js/jquery-1.4.4.min.js"></script>
</head>

<body>
    <header id="header">
      <div class="header_l header_return"> <a class="ico_10" href="user.php"> 返回 </a> </div>
      <h1> <?php echo $this->_var['lang']['label_order']; ?> </h1>
    </header>
    <section class="wrap order_list">
      <section class="order_box padd1 radius10 single_item">
      <table width="100%" border="0" cellpadding="5" cellspacing="0" class="ectouch_table">
      	<tr>
        	<td class="order_status" style="border-bottom:1px #CCCCCC dashed"></td>
        </tr>
        <tr>
        	<td class="order_content"></td>
        </tr>
        <tr>
          <td class="order_handler"></td>
        </tr>
        <tr style="display:none;">
            <td class="order_tracking"></td>
        </tr>
      </table>
    </section>
    <a href="javascript:;" style="text-align:center;" class="get_more"></a>
    </section>
	<script type="text/javascript" src="themesmobile/mobile/js/jquery.more.js"></script>
    <script type="text/javascript">
    <?php $_from = $this->_var['lang']['merge_order_js']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
	    var <?php echo $this->_var['key']; ?> = "<?php echo $this->_var['item']; ?>";
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    jQuery(function($){
    	$('.order_list').more({'address': 'user.php?act=async_order_list', amount: 5, 'spinner_code':'<div style="text-align:center; margin:10px;"><img src="<?php echo $this->_var['ectouch_themes']; ?>/images/loader.gif" /></div>'});
		$(window).scroll(function () {
			if ($(window).scrollTop() == $(document).height() - $(window).height()) {
				$('.get_more').click();
			}
		});
    });
    </script>

</body>
</html>
