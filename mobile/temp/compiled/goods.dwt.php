<!DOCTYPE html>
<html>
<head>
<meta name="Generator" content="ECSHOP v2.7.3" />
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<meta charset="utf-8" />
<title>美美商城 </title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta name="format-detection" content="telephone=no" />
<link href="themesmobile/mobile/img/touch-icon.png" rel="apple-touch-icon-precomposed" />
<link href="themesmobile/mobile/img/favicon.ico" rel="shortcut icon" type="image/x-icon" />
<link href="themesmobile/mobile/css/ectouch.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="themesmobile/mobile/js/jquery-1.4.4.min.js"></script>
<?php echo $this->smarty_insert_scripts(array('files'=>'jquery.json.js,common.js')); ?>
<script type="text/javascript">
// 筛选商品属性
jQuery(function($) {
  $(".info").click(function(){
    $('.goodsBuy .fields').slideToggle("fast");
  });
})

function changenum(diff) {
  var num = parseFloat(document.getElementById('goods_number').value);
  var goods_number = num + Number(diff);
  var goods_number=goods_number.toFixed(1)
  if( goods_number >= 1){
    document.getElementById('goods_number').value = goods_number;//更新数量
    changePrice();
  }
}

</script>
<?php echo $this->_var['re_share']; ?>
</head>

<body>
<!--<header id="header">
  <div class="header_l header_return"> <a class="ico_10" href="cat_all.php"> 返回 </a> </div>
  <h1> 商品详情 </h1>
  <div class="header_r header_search">  </div>
</header>-->

 
<script src="themesmobile/js/TouchSlide.js"></script>
<section class="goods_slider">
  <div id="slideBox" class="slideBox">
    <div class="scroller"> 
      <!--<div><a href="javascript:showPic()"><img src="<?php echo $this->_var['goods']['goods_thumb']; ?>"  alt="<?php echo $this->_var['goods']['goods_name']; ?>" /></a></div>-->
      <ul>
        <li><a href="javascript:showPic()" id="first_img"><img   alt="" src="<?php echo $this->_var['site_url']; ?><?php echo $this->_var['goods']['original_img']; ?>"/></a></li>
        <?php if ($this->_var['pictures']): ?> 
        <?php $_from = $this->_var['pictures']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'picture');$this->_foreach['no'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['no']['total'] > 0):
    foreach ($_from AS $this->_var['picture']):
        $this->_foreach['no']['iteration']++;
?> 
        <?php if ($this->_foreach['no']['iteration'] > 1): ?>
        <li><a href="javascript:showPic()"><img alt="" src="<?php echo $this->_var['site_url']; ?><?php echo $this->_var['picture']['img_url']; ?>"/></a></li>
        <?php endif; ?> 
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?> 
        <?php endif; ?>
      </ul>
    </div>
    <div class="icons">
      <ul>
        <i class="current"></i> 
        <?php if ($this->_var['pictures']): ?> 
        <?php $_from = $this->_var['pictures']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'picture');$this->_foreach['no'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['no']['total'] > 0):
    foreach ($_from AS $this->_var['picture']):
        $this->_foreach['no']['iteration']++;
?> 
        <i class="current"></i> 
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?> 
        <?php endif; ?>
      </ul>
    </div>
    
    <a class="collect_self" id="collect_box" href="javascript:collect(<?php echo $this->_var['goods']['goods_id']; ?>)"><img src="themesmobile/mobile/img/ico_star.png"></a>
    
  </div>
  <div class="blank2"></div>
</section>
<script type="text/javascript">
TouchSlide({ 
  slideCell:"#slideBox",
  titCell:".icons ul", //开启自动分页 autoPage:true ，此时设置 titCell 为导航元素包裹层
  mainCell:".scroller ul", 
  effect:"leftLoop", 
  autoPage:true,//自动分页
  autoPlay:false //自动播放
});

function showPic(){
  var data = document.getElementById("slideBox").className;
  var reCat = /ui-gallery/;
  //str1.indexOf(str2);
  if( reCat.test(data) ){
    document.getElementById("slideBox").className = 'slideBox';
  }else{
    document.getElementById("slideBox").className = 'slideBox ui-gallery';
    //document.getElementById("slideBox").style.position = 'fixed';
  }
}
</script> 
 

<section class="goodsInfo">
  <div class="title">
    <h1> <?php echo $this->_var['goods']['goods_style_name']; ?> </h1>
  </div>
  <ul>
    <?php if ($this->_var['goods']['is_promote'] && $this->_var['goods']['gmt_end_time']): ?>
    <li><?php echo $this->_var['lang']['promote_price']; ?>
    <b class="price"><?php echo $this->_var['goods']['promote_price']; ?>元/
    <?php if ($this->_var['goods']['goods_valuation'] == 1): ?> 卷<?php endif; ?>
    <?php if ($this->_var['goods']['goods_valuation'] == 2): ?>平方<?php endif; ?> 
    <?php if ($this->_var['goods']['goods_valuation'] == 3): ?> 米<?php endif; ?> 
    <?php if ($this->_var['goods']['goods_valuation'] == 4): ?> 本<?php endif; ?> 
    <?php if ($this->_var['goods']['goods_valuation'] == 5): ?> 瓶<?php endif; ?> 
    <?php if ($this->_var['goods']['goods_valuation'] == 6): ?> 袋<?php endif; ?> 
    <?php if ($this->_var['goods']['goods_valuation'] == 7): ?> 包<?php endif; ?> 
    <?php if ($this->_var['goods']['goods_valuation'] == 8): ?> 箱<?php endif; ?> 
    <?php if ($this->_var['goods']['goods_valuation'] == 9): ?> 罐<?php endif; ?> 
    

    </b>　
    <?php if ($this->_var['cfg']['show_marketprice']): ?>
    <del><?php echo $this->_var['goods']['market_price']; ?></del> 
    <?php endif; ?></li> 

    <?php else: ?>
    <li>优惠价:
    <b class="price">
    <?php echo $this->_var['goods']['shop_price_formated']; ?>元/
    <?php if ($this->_var['goods']['goods_valuation'] == 1): ?>卷<?php endif; ?>
    <?php if ($this->_var['goods']['goods_valuation'] == 2): ?>平方<?php endif; ?> 
    <?php if ($this->_var['goods']['goods_valuation'] == 3): ?> 米<?php endif; ?> 
    <?php if ($this->_var['goods']['goods_valuation'] == 4): ?> 本<?php endif; ?>  
    <?php if ($this->_var['goods']['goods_valuation'] == 5): ?> 瓶<?php endif; ?> 
    <?php if ($this->_var['goods']['goods_valuation'] == 6): ?> 袋<?php endif; ?> 
    <?php if ($this->_var['goods']['goods_valuation'] == 7): ?> 包<?php endif; ?> 
    <?php if ($this->_var['goods']['goods_valuation'] == 8): ?> 箱<?php endif; ?> 
    <?php if ($this->_var['goods']['goods_valuation'] == 9): ?> 罐<?php endif; ?> 
    
    </b>　<?php if ($this->_var['cfg']['show_marketprice']): ?>
    <del><?php echo $this->_var['goods']['market_price']; ?></del> 
    <span class="zj_zhekou"><?php echo $this->_var['goods']['zhekou']; ?></span>
    <?php endif; ?></li>
    <?php endif; ?> 
    
  </ul>
  <?php if ($this->_var['goods']['is_promote'] && $this->_var['goods']['gmt_end_time']): ?> 
  <?php echo $this->smarty_insert_scripts(array('files'=>'lefttime.js')); ?>
  <ul>
    <li> <span class="time">时间剩余：<time class="countdown" id="leftTime"><?php echo $this->_var['lang']['please_waiting']; ?></time></span> </li>
  </ul>
  <?php endif; ?>
  <?php if ($this->_var['promotion']): ?>
  <ul>
    <li>
    <?php $_from = $this->_var['promotion']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
    <?php echo $this->_var['lang']['activity']; ?>
    <?php if ($this->_var['item']['type'] == "snatch"): ?>
    <a href="snatch.php" title="<?php echo $this->_var['lang']['snatch']; ?>" class="rule c333">[<?php echo $this->_var['lang']['snatch']; ?>]</a>
    <?php elseif ($this->_var['item']['type'] == "group_buy"): ?>
    <a href="group_buy.php" title="<?php echo $this->_var['lang']['group_buy']; ?>" class="rule c333">[<?php echo $this->_var['lang']['group_buy']; ?>]</a>
    <?php elseif ($this->_var['item']['type'] == "auction"): ?>
    <a href="auction.php" title="<?php echo $this->_var['lang']['auction']; ?>" class="rule c333">[<?php echo $this->_var['lang']['auction']; ?>]</a>
    <?php elseif ($this->_var['item']['type'] == "favourable"): ?>
    <a href="#" title="<?php echo $this->_var['lang']['favourable']; ?>" class="rule c333">[<?php echo $this->_var['lang']['favourable']; ?>]</a>
    <?php endif; ?>
    <a href="#" title="<?php echo $this->_var['lang'][$this->_var['item']['type']]; ?> <?php echo $this->_var['item']['act_name']; ?><?php echo $this->_var['item']['time']; ?>" class="rule c333"><?php echo $this->_var['item']['act_name']; ?></a><br />
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    </li>
  </ul>
  <?php endif; ?>
  
  <!--<ul>
    <li>月销量：<?php echo $this->_var['sales_count']; ?>件</li>
  </ul>-->
</section>

<div class="wrap">
  <section class="goodsBuy radius5">
    <input id="goodsBuy-open" type="checkbox">
    <label class="info" for="goodsBuy-open">
    <div class="selected"> </div>
    </label>
    <form action="javascript:addToCart(<?php echo $this->_var['goods']['goods_id']; ?>)" method="post" name="ECS_FORMBUY" id="ECS_FORMBUY" >
     <div class="fields" style="display:block">  
        <ul class="ul2">
           
          <?php $_from = $this->_var['specification']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('spec_key', 'spec');if (count($_from)):
    foreach ($_from AS $this->_var['spec_key'] => $this->_var['spec']):
?>
          <li>
          <h2><?php echo $this->_var['spec']['name']; ?>：</h2>
            <div class="items">
            
            <?php if ($this->_var['spec']['attr_type'] == 1): ?>
                    	<?php $_from = $this->_var['spec']['values']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'value');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['value']):
?>
                    	<input type="radio" name="spec_<?php echo $this->_var['spec_key']; ?>" value="<?php echo $this->_var['value']['id']; ?>" id="spec_value_<?php echo $this->_var['value']['id']; ?>" <?php if ($this->_var['key'] == 0): ?>checked<?php endif; ?> onclick="changePrice(<?php echo $this->_var['value']['id']; ?>)" />
                    	<label for="spec_value_<?php echo $this->_var['value']['id']; ?>"><?php if ($this->_var['value']['product_number'] == 0): ?><?php echo $this->_var['value']['label']; ?>-<font class="price">缺货</font><?php else: ?><?php echo $this->_var['value']['label']; ?>-<font class="price"><?php echo $this->_var['value']['product_number']; ?><?php echo $this->_var['goods']['measure_unit']; ?></font><?php endif; ?></label> 
                    	<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>         
                    <?php else: ?>
          <?php $_from = $this->_var['spec']['values']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'value');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['value']):
?>
          <input type="radio" name="spec_<?php echo $this->_var['spec_key']; ?>" value="<?php echo $this->_var['value']['id']; ?>" id="spec_value_<?php echo $this->_var['value']['id']; ?>" <?php if ($this->_var['key'] == 0): ?>checked<?php endif; ?> onclick="changePrice(<?php echo $this->_var['value']['id']; ?>)" />
          <label for="spec_value_<?php echo $this->_var['value']['id']; ?>"><?php echo $this->_var['value']['label']; ?></label> 
          <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            <?php endif; ?>
            </div>
      </li>
          <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?> 
          
        </ul>
        <ul class="quantity">
          <div style="float:left;margin-bottom:20px">
                  <h2>数量：</h2>
                  <div class="items"> <span class="ui-number radius5">
                  	<?php if ($this->_var['goods']['goods_valuation'] == 1 || $this->_var['goods']['goods_valuation'] == 4): ?>
                    <?php if ($this->_var['goods']['goods_id'] > 0 && $this->_var['goods']['is_gift'] == 0 && $this->_var['goods']['parent_id'] == 0): ?>
                    <button type="button" class="decrease radius5" onclick="changenum(- 1)">-</button>
                    <input class="num" name="number" id="goods_number" value="1" min="1" max="<?php echo $this->_var['goods']['goods_number']; ?>" />
                    <button type="button" class="increase radius5" onclick="changenum(1)">+</button>
                    <?php else: ?> 
                    <?php echo $this->_var['goods']['goods_number']; ?> 
                    <?php endif; ?>
                    <?php endif; ?>
                    <?php if ($this->_var['goods']['goods_valuation'] == 2 || $this->_var['goods']['goods_valuation'] == 3): ?>
                    <?php if ($this->_var['goods']['goods_id'] > 0 && $this->_var['goods']['is_gift'] == 0 && $this->_var['goods']['parent_id'] == 0): ?>
                    <button type="button" class="decrease radius5" onclick="changenum(- 0.1)">-</button>
                    <input class="num" name="number" id="goods_number" value="1" min="1" max="<?php echo $this->_var['goods']['goods_number']; ?>" />
                    <button type="button" class="increase radius5" onclick="changenum(0.1)">+</button>
                    <?php else: ?> 
                    <?php echo $this->_var['goods']['goods_number']; ?> 
                    <?php endif; ?>
                    <?php endif; ?>    
                    </span> 
                    </div>
          </div>
          <div style="float:right;margin-right:20px">
            <h2><?php echo $this->_var['lang']['amount']; ?>：</h2>
            <div class="items">
                <font id="ECS_GOODS_AMOUNT" class="price"></font>
                <font color="#b0251e" style="font-size:small;font-weight: bold">元
			    </font>
            </div>
            
          </div>
        </ul>

      </div>
      
    </form>
  </section>
  <div class="guarantee">100%正品保证</div>
</div>

<script type="text/javascript">
//介绍 评价 咨询切换
var tab_now = 1;
function tab(id){
  document.getElementById('tabs' + tab_now).className = document.getElementById('tabs' + tab_now).className.replace('current', '');
  document.getElementById('tabs' + id).className = document.getElementById('tabs' + id).className.replace('', 'current');

  tab_now = id;
  if (id == 1) {
    document.getElementById('tab1').className = '';
    document.getElementById('tab2').className = 'hidden';
    document.getElementById('tab3').className = 'hidden';
  }else if (id == 2) {
    document.getElementById('tab1').className = 'hidden';
    document.getElementById('tab2').className = '';
    document.getElementById('tab3').className = 'hidden';
  }else if (id == 3) {
    document.getElementById('tab1').className = 'hidden';
    document.getElementById('tab2').className = 'hidden';
    document.getElementById('tab3').className = '';
  }
}
</script> 


<section class="s-detail">
  <header>
    <ul style="position: static;" id="detail_nav">
      <li id="tabs1" onClick="tab(1)" class="current"> 详情 </li>  

<!--       <li id="tabs2" onClick="tab(2)" class=""> 评价 <span class="review-count">(<?php echo $this->_var['goods']['comment_count']; ?>)</span> </li> -->
      <!--<li id="tabs3" onClick="tab(3)" class=""> 热销 </li>-->
    </ul>
  </header>
 <style>
   .goods_attr{
    background:white;
   }
   .goods_attr h2{
    color:#999;
    margin-bottom:0.6rem; 
   }
   .goods_attr li{
    padding:  2px 20px;
   }
   .attr_style{
    display: inline-block;
    border: 1px solid #ceced0;
    background-color: #fafafa;
    min-width: 2rem;
    padding: 0rem 1rem;
    margin: 0 0.5rem 0.5rem 0;
    max-width: 100%;
    text-align: center;
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
    -webkit-border-radius: 0.3rem;
    border-radius: 0.3rem; 
   }
 </style> 
  <ul  class="goods_attr">
      <?php $_from = $this->_var['specification']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('spec_key', 'spec');if (count($_from)):
    foreach ($_from AS $this->_var['spec_key'] => $this->_var['spec']):
?>
          <li>
          <?php if ($this->_var['spec']['name'] != '型号'): ?>
          <h2><?php echo $this->_var['spec']['name']; ?>:</h2>          
          <?php $_from = $this->_var['spec']['values']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'value');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['value']):
?>
          <div class="attr_style"><?php echo $this->_var['value']['label']; ?></div>
          <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
          <?php endif; ?>
          </li>
          <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
  </ul>
  
  <div id="tab1">
    <div class="desc wrap" id="desc_zj">
      <div class="blank2"></div>
      <?php echo $this->_var['goods']['goods_desc']; ?>
    </div>
  </div>
  
  <div id="big_img" class="big_img_class">
    <div class="big_img_class_in">
    <img src="">
  </div>
  </div>
  <script type="text/javascript">

  $("#desc_zj img").click(function(){
    
    var src1 = $(this).attr("src");
    $("#big_img img").attr("src",src1);
      $("#big_img").show();
    $(".option_zj").hide();
    $("#collect_box").hide();
    $("#content").hide();
    
    var wiH = $(window).height();
    var imgH = $("#big_img img").height();
    //alert(wiH);
    //alert(imgH);
    if(imgH<wiH){
      var ph = Math.round((wiH-imgH)/2);
      $("#big_img img").css("padding-top",ph+"px");
    }
    
    });
  $("#big_img").click(function (){
    $("#big_img").hide();
    $(".option_zj").show();
    $("#collect_box").show();
    $("#content").show();
    $("#big_img img").css("padding-top","0px");
    });

</script>

<!--  <script>
  $("#desc_zj img").width('100% !important');
  $("#desc_zj img").height('auto !important');
  </script>-->
  <div id="tab2" class="hidden">
    <div class="wrap">
      <div class="blank2"></div>
      <?php echo $this->fetch('library/comments.lbi'); ?> </div>
  </div>
  <div id="tab3" class="hidden">
    <div class="wrap">
      <ul class="m-recommend ">
        <div class="blank2"></div>
        <?php $_from = $this->_var['related_goods']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'releated_goods_data');$this->_foreach['related_goods'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['related_goods']['total'] > 0):
    foreach ($_from AS $this->_var['releated_goods_data']):
        $this->_foreach['related_goods']['iteration']++;
?> 
        <li class="flex_in  "   <?php if (($this->_foreach['related_goods']['iteration'] - 1) % 2 == 1): ?> style="float:right" <?php endif; ?> > <a href="<?php echo $this->_var['goods']['url']; ?>">
        <div class="summary radius5"> <img src="<?php echo $this->_var['site_url']; ?><?php echo $this->_var['releated_goods_data']['goods_thumb']; ?>" alt=""/>
          <div class="price"> 
            
            <?php if ($this->_var['releated_goods_data']['promote_price'] != 0): ?> 
            <?php echo $this->_var['releated_goods_data']['formated_promote_price']; ?> 
            <?php else: ?> 
            <?php echo $this->_var['releated_goods_data']['shop_price']; ?> 
            <?php endif; ?> 
            
          </div>
        </div>
        <?php if ($this->_var['goods']['goods_comment']): ?>
        <div class="reviews"> 
          <?php $_from = $this->_var['goods']['goods_comment']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'comment');$this->_foreach['comment'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['comment']['total'] > 0):
    foreach ($_from AS $this->_var['comment']):
        $this->_foreach['comment']['iteration']++;
?> 
          <?php if ($this->_foreach['comment']['iteration'] < 4): ?>
          <blockquote> <span class="user"><?php if ($this->_var['comment']['username']): ?><?php echo htmlspecialchars($this->_var['comment']['username']); ?><?php else: ?><?php echo $this->_var['lang']['anonymous']; ?><?php endif; ?></span> <?php echo $this->_var['comment']['content']; ?> </blockquote>
          <?php endif; ?> 
          <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?> 
        </div>
        <?php endif; ?> 
        </a>
        </li>
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
      </ul>
    </div>
  </div>
</section>

 


<script type="text/javascript">
var goods_id = <?php echo $this->_var['goods_id']; ?>;
var goodsattr_style = <?php echo empty($this->_var['cfg']['goodsattr_style']) ? '1' : $this->_var['cfg']['goodsattr_style']; ?>;
var gmt_end_time = <?php echo empty($this->_var['promote_end_time']) ? '0' : $this->_var['promote_end_time']; ?>;
<?php $_from = $this->_var['lang']['goods_js']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
var <?php echo $this->_var['key']; ?> = "<?php echo $this->_var['item']; ?>";
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
var goodsId = <?php echo $this->_var['goods_id']; ?>;
var now_time = <?php echo $this->_var['now_time']; ?>;


onload = function(){
  changePrice();
  fixpng();
  try {onload_leftTime();}
  catch (e) {}
}

/**
 * 点选可选属性或改变数量时修改商品价格的函数
 */
function changePrice(id)
{
  var attr = getSelectedAttributes(document.forms['ECS_FORMBUY']);
  var qty = document.forms['ECS_FORMBUY'].elements['number'].value;
  Ajax.call('goods.php', 'act=price&id=' + goodsId + '&attr=' + attr + '&number=' + qty, changePriceResponse, 'GET', 'JSON'); 
  
  $.ajax({
	  url:"goods.php",
	  type:"POST",
	  data:{'id':id,'act':'change_img'},
	  dataType:"json",
	  success:function(re){
	  	if(re.attr_img != ''){
	  		$("#first_img>img").attr('src',re.attr_img);
	  	}
	  }
  })

}

/**
 * 接收返回的信息
 */
function changePriceResponse(res)
{
  if (res.err_msg.length > 0)
  {
    alert(res.err_msg);
  }
  else
  {
    //document.forms['ECS_FORMBUY'].elements['number'].value = res.qty;
    if (document.getElementById('ECS_GOODS_AMOUNT'))
      document.getElementById('ECS_GOODS_AMOUNT').innerHTML = res.result;
  }
}

</script>
<?php echo $this->smarty_insert_scripts(array('files'=>'transport.js,utils.js')); ?>
<div class="option_zj" > 
<button type="button" class="btn buy radius5" onClick="addToCart_quick(<?php echo $this->_var['goods']['goods_id']; ?>)">立即购买</button>
<button type="button" class="btn cart radius5" onclick="addToCart(<?php echo $this->_var['goods']['goods_id']; ?>);"><div class="ico_01"></div>加入购物车</button>
<script type="text/javascript">
      function showDiv(){
        document.getElementById('popDiv').style.display = 'block';
        document.getElementById('hidDiv').style.display = 'block';
        document.getElementById('cartNum').innerHTML = document.getElementById('goods_number').value;
        document.getElementById('cartPrice').innerHTML = document.getElementById('ECS_GOODS_AMOUNT').innerHTML;
            }
            function closeDiv(){
                document.getElementById('popDiv').style.display = 'none';
        document.getElementById('hidDiv').style.display = 'none';
            }
     </script>
        
        <div class="tipMask" id="hidDiv" style="display:none" ></div>
        <div class="popGeneral" id="popDiv" >
          <div class="tit">
            <h4>商品加入购物车</h4>
            <span class="ico_08" onclick="closeDiv()"><a href="javascript:"></a></span> </div>
          <div id="main">
            <div id="left"> <img width="115" height="115" src="<?php echo $this->_var['site_url']; ?><?php echo $this->_var['goods']['original_img']; ?>"> </div>
            <div id="right">
              <p><?php echo $this->_var['goods']['goods_name']; ?></p>
              <span> 加入数量： <b id="cartNum"></b></span> <br/><span> 总计金额： <b id="cartPrice"></b></span> 
            </div>
          </div>
          <div class="popbtn"> <a class="bnt1 flex_in radius5" onclick="closeDiv()" href="javascript:void(0);">继续购物</a> <a class="bnt2 flex_in radius5" href="flow.php">去结算</a> </div>
        </div>
         
</div>
</body>
</html>