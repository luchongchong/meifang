<?php exit;?>a:3:{s:8:"template";a:2:{i:0;s:53:"/mnt/www/meifang/mobile/themesmobile/mobile/goods.dwt";i:1;s:64:"/mnt/www/meifang/mobile/themesmobile/mobile/library/comments.lbi";}s:7:"expires";i:1491879457;s:8:"maketime";i:1491879457;}<!DOCTYPE html>
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
<script type="text/javascript" src="js/jquery.json.js"></script><script type="text/javascript" src="js/common.js"></script><script type="text/javascript">
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
<script src='http://res.wx.qq.com/open/js/jweixin-1.0.0.js' type='text/javascript'></script>
<script type='text/javascript' language='javascript'>
wx.config({
    debug: false,
    appId: 'wx2326ecf73474cfaa',
    timestamp: 1491879457,
    nonceStr: 'ixjq3ef9no7yc24z4x7zy7h9egmfupnr',
    signature: 'adec5a23a7e46609d2b479b9dd8941c7d78be780',
    jsApiList: [
		'onMenuShareTimeline',
		'onMenuShareAppMessage',
    ]
});
wx.ready(function () {
	//分享到朋友圈
	wx.onMenuShareTimeline({
    title: '702系列 汽车总动员儿童房壁纸男孩房卧室墙纸',
    link: 'http://test.mfmb58.com/mobile/goods4.php?id=445',
    imgUrl: '../images/201703/source_img/445_G_1490834725052.jpg',
    success: function () { 
		$.get('http://test.mfmb58.com/mobile/user.php?act=point_share&user_id=171910', function(result){	
  		});
    },
    cancel: function () { 
    }
	});
	
	//分享到朋友
	wx.onMenuShareAppMessage({
    title: '702系列 汽车总动员儿童房壁纸男孩房卧室墙纸',
    desc: '美房美邦',
    link: 'http://test.mfmb58.com/mobile/goods4.php?id=445',
    imgUrl: '../images/201703/source_img/445_G_1490834725052.jpg',
    type: '',
    dataUrl: '',
    success: function () { 
		$.get('http://test.mfmb58.com/mobile/user.php?act=point_share&user_id=171910', function(result){	
  		});
    },
    cancel: function () { 
    }
	});
});
</script>
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
      <!--<div><a href="javascript:showPic()"><img src="../images/201703/thumb_img/445_thumb_G_1490834725978.jpg"  alt="702系列 汽车总动员儿童房壁纸男孩房卧室墙纸" /></a></div>-->
      <ul>
        <li><a href="javascript:showPic()" id="first_img"><img   alt="" src="../images/201703/source_img/445_G_1490834725052.jpg"/></a></li>
         
         
         
         
                <li><a href="javascript:showPic()"><img alt="" src="../images/201703/goods_img/_P_20170330164456_17.jpg"/></a></li>
         
         
                <li><a href="javascript:showPic()"><img alt="" src="../images/201703/goods_img/_P_20170330164455_77.jpg"/></a></li>
         
         
                <li><a href="javascript:showPic()"><img alt="" src="../images/201703/goods_img/_P_20170330164455_84.jpg"/></a></li>
         
         
                <li><a href="javascript:showPic()"><img alt="" src="../images/201703/goods_img/445_P_1490834725740.jpg"/></a></li>
         
         
              </ul>
    </div>
    <div class="icons">
      <ul>
        <i class="current"></i> 
         
         
        <i class="current"></i> 
         
        <i class="current"></i> 
         
        <i class="current"></i> 
         
        <i class="current"></i> 
         
        <i class="current"></i> 
         
              </ul>
    </div>
    
    <a class="collect_self" id="collect_box" href="javascript:collect(445)"><img src="themesmobile/mobile/img/ico_star.png"></a>
    
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
    <h1> 702系列 汽车总动员儿童房壁纸男孩房卧室墙纸 </h1>
  </div>
  <ul>
        <li>优惠价:
    <b class="price">
    ￥99元/
    卷     
     
      
     
     
     
     
     
    
    </b>　    <del>￥258</del> 
    <span class="zj_zhekou"></span>
    </li>
     
    
  </ul>
      
  <!--<ul>
    <li>月销量：件</li>
  </ul>-->
</section>
<div class="wrap">
  <section class="goodsBuy radius5">
    <input id="goodsBuy-open" type="checkbox">
    <label class="info" for="goodsBuy-open">
    <div class="selected"> </div>
    </label>
    <form action="javascript:addToCart(445)" method="post" name="ECS_FORMBUY" id="ECS_FORMBUY" >
     <div class="fields" style="display:block">  
        <ul class="ul2">
           
                    <li>
          <h2>型号：</h2>
            <div class="items">
            
                                <input type="radio" name="spec_227" value="2220" id="spec_value_2220" checked onclick="changePrice(2220)" />
          <label for="spec_value_2220">281301</label> 
                    <input type="radio" name="spec_227" value="2221" id="spec_value_2221"  onclick="changePrice(2221)" />
          <label for="spec_value_2221">281302</label> 
                    <input type="radio" name="spec_227" value="2222" id="spec_value_2222"  onclick="changePrice(2222)" />
          <label for="spec_value_2222">281303</label> 
                    <input type="radio" name="spec_227" value="2223" id="spec_value_2223"  onclick="changePrice(2223)" />
          <label for="spec_value_2223">281304</label> 
                    <input type="radio" name="spec_227" value="2224" id="spec_value_2224"  onclick="changePrice(2224)" />
          <label for="spec_value_2224">281305</label> 
                    <input type="radio" name="spec_227" value="2225" id="spec_value_2225"  onclick="changePrice(2225)" />
          <label for="spec_value_2225">281401</label> 
                    <input type="radio" name="spec_227" value="2226" id="spec_value_2226"  onclick="changePrice(2226)" />
          <label for="spec_value_2226">281402</label> 
                    <input type="radio" name="spec_227" value="2227" id="spec_value_2227"  onclick="changePrice(2227)" />
          <label for="spec_value_2227">281403</label> 
                    <input type="radio" name="spec_227" value="2228" id="spec_value_2228"  onclick="changePrice(2228)" />
          <label for="spec_value_2228">281404</label> 
                    <input type="radio" name="spec_227" value="2229" id="spec_value_2229"  onclick="changePrice(2229)" />
          <label for="spec_value_2229">281405</label> 
                                  </div>
      </li>
                    <li>
          <h2>规格：</h2>
            <div class="items">
            
                                <input type="radio" name="spec_230" value="4579" id="spec_value_4579" checked onclick="changePrice(4579)" />
          <label for="spec_value_4579">0.53*10</label> 
                                  </div>
      </li>
                    <li>
          <h2>风格：</h2>
            <div class="items">
            
                                <input type="radio" name="spec_231" value="4580" id="spec_value_4580" checked onclick="changePrice(4580)" />
          <label for="spec_value_4580">儿童</label> 
                                  </div>
      </li>
           
          
        </ul>
        <ul class="quantity">
          <div style="float:left;margin-bottom:20px">
                  <h2>数量：</h2>
                  <div class="items"> <span class="ui-number radius5">
                  	                                        <button type="button" class="decrease radius5" onclick="changenum(- 1)">-</button>
                    <input class="num" name="number" id="goods_number" value="1" min="1" max="100" />
                    <button type="button" class="increase radius5" onclick="changenum(1)">+</button>
                                                                
                    </span> 
                    </div>
          </div>
          <div style="float:right;margin-right:20px">
            <h2>商品总价：</h2>
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
<!--       <li id="tabs2" onClick="tab(2)" class=""> 评价 <span class="review-count">()</span> </li> -->
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
                <li>
                    </li>
                    <li>
                    <h2>规格:</h2>          
                    <div class="attr_style">0.53*10</div>
                              </li>
                    <li>
                    <h2>风格:</h2>          
                    <div class="attr_style">儿童</div>
                              </li>
            </ul>
  
  <div id="tab1">
    <div class="desc wrap" id="desc_zj">
      <div class="blank2"></div>
      <img alt="" src="/includes/kindeditor/php/../../../images/upload/image/20160303/20160303173308_82247.png" /><img alt="" src="/includes/kindeditor/php/../../../images/upload/image/20160303/20160303173308_78767.png" /><img alt="" src="/includes/kindeditor/php/../../../images/upload/image/20160303/20160303173309_62307.png" /><img alt="" src="/includes/kindeditor/php/../../../images/upload/image/20160303/20160303173309_40109.png" /><img alt="" src="/includes/kindeditor/php/../../../images/upload/image/20160303/20160303173310_13871.png" /><img alt="" src="/includes/kindeditor/php/../../../images/upload/image/20160303/20160303173311_74077.png" /><img alt="" src="/includes/kindeditor/php/../../../images/upload/image/20160303/20160303173311_78555.png" /><img alt="" src="/includes/kindeditor/php/../../../images/upload/image/20160303/20160303173312_62107.png" /><img alt="" src="/includes/kindeditor/php/../../../images/upload/image/20160303/20160303173313_27921.png" /><img alt="" src="/includes/kindeditor/php/../../../images/upload/image/20160303/20160303173313_57534.png" /><img alt="" src="/includes/kindeditor/php/../../../images/upload/image/20160303/20160303173316_87765.png" /><img alt="" src="/includes/kindeditor/php/../../../images/upload/image/20160303/20160303173316_93948.png" /><img alt="" src="/includes/kindeditor/php/../../../images/upload/image/20160303/20160303173317_79647.png" /><img alt="" src="/includes/kindeditor/php/../../../images/upload/image/20160303/20160303173317_95167.png" /><img alt="" src="/includes/kindeditor/php/../../../images/upload/image/20160303/20160303173323_46222.png" /><img alt="" src="/includes/kindeditor/php/../../../images/upload/image/20160303/20160303173324_21684.png" /><img alt="" src="/includes/kindeditor/php/../../../images/upload/image/20160303/20160303173324_90761.png" /><img alt="" src="/includes/kindeditor/php/../../../images/upload/image/20160303/20160303173325_63560.png" /><img alt="" src="/includes/kindeditor/php/../../../images/upload/image/20160303/20160303173325_76015.png" /><img alt="" src="/includes/kindeditor/php/../../../images/upload/image/20160303/20160303173326_17686.png" /><img alt="" src="/includes/kindeditor/php/../../../images/upload/image/20160303/20160303173347_62557.png" /> 
<p>
	<br />
</p>    </div>
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
      <script type="text/javascript" src="js/utils.js"></script><div id="ECS_COMMENT"> 554fcae493e564ee0dc75bdf2ebf94cacomments|a:3:{s:4:"name";s:8:"comments";s:4:"type";i:0;s:2:"id";i:445;}554fcae493e564ee0dc75bdf2ebf94ca</div>
 </div>
  </div>
  <div id="tab3" class="hidden">
    <div class="wrap">
      <ul class="m-recommend ">
        <div class="blank2"></div>
              </ul>
    </div>
  </div>
</section>
 
<script type="text/javascript">
var goods_id = 445;
var goodsattr_style = 1;
var gmt_end_time = 0;
var day = "天";
var hour = "小时";
var minute = "分钟";
var second = "秒";
var end = "结束";
var goodsId = 445;
var now_time = 1491850657;
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
<script type="text/javascript" src="js/transport.js"></script><div class="option_zj" > 
<button type="button" class="btn buy radius5" onClick="addToCart_quick(445)">立即购买</button>
<button type="button" class="btn cart radius5" onclick="addToCart(445);"><div class="ico_01"></div>加入购物车</button>
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
            <div id="left"> <img width="115" height="115" src="../images/201703/source_img/445_G_1490834725052.jpg"> </div>
            <div id="right">
              <p>702系列 汽车总动员儿童房壁纸男孩房卧室墙纸</p>
              <span> 加入数量： <b id="cartNum"></b></span> <br/><span> 总计金额： <b id="cartPrice"></b></span> 
            </div>
          </div>
          <div class="popbtn"> <a class="bnt1 flex_in radius5" onclick="closeDiv()" href="javascript:void(0);">继续购物</a> <a class="bnt2 flex_in radius5" href="flow.php">去结算</a> </div>
        </div>
         
</div>
</body>
</html>