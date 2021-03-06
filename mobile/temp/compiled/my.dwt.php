<!DOCTYPE html>
<html lang="en">
<head>
<meta name="Generator" content="ECSHOP v2.7.3" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>美美商城</title>
        <meta name="keywords" content=" " />
    <meta name="description" content=" " />
    <meta property="og:type" content="game">
    <meta property="og:title" content="美房美邦">
    <meta property="og:image" content=" ">
    <meta property="og:description" content="美房美邦">
    <link rel="apple-touch-icon" href="favicon.ico" />
    <link rel="shortcut icon" href="favicon.ico" />
    <link rel="icon" href="favicon.ico" />
    <meta name="viewport" content="initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=0,width=device-width">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="msapplication-tap-highlight" content="no">
    <link rel="stylesheet" type="text/css" href="themesmobile/mobile/css/common.css">
    <link rel="stylesheet" type="text/css" href="themesmobile/mobile/css/swiper.css">
    <link rel="stylesheet" type="text/css" href="themesmobile/mobile/css/my.css">
    <style type="text/css">
        .pop{
          position:absolute;
          left:0;
          top:10%;
          width:100%;
          background:#eee;
          border:1px solid #ccc;
          z-index:100;
        }
        .pop_head{
          position:relative;
          height:20px;
          background:#ccc
        }
        .pop_head a{
          position:absolute;
          right:8px;
          line-height:20px;
          color:#000;
          text-decoration:none
        }
        .pop_head a:hover{
          color:#f60;
          text-decoration:none
        }
        .pop_body{padding:8px}
        .button {
            color: #19667d;
            background: #70c9e3;
            margin: 10px;
            padding: 0 20px;
            text-align: center;
            text-decoration: none;
            font: bold 12px/25px Arial, sans-serif;
            text-shadow: 1px 1px 1px rgba(255,255,255, .22);
            -webkit-border-radius: 30px;
            -moz-border-radius: 30px;
            border-radius: 30px;
         
            -webkit-box-shadow: 1px 1px 1px rgba(0,0,0, .29), inset 1px 1px 1px rgba(255,255,255, .44);
            -moz-box-shadow: 1px 1px 1px rgba(0,0,0, .29), inset 1px 1px 1px rgba(255,255,255, .44);
            box-shadow: 1px 1px 1px rgba(0,0,0, .29), inset 1px 1px 1px rgba(255,255,255, .44);
         
            -webkit-transition: all 0.15s ease;
            -moz-transition: all 0.15s ease;
            -o-transition: all 0.15s ease;
            -ms-transition: all 0.15s ease;
            transition: all 0.15s ease;
        }
    </style>
</head>
<body class="my-wrap">
    <div class="myhd">
        <img src="themesmobile/img/mybg.jpg" alt="" class="bg">
        <img src="themesmobile/img/IQr.jpg" alt="" class="qr">
    </div>
    <div class="hd-txt">
        <p>加入美房美邦，开始美美生活</p>
        <p>您的会员号为：<?php echo $this->_var['info']['username']; ?></p>
        <p><?php echo $this->_var['rank_name']; ?></p>
    </div>
    <div class="content mb_footer">
        <ul class="options">
            <li>
                <p>账户余额</p>
                <p><?php echo $this->_var['info']['surplus']; ?>元</p>
            </li>
            <li>
                <p>剩余积分</p>
                <p><?php echo $this->_var['surplus_num']; ?>分</p>
            </li>
            <li>
                <p>分享积分</p>
                <p><?php echo $this->_var['share_sum']; ?>分</p>
            </li>
            <li>
                <p>签到积分</p>
                <p><?php echo $this->_var['record_sum']; ?>分</p>
            </li>
        </ul>
        <ul class="list">
        <?php if ($this->_var['info']['user_rank'] == 99 || $this->_var['info']['user_rank'] == 102 || $this->_var['info']['user_rank'] == 103): ?>
            <li>
                <i class="iconfont">&#xe622;</i>
                <a href='./user.php?act=qr_weixin'>我的二维码</a>
                    
            </li>
            <li>
                <i class="iconfont">&#xe61c;</i><span>我的余额</span>
                <div class="sign" style="background-color:white;">转入&nbsp;<a href="./user.php?act=account_raply" class="button">提现</a></div>
            </li>
            <li>
                <a href="./user.php?act=weixin_fans"><i class="iconfont">&#xe61c;</i><span>我的小伙伴</span>
            </li>
            <?php endif; ?>

              <?php if ($this->_var['info']['user_rank'] == 102): ?>
                 <li>
                       <a href="./user.php?act=sample_manage"><i class="iconfont">&#xe61c;</i><span>样本管理</span>
                 </li>

               <?php endif; ?>
            <li>
                <a href="./user.php?act=intro_fans"><i class="iconfont">&#xe61c;</i><span>我的分店</span>
            </li>            
            <li>
                <a href="./user.php?act=my_income">
                <i class="iconfont">&#xe621;</i><span>我的收益</span>
            </li>
            <li>
                <a href="./user.php?act=present_list"><i class="iconfont">&#xe620;</i><span>提现记录</span></a>
            </li>
            <li>
                <a href="./user.php?act=order_list"><i class="iconfont">&#xe620;</i><span>我的订单</span></a>
            </li>
            <li>
               <a href="./user.php?act=profile"><i class="iconfont">&#xe61e;</i><span>用户信息</span>
            </li>   
            <li>
                <a href="./user.php?act=vip_login"><i class="iconfont">&#xe61f;</i><span>网站会员登录</span>
            </li>
        </ul>
    </div>

    <div class="JQ_include" src="include/footer.html"></div>
    <script src="themesmobile/js/config.js"></script>
    <script src="themesmobile/js/lib/jquery-1.9.1.min.js"></script>
    <script src="themesmobile/js/lib/template.min.js"></script>
    <script src="themesmobile/mobile/dist/swiper.min.js"></script>
    <script src="themesmobile/js/global.js"></script>
    <script src="themesmobile/js/worldunion.js"></script>

</body>
</html>
<script type="text/javascript"> 
    function show(ev,id){ 
      var ev=window.event||ev;
      ev.stopPropagation?ev.stopPropagation():ev.cancelBubble=true;
      var obj=document.getElementById(id); 
      obj.style.display=""; 
    } 
    function hide(evt,id){ 
      var obj=document.getElementById(id); 
      var target=evt.target?evt.target:evt.srcElement;
      if(target.id=="pop"||target.id=="pop_head"||target.id=="pop_body")
      return;
      else
      obj.style.display="none"; 
    } 
    document.onclick=function(ev){
      var ev=window.event||ev;
      hide(ev,'pop');
    }
    window.onload=function(){
      var theclose=document.getElementById("closeid");
      var theshow=document.getElementById("showid");
       
      theclose.onclick=function(){
        var ev=window.event||ev;
        hide(ev,'pop')
      }
       
      theshow.onclick=function(ev){
        var ev=window.event||ev;
        show(ev,"pop")
      }
    }
</script>