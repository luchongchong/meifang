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
<script src="themesmobile/js/jquery.min.js"  type="text/javascript"></script>
 <style>
        body{
            margin:0;
            padding: 0;
            border:none; 
        }
        .header{
        	width: 100%;
        	height: auto;
        }
        .header img{
        	width: 100%;
        	height: auto;
        	background-size: 100% 100%；
        }
        .join{
            background:white;
            width: 100%;
            height:auto;
            color:#B60007;
            font-weight:bolder;
            font-size: 1.2rem;
            padding:2rem 0 1rem 0;
            

        }
        .join img{
            width: 80px;
            float: left;
            margin-left: 1.2rem;
            padding-right:0.5rem;
        }
        .join .headtitle{
            margin-top: 2rem;
        }
        .fenxiang{
        	background:#B60007;
        }	
        .fenxiang img{
            margin-top:3rem;
        	width:70%;
        	margin-left:15%;
        	margin-bottom:1.1rem;  
        }


        .footer{
            background:#B60007;
        	width: 100%;
            height:auto;
            color:white;
            font-size: 1rem;
            text-align: center;
            padding:20px 0px 20px 0px;
        }
        .nickname{
            color: white;
            position: relative;
            margin-left: 60%;
            padding-bottom: 1rem;
        }
    </style>
<?php echo $this->_var['re_share']; ?>
</head>

</head>
<body>
<div>
    <div class="header">
     <img src="themesmobile/mobile/img/head_1.png">
    <div>
    <div class="join">
        <img src="<?php echo $this->_var['user']['headimgurl']; ?>" >
        <div class="headtitle">
       扫我 惊喜多多,<br>
       "免费"墙纸只在美房美邦
       </div> 
    </div>
    <div class="fenxiang">
    	<img src="<?php echo $this->_var['qr_url']; ?>" /> 
        <div class="footer">
        长按，识别二维码，关注我<br> 
        了解墙纸 购买墙纸 成为会员,获得二维码<br> 
        扫码圈粉 粉丝购买 即刻返利<br> 
        免费墙纸就是那么简单
        </div>
        <div class="nickname">
            [<?php echo $this->_var['user']['nickname']; ?>]
        </div> 
    </div>
    
</div>
</body>
</html>
