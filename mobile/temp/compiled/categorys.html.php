<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"/>
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="format-detection" content="telephone=no">
<title>产品分类</title>
<link rel="stylesheet" type="text/css" href="themesmobile/mobile/css/style.css" />
<link rel="stylesheet" type="text/css" href="themesmobile/mobile/css/animate.min.css" />
<script type="text/javascript" src="themesmobile/mobile/js/jquery-1.12.4.min.js"></script>
<script type="text/javascript" src="themesmobile/mobile/js/swiper-3.3.1.jquery.min.js"></script>
<script type="text/javascript" src="themesmobile/mobile/js/auto-size.js"></script>
<link rel="stylesheet" type="text/css" href="themesmobile/mobile/css/common.css">
<link rel="stylesheet" type="text/css" href="themesmobile/mobile/css/pro.css">
</head>

<body>
	<div class="mobile_wrap bj1 ohei">
		<div class="center_top fixed">
        	<a href="user.php" class="fl">
        	<img src="themesmobile/mobile/img/ico0.png" alt="">个人中心</a>
            <div class="search">
            	<form action="searchfor.php" class="search-form">
				<input type="text" name="keywords" id="keywordfoot" />
			</form>
            </div>
            <a href="searchfor.php" class="fr">搜索</a>
        </div>
        <div class="back1"></div>
        
        <div class="main_layout dt">
        	<div class="con_left">
            	<ul>
            		<?php $_from = $this->_var['categories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'cat');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['cat']):
?>
            		<?php if ($this->_var['cat']['cat_id'] == $this->_var['current_id']): ?>
                    <li class="acti">
                    <?php if ($this->_var['cat']['url'] != ''): ?>
                    <a class="switchBtn switchBtn-album" href="./<?php echo $this->_var['cat']['url']; ?>"><?php echo $this->_var['cat']['name']; ?></a>
                    <?php else: ?>
                    <a class="switchBtn switchBtn-album" href="category.php?id=<?php echo $this->_var['cat']['cat_id']; ?>"><?php echo $this->_var['cat']['name']; ?></a>
                    <?php endif; ?>
                    </li>
                    <?php else: ?>
                    <li>
                    <?php if ($this->_var['cat']['url'] != ''): ?>
                    <a class="switchBtn switchBtn-album" href="./<?php echo $this->_var['cat']['url']; ?>"><?php echo $this->_var['cat']['name']; ?></a>
                    <?php else: ?>
                    <a class="switchBtn switchBtn-album" href="category.php?id=<?php echo $this->_var['cat']['cat_id']; ?>"><?php echo $this->_var['cat']['name']; ?></a>
                    <?php endif; ?>
                    </li>                    
                    <?php endif; ?>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            	</ul>
            </div>
            <div class="right_con">
            	<div class="banner" style="width:100%;">
                	<img src="themesmobile/mobile/img/img0.jpg" alt="">
                </div>
                <div class="type">
                <?php $_from = $this->_var['rows']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'cat');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['cat']):
?>
                	<h2>
                	<span>
 					<?php echo $this->_var['cat']['cat_name']; ?>
                	</span>
                	<a href="#">
                	<img src="themesmobile/mobile/img/ico3.png" alt="">排行榜</a>
                	</h2>
                    <ul class="dt">
                    <?php $_from = $this->_var['cat']['cat_id']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'cats');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['cats']):
?>
                    <a href="categorys.php?act=fenlei&cat_id=<?php echo $this->_var['cats']['cat_id']; ?>">
                    	<li>
                        	<div class="pictd">
                            	<img src="../images/fenlei/<?php echo $this->_var['cats']['type_img']; ?>" alt=""  height="100%" width="100%" >
                            </div>
                            <p><?php echo $this->_var['cats']['cat_name']; ?></p>
                        </li>
                      </a>
                     <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>   
                    </ul>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                </div>
            </div>
        </div>
           
    </div>
    
    <div class="back"></div>
    <div class="bottom fixed">
        <ul>
            <li class="acti"><a href="./index.php"><div class="pic"><i><img width="25%" src="themesmobile/mobile/img/ico4.png" alt=""></i></div><p>美美商城</p></a></li>
            <li><a href="./flow.php"><div class="pic"><i><img width="25%" src="themesmobile/mobile/img/ico5.png" alt=""></i></div><p>购物车</p></a></li>
            <li><a href="./category.php"><div class="pic"><i><img width="25%" src="themesmobile/mobile/img/ico6.png" alt=""></i></div><p>产品分类</p></a></li>
            <li><a href="./points.php"><div class="pic"><i><img width="25%" src="themesmobile/mobile/img/ico7.png" alt=""></i></div><p>积分商城</p></a></li>
            <li><a href="./experience.php?act=default"><div class="pic"><i><img width="25%" src="themesmobile/mobile/img/ico8.png" alt=""></i></div><p>全国门店</p></a></li>
            
        </ul>
    </div>
        
</body>
<script type="text/javascript" src="themesmobile/mobile/js/type.js"></script>
<script>
	imgauto(".bottom ul li .pic i img");
	click_addname(".main_layout .con_left ul li","acti",0)
</script>
</html>
