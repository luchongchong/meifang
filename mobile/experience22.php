<?php

/**
 * MEIFANG 首页文件
 * ============================================================================ 
 * 版权所有 2005-2014 上海优辉商务，并保留所有权利。
 * 网站地址: http://www.j345.net
 * ----------------------------------------------------------------------------
 * 优辉网络,共创你我
 * ============================================================================
 * $Author: liubo $
 * $Id: index.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
$act = !empty($_REQUEST['act']) ? $_REQUEST['act'] : 'default';

if ($act == 'default')//主页面
{
	
    $ad = adv_one(19);
    $smarty->assign('ad',$ad);
    $sql = "select * from ". $GLOBALS['ecs']->table('store')."order by store_id asc limit 4";
	$re = $GLOBALS['db']->getAll($sql);
	$i=1;

	foreach ($re as $key => $value) {
	    $img_sql="SELECT  store_shop FROM". $GLOBALS['ecs']->table('images_upload')." WHERE user_id=".$value['userid']." and store_shop != ''";
	   
	    if(!empty($GLOBALS['db']->getOne($img_sql))){
	    	$re[$key]['store_img']="../upload/".$GLOBALS['db']->getOne($img_sql);
	    }else{
	    	if(!empty($re[$key]['store_img'])){
	    		$re[$key]['store_img']= '../'.$re[$key]['store_img'];
	    	}
	    }
	   
	    $data=get_address($value['region_1'], $value['region_2'], $value['region_3']);
	    $result_all[$i]=array_merge($re[$key],$data);
	    $i++;
	}
	
	//得到一共有多少店面
	$store_num=$GLOBALS['db']->getOne("select count(*) from ". $GLOBALS['ecs']->table('store'));
	$smarty->assign('store_num',$store_num);
	//根据用户所在位置查找离用户最近的门店
	//通过用户id获取经纬度
	//得到地址
	$smarty->assign('province_list',       get_regions(1,1));
	$province_name=empty($_REQUEST['province'])?'':$_REQUEST['province'];
	//var_dump($province_name);die();
	$smarty->assign('province_name',$province_name);
	$city_name    =empty($_REQUEST['city'])    ?'':$_REQUEST['city'];
	$smarty->assign('city_name',$city_name);
	$area_name    =empty($_REQUEST['area'])    ?'':$_REQUEST['area'];
	$smarty->assign('area_name',$area_name );
	//得到地址id
	$province_id = $GLOBALS['db']->getOne("SELECT `region_id` FROM " .$GLOBALS['ecs']->table('region'). " WHERE region_name='$province_name'");
	$city_id = $GLOBALS['db']->getAll("SELECT `region_id` FROM " .$GLOBALS['ecs']->table('region'). " WHERE region_name='$city_name'");
	if(count($city_id)>1){
	    $city_id=$city_id[1]['region_id'];
	}else{
	    $city_id=$city_id[0]['region_id'];
	}
	$area_id = $GLOBALS['db']->getOne("SELECT `region_id` FROM " .$GLOBALS['ecs']->table('region'). " WHERE region_name='$area_name'");
	$user_id = $_SESSION['user_id'];
	if(!$user_id){
	    $user_id = $_REQUEST['user_id'];
	}
	if($user_id){
    	$sql_user = "SELECT * FROM ".$ecs->table('weixin_user')." WHERE ecuid=".$user_id;
    	$user_info = $GLOBALS['db']->getRow($sql_user);
    	$long = isset($user_info['Longitude'])?$user_info['Longitude']:LONG;
    	$lat = isset($user_info['Latitude'])?$user_info['Latitude']:LAT;
	}else{
	    $long = LONG;
	    $lat  = LAT;
	}
	$smarty->assign('y',$lat);
	$smarty->assign('x',$long);
	require(dirname(__FILE__) . '/includes/cls_earth.php');
	$earth = new earth();
	$page = 1;
	$page_size = 15;
    	//经纬度限制
    	if($long&&$lat||!empty($province_name)||!empty($city_name)||!empty($area_name)){
    	    $res  =  $earth->getMNlnByDis($long,$lat,999000);
    	    if(!empty($province_name)){
    	        $where="region_1=$province_id";
    	        if(!empty($city_name)){
    	            $where.="  and region_2=$city_id ";
    	            if(!empty($area_name)){
    	                $where.=" and region_3=$area_id";
    	            }
    	        }
    	        
    	        $sql = "select store_id  from ".$ecs->table('store')." where ".$where;
    	
    	     
    	    }else{
    	        $where="long_1!='' and lat!='' and long_1>=".$res['minLng']." and long_1<=".$res['maxLng']." and lat>=".$res['minLat']." and lat<=".$res['maxLat'];
    	        $field = "`store_id`,`long_1`,`lat`";
    	        $sql = "select ".$field." from ".$ecs->table('store')." where ".$where;
    	    }
    	    
    	    $allRes = $GLOBALS['db']->getAll($sql);
    	    if(empty($province_name)){
    	    $allRes_a = array();
    	    if($allRes){
    	        foreach($allRes as $v_a){
    	            $distance_a = $earth->getDistance($long,$lat, $v_a['long_1'], $v_a['lat']);
    	            $allRes_a[$v_a['store_id']]=$distance_a;
    	        }
    	    }
    	    //值排序
    	    asort($allRes_a);
    	    //值变键名
    	    $data_res = array_keys($allRes_a);
    	    //数组切割用于分页
    	    $data_res = array_chunk($data_res, $page_size);
    	    //print_r($data_res);
    	
    	    $id_str = implode(',',$data_res[$page-1]);
    	    
    	    $condition = '';
    	    if($id_str){
    	    	$condition = " and store_id in(".$id_str.")";
    	    }
    	   
    	    
    	    $sql_store = "select * from ".$ecs->table('store'). "where shop_no not like '087%' and shop_no not like '127%'".$condition ;
    	    $result = $GLOBALS['db']->getAll($sql_store);
    	   
        	    foreach($result as &$v){
        	        $v['distance'] = $earth->getDistance($long,$lat, $v['long_1'], $v['lat']);
        	        if($v['distance']>1000){
        	            $distance = $v['distance']/1000;
        	            $v['distance_show'] = sprintf("%.2f", $distance).'km';
        	        }else{
        	            $v['distance_show'] = $v['distance'].'m';
        	        }
        	    }
    	    }else{
    	       if($allRes){
        	        //值变键名
        	        for ($i=0;$i<count($allRes);$i++){
        	            $arr[]=$allRes[$i]['store_id'];
        	        }
        	        //$data_res = array_keys($allRes);
        	        //数组切割用于分页
        	        $data_res = array_chunk($arr, $page_size);
        	        //print_r($data_res);
        	        
        	        $id_str = implode(',',$data_res[$page-1]);
        	        $condition = " and store_id in(".$id_str.")";
        	        
        	        $sql_store = "select * from ".$ecs->table('store')." where shop_no not like '087%' and shop_no not like '127%' ".$condition;
                    $result = $GLOBALS['db']->getAll($sql_store);
    	       }else{
    	        $result=$allRes;
    	       }
    	    }
    	    
    	    //数组排序
    	    $sort = array(
    	                    'direction' => 'SORT_ASC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
    	                    'field'     => 'distance', //排序字段
    	    );
    	    
    	    $result = arrayOrder($sort,$result);
    	}
	   for($i=0;$i<count($result);$i++){
	    $lat=$result[$i]['lat'];
	    $long=$result[$i]['long_1'];
	    $q = "http://api.map.baidu.com/ag/coord/convert?from=0&to=4&x=$long&y=$lat";
	    $res = json_decode(file_get_contents($q));
	    $result[$i]['lat']=$res->y;
	    $result[$i]['long_1']=$res->x;
	}
	$smarty->assign('user_id',$user_id);
 	$smarty->assign('result',$result);
 	$smarty->assign('makies', json_encode($result));
 	$smarty->assign('new_stores', $result_all);
 	$smarty->assign('num', count($result_all));
 	//die();
 	$smarty->display('experience.dwt');
}else if($act== 'store_detail'){//店面详情页
    $store_id=isset($_REQUEST['store_id'])?$_REQUEST['store_id']:'1';
    $smarty->assign('store_id',$store_id);
    //查找相关门店
    $sql = "select * from ". $GLOBALS['ecs']->table('store')."WHERE store_id=".$store_id;
    $re = $GLOBALS['db']->getRow($sql);
    //拼接地址
    $data=get_address($re['region_1'], $re['region_2'], $re['region_3']);
	$re=array_merge($re,$data);//var_dump($re);die();
	$re['store_img']=!empty($re['store_img'])?"../".$re['store_img']:'';
	//获取用户评价
	$comment_list=get_comment_list(COMMENT_TYPE_DIANMIAN,$store_id);
	
	//获得评论用户的头像
	for($i=0;$i<count($comment_list['item']);$i++){
	    $comment_list['item'][$i]['user_img']=$GLOBALS['db']->getOne('SELECT headimgurl FROM ecs_weixin_user WHERE ecuid='.$comment_list['item'][$i]['user_id']);
	}
	//获得好评率
	$comment_rank=$GLOBALS['db']->getAll(' SELECT count(*) as num,comment_rank from '.$GLOBALS['ecs']->table('comment').' where id_value='.$store_id.' and comment_type=3 group by comment_rank');
	for($i=0;$i<count($comment_rank);$i++){
	    if($comment_rank[$i]['comment_rank'] == 1){
	        $positive_feedback_num=$comment_rank[$i]['num'];
	    }
	    $num+=$comment_rank[$i]['num'];
	}
	$positive_feedback=intval($positive_feedback_num/$num*100);
	
	$smarty->assign('positive_feedback',$positive_feedback);

	//获得施工队列表
	$construction_team=get_mendian_list();
	$smarty->assign('construction_list',$construction_team);
	//获得店员形象
	$assistant=$GLOBALS['db']->getAll('SELECT * FROM '.$GLOBALS['ecs']->table('shop_assistant').' WHERE user_id='.$re['userid']);
	for($i=0;$i<count($assistant);$i++){
	    $assistant[$i]['shop_assistant']="../upload/".$assistant[$i]['shop_assistant'];
	}
	$smarty->assign('assistant_list',$assistant);
	//获得门店的图片
	$store_img=$GLOBALS['db']->getAll("SELECT  store_shops FROM". $GLOBALS['ecs']->table('images_upload')." WHERE user_id=".$re['userid']);
 	foreach ($store_img as $k=>$img){
 		if(!empty($img['store_shops'])){
 			$store_img[$k]['store_shops'] = "../upload/".$img['store_shops'];
 		}else{
 			unset($store_img[$k]);
 		}
 	}
 	
 	//门店店招
 	$store_shop = $GLOBALS['db']->getOne("SELECT store_shop FROM".$GLOBALS['ecs']->table('images_upload')."WHERE user_id=" .$re['userid']);
 	if(!empty($store_shop)){
 		$store_shop = "../upload/".$store_shop;
 	}
 	$smarty->assign('store_shop',$store_shop);
 	
 	
 	$smarty->assign('store_shops',$store_img);
	//获得专享服务
	$exclusive_list=$_LANG['exclusive_list'];
	$smarty->assign('exclusive_list',$exclusive_list);
	$smarty->assign('comment_list',$comment_list);
	
	$smarty->assign('store_detail', $re);
    $smarty->display('figure.dwt');
}
else if($act == 'store_list'){
	$id = $_GET['id'];
	$str = "";
	if($id){
		$str .= "<table class='bespoke_box_in'><tr><td>选择</td><td>店铺名</td><td>详细地址</td></tr>";
		$sql_store = "select * from ".$ecs->table('store')." where region_3 = ".$id;
		$res = $db->getAll($sql_store);
		if($res){
			foreach($res as $v){
				$str .= "<tr><td><input type='radio' name='store_id' value='".$v['store_id']."'/></td><td>".$v['name']."</td><td>".$v['address']."</td></tr>";
			}
		}else{
				$str .= "<tr><td colspan='3'>该地区没有店铺</td></tr>";
			}
		$str .= "</table>";	
	}
	echo $str;
}
else if($act == 'experience_baidu'){
	//获取用户经纬度
	$user_id = $_SESSION['user_id'];
	if(!$user_id){
		$user_id = $_REQUEST['user_id'];
	}
	$store_id = intval($_REQUEST['store_id']);
	$sql_user = "SELECT * FROM ".$ecs->table('users')." WHERE user_id=".$user_id;
	$user_info = $db->getRow($sql_user);
	$smarty->assign('user_info',$user_info);
	//获取门店经纬度
	$sql_store = "select * from ".$ecs->table('store')." where store_id=".$store_id;
	$store_info = $db->getRow($sql_store);
	$smarty->assign('store_info',$store_info);
	//获取所在城市
	$city_name = "";
	if($store_info['region_2']){
		$sql_city = "select `region_name` from ".$ecs->table('region')." where region_id=".$store_info['region_2'];
		$region_name = $db->getOne($sql_city);
		if($region_name){
			$city_name = $region_name;
		}
	}
	
	$smarty->assign('city_name',$city_name);
	
	$smarty->display('experience_baidu.dwt');
}
else if($act == 'experience_list'){
	//通过用户id获取经纬度
	$user_id = $_SESSION['user_id'];
	if(!$user_id){
		$user_id = $_REQUEST['user_id'];
	}
	$sql_user = "SELECT * FROM ".$ecs->table('users')." WHERE user_id=".$user_id;
	$user_info = $db->getRow($sql_user);
	$long = $user_info['long'];
	$lat = $user_info['lat'];
	
	require(dirname(__FILE__) . '/includes/cls_earth.php');
	$earth = new earth(); 
	$page = 1;
	$page_size = 15;
	//经纬度限制
	if($long&&$lat){	
   		$res  =  $earth->getMNlnByDis($long,$lat,999000);
   		
   		$where="long_1!='' and lat!='' and long_1>=".$res['minLng']." and long_1<=".$res['maxLng']." and lat>=".$res['minLat']." and lat<=".$res['maxLat'];
   		$field = "`store_id`,`long_1`,`lat`";
   		$sql = "select ".$field." from ".$ecs->table('store')." where ".$where;
   		$allRes = $db->getAll($sql);
   		$allRes_a = array();
   		if($allRes){
	   		foreach($allRes as $v_a){
	   			$distance_a = $earth->getDistance($long,$lat, $v_a['long_1'], $v_a['lat']);
	   			$allRes_a[$v_a['store_id']]=$distance_a;
	   		}
   		}
   		//值排序
   		asort($allRes_a);
   		//值变键名
   		$data_res = array_keys($allRes_a);
   		//数组切割用于分页
		$data_res = array_chunk($data_res, $page_size);
   		//print_r($data_res);

   		$id_str = implode(',',$data_res[$page-1]);
   		$condition = " and store_id in(".$id_str.")";
   		$sql_store = "select * from ".$ecs->table('store')." where shop_no not like '087%' and shop_no not like '127%' ".$condition;
   		$result = $db->getAll($sql_store);
   		foreach ($result as $key => $value) {
   		    $province_id = $value['region_1'];
   		    $city_id = $value['region_2'];
   		    $district_id = $value['region_3'];
   		    $province = $db->getOne("SELECT `region_name` FROM " .$ecs->table('region'). " WHERE region_id='$province_id'");
   		    $city = $db->getOne("SELECT `region_name` FROM " .$ecs->table('region'). " WHERE region_id='$city_id'");
   		    $district = $db->getOne("SELECT `region_name` FROM " .$ecs->table('region'). " WHERE region_id='$district_id'");
   		    $result[$key]['province'] = $province;
   		    $result[$key]['city'] = $city;
   		    $result[$key]['district'] = $district;
   		}
   		foreach($result as &$v){
   			$v['distance'] = $earth->getDistance($long,$lat, $v['long_1'], $v['lat']);
			if($v['distance']>1000){
				$distance = $v['distance']/1000;
				$v['distance_show'] = sprintf("%.2f", $distance).'km';					
			}else{
				$v['distance_show'] = $v['distance'].'m';
			}
   		}
   		//数组排序
		$sort = array(  
			        'direction' => 'SORT_ASC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序  
			        'field'     => 'distance', //排序字段  
		);
		$result = arrayOrder($sort,$result);
   		
//   		print_r($result);
	}
	
	//获得最新的私家分店
	$sql = "select * from ". $GLOBALS['ecs']->table('store')."order by store_id asc limit 4";
	$re = $GLOBALS['db']->getAll($sql);
	
	$ad = adv_one(19);
    $smarty->assign('ad',$ad);
    $smarty->assign('user_id',$user_id);
	$smarty->assign('result',$result);
	$smarty->assign('new_stores', $re);
	$smarty->assign('num', count($re));
	$smarty->display('experience.dwt');
}else if($act == 'experience_list_1'){
    
	//通过用户id获取经纬度
	$user_id = $_SESSION['user_id'];
	if(!$user_id){
		$user_id = $_REQUEST['user_id'];
	}
	
	$sql_user = "SELECT * FROM ".$ecs->table('weixin_user')." WHERE ecuid=".$user_id;
	$user_info = $db->getRow($sql_user);
	$long = $user_info['Longitude'];
	$lat = $user_info['Latitude'];
	require(dirname(__FILE__) . '/includes/cls_earth.php');
	$earth = new earth();
	//得到地址
	$province_name=empty($_REQUEST['province'])?'':$_REQUEST['province'];
	$city_name    =empty($_REQUEST['city'])    ?'':$_REQUEST['city'];
	$area_name    =empty($_REQUEST['area'])    ?'':$_REQUEST['area'];
	//得到地址id
	$province_id = $GLOBALS['db']->getOne("SELECT `region_id` FROM " .$GLOBALS['ecs']->table('region'). " WHERE region_name='$province_name'");
	$city_id = $GLOBALS['db']->getOne("SELECT `region_id` FROM " .$GLOBALS['ecs']->table('region'). " WHERE region_name='$city_name'");
	if(count($city_id)>1){
	    $city_id=$city_id[1]['region_id'];
	}else{
	    $city_id=$city_id[0]['region_id'];
	}
	$area_id = $GLOBALS['db']->getOne("SELECT `region_id` FROM " .$GLOBALS['ecs']->table('region'). " WHERE region_name='$area_name'");
	
	
	
	
	$page = intval($_REQUEST['page']);
	$page_size = 15;
	$result = array();
//经纬度限制
	if($long&&$lat||!empty($province_name)||!empty($city_name)||!empty($area_name)){
	    $res  =  $earth->getMNlnByDis($long,$lat,999000);
	    if(!empty($province_name)){
	        $where="region_1=$province_id";
	        if(!empty($city_name)){
	            $where.="  and region_2=$city_id ";
	            if(!empty($area_name)){
	                $where.=" and region_3=$area_id";
	            }
	        }
	        
	        $sql = "select store_id  from ".$ecs->table('store')." where ".$where;
	     
	    }else{
	        $where="long_1!='' and lat!='' and long_1>=".$res['minLng']." and long_1<=".$res['maxLng']." and lat>=".$res['minLat']." and lat<=".$res['maxLat'];
	        $field = "`store_id`,`long_1`,`lat`";
	        $sql = "select ".$field." from ".$ecs->table('store')." where ".$where;
	    }
	    
	    $allRes = $GLOBALS['db']->getAll($sql);
	    if(empty($province_name)){
	    $allRes_a = array();
	    if($allRes){
	        foreach($allRes as $v_a){
	            $distance_a = $earth->getDistance($long,$lat, $v_a['long_1'], $v_a['lat']);
	            $allRes_a[$v_a['store_id']]=$distance_a;
	        }
	    }
	    //值排序
	    asort($allRes_a);
	    //值变键名
	    $data_res = array_keys($allRes_a);
	    //数组切割用于分页
	    $data_res = array_chunk($data_res, $page_size);
	    //print_r($data_res);
	
	    $id_str = implode(',',$data_res[$page-1]);
	    if(!empty($id_str)){
    	    $condition = " and store_id in(".$id_str.")";
    	    $sql_store = "select * from ".$ecs->table('store')." where shop_no not like '087%' and shop_no not like '127%' ".$condition;
    	    $result = $GLOBALS['db']->getAll($sql_store);
    	    
        	    foreach($result as &$v){
        	        $v['distance'] = $earth->getDistance($long,$lat, $v['long_1'], $v['lat']);
        	        if($v['distance']>1000){
        	            $distance = $v['distance']/1000;
        	            $v['distance_show'] = sprintf("%.2f", $distance).'km';
        	        }else{
        	            $v['distance_show'] = $v['distance'].'m';
        	        }
        	    }
	    }else{
	        $result = false;
	    }
	    }else{
	       if($allRes){
    	        //值变键名
    	        for ($i=0;$i<count($allRes);$i++){
    	            $arr[]=$allRes[$i]['store_id'];
    	        }
    	        //$data_res = array_keys($allRes);
    	        //数组切割用于分页
    	        $data_res = array_chunk($arr, $page_size);
    	        //print_r($data_res);
    	        
    	        $id_str = implode(',',$data_res[$page-1]);
    	        if(!empty($id_str)){
        	        $condition = " and store_id in(".$id_str.")";
        	        $sql_store = "select * from ".$ecs->table('store')." where shop_no not like '087%' and shop_no not like '127%' ".$condition;
        	        $result = $GLOBALS['db']->getAll($sql_store);
    	        }else{
    	            $result =false;
    	        }
	       }else{
	        $result=$allRes;
	       }    
	    }
	    
	    //数组排序
	    $sort = array(
	                    'direction' => 'SORT_ASC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
	                    'field'     => 'distance', //排序字段
	    );
	    if(count($result)>0){
	        $result = arrayOrder($sort,$result);
	    }else{
	        $result = false;
	    }
	}
	$str = '';
	if($result){
	    foreach($result as $vv){
	        $str .= "<li><p> <a href='experience.php?act=store_detail&store_id=".$vv['store_id']."'>".$vv['name']."</a></p>"."<p class='txt'>地址：".$vv['city'].$vv['province'].$vv['district'].$vv['address']."</p><p class='txt'>电话：".$vv['tel']."</p></li>";
	    }
	
	}
	echo $str;
	
}elseif ($act == 'comment_list'){
    //获取用户评价
    $store_id=$_REQUEST['store_id'];
    $comment_list=get_comment_list(COMMENT_TYPE_DIANMIAN,$store_id);
    
    if(empty($comment_list)){
        echo false;
        exit();
    }
    //获得评论用户的头像
    for($i=0;$i<count($comment_list['item']);$i++){
        $comment_list['item'][$i]['user_img']=$GLOBALS['db']->getOne('SELECT headimgurl FROM ecs_weixin_user WHERE ecuid='.$comment_list['item'][$i]['user_id']);
    }
    
    foreach($comment_list['item'] as $vv){
        $str .="<dl class='pin1'><dt><img src='".$vv['user_img']."'/><span class='weixin'>".$vv['user_name']."</span>
                </dt><dd class='one' style='margib-left:5%；'><p>".$vv['content']."</p><pclass='time'>".$vv['add_time']."</p>
                </dl>" ;
    }
    echo $str;
}elseif($act =='mendian_list'){
 
    $construction_team=get_mendian_list();
   
    foreach ($construction_team['item'] as $vv){
        $str.="<dl class='pin1'><dd class='one' style='color:#666;padding:2%;'>".$vv['construction_name']."&nbsp;&nbsp;&nbsp;<span style='font-size:0.5em;'>好评率：".$vv['positive']."%</span>";
            if($vv['positive']>='95'){
                $str.="<img src='themesmobile/mobile/img/2_03.png'>
                    <img src='themesmobile/mobile/img/2_03.png>
                    <img src='themesmobile/mobile/img/2_03.png'>
                    <img src='themesmobile/mobile/img/2_03.png'>
                    <img src='themesmobile/mobile/img/2_03.png'>";
                
            }elseif($vv['positive']>='85'){
                $str.="<img src='themesmobile/mobile/img/2_03.png'>
                <img src='themesmobile/mobile/img/2_03.png'>
                <img src='themesmobile/mobile/img/2_03.png'>
                <img src='themesmobile/mobile/img/2_03.png'>
                <img src='themesmobile/mobile/img/2_05.png'>";
            }elseif ($vv['positive']>='75'){
                $str.="<img src='themesmobile/mobile/img/2_03.png'>
                <img src='themesmobile/mobile/img/2_03.png'>
                <img src='themesmobile/mobile/img/2_03.png'>
                <img src='themesmobile/mobile/img/2_05.png'>
                <img src='themesmobile/mobile/img/2_05.png'>";
            }elseif($vv['positive']>='60'){
                $str.="<img src='themesmobile/mobile/img/2_03.png'>
                <img src='themesmobile/mobile/img/2_03.png'>
                <img src='themesmobile/mobile/img/2_05.png'>
                <img src='themesmobile/mobile/img/2_05.png'>
                <img src='themesmobile/mobile/img/2_05.png'>";
            }elseif ($vv['positive']<'60'){
                $str.="<img src='themesmobile/mobile/img/2_03.png'>
                <img src='themesmobile/mobile/img/2_05.png'>
                <img src='themesmobile/mobile/img/2_05.png'>
                <img src='themesmobile/mobile/img/2_05.png'>
                <img src='themesmobile/mobile/img/2_05.png'>";
            }
            $str.='</dd></dl>';
    }
    echo $str;
}

	//$sort 排序方法
	//$oldArray 排序数组
	function arrayOrder($sort='',$oldArray=array()){
		if(!$sort){
			$sort = array(  
			        'direction' => 'SORT_ASC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序  
			        'field'     => 'distance', //排序字段  
			);
		}
		  
		$arrSort = array();  
		foreach($oldArray AS $uniqid => $row){  
		    foreach($row AS $key=>$value){  
		        $arrSort[$key][$uniqid] = $value;  
		    }  
		} 
		 
		if($sort['direction']){  
		    array_multisort($arrSort[$sort['field']], constant($sort['direction']), $oldArray);  
		}
		
		return $oldArray;
		
	}

function adv_one($id){	
	$sql = "select * from ". $GLOBALS['ecs']->table('ad') ." where position_id = ".$id." and start_time<=".time()." and end_time>=".time();
	$re = $GLOBALS['db']->getRow($sql);
	return $re;
}
//获取地址
function get_address($province_id,$city_id,$district_id){
    $province = $GLOBALS['db']->getOne("SELECT `region_name` FROM " .$GLOBALS['ecs']->table('region'). " WHERE region_id='$province_id'");
    $city = $GLOBALS['db']->getOne("SELECT `region_name` FROM " .$GLOBALS['ecs']->table('region'). " WHERE region_id='$city_id'");
    $district = $GLOBALS['db']->getOne("SELECT `region_name` FROM " .$GLOBALS['ecs']->table('region'). " WHERE region_id='$district_id'");
   
    $data=array(
             'province' => $province,
             'city'     => $city,
             'district' => $district
              );
    return $data;
}

/**
 * 获取评论列表
 * @access  public
 * @return  array
 */
function get_comment_list($comment_type,$id_value)
{
    
    /* 查询条件 */
    $filter['keywords']     = empty($_REQUEST['keywords']) ? 0 : trim($_REQUEST['keywords']);
    if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
    {
        $filter['keywords'] = json_str_iconv($filter['keywords']);
    }
    $filter['sort_by']      = empty($_REQUEST['sort_by']) ? 'add_time' : trim($_REQUEST['sort_by']);
    $filter['sort_order']   = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
    
    $where = (!empty($filter['keywords'])) ? " AND content LIKE '%" . mysql_like_quote($filter['keywords']) . "%' " : '';
    $sql = "SELECT count(*) FROM " .$GLOBALS['ecs']->table('comment'). " WHERE comment_type=$comment_type and id_value=$id_value $where";
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);
   
//     /* 分页大小 */
    $filter = page_and_size($filter);
    if($filter['page_count']<$_REQUEST['page']){
        return false;
    }
    /* 获取评论数据 */
    $arr = array();
    $sql  = "SELECT * FROM " .$GLOBALS['ecs']->table('comment'). " WHERE comment_type=$comment_type and id_value=$id_value". $where  .
    " ORDER BY $filter[sort_by] $filter[sort_order] ".
    " LIMIT ". $filter['start'] .", $filter[page_size]";
    $res  = $GLOBALS['db']->query($sql);
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $sql = ($row['comment_type'] == 0) ?
        "SELECT goods_name FROM " .$GLOBALS['ecs']->table('goods'). " WHERE goods_id='$row[id_value]'" :
        "SELECT title FROM ".$GLOBALS['ecs']->table('article'). " WHERE article_id='$row[id_value]'";
        $row['title'] = $GLOBALS['db']->getOne($sql);

        $row['add_time'] = local_date($GLOBALS['_CFG']['time_format'], $row['add_time']);

        $arr[] = $row;
    }
    $filter['keywords'] = stripslashes($filter['keywords']);
    $arr = array('item' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}
function get_mendian_list(){
   
    /* 查询条件 */
    $filter['keywords']     = empty($_REQUEST['keywords']) ? 0 : trim($_REQUEST['keywords']);
    if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
    {
        $filter['keywords'] = json_str_iconv($filter['keywords']);
    }
    $filter['sort_by']      = empty($_REQUEST['sort_by']) ? 'add_time' : trim($_REQUEST['sort_by']);
    $filter['sort_order']   = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
    
    $where = (!empty($filter['keywords'])) ? " AND content LIKE '%" . mysql_like_quote($filter['keywords']) . "%' " : '';
    $sql = "SELECT count(*) FROM " .$GLOBALS['ecs']->table('comment'). " user_construction_team $where";
  
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);
 
    //     /* 分页大小 */
    $filter = page_and_size($filter);
    
   
    if($filter['page_count']<$_REQUEST['page']){
        return false;
    }
    /* 获取评论数据 */
    $arr = array();
    $sql  = "SELECT * FROM  user_construction_team". $where .
    " LIMIT ". $filter['start'] .", $filter[page_size]";
    $res  = $GLOBALS['db']->query($sql);
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        
         //获得好评率
        $comment_rank=$GLOBALS['db']->getAll(' SELECT count(*) as num,comment_rank from '.$GLOBALS['ecs']->table('comment').' where id_value='.$row['construction_id'].' and comment_type=3 group by comment_rank');
        for($i=0;$i<count($comment_rank);$i++){
            if($comment_rank[$i]['comment_rank'] == 1){
                $positive_feedback_num=$comment_rank[$i]['num'];
            }
            $num+=$comment_rank[$i]['num'];
        }
        
        $row['positive']=intval($positive_feedback_num/$num*100);
        
        $arr[] = $row;
    }
    $filter['keywords'] = stripslashes($filter['keywords']);
    $arr = array('item' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}
/**
 * 分页的信息加入条件的数组
 *
 * @access  public
 * @return  array
 */
function page_and_size($filter)
{
    if (isset($_REQUEST['page_size']) && intval($_REQUEST['page_size']) > 0)
    {
        $filter['page_size'] = intval($_REQUEST['page_size']);
    }
    elseif (isset($_COOKIE['ECSCP']['page_size']) && intval($_COOKIE['ECSCP']['page_size']) > 0)
    {
        $filter['page_size'] = intval($_COOKIE['ECSCP']['page_size']);
    }
    else
    {
        $filter['page_size'] = 10;
    }

    /* 每页显示 */
    $filter['page'] = (empty($_REQUEST['page']) || intval($_REQUEST['page']) <= 0) ? 1 : intval($_REQUEST['page']);

    /* page 总数 */
    $filter['page_count'] = (!empty($filter['record_count']) && $filter['record_count'] > 0) ? ceil($filter['record_count'] / $filter['page_size']) : 1;

    /* 边界处理 */
    if ($filter['page'] > $filter['page_count'])
    {
        $filter['page'] = $filter['page_count'];
    }

    $filter['start'] = ($filter['page'] - 1) * $filter['page_size'];

    return $filter;
}
?>