<!DOCTYPE HTML>
<html>
<head>
<meta name="Generator" content="ECSHOP v2.7.3" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width,height=device-height,initial-scale=1.0,maximum-scale=1.0,user-scalable=yes;" />


<meta name="apple-mobile-web-app-title" content="标题">

<meta name="apple-mobile-web-app-capable" content="yes"/>
<meta content="no" name="apple-touch-fullscreen">
<meta content="yes" name="full-screen">
<meta content="black" name="apple-mobile-web-app-status-bar-style">
<meta content="telephone=yes,email=yes" name="format-detection">
<meta content="address=no" name="format-detection">
<title>明星热卖</title>
<link rel="stylesheet" type="text/css" href="themesmobile/mobile/mendiancss/config.css">
<link rel="stylesheet" type="text/css" href="themesmobile/mobile/mendiancss/ms11.css">
<link href="themesmobile/mobile/css/ectouch.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="themesmobile/mobile/js/jquery-1.4.4.min.js"></script>
<?php echo $this->smarty_insert_scripts(array('files'=>'transport.js,utils.js')); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'jquery.json.js,common.js')); ?>
</head>
<body>
	
		<div class="top_bg">
			<div class="jmy_bg">
				<div class="my_nei">
					<img src="themesmobile/mobile/img/1_03.png" width="10" height="17" alt="">
                    <span><?php echo $this->_var['lang'][$this->_var['intor']]; ?></span>
				</div>
			</div>
		</div>
        <div class="content1">
        	<!--<P>无纺</P>-->
        	<?php $_from = $this->_var['goods_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods_info');$this->_foreach['goods_list'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['goods_list']['total'] > 0):
    foreach ($_from AS $this->_var['goods_info']):
        $this->_foreach['goods_list']['iteration']++;
?>
        	<div class="one">
                <dl class="starhot">
                    <a href="<?php echo $this->_var['goods_info']['url']; ?>" class="">
                    <dt><img src="<?php echo $this->_var['goods_info']['goods_thumb']; ?>"></dt>
                    <dd><?php echo $this->_var['goods_info']['goods_name']; ?></dd>
                    </a>
                    
                </dl>
                <dl class="goumai">
                    <dt><img src="themesmobile/mobile/img/明星热卖_05.png"></dt>
                    <dd>
                        <span  class="now"><?php echo $this->_var['goods_info']['shop_price']; ?></span>
                        <span  class="before"><?php echo $this->_var['goods_info']['market_price']; ?></span>
                        <span  class="buy" onClick="addToCart_quick(<?php echo $this->_var['goods_info']['goods_id']; ?>)">立即购买</span>
                    </dd>
                </dl>
           </div>
           <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </div>
        <div style="" class="pagenav-wrapper" id="J_PageNavWrap">
          <div class="pagenav-content">
            <div class="pagenav" id="J_PageNav">
              <?php if ($this->_var['pager']['page'] != '1'): ?>
              <div class="p-prev p-gray"  style="height:30px;line-height:30px; text-align:center;"> <a href="<?php echo $this->_var['pager']['page_prev']; ?>"><?php echo $this->_var['lang']['page_prev']; ?></a> </div>
              <?php endif; ?>
             <?php if ($this->_var['pager']['page'] != $this->_var['pager']['page_count']): ?>
             <div class="p-next" style="height:30px;line-height:30px; text-align:center;" > <a  href="<?php echo $this->_var['pager']['page_next']; ?>"><?php echo $this->_var['lang']['page_next']; ?></a> </div>
             <?php endif; ?>
            </div>
          </div>
        </div>
</body>
</html>
<script type="text/javascript" src="themesmobile/js/lib/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="themesmobile/js/jquery.more.js"></script>
<script type="text/javascript" src="themesmobile/js/ectouch.js"></script>
<script type="Text/Javascript" language="JavaScript">
<!--

function selectPage(sel)
{
  sel.form.submit();
}

//-->
</script> 
<script type="text/javascript">
<?php $_from = $this->_var['lang']['compare_js']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
<?php if ($this->_var['key'] != 'button_compare'): ?>
var <?php echo $this->_var['key']; ?> = "<?php echo $this->_var['item']; ?>";
<?php else: ?>
var button_compare = '';
<?php endif; ?>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
var compare_no_goods = "<?php echo $this->_var['lang']['compare_no_goods']; ?>";
var btn_buy = "<?php echo $this->_var['lang']['btn_buy']; ?>";
var is_cancel = "<?php echo $this->_var['lang']['is_cancel']; ?>";
var select_spe = "<?php echo $this->_var['lang']['select_spe']; ?>";
</script>
