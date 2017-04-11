<!DOCTYPE html>
<html>
<head>
<meta name="Generator" content="ECSHOP v2.7.3" />
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>美美商城</title>
    <div class="JQ_include" src="themesmobile/mobile/include/meta.dwt"></div>

    <link href="themesmobile/mobile/css/common.css" rel="stylesheet">
    <link href="themesmobile/mobile/css/store.css" rel="stylesheet">
    <link href="themesmobile/mobile/css/slideshow.css" rel="stylesheet">
</head>
<body>
    <header>
        <a href="javascript:history.go(-1)" class="i-back iconfont">&#xe624;</a>
        <h1>
            <form action="" class="search-form">
                <i class="iconfont">&#xe600;</i>
                <input type="text">
            </form>
        </h1>
        <a href="category.php" class="category">分类<i class="iconfont">&#xe602;</i></a>
    </header>
    <section>
        <div class="store-hd">最新门店</div>
         
         
         <?php if ($this->_var['new_stores'] != ''): ?>
            <div class="comiis_wrapad" id="slideContainer">
                <div id="frameHlicAe" class="frame cl">
                    <div class="temp"></div>
                    <div class="block">
                      
<div class="cl">
                            <ul class="slideshow" id="slidesImgs">
                                 <li><a href="experience.php?act=store_detail&store_id=<?php echo $this->_var['news']['store_id']; ?>"><img src="http://www.mfmb58.com/upload/store1.jpg"  alt="" style="width:100%;height:100%;"/><span class="title"><?php echo $this->_var['news']['name']; ?></a></span></li>
                                 <li><a href="experience.php?act=store_detail&store_id=<?php echo $this->_var['news']['store_id']; ?>"><img src="http://www.mfmb58.com/upload/store2.jpg"  alt="" style="width:100%;height:100%;"/><span class="title"><?php echo $this->_var['news']['name']; ?></a></span></li>
                                 <li><a href="experience.php?act=store_detail&store_id=<?php echo $this->_var['news']['store_id']; ?>"><img src="http://www.mfmb58.com/upload/store3.jpg"  alt="" style="width:100%;height:100%;"/><span class="title"><?php echo $this->_var['news']['name']; ?></a></span></li>
                                 <li><a href="experience.php?act=store_detail&store_id=<?php echo $this->_var['news']['store_id']; ?>"><img src="http://www.mfmb58.com/upload/store4.jpg"  alt="" style="width:100%;height:100%;"/><span class="title"><?php echo $this->_var['news']['name']; ?></a></span></li>
                               </ul>
                        </div>
                        <div class="slidebar" id="slideBar">
                            <ul>
                                <?php $_from = $this->_var['new_stores']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'news');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['news']):
?>
                                <li ><?php echo $this->_var['k']; ?></li>
                                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                
            </script>
            
         
         <?php endif; ?>
        </div>
    </section>
    <section class="map mb_footer">
        <div class="map-hd">全国范围<span><?php echo $this->_var['store_num']; ?>家门店可以使用（门店信息仅供参考）</span></div>
        <div class="sel-address cle">
            <div class="txt">所在地：</div>
            <div id="provinceSelect" class="sel">
                  <i class="iconfont">&#xe61a;</i>
                   <?php if ($this->_var['province_name']): ?>
                   <label class="sel-skin"><?php echo $this->_var['province_name']; ?></label>
                   <?php else: ?>
                    <label class="sel-skin">选择省份</label>
                   <?php endif; ?>
                  <ul id="province">
                  
                  </ul>
                  <input type="hidden" id="provinceInput">
            </div>
            <div id="citySelect" class="sel">
                  <i class="iconfont">&#xe61a;</i>
                  <?php if ($this->_var['city_name']): ?>
                   <label class="sel-skin"><?php echo $this->_var['city_name']; ?></label>
                   <?php else: ?>
                    <label class="sel-skin">选择城市</label>
                   <?php endif; ?>
                  
                  <ul id="city">
                  </ul>
                  <input type="hidden" id="cityInput">
            </div>
            <div id="areaSelect" class="sel">
                  <i class="iconfont">&#xe61a;</i>
                  <?php if ($this->_var['area_name']): ?>
                   <label class="sel-skin"><?php echo $this->_var['area_name']; ?></label>
                   <?php else: ?>
                    <label class="sel-skin">选择地区</label>
                   <?php endif; ?>
                  
                  <ul id="area">
                  </ul>
                  <input type="hidden" id="areaInput">
            </div>
        </div>
        <div class="map-box" id="map"></div>
        <ul class="store-list" id="weixin_list">
            <?php if ($this->_var['result']): ?>
            <?php $_from = $this->_var['result']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'res');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['res']):
?>
            <li>
                <p> <a href="experience.php?act=store_detail&store_id=<?php echo $this->_var['res']['store_id']; ?>"><?php echo $this->_var['res']['name']; ?></a> </p>
                <p class="txt">地址：<?php echo $this->_var['res']['city']; ?><?php echo $this->_var['res']['province']; ?><?php echo $this->_var['res']['district']; ?><?php echo $this->_var['res']['address']; ?></p>
                <p class="txt">电话：<?php echo $this->_var['res']['tel']; ?></p>
            </li>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            <?php else: ?>
                <li style="text-align:center;margin-top:30px;">本地区尚未有门店开始请用户选择其他区域</li>
            <?php endif; ?>
        </ul>
    </section>

    <div class="JQ_include" src="themesmobile/mobile/include/footer.dwt"></div>
    
    <script src="themesmobile/js/lib/jquery-1.9.1.min.js"></script>
    <script src="themesmobile/mobile/dist/swiper.min.js"></script>
    <script src="themesmobile/js/lib/template.min.js"></script>
    <script src="themesmobile/js/plug.js"></script>
    <script src="themesmobile/js/global.js"></script>
    <script src="themesmobile/js/worldunion.js"></script>
    <script src="themesmobile/js/loadHtml.js"></script>
    <script src="themesmobile/js/address.js"></script> 
    <script src="themesmobile/mobile/js/slideshow.js"></script> 
    <script type="text/javascript" src="http://api.map.baidu.com/api?v=1.4"></script>
    <script>   
    $(document).ready(function () {
    var page = 1;
    var is_none = false;
    var area='<?php echo $this->_var['area_name']; ?>';
    var city='<?php echo $this->_var['city_name']; ?>';
    var province='<?php echo $this->_var['province_name']; ?>';
    $(window).scroll(function () {
        if ($(document).scrollTop() >= $(document).height() - $(window).height()) {  
            if(is_none)
            {
                return false;
            }
            page++;
            $.get('./experience.php?act=experience_list_1&province='+province+'&area='+area+'&city='+city+'&user_id=<?php echo $this->_var['user_id']; ?>&page='+page,function(res){
                $('.sjjzz').hide();
                if(res == false || res == null){
                    $('#weixin_list').append('<li class="sjjzz"><td>数据全部加载完毕!</td></tr>');
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
    <script>
       $(function(){
           var province ='';
           var city='';
           var area='';
            $('#province').click(function(e){
               province=$(e.target).text();
            })
            $('#city').click(function(e){
              city=$(e.target).text();
            })
            $('#area').click(function(e){
                area=$(e.target).text();
                window.location.href='./experience.php?act=default&province='+province+'&area='+area+'&city='+city+'&user_id=<?php echo $this->_var['user_id']; ?>';
             })
       })
    </script>
    <script>
    SlideShow(3000);
    $(function (){
        setTimeout(function() {
            new myAddress("province");
            //创建和初始化地图函数：
            function initMap(){
              createMap();//创建地图
              setMapEvent();//设置地图事件
              addMapControl();//向地图添加控件
              addMapOverlay();//向地图添加覆盖物
              theLocation();//指定地点
            }
            function createMap(){ 
              map = new BMap.Map("map"); 
              map.centerAndZoom(new BMap.Point(<?php echo $this->_var['x']; ?>,<?php echo $this->_var['y']; ?>),13);
            }
            function theLocation(){
                var city ='<?php echo $this->_var['area_name']; ?>';
                if(city != ""){
                    map.centerAndZoom(city,11);      // 用城市名设置地图中心点
                }
            }
            function showLocation(){
                var addr='<?php echo $this->_var['province_name']; ?>'+'<?php echo $this->_var['city_name']; ?>'+'<?php echo $this->_var['area']; ?>';
                if(addr !=''){
                    map.centerAndZoom(addr,12);
                }
            }
            function setMapEvent(){
              map.enableScrollWheelZoom();
              map.enableKeyboard();
              map.enableDragging();
              map.enableDoubleClickZoom()
            }
            function addClickHandler(target,window){
              target.addEventListener("click",function(){
                target.openInfoWindow(window);
              });
            }
            markies=<?php echo $this->_var['makies']; ?>;
            
            function addMapOverlay(){
            
              for(var index = 0; index < markies.length; index++ ){
                var lng =markies[index].long_1;
                var lat =markies[index].lat;
                var title=markies[index].address;
                var content=markies[index].address;
                var point = new BMap.Point(lng,lat);
                var marker = new BMap.Marker(point);
                var label = new BMap.Label(title,{offset: new BMap.Size(25,5)});
                var opts = {
                  width: 200,
                  title:title,
                  enableMessage: false
                };
                var infoWindow = new BMap.InfoWindow(content,opts);
                marker.setLabel(label);
                addClickHandler(marker,infoWindow);
                map.addOverlay(marker);
              };
            }
            //向地图添加控件
            function addMapControl(){
              var scaleControl = new BMap.ScaleControl({anchor:BMAP_ANCHOR_BOTTOM_LEFT});
              scaleControl.setUnit(BMAP_UNIT_IMPERIAL);
              map.addControl(scaleControl);
              var navControl = new BMap.NavigationControl({anchor:BMAP_ANCHOR_TOP_LEFT,type:BMAP_NAVIGATION_CONTROL_LARGE});
              map.addControl(navControl);
            }
            var map;    
            initMap();
        }, 800);
    });
    </script>
    <script>
    $(document).ready(function() {
    $(".imageRotation").each(function(){
        // 获取有关参数
        var imageRotation = this,  // 图片轮换容器
            imageBox = $(imageRotation).children(".imageBox")[0],  // 图片容器
            titleBox = $(imageRotation).children(".titleBox")[0],  // 标题容器
            titleArr = $(titleBox).children(),  // 所有标题（数组）
            icoBox = $(imageRotation).children(".icoBox")[0],  // 图标容器
            icoArr = $(icoBox).children(),  // 所有图标（数组）
            imageWidth = $(imageRotation).width(),  // 图片宽度
            imageNum = $(imageBox).children().size(),  // 图片数量
            imageReelWidth = imageWidth*imageNum,  // 图片容器宽度
            activeID = parseInt($($(icoBox).children(".active")[0]).attr("rel")),  // 当前图片ID
            nextID = 0,  // 下张图片ID
            setIntervalID,  // setInterval() 函数ID
            intervalTime = 4000,  // 间隔时间
            imageSpeed =500,  // 图片动画执行速度
            titleSpeed =250;  // 标题动画执行速度
        // 设置 图片容器 的宽度
        $(imageBox).css({'width' : imageReelWidth + "px"});
        // 图片轮换函数
        var rotate=function(clickID){
            if(clickID){nextID = clickID;}
            else{nextID=activeID<=3 ? activeID+1 : 1;}
            // 交换图标
            $(icoArr[activeID-1]).removeClass("active");
            $(icoArr[nextID-1]).addClass("active");
            // 交换标题
            $(titleArr[activeID-1]).animate(
                {bottom:"-40px"},
                titleSpeed,
                function(){
                    $(titleArr[nextID-1]).animate({bottom:"0px"} , titleSpeed);
                }
            );
            // 交换图片
            $(imageBox).animate({left:"-"+(nextID-1)*imageWidth+"px"} , imageSpeed);
            // 交换IP
            activeID = nextID;
        }
        setIntervalID=setInterval(rotate,intervalTime);
        $(imageBox).hover(
            function(){clearInterval(setIntervalID);},
            function(){setIntervalID=setInterval(rotate,intervalTime);}
        );   
        $(icoArr).click(function(){
            clearInterval(setIntervalID);
            var clickID = parseInt($(this).attr("rel"));
            rotate(clickID);
            setIntervalID=setInterval(rotate,intervalTime);
        });
    });
});
    </script>
</body>
</html>
