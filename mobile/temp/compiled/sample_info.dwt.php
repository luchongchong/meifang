<!doctype html>
<html>
<head>
<meta name="Generator" content="ECSHOP v2.7.3" />
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"/>
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="format-detection" content="telephone=no">
<title>样本展示</title>
<link href="themesmobile/mobile/yangben/css/ectouch.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="themesmobile/mobile/yangben/css/style.css" />
<link rel="stylesheet" type="text/css" href="themesmobile/mobile/yangben/css/animate.min.css" />
<script type="text/javascript" src="themesmobile/mobile/yangben/js/jquery-1.12.4.min.js"></script>
<script type="text/javascript" src="themesmobile/mobile/yangben/js/swiper-3.3.1.jquery.min.js"></script>
<script type="text/javascript" src="themesmobile/mobile/yangben/js/auto-size.js"></script>
<?php echo $this->smarty_insert_scripts(array('files'=>'jquery.json.js,common.js')); ?>
</head>

<body>
	<div class="mobile_wrap bj1 ohei">
		<div class="center_top fixed">
        	<a href="./user.php" class="fl"><img src="themesmobile/img/ico0.png" alt="">个人中心</a>
           <form action="searchfor.php" class="search-form" id="search">
                          <div class="search">
                             <input name="keywords" id="keywordfoot" type="text">
                              <button></button>
                          </div>
                          <a href="javascript:void(0)" onclick="return check('keywordfoot')" class="fr">搜索</a>

                     </form>
        </div>
        <div class="back1"></div>
        
        <div class="Edition">
        	<div class="img">
            	<img src="<?php echo $this->_var['goods']['goods_thumb']; ?>" alt="" style="width:157px;height:157px;">
            </div>
            <div class="text">
            	<b>版本编号：《<?php echo $this->_var['goods_attr_list']['version_number']; ?>》</b>
                <p>工艺特点：<?php echo $this->_var['goods_attr_list']['technology']; ?></p>
                <strong>适用场所：<?php echo $this->_var['goods_attr_list']['place']; ?></strong>
            </div>
              <div class="price">
               <b>价格：<?php echo $this->_var['goods_attr_list']['shop_price']; ?>元</b>
               </div>
        </div>
        <div class="introduce">
        	<h2><i></i>版本介绍<span>jieshao</span></h2>
            <div class="text">
            	<p><?php echo $this->_var['goods_attr_list']['intro']; ?></p>
            </div>
        </div>
        <div class="Exhibition">
        	<h2><i></i>花型展示<span>zhanshi</span></h2>
            <ul>
                   <?php $_from = $this->_var['pattern_show_img']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'show');$this->_foreach['no'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['no']['total'] > 0):
    foreach ($_from AS $this->_var['show']):
        $this->_foreach['no']['iteration']++;
?>
                	<li>
                      <a href="<?php echo $this->_var['show']['link_url']; ?>">
                                	<div class="pict">
                                    	<img src="<?php echo $this->_var['show']['img_url']; ?>" alt="">
                                    </div>
                                    <p><?php echo $this->_var['show']['img_desc']; ?></p>
                                       <a>
                                </li>
                  <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>


                

            </ul>
        </div>




        <div class="back">

        </div>
        <?php echo $this->smarty_insert_scripts(array('files'=>'transport.js,utils.js')); ?>
          <div class="bottom fixed">
                          <div class="option_zj" >
                            <button type="button" class="btn buy radius5" onClick="addToCart_quick(<?php echo $this->_var['goods']['goods_id']; ?>)">加入购物车</button>
                        </div>
            </div>
        <div class="bottom fixed">
            <ul>
                <li class="acti"><a href="./index.php"><div class="pic"><i><img src="themesmobile/img/ico4.png" alt=""></i></div><p>美美商城</p></a></li>
                <li><a href="./flow.php"><div class="pic"><i><img src="themesmobile/img/ico5.png" alt=""></i></div><p>购物车</p></a></li>
                <li><a href="./category.php"><div class="pic"><i><img src="themesmobile/img/ico6.png" alt=""></i></div><p>产品分类</p></a></li>
                <li><a href="./points.php"><div class="pic"><i><img src="themesmobile/img/ico7.png" alt=""></i></div><p>积分商城</p></a></li>
                <li><a href="./experience.php?act=default"><div class="pic"><i><img src="themesmobile/img/ico8.png" alt=""></i></div><p>全国门店</p></a></li>
                
            </ul>
    	</div>
            
    </div>


</body>
<script type="text/javascript" src="themesmobile/mobile/yangben/js/type.js"></script>
<script>
	imgauto(".bottom ul li .pic i img");
	function check(Id){
    	var strings = document.getElementById(Id).value;
    	if(strings.replace(/(^\s*)|(\s*$)/g, "").length == 0){
    		return false;
    	}
    	//return true;
    	$('#search').submit();
    }
</script>

</html>
