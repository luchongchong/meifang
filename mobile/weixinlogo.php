<?php

/**
 * MEIFANG 会员中心
 * ============================================================================ 
 * 版权所有 2005-2014 上海优辉商务，并保留所有权利。
 * 网站地址: http://www.j345.net
 * ----------------------------------------------------------------------------
 * 优辉网络,共创你我
 * ============================================================================
 * $Author: liubo $
 * $Id: user.php 17217 2011-01-19 06:29:08Z liubo $
*/

session_start();
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');

$_SESSION['user_id'] = 0;

	function getInfo($url){
        $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		return curl_exec($ch);
	}
	
	function object_array($array)
	{
  		 if(is_object($array))
   		{
   	 	$array = (array)$array;
   		}
   		if(is_array($array))
   		{
    		foreach($array as $key=>$value)
    		{
     			$array[$key] = object_array($value);
    		}
   		}
   		return $array;
	}
	if(@$_GET['code']){	
		
	   	$ret_info = $db -> getRow("SELECT * FROM " . $GLOBALS['ecs']->table('weixin_config') . " WHERE `id` = 1");		
		//获取openid
		$code = $_GET['code'];
		$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$appid."&secret=".$appsecret."&code=".$code."&grant_type=authorization_code";
		$re = getInfo($url);
		$re = json_decode($re);	
		//$_SESSION['test_session'] = $re;			
		$re = object_array($re);
		$wechaId = $re['openid'];
		if(!$wechaId){	
			//获取openid
			//$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$ret_info['appid']."&secret=".$ret_info['appsecret']."&code=".$code."&grant_type=authorization_code";
			$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$ret_info['appid']."&secret=".$ret_info['appsecret']."&code=".$code."&grant_type=authorization_code";
			$re = getInfo($url);
			$re = json_decode($re);
			$re = object_array($re);
			$wechaId = $re['openid'];
		}
		
		//获得用户的id
		$sql_weixin='SELECT * FROM'.$ecs->table('weixin_user').'WHERE fake_id="'.$wechaId.'" and fake_id!=""';			
		$weixin_data=$db->getRow($sql_weixin);
                if(!$weixin_data){
		    $_SESSION['user_id'] = 0;
		    ecs_header("Location:user.php\n");
		    exit();
		}
		//登录用
		//$sql_weixin = "SELECT * FROM ".$ecs->table('weixin_user')." WHERE wxid='$wechaId'";	
		//$data_weixin = $db->getRow($sql_weixin);
		$sql_user = "SELECT * FROM ".$ecs->table('users')." WHERE user_id = '".$weixin_data['ecuid']."'";
		$data_user = $db->getRow($sql_user);
		if(!$data_user){
			$_SESSION['user_id'] = 0;
			ecs_header("Location:user.php\n");
			exit();
		}
		
		$_SESSION['openid_self'] = $wechaId;
		$_SESSION['user_id'] = $data_user['user_id'];
		$_SESSION['user_name'] = $data_user['user_name'];
		$_SESSION['user_rank'] = $data_user['user_rank'];
		update_user_info();
		$state = $_GET['state'];
	//	var_dump(is_string($state));die;
		if($state == 1){//首页
			ecs_header("Location:index.php\n");
			exit();
		}elseif($state == 2){//会员中心
			ecs_header("Location:user.php\n");
			exit();
		}elseif($state == 3){//微信官网
			ecs_header("Location:weigw.php\n");
			exit();
		}elseif($state == 4){//样本系列
		    
			ecs_header("Location:sample.php\n");
			exit();
		}elseif($state == 5){//美美连锁店
			ecs_header("Location:experience.php\n");
			exit();
		}elseif($state == 6){//附近体验店2
			ecs_header("Location:experience.php?act=default&user_id=".$data_user['user_id']."\n");
			exit();
		}elseif ($state==7){
		    ecs_header("Location:points.php\n");
		    exit();
		}elseif (is_string($state)){
		    ecs_header("Location:goods.php?id=$state\n");
		    exit();
		}else{
			ecs_header("Location:index.php\n");
			exit;
		}
	}else{
		
		ecs_header("Location:index.php\n");
		exit;
	}

?>
