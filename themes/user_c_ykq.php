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
 * $Id: user_c.php 17217 2011-01-19 06:29:08Z liubo $
 */

set_time_limit(0);
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/* 载入语言文件 */
require_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/user_c.php');
$smarty->assign('lang',          $_LANG);
$smarty->assign('action',          $_GET['act']);
$user_id = $_SESSION['user_id'];
$action  = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : 'default';

$affiliate = unserialize($GLOBALS['_CFG']['affiliate']);
$smarty->assign('affiliate', $affiliate);
$back_act='';

include_once(ROOT_PATH .'includes/lib_clips.php');
$user_info_c = get_user_default($user_id);
$smarty->assign('user_info_c',        $user_info_c);


if ($rank = get_rank_info())
{
	$smarty->assign('rank_name', $rank['rank_name']);
}


// 不需要登录的操作或自己验证是否登录（如ajax处理）的act
$not_login_arr =
array('login','act_login','register','act_register','act_edit_password','get_password','send_pwd_email','password', 'signin', 'add_tag', 'collect', 'return_to_cart', 'logout', 'email_list', 'validate_email', 'send_hash_mail', 'order_query', 'is_registered', 'check_email','clear_history','qpassword_name', 'get_passwd_question', 'check_answer', 'oath' , 'oath_login', 'other_login');

/* 显示页面的action列表 */
$ui_arr = array('register', 'login', 'profile', 'order_list', 'order_detail_c', 'address_list', 'collection_list',
'message_list', 'tag_list', 'get_password', 'reset_password', 'booking_list', 'add_booking', 'account_raply_c',
'account_deposit', 'account_log_c', 'account_detail', 'act_account', 'pay', 'default', 'bonus', 'group_buy', 'group_buy_detail', 'affiliate', 'comment_list','validate_email','track_packages', 'transform_points','qpassword_name', 'get_passwd_question', 'check_answer' , 'face','weixin_fans','order_list_c','gain_rebate','download_file');

//print_r($_SESSION);

if($_SESSION['user_rank']!=102&&$_SESSION['user_rank']!=103){
	$_SESSION['user_id']=0;
}

/* 未登录处理 */
if (empty($_SESSION['user_id']))
{
	if (!in_array($action, $not_login_arr))
	{
		if (in_array($action, $ui_arr))
		{
			/* 如果需要登录,并是显示页面的操作，记录当前操作，用于登录后跳转到相应操作
			 if ($action == 'login')
			 {
			 if (isset($_REQUEST['back_act']))
			 {
			 $back_act = trim($_REQUEST['back_act']);
			 }
			 }
			 else
			 {}*/
			if (!empty($_SERVER['QUERY_STRING']))
			{
				$back_act = 'user_c.php?' . strip_tags($_SERVER['QUERY_STRING']);
			}
			$action = 'login';
		}
		else
		{
			//未登录提交数据。非正常途径提交数据！
			die($_LANG['require_login']);
		}
	}
}



/* 如果是显示页面，对页面进行相应赋值 */
if (in_array($action, $ui_arr))
{
	assign_template();
	$position = assign_ur_here(0, $_LANG['user_center']);
	$smarty->assign('page_title', $position['title']); // 页面标题
	$smarty->assign('categories_pro',  get_categories_tree_pro()); // 分类树加强版
	$smarty->assign('ur_here',    $position['ur_here']);
	$sql = "SELECT value FROM " . $ecs->table('shop_config') . " WHERE id = 419";
	$row = $db->getRow($sql);
	$car_off = $row['value'];
	$smarty->assign('car_off',       $car_off);
	/* 是否显示积分兑换 */
	if (!empty($_CFG['points_rule']) && unserialize($_CFG['points_rule']))
	{
		$smarty->assign('show_transform_points',     1);
	}
	$smarty->assign('helps',      get_shop_help());        // 网店帮助
	$smarty->assign('data_dir',   DATA_DIR);   // 数据目录
	$smarty->assign('action',     $action);
	$smarty->assign('lang',       $_LANG);
}

//用户中心欢迎页
if ($action == 'default')
{

	include_once(ROOT_PATH .'includes/lib_clips.php');
	include_once(ROOT_PATH .'includes/lib_transaction.php');



	//获取微信二维码
	$sql_qr = "SELECT `ticket` FROM `wxch_qr` WHERE `type` = 'tj' and `affiliate` = ".$_SESSION['user_id'];
	$ticket = $db->getOne($sql_qr);
	$qr_url = "http://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".$ticket;
	$smarty->assign('qr_url', $qr_url);
	$userinfo  = get_consignee_list($user_id);
	$smarty->assign('userinfo',    $userinfo);
	$smarty->assign('info',        get_user_default($user_id));
	$smarty->assign('user_notice', $_CFG['user_notice']);
	$smarty->assign('prompt',      get_user_prompt($user_id));
	$smarty->display('user_clips_c.dwt');

}

/* 显示会员注册界面 */
if ($action == 'register')
{
	if ((!isset($back_act)||empty($back_act)) && isset($GLOBALS['_SERVER']['HTTP_REFERER']))
	{
		$back_act = strpos($GLOBALS['_SERVER']['HTTP_REFERER'], 'user_c.php') ? './index.php' : $GLOBALS['_SERVER']['HTTP_REFERER'];
	}

	/* 取出注册扩展字段 */
	$sql = 'SELECT * FROM ' . $ecs->table('reg_fields') . ' WHERE type < 2 AND display = 1 ORDER BY dis_order, id';
	$extend_info_list = $db->getAll($sql);
	$smarty->assign('extend_info_list', $extend_info_list);

	/* 验证码相关设置 */
	if ((intval($_CFG['captcha']) & CAPTCHA_REGISTER) && gd_version() > 0)
	{
		$smarty->assign('enabled_captcha', 1);
		$smarty->assign('rand',            mt_rand());
	}

	/* 密码提示问题 */
	$smarty->assign('passwd_questions', $_LANG['passwd_questions']);

	/* 增加是否关闭注册 */
	$smarty->assign('shop_reg_closed', $_CFG['shop_reg_closed']);
	//    $smarty->assign('back_act', $back_act);
	$smarty->display('user_passport.dwt');
}

/* 注册会员的处理 */
elseif ($action == 'act_register')
{
	/* 增加是否关闭注册 */
	if ($_CFG['shop_reg_closed'])
	{
		$smarty->assign('action',     'register');
		$smarty->assign('shop_reg_closed', $_CFG['shop_reg_closed']);
		$smarty->display('user_passport.dwt');
	}
	else
	{
		include_once(ROOT_PATH . 'includes/lib_passport.php');

		$username = isset($_POST['username']) ? trim($_POST['username']) : '';
		$password = isset($_POST['password']) ? trim($_POST['password']) : '';
		$email    = isset($_POST['email']) ? trim($_POST['email']) : '';
		$other['msn'] = isset($_POST['extend_field1']) ? $_POST['extend_field1'] : '';
		$other['qq'] = isset($_POST['extend_field2']) ? $_POST['extend_field2'] : '';
		$other['office_phone'] = isset($_POST['extend_field3']) ? $_POST['extend_field3'] : '';
		$other['home_phone'] = isset($_POST['extend_field4']) ? $_POST['extend_field4'] : '';
		$other['mobile_phone'] = isset($_POST['extend_field5']) ? $_POST['extend_field5'] : '';
		$sel_question = empty($_POST['sel_question']) ? '' : compile_str($_POST['sel_question']);
		$passwd_answer = isset($_POST['passwd_answer']) ? compile_str(trim($_POST['passwd_answer'])) : '';


		$back_act = isset($_POST['back_act']) ? trim($_POST['back_act']) : '';

		if(empty($_POST['agreement']))
		{
			show_message_c($_LANG['passport_js']['agreement']);
		}
		if (strlen($username) < 3)
		{
			show_message_c($_LANG['passport_js']['username_shorter']);
		}

		if (strlen($password) < 6)
		{
			show_message_c($_LANG['passport_js']['password_shorter']);
		}

		if (strpos($password, ' ') > 0)
		{
			show_message_c($_LANG['passwd_balnk']);
		}

		/* 验证码检查 */
		if ((intval($_CFG['captcha']) & CAPTCHA_REGISTER) && gd_version() > 0)
		{
			if (empty($_POST['captcha']))
			{
				show_message_c($_LANG['invalid_captcha'], $_LANG['sign_up'], 'user_c.php?act=register', 'error');
			}

			/* 检查验证码 */
			include_once('includes/cls_captcha.php');

			$validator = new captcha();
			if (!$validator->check_word($_POST['captcha']))
			{
				show_message_c($_LANG['invalid_captcha'], $_LANG['sign_up'], 'user_c.php?act=register', 'error');
			}
		}

		if (register($username, $password, $email, $other) !== false)
		{
			/*把新注册用户的扩展信息插入数据库*/
			$sql = 'SELECT id FROM ' . $ecs->table('reg_fields') . ' WHERE type = 0 AND display = 1 ORDER BY dis_order, id';   //读出所有自定义扩展字段的id
			$fields_arr = $db->getAll($sql);

			$extend_field_str = '';    //生成扩展字段的内容字符串
			foreach ($fields_arr AS $val)
			{
				$extend_field_index = 'extend_field' . $val['id'];
				if(!empty($_POST[$extend_field_index]))
				{
					$temp_field_content = strlen($_POST[$extend_field_index]) > 100 ? mb_substr($_POST[$extend_field_index], 0, 99) : $_POST[$extend_field_index];
					$extend_field_str .= " ('" . $_SESSION['user_id'] . "', '" . $val['id'] . "', '" . compile_str($temp_field_content) . "'),";
				}
			}
			$extend_field_str = substr($extend_field_str, 0, -1);

			if ($extend_field_str)      //插入注册扩展数据
			{
				$sql = 'INSERT INTO '. $ecs->table('reg_extend_info') . ' (`user_id`, `reg_field_id`, `content`) VALUES' . $extend_field_str;
				$db->query($sql);
			}

			/* 写入密码提示问题和答案 */
			if (!empty($passwd_answer) && !empty($sel_question))
			{
				$sql = 'UPDATE ' . $ecs->table('users') . " SET `passwd_question`='$sel_question', `passwd_answer`='$passwd_answer'  WHERE `user_id`='" . $_SESSION['user_id'] . "'";
				$db->query($sql);
			}
			/* 判断是否需要自动发送注册邮件 */
			if ($GLOBALS['_CFG']['member_email_validate'] && $GLOBALS['_CFG']['send_verify_email'])
			{
				send_regiter_hash($_SESSION['user_id']);
			}
			$ucdata = empty($user->ucdata)? "" : $user->ucdata;
			show_message_c(sprintf($_LANG['register_success'], $username . $ucdata), array($_LANG['back_up_page'], $_LANG['profile_lnk']), array($back_act, 'user_c.php'), 'info');
		}
		else
		{
			$err->show($_LANG['sign_up'], 'user_c.php?act=register');
		}
	}
}

//  第三方登录接口
elseif($action == 'oath')
{
	$type = empty($_REQUEST['type']) ?  '' : $_REQUEST['type'];

	if($type == "taobao"){
		header("location:includes/website/tb_index.php");exit;
	}

	include_once(ROOT_PATH . 'includes/website/jntoo.php');

	$c = &website($type);
	if($c)
	{
		if (empty($_REQUEST['callblock']))
		{
			if (empty($_REQUEST['callblock']) && isset($GLOBALS['_SERVER']['HTTP_REFERER']))
			{
				$back_act = strpos($GLOBALS['_SERVER']['HTTP_REFERER'], 'user_c.php') ? 'index.php' : $GLOBALS['_SERVER']['HTTP_REFERER'];
			}
			else
			{
				$back_act = 'index.php';
			}
		}
		else
		{
			$back_act = trim($_REQUEST['callblock']);
		}

		if($back_act[4] != ':') $back_act = $ecs->url().$back_act;
		$open = empty($_REQUEST['open']) ? 0 : intval($_REQUEST['open']);

		$url = $c->login($ecs->url().'user_c.php?act=oath_login&type='.$type.'&callblock='.urlencode($back_act).'&open='.$open);
		if(!$url)
		{
			show_message_c( $c->get_error() , '首页', $ecs->url() , 'error');
		}
		header('Location: '.$url);
	}
	else
	{
		show_message_c('服务器尚未注册该插件！' , '首页',$ecs->url() , 'error');
	}
}



//  处理第三方登录接口
elseif($action == 'oath_login')
{
	$type = empty($_REQUEST['type']) ?  '' : $_REQUEST['type'];

	include_once(ROOT_PATH . 'includes/website/jntoo.php');
	$c = &website($type);
	if($c)
	{
		$access = $c->getAccessToken();
		if(!$access)
		{
			show_message_c( $c->get_error() , '首页', $ecs->url() , 'error');
		}
		$c->setAccessToken($access);
		$info = $c->getMessage();
		if(!$info)
		{
			show_message_c($c->get_error() , '首页' , $ecs->url() , 'error' , false);
		}
		if(!$info['user_id'])
		show_message_c($c->get_error() , '首页' , $ecs->url() , 'error' , false);


		$info_user_id = $type .'_'.$info['user_id']; //  加个标识！！！防止 其他的标识 一样  // 以后的ID 标识 将以这种形式 辨认
		$info['name'] = str_replace("'" , "" , $info['name']); // 过滤掉 逗号 不然出错  很难处理   不想去  搞什么编码的了
		if(!$info['user_id'])
		show_message_c($c->get_error() , '首页' , $ecs->url() , 'error' , false);


		$sql = 'SELECT user_name,password,aite_id FROM '.$ecs->table('users').' WHERE aite_id = \''.$info_user_id.'\' OR aite_id=\''.$info['user_id'].'\'';

		$count = $db->getRow($sql);
		if(!$count)   // 没有当前数据
		{
			if($user->check_user($info['name']))  // 重名处理
			{
				$info['name'] = $info['name'].'_'.$type.(rand(10000,99999));
			}
			$user_pass = $user->compile_password(array('password'=>$info['user_id']));
			$sql = 'INSERT INTO '.$ecs->table('users').'(user_name , password, aite_id , sex , reg_time , user_rank , is_validated) VALUES '.
					"('$info[name]' , '$user_pass' , '$info_user_id' , '$info[sex]' , '".gmtime()."' , '$info[rank_id]' , '1')" ;
			$db->query($sql);
		}
		else
		{
			$sql = '';
			if($count['aite_id'] == $info['user_id'])
			{
				$sql = 'UPDATE '.$ecs->table('users')." SET aite_id = '$info_user_id' WHERE aite_id = '$count[aite_id]'";
				$db->query($sql);
			}
			if($info['name'] != $count['user_name'])   // 这段可删除
			{
				if($user->check_user($info['name']))  // 重名处理
				{
					$info['name'] = $info['name'].'_'.$type.(rand()*1000);
				}
				$sql = 'UPDATE '.$ecs->table('users')." SET user_name = '$info[name]' WHERE aite_id = '$info_user_id'";
				$db->query($sql);
			}
		}
		$user->set_session($info['name']);
		$user->set_cookie($info['name']);
		update_user_info();
		recalculate_price();

		if(!empty($_REQUEST['open']))
		{
			die('<script>window.opener.window.location.reload(); window.close();</script>');
		}
		else
		{
			ecs_header('Location: '.$_REQUEST['callblock']);

		}

	}

}

//  处理其它登录接口
elseif($action == 'other_login')
{
	$type = empty($_REQUEST['type']) ?  '' : $_REQUEST['type'];
	session_start();
	$info = $_SESSION['user_info'];

	if(empty($info)){
		show_message_c("非法访问或请求超时！" , '首页' , $ecs->url() , 'error' , false);
	}
	if(!$info['user_id'])
	show_message_c("非法访问或访问出错，请联系管理员！", '首页' , $ecs->url() , 'error' , false);


	$info_user_id = $type .'_'.$info['user_id']; //  加个标识！！！防止 其他的标识 一样  // 以后的ID 标识 将以这种形式 辨认
	$info['name'] = str_replace("'" , "" , $info['name']); // 过滤掉 逗号 不然出错  很难处理   不想去  搞什么编码的了


	$sql = 'SELECT user_name,password,aite_id FROM '.$ecs->table('users').' WHERE aite_id = \''.$info_user_id.'\' OR aite_id=\''.$info['user_id'].'\'';

	$count = $db->getRow($sql);
	$login_name = $info['name'];
	if(!$count)   // 没有当前数据
	{
		if($user->check_user($info['name']))  // 重名处理
		{
			$info['name'] = $info['name'].'_'.$type.(rand()*1000);
		}
		$login_name = $info['name'];
		$user_pass = $user->compile_password(array('password'=>$info['user_id']));
		$sql = 'INSERT INTO '.$ecs->table('users').'(user_name , password, aite_id , sex , reg_time , user_rank , is_validated) VALUES '.
				"('$info[name]' , '$user_pass' , '$info_user_id' , '$info[sex]' , '".gmtime()."' , '$info[rank_id]' , '1')" ;
		$db->query($sql);
	}
	else
	{
		$login_name = $count['user_name'];
		$sql = '';
		if($count['aite_id'] == $info['user_id'])
		{
			$sql = 'UPDATE '.$ecs->table('users')." SET aite_id = '$info_user_id' WHERE aite_id = '$count[aite_id]'";
			$db->query($sql);
		}
	}



	$user->set_session($login_name);
	$user->set_cookie($login_name);
	update_user_info();
	recalculate_price();

	$redirect_url =  "http://".$_SERVER["HTTP_HOST"].str_replace("user_c.php", "index.php", $_SERVER["REQUEST_URI"]);
	header('Location: '.$redirect_url);

}

/* 验证用户注册邮件 */

elseif ($action == 'validate_email')
{
	$hash = empty($_GET['hash']) ? '' : trim($_GET['hash']);
	if ($hash)
	{
		include_once(ROOT_PATH . 'includes/lib_passport.php');
		$id = register_hash('decode', $hash);
		if ($id > 0)
		{
			$sql = "UPDATE " . $ecs->table('users') . " SET is_validated = 1 WHERE user_id='$id'";
			$db->query($sql);
			$sql = 'SELECT user_name, email FROM ' . $ecs->table('users') . " WHERE user_id = '$id'";
			$row = $db->getRow($sql);
			show_message_c(sprintf($_LANG['validate_ok'], $row['user_name'], $row['email']),$_LANG['profile_lnk'], 'user_c.php');
		}
	}
	show_message_c($_LANG['validate_fail']);
}

/* 验证用户注册用户名是否可以注册 */
elseif ($action == 'is_registered')
{
	include_once(ROOT_PATH . 'includes/lib_passport.php');

	$username = trim($_GET['username']);
	$username = json_str_iconv($username);

	if ($user->check_user($username) || admin_registered($username))
	{
		echo 'false';
	}
	else
	{
		echo 'true';
	}
}

/* 验证用户邮箱地址是否被注册 */
elseif($action == 'check_email')
{
	$email = trim($_GET['email']);
	if ($user->check_email($email))
	{
		echo 'false';
	}
	else
	{
		echo 'ok';
	}
}
/* 用户登录界面 */
elseif ($action == 'login')
{
	if (empty($back_act))
	{
		if (empty($back_act) && isset($GLOBALS['_SERVER']['HTTP_REFERER']))
		{
			$back_act = strpos($GLOBALS['_SERVER']['HTTP_REFERER'], 'user_c.php') ? './index.php' : $GLOBALS['_SERVER']['HTTP_REFERER'];
		}
		else
		{
			$back_act = 'user_c.php';
		}

	}


	$captcha = intval($_CFG['captcha']);
	if (($captcha & CAPTCHA_LOGIN) && (!($captcha & CAPTCHA_LOGIN_FAIL) || (($captcha & CAPTCHA_LOGIN_FAIL) && $_SESSION['login_fail'] > 2)) && gd_version() > 0)
	{
		$GLOBALS['smarty']->assign('enabled_captcha', 1);
		$GLOBALS['smarty']->assign('rand', mt_rand());
	}

	$smarty->assign('back_act', $back_act);
	$smarty->display('user_passport_c.dwt');
}



/* 处理会员的登录 */
elseif ($action == 'act_login')
{
	$username = isset($_POST['username']) ? trim($_POST['username']) : '';
	$password = isset($_POST['password']) ? trim($_POST['password']) : '';
	$back_act = isset($_POST['back_act']) ? trim($_POST['back_act']) : '';


// 	$captcha = intval($_CFG['captcha']);
// 	if (($captcha & CAPTCHA_LOGIN) && (!($captcha & CAPTCHA_LOGIN_FAIL) || (($captcha & CAPTCHA_LOGIN_FAIL) && $_SESSION['login_fail'] > 2)) && gd_version() > 0)
// 	{
// 		if (empty($_POST['captcha']))
// 		{
// 			show_message_c($_LANG['invalid_captcha'], $_LANG['relogin_lnk'], 'user_c.php', 'error');
// 		}

// 		/* 检查验证码 */
// 		include_once('includes/cls_captcha.php');

// 		$validator = new captcha();
// 		$validator->session_word = 'captcha_login';
// 		if (!$validator->check_word($_POST['captcha']))
// 		{
// 			show_message_c($_LANG['invalid_captcha'], $_LANG['relogin_lnk'], 'user_c.php', 'error');
// 		}
// 	}

	//是否为加盟商
	$sql_zj1 = "select * from ".$ecs->table('users')." where user_name = '".$username."'";
	$re = $db->getRow($sql_zj1);
	if($re['ec_salt']){

		$password_zj2 = md5(md5($password).$re['ec_salt']);
	}else{

		$password_zj2 = md5($password);
	}
	$sql_zj2 = "select * from ".$ecs->table('users')." where password = '".$password_zj2."' and user_name = '".$username."'";
	$re1 = $db->getRow($sql_zj2);

// 	/* print_r($sql_zj2);
// 		print_r($re1);
// 		exit(); */

	if(!($re1['user_rank'] == 102 || $re1['user_rank'] == 103)){
		$_SESSION['login_fail'] ++ ;
		show_message_c($_LANG['login_failure_1'], $_LANG['relogin_lnk'], 'user_c.php', 'error');
	}


	if ($user->login($username, $password,isset($_POST['remember'])))
	{
		update_user_info();
		recalculate_price();

		$ucdata = isset($user->ucdata)? $user->ucdata : '';
		show_message_c($_LANG['login_success'] . $ucdata , $_LANG['back_up_page'], 'user_c.php', 'info');
	}
	else
	{
		$_SESSION['login_fail'] ++ ;
		show_message_c($_LANG['login_failure'], $_LANG['relogin_lnk'], 'user_c.php', 'error');
	}
}

/* 处理 ajax 的登录请求 */
elseif ($action == 'signin')
{
	include_once('includes/cls_json.php');
	$json = new JSON;

	$username = !empty($_POST['username']) ? json_str_iconv(trim($_POST['username'])) : '';
	$password = !empty($_POST['password']) ? trim($_POST['password']) : '';
	$captcha = !empty($_POST['captcha']) ? json_str_iconv(trim($_POST['captcha'])) : '';
	$result   = array('error' => 0, 'content' => '');

	$captcha = intval($_CFG['captcha']);
	if (($captcha & CAPTCHA_LOGIN) && (!($captcha & CAPTCHA_LOGIN_FAIL) || (($captcha & CAPTCHA_LOGIN_FAIL) && $_SESSION['login_fail'] > 2)) && gd_version() > 0)
	{
		if (empty($captcha))
		{
			$result['error']   = 1;
			$result['content'] = $_LANG['invalid_captcha'];
			die($json->encode($result));
		}

		/* 检查验证码 */
		include_once('includes/cls_captcha.php');

		$validator = new captcha();
		$validator->session_word = 'captcha_login';
		if (!$validator->check_word($_POST['captcha']))
		{

			$result['error']   = 1;
			$result['content'] = $_LANG['invalid_captcha'];
			die($json->encode($result));
		}
	}

	if ($user->login($username, $password))
	{
		update_user_info();  //更新用户信息
		recalculate_price(); // 重新计算购物车中的商品价格
		$smarty->assign('user_info', get_user_info());
		$ucdata = empty($user->ucdata)? "" : $user->ucdata;
		$result['ucdata'] = $ucdata;
		$result['content'] = $smarty->fetch('library/member_info.lbi');
	}
	else
	{
		$_SESSION['login_fail']++;
		if ($_SESSION['login_fail'] > 2)
		{
			$smarty->assign('enabled_captcha', 1);
			$result['html'] = $smarty->fetch('library/member_info.lbi');
		}
		$result['error']   = 1;
		$result['content'] = $_LANG['login_failure'];
	}
	die($json->encode($result));
}

/* 退出会员中心 */
elseif ($action == 'logout')
{
	if ((!isset($back_act)|| empty($back_act)) && isset($GLOBALS['_SERVER']['HTTP_REFERER']))
	{
		$back_act = strpos($GLOBALS['_SERVER']['HTTP_REFERER'], 'user_c.php') ? './index.php' : $GLOBALS['_SERVER']['HTTP_REFERER'];
	}

	$user->logout();
	$ucdata = empty($user->ucdata)? "" : $user->ucdata;
	show_message_c($_LANG['logout'] . $ucdata, $_LANG['back_up_page'], 'user_c.php', 'info');
}

/* 个人资料页面 */
elseif ($action == 'profile_c')
{
	include_once(ROOT_PATH . 'includes/lib_transaction.php');

	$user_info = get_profile($user_id);

	/* 取出注册扩展字段 */
	$sql = 'SELECT * FROM ' . $ecs->table('reg_fields') . ' WHERE type < 2 AND display = 1 ORDER BY dis_order, id';
	$extend_info_list = $db->getAll($sql);

	$sql = 'SELECT reg_field_id, content ' .
           'FROM ' . $ecs->table('reg_extend_info') .
           " WHERE user_id = $user_id";
	$extend_info_arr = $db->getAll($sql);

	$temp_arr = array();
	foreach ($extend_info_arr AS $val)
	{
		$temp_arr[$val['reg_field_id']] = $val['content'];
	}

	foreach ($extend_info_list AS $key => $val)
	{
		switch ($val['id'])
		{
			case 1:     $extend_info_list[$key]['content'] = $user_info['msn']; break;
			case 2:     $extend_info_list[$key]['content'] = $user_info['qq']; break;
			case 3:     $extend_info_list[$key]['content'] = $user_info['office_phone']; break;
			case 4:     $extend_info_list[$key]['content'] = $user_info['home_phone']; break;
			case 5:     $extend_info_list[$key]['content'] = $user_info['mobile_phone']; break;
			default:    $extend_info_list[$key]['content'] = empty($temp_arr[$val['id']]) ? '' : $temp_arr[$val['id']] ;
		}
	}




	$smarty->assign('extend_info_list', $extend_info_list);

	/* 密码提示问题 */
	$smarty->assign('passwd_questions', $_LANG['passwd_questions']);

	$smarty->assign('profile', $user_info);
	$smarty->display('user_profile_c.dwt');
}

/* 个人资料页面 */
elseif ($action == 'info_c')
{

	include_once(ROOT_PATH . 'includes/lib_transaction.php');
	$user_info = get_profile($user_id);
	$smarty->assign('user_id', $user_id);
	$smarty->assign('profile', $user_info);
	$smarty->display('user_info_c.dwt');
}


/* 修改个人资料的处理 */
elseif ($action == 'act_edit_profile')
{
	include_once(ROOT_PATH . 'includes/lib_transaction.php');

	$birthday = trim($_POST['birthdayYear']) .'-'. trim($_POST['birthdayMonth']) .'-'.
	trim($_POST['birthdayDay']);
	$email = trim($_POST['email']);
	$other['msn'] = $msn = isset($_POST['extend_field1']) ? trim($_POST['extend_field1']) : '';
	$other['qq'] = $qq = isset($_POST['extend_field2']) ? trim($_POST['extend_field2']) : '';
	$other['office_phone'] = $office_phone = isset($_POST['extend_field3']) ? trim($_POST['extend_field3']) : '';
	$other['home_phone'] = $home_phone = isset($_POST['extend_field4']) ? trim($_POST['extend_field4']) : '';
	$other['mobile_phone'] = $mobile_phone = isset($_POST['extend_field5']) ? trim($_POST['extend_field5']) : '';
	$sel_question = empty($_POST['sel_question']) ? '' : compile_str($_POST['sel_question']);
	$passwd_answer = isset($_POST['passwd_answer']) ? compile_str(trim($_POST['passwd_answer'])) : '';

	/* 更新用户扩展字段的数据 */
	$sql = 'SELECT id FROM ' . $ecs->table('reg_fields') . ' WHERE type = 0 AND display = 1 ORDER BY dis_order, id';   //读出所有扩展字段的id
	$fields_arr = $db->getAll($sql);

	foreach ($fields_arr AS $val)       //循环更新扩展用户信息
	{
		$extend_field_index = 'extend_field' . $val['id'];
		if(isset($_POST[$extend_field_index]))
		{
			$temp_field_content = strlen($_POST[$extend_field_index]) > 100 ? mb_substr(htmlspecialchars($_POST[$extend_field_index]), 0, 99) : htmlspecialchars($_POST[$extend_field_index]);
			$sql = 'SELECT * FROM ' . $ecs->table('reg_extend_info') . "  WHERE reg_field_id = '$val[id]' AND user_id = '$user_id'";
			if ($db->getOne($sql))      //如果之前没有记录，则插入
			{
				$sql = 'UPDATE ' . $ecs->table('reg_extend_info') . " SET content = '$temp_field_content' WHERE reg_field_id = '$val[id]' AND user_id = '$user_id'";
			}
			else
			{
				$sql = 'INSERT INTO '. $ecs->table('reg_extend_info') . " (`user_id`, `reg_field_id`, `content`) VALUES ('$user_id', '$val[id]', '$temp_field_content')";
			}
			$db->query($sql);
		}
	}

	/* 写入密码提示问题和答案 */
	if (!empty($passwd_answer) && !empty($sel_question))
	{
		$sql = 'UPDATE ' . $ecs->table('users') . " SET `passwd_question`='$sel_question', `passwd_answer`='$passwd_answer'  WHERE `user_id`='" . $_SESSION['user_id'] . "'";
		$db->query($sql);
	}

	if (!empty($office_phone) && !preg_match( '/^[\d|\_|\-|\s]+$/', $office_phone ) )
	{
		show_message_c($_LANG['passport_js']['office_phone_invalid']);
	}
	if (!empty($home_phone) && !preg_match( '/^[\d|\_|\-|\s]+$/', $home_phone) )
	{
		show_message_c($_LANG['passport_js']['home_phone_invalid']);
	}
	if (!is_email($email))
	{
		show_message_c($_LANG['msg_email_format']);
	}
	if (!empty($msn) && !is_email($msn))
	{
		show_message_c($_LANG['passport_js']['msn_invalid']);
	}
	if (!empty($qq) && !preg_match('/^\d+$/', $qq))
	{
		show_message_c($_LANG['passport_js']['qq_invalid']);
	}
	if (!empty($mobile_phone) && !preg_match('/^[\d-\s]+$/', $mobile_phone))
	{
		show_message_c($_LANG['passport_js']['mobile_phone_invalid']);
	}


	$profile  = array(
        'user_id'  => $user_id,
        'email'    => isset($_POST['email']) ? trim($_POST['email']) : '',
        'sex'      => isset($_POST['sex'])   ? intval($_POST['sex']) : 0,
        'birthday' => $birthday,
        'other'    => isset($other) ? $other : array(),
        'user_name'=>$user_name,
        'msn' => $orther['msn'],
        'mobile_phone'=>$orther['mobile_phone']
	);


	if (edit_profile($profile))
	{
		show_message_c($_LANG['edit_profile_success'], $_LANG['profile_lnk'], 'user_c.php?act=profile', 'info');
	}
	else
	{
		if ($user->error == ERR_EMAIL_EXISTS)
		{
			$msg = sprintf($_LANG['email_exist'], $profile['email']);
		}
		else
		{
			$msg = $_LANG['edit_profile_failed'];
		}
		show_message_c($msg, '', '', 'info');
	}
}

/* 密码找回-->修改密码界面 */
elseif ($action == 'get_password')
{
	include_once(ROOT_PATH . 'includes/lib_passport.php');

	if (isset($_GET['code']) && isset($_GET['uid'])) //从邮件处获得的act
	{
		$code = trim($_GET['code']);
		$uid  = intval($_GET['uid']);

		/* 判断链接的合法性 */
		$user_info = $user->get_profile_by_id($uid);
		if (empty($user_info) || ($user_info && md5($user_info['user_id'] . $_CFG['hash_code'] . $user_info['reg_time']) != $code))
		{
			show_message_c($_LANG['parm_error'], $_LANG['back_home_lnk'], './', 'info');
		}

		$smarty->assign('uid',    $uid);
		$smarty->assign('code',   $code);
		$smarty->assign('action', 'reset_password');
		$smarty->display('user_passport.dwt');
	}
	else
	{
		//显示用户名和email表单
		$smarty->display('user_passport.dwt');
	}
}

/* 密码找回-->输入用户名界面 */
elseif ($action == 'qpassword_name')
{
	//显示输入要找回密码的账号表单
	$smarty->display('user_passport.dwt');
}

/* 密码找回-->根据注册用户名取得密码提示问题界面 */
elseif ($action == 'get_passwd_question')
{
	if (empty($_POST['user_name']))
	{
		show_message_c($_LANG['no_passwd_question'], $_LANG['back_home_lnk'], './', 'info');
	}
	else
	{
		$user_name = trim($_POST['user_name']);
	}

	//取出会员密码问题和答案
	$sql = 'SELECT user_id, user_name, passwd_question, passwd_answer FROM ' . $ecs->table('users') . " WHERE user_name = '" . $user_name . "'";
	$user_question_arr = $db->getRow($sql);

	//如果没有设置密码问题，给出错误提示
	if (empty($user_question_arr['passwd_answer']))
	{
		show_message_c($_LANG['no_passwd_question'], $_LANG['back_home_lnk'], './', 'info');
	}

	$_SESSION['temp_user'] = $user_question_arr['user_id'];  //设置临时用户，不具有有效身份
	$_SESSION['temp_user_name'] = $user_question_arr['user_name'];  //设置临时用户，不具有有效身份
	$_SESSION['passwd_answer'] = $user_question_arr['passwd_answer'];   //存储密码问题答案，减少一次数据库访问

	$captcha = intval($_CFG['captcha']);
	if (($captcha & CAPTCHA_LOGIN) && (!($captcha & CAPTCHA_LOGIN_FAIL) || (($captcha & CAPTCHA_LOGIN_FAIL) && $_SESSION['login_fail'] > 2)) && gd_version() > 0)
	{
		$GLOBALS['smarty']->assign('enabled_captcha', 1);
		$GLOBALS['smarty']->assign('rand', mt_rand());
	}

	$smarty->assign('passwd_question', $_LANG['passwd_questions'][$user_question_arr['passwd_question']]);
	$smarty->display('user_passport.dwt');
}

/* 密码找回-->根据提交的密码答案进行相应处理 */
elseif ($action == 'check_answer')
{
	$captcha = intval($_CFG['captcha']);
	if (($captcha & CAPTCHA_LOGIN) && (!($captcha & CAPTCHA_LOGIN_FAIL) || (($captcha & CAPTCHA_LOGIN_FAIL) && $_SESSION['login_fail'] > 2)) && gd_version() > 0)
	{
		if (empty($_POST['captcha']))
		{
			show_message_c($_LANG['invalid_captcha'], $_LANG['back_retry_answer'], 'user_c.php?act=qpassword_name', 'error');
		}

		/* 检查验证码 */
		include_once('includes/cls_captcha.php');

		$validator = new captcha();
		$validator->session_word = 'captcha_login';
		if (!$validator->check_word($_POST['captcha']))
		{
			show_message_c($_LANG['invalid_captcha'], $_LANG['back_retry_answer'], 'user_c.php?act=qpassword_name', 'error');
		}
	}

	if (empty($_POST['passwd_answer']) || $_POST['passwd_answer'] != $_SESSION['passwd_answer'])
	{
		show_message_c($_LANG['wrong_passwd_answer'], $_LANG['back_retry_answer'], 'user_c.php?act=qpassword_name', 'info');
	}
	else
	{
		$_SESSION['user_id'] = $_SESSION['temp_user'];
		$_SESSION['user_name'] = $_SESSION['temp_user_name'];
		unset($_SESSION['temp_user']);
		unset($_SESSION['temp_user_name']);
		$smarty->assign('uid',    $_SESSION['user_id']);
		$smarty->assign('action', 'reset_password');
		$smarty->display('user_passport.dwt');
	}
}

/* 发送密码修改确认邮件 */
elseif ($action == 'send_pwd_email')
{
	include_once(ROOT_PATH . 'includes/lib_passport.php');

	/* 初始化会员用户名和邮件地址 */
	$user_name = !empty($_POST['user_name']) ? trim($_POST['user_name']) : '';
	$email     = !empty($_POST['email'])     ? trim($_POST['email'])     : '';

	//用户名和邮件地址是否匹配
	$user_info = $user->get_user_info($user_name);

	if ($user_info && $user_info['email'] == $email)
	{
		//生成code
		//$code = md5($user_info[0] . $user_info[1]);

		$code = md5($user_info['user_id'] . $_CFG['hash_code'] . $user_info['reg_time']);
		//发送邮件的函数
		if (send_pwd_email($user_info['user_id'], $user_name, $email, $code))
		{
			show_message_c($_LANG['send_success'] . $email, $_LANG['back_home_lnk'], './', 'info');
		}
		else
		{
			//发送邮件出错
			show_message_c($_LANG['fail_send_password'], $_LANG['back_page_up'], './', 'info');
		}
	}
	else
	{
		//用户名与邮件地址不匹配
		show_message_c($_LANG['username_no_email'], $_LANG['back_page_up'], '', 'info');
	}
}

/* 重置新密码 */
elseif ($action == 'reset_password')
{
	//显示重置密码的表单
	$smarty->display('user_passport.dwt');
}

/* 修改会员密码 */
elseif ($action == 'act_edit_password')
{
	include_once(ROOT_PATH . 'includes/lib_passport.php');

	$old_password = isset($_POST['old_password']) ? trim($_POST['old_password']) : null;
	$new_password = isset($_POST['new_password']) ? trim($_POST['new_password']) : '';
	$user_id      = isset($_POST['uid'])  ? intval($_POST['uid']) : $user_id;
	$code         = isset($_POST['code']) ? trim($_POST['code'])  : '';

	if (strlen($new_password) < 6)
	{
		show_message_c($_LANG['passport_js']['password_shorter']);
	}

	$user_info = $user->get_profile_by_id($user_id); //论坛记录

	if (($user_info && (!empty($code) && md5($user_info['user_id'] . $_CFG['hash_code'] . $user_info['reg_time']) == $code)) || ($_SESSION['user_id']>0 && $_SESSION['user_id'] == $user_id && $user->check_user($_SESSION['user_name'], $old_password)))
	{

		if ($user->edit_user(array('username'=> (empty($code) ? $_SESSION['user_name'] : $user_info['user_name']), 'old_password'=>$old_password, 'password'=>$new_password), empty($code) ? 0 : 1))
		{
			$sql="UPDATE ".$ecs->table('users'). "SET `ec_salt`='0' WHERE user_id= '".$user_id."'";
			$db->query($sql);
			$user->logout();
			show_message_c($_LANG['edit_password_success'], $_LANG['relogin_lnk'], 'user_c.php?act=login', 'info');
		}
		else
		{
			show_message_c($_LANG['edit_password_failure'], $_LANG['back_page_up'], '', 'info');
		}
	}
	else
	{
		show_message_c($_LANG['edit_password_failure'], $_LANG['back_page_up'], '', 'info');
	}

}
/* 修改会员信息 */
elseif ($action == 'act_edit_info'){
	include_once(ROOT_PATH . 'includes/lib_transaction.php');

	$user_id   = isset($_POST['user_id'])  ? intval($_POST['user_id']) : $user_id;
	$user_info = get_profile($user_id);
	$email = isset($_POST['email']) ? trim($_POST['email']) : $user_info['email'];
	$msn = isset($_POST['msn']) ? trim($_POST['msn']) : $user_info['msn'];
	$mobile_phone = isset($_POST['mobile_phone']) ? trim($_POST['mobile_phone']) : $user_info['mobile_phone'];
	$bank_name = isset($_POST['bank_name']) ? trim($_POST['bank_name']) : $user_info['bank_name'];
	$bank_address = isset($_POST['bank_address']) ? trim($_POST['bank_address']) : $user_info['bank_address'];
	$card_no = isset($_POST['card_no']) ? trim($_POST['card_no']) : $user_info['card_no'];
	$card_name = isset($_POST['card_name']) ? trim($_POST['card_name']) : $user_info['card_name'];



	$sql = 'UPDATE ' . $ecs->table('users') . " set `email` = '$email' ,`msn`='$msn',`mobile_phone`='$mobile_phone',`bank_name`='$bank_name',`bank_address`='$bank_address',`card_no`='$card_no',`card_name`='$card_name'  WHERE user_id = '$user_id'";

	$db->query($sql);
	header('location:user_c.php?act=info_c');
}

/* 添加一个红包 */
elseif ($action == 'act_add_bonus')
{
	include_once(ROOT_PATH . 'includes/lib_transaction.php');

	$bouns_sn = isset($_POST['bonus_sn']) ? intval($_POST['bonus_sn']) : '';

	if (add_bonus($user_id, $bouns_sn))
	{
		show_message_c($_LANG['add_bonus_sucess'], $_LANG['back_up_page'], 'user_c.php?act=bonus', 'info');
	}
	else
	{
		$err->show($_LANG['back_up_page'], 'user_c.php?act=bonus');
	}
}

/* 查看订单列表 */
elseif ($action == 'order_list')
{
	include_once(ROOT_PATH . 'includes/lib_transaction.php');

	$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

	$record_count = $db->getOne("SELECT COUNT(*) FROM " .$ecs->table('order_info'). " WHERE user_id = '$user_id'");

	$pager  = get_pager('user_c.php', array('act' => $action), $record_count, $page);

	$orders = get_user_orders($user_id, $pager['size'], $pager['start']);
	$merge  = get_user_merge($user_id);

	$smarty->assign('merge',  $merge);
	$smarty->assign('pager',  $pager);
	$smarty->assign('orders', $orders);
	$smarty->display('user_transaction.dwt');
}

/* 查看订单详情 */
elseif ($action == 'order_detail_c')
{
	include_once(ROOT_PATH . 'includes/lib_transaction.php');
	include_once(ROOT_PATH . 'includes/lib_payment.php');
	include_once(ROOT_PATH . 'includes/lib_order.php');
	include_once(ROOT_PATH . 'includes/lib_clips.php');

	$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

	/* 订单详情 */
	$order = get_order_detail($order_id, $user_id, false);

	if ($order === false)
	{
		$err->show($_LANG['back_home_lnk'], './');

		exit;
	}

	/* 是否显示添加到购物车 */
	if ($order['extension_code'] != 'group_buy' && $order['extension_code'] != 'exchange_goods')
	{
		$smarty->assign('allow_to_cart', 1);
	}

	/* 订单商品 */
	$goods_list = order_goods($order_id);
	foreach ($goods_list AS $key => $value)
	{
		$goods_list[$key]['market_price'] = price_format($value['market_price'], false);
		$goods_list[$key]['goods_price']  = price_format($value['goods_price'], false);
		$goods_list[$key]['subtotal']     = price_format($value['subtotal'], false);
	}

	/* 设置能否修改使用余额数 */
	if ($order['order_amount'] > 0)
	{
		if ($order['order_status'] == OS_UNCONFIRMED || $order['order_status'] == OS_CONFIRMED)
		{
			$user = user_info($order['user_id']);
			if ($user['user_money'] + $user['credit_line'] > 0)
			{
				$smarty->assign('allow_edit_surplus', 1);
				$smarty->assign('max_surplus', sprintf($_LANG['max_surplus'], $user['user_money']));
			}
		}
	}

	/* 未发货，未付款时允许更换支付方式 */
	if ($order['order_amount'] > 0 && $order['pay_status'] == PS_UNPAYED && $order['shipping_status'] == SS_UNSHIPPED)
	{
		$payment_list = available_payment_list(false, 0, true);

		/* 过滤掉当前支付方式和余额支付方式 */
		if(is_array($payment_list))
		{
			foreach ($payment_list as $key => $payment)
			{
				if ($payment['pay_id'] == $order['pay_id'] || $payment['pay_code'] == 'balance')
				{
					unset($payment_list[$key]);
				}
			}
		}
		$smarty->assign('payment_list', $payment_list);
	}

	/* 订单 支付 配送 状态语言项 */
	$order['order_status'] = $_LANG['os'][$order['order_status']];
	$order['pay_status'] = $_LANG['ps'][$order['pay_status']];
	$order['shipping_status'] = $_LANG['ss'][$order['shipping_status']];
	$smarty->assign('user_rank',   $_SESSION['user_rank']);
	$smarty->assign('order',      $order);
	$smarty->assign('goods_list', $goods_list);
	$smarty->display('user_order_detail_c.dwt');
}

/* 取消订单 */
elseif ($action == 'cancel_order')
{
	include_once(ROOT_PATH . 'includes/lib_transaction.php');
	include_once(ROOT_PATH . 'includes/lib_order.php');

	$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

	if (cancel_order($order_id, $user_id))
	{
		ecs_header("Location: user_c.php?act=order_list\n");
		exit;
	}
	else
	{
		$err->show($_LANG['order_list_lnk'], 'user_c.php?act=order_list');
	}
}

/* 收货地址列表界面*/
elseif ($action == 'address_list')
{
	include_once(ROOT_PATH . 'includes/lib_transaction.php');
	include_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/shopping_flow.php');
	$smarty->assign('lang',  $_LANG);

	/* 取得国家列表、商店所在国家、商店所在国家的省列表 */
	$smarty->assign('country_list',       get_regions());
	$smarty->assign('shop_province_list', get_regions(1, $_CFG['shop_country']));

	/* 获得用户所有的收货人信息 */
	$consignee_list = get_consignee_list($_SESSION['user_id']);

	if (count($consignee_list) < 5 && $_SESSION['user_id'] > 0)
	{
		/* 如果用户收货人信息的总数小于5 则增加一个新的收货人信息 */
		$consignee_list[] = array('country' => $_CFG['shop_country'], 'email' => isset($_SESSION['email']) ? $_SESSION['email'] : '');
	}

	$smarty->assign('consignee_list', $consignee_list);

	//取得国家列表，如果有收货人列表，取得省市区列表
	foreach ($consignee_list AS $region_id => $consignee)
	{
		$consignee['country']  = isset($consignee['country'])  ? intval($consignee['country'])  : 0;
		$consignee['province'] = isset($consignee['province']) ? intval($consignee['province']) : 0;
		$consignee['city']     = isset($consignee['city'])     ? intval($consignee['city'])     : 0;

		$province_list[$region_id] = get_regions(1, $consignee['country']);
		$city_list[$region_id]     = get_regions(2, $consignee['province']);
		$district_list[$region_id] = get_regions(3, $consignee['city']);
	}

	/* 获取默认收货ID */
	$address_id  = $db->getOne("SELECT address_id FROM " .$ecs->table('users'). " WHERE user_id='$user_id'");

	//赋值于模板
	$smarty->assign('real_goods_count', 1);
	$smarty->assign('shop_country',     $_CFG['shop_country']);
	$smarty->assign('shop_province',    get_regions(1, $_CFG['shop_country']));
	$smarty->assign('province_list',    $province_list);
	$smarty->assign('address',          $address_id);
	$smarty->assign('city_list',        $city_list);
	$smarty->assign('district_list',    $district_list);
	$smarty->assign('currency_format',  $_CFG['currency_format']);
	$smarty->assign('integral_scale',   $_CFG['integral_scale']);
	$smarty->assign('name_of_region',   array($_CFG['name_of_region_1'], $_CFG['name_of_region_2'], $_CFG['name_of_region_3'], $_CFG['name_of_region_4']));

	$smarty->display('user_transaction.dwt');
}

/* 添加/编辑收货地址的处理 */
elseif ($action == 'act_edit_address')
{
	include_once(ROOT_PATH . 'includes/lib_transaction.php');
	include_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/shopping_flow.php');
	$smarty->assign('lang', $_LANG);

	$address = array(
        'user_id'    => $user_id,
        'address_id' => intval($_POST['address_id']),
        'country'    => isset($_POST['country'])   ? intval($_POST['country'])  : 0,
        'province'   => isset($_POST['province'])  ? intval($_POST['province']) : 0,
        'city'       => isset($_POST['city'])      ? intval($_POST['city'])     : 0,
        'district'   => isset($_POST['district'])  ? intval($_POST['district']) : 0,
        'address'    => isset($_POST['address'])   ? compile_str(trim($_POST['address']))    : '',
        'consignee'  => isset($_POST['consignee']) ? compile_str(trim($_POST['consignee']))  : '',
        'email'      => isset($_POST['email'])     ? compile_str(trim($_POST['email']))      : '',
        'tel'        => isset($_POST['tel'])       ? compile_str(make_semiangle(trim($_POST['tel']))) : '',
        'mobile'     => isset($_POST['mobile'])    ? compile_str(make_semiangle(trim($_POST['mobile']))) : '',
        'best_time'  => isset($_POST['best_time']) ? compile_str(trim($_POST['best_time']))  : '',
        'sign_building' => isset($_POST['sign_building']) ? compile_str(trim($_POST['sign_building'])) : '',
        'zipcode'       => isset($_POST['zipcode'])       ? compile_str(make_semiangle(trim($_POST['zipcode']))) : '',
	);

	if (update_address($address))
	{
		show_message_c($_LANG['edit_address_success'], $_LANG['address_list_lnk'], 'user_c.php?act=address_list');
	}
}

/* 删除收货地址 */
elseif ($action == 'drop_consignee')
{
	include_once('includes/lib_transaction.php');

	$consignee_id = intval($_GET['id']);

	if (drop_consignee($consignee_id))
	{
		ecs_header("Location: user_c.php?act=address_list\n");
		exit;
	}
	else
	{
		show_message_c($_LANG['del_address_false']);
	}
}

/* 显示收藏商品列表 */
elseif ($action == 'collection_list')
{
	include_once(ROOT_PATH . 'includes/lib_clips.php');

	$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

	$record_count = $db->getOne("SELECT COUNT(*) FROM " .$ecs->table('collect_goods').
                                " WHERE user_id='$user_id' ORDER BY add_time DESC");

	$pager = get_pager('user_c.php', array('act' => $action), $record_count, $page);
	$smarty->assign('pager', $pager);
	$smarty->assign('goods_list', get_collection_goods($user_id, $pager['size'], $pager['start']));
	$smarty->assign('url',        $ecs->url());
	$lang_list = array(
        'UTF8'   => $_LANG['charset']['utf8'],
        'GB2312' => $_LANG['charset']['zh_cn'],
        'BIG5'   => $_LANG['charset']['zh_tw'],
	);
	$smarty->assign('lang_list',  $lang_list);
	$smarty->assign('user_id',  $user_id);
	$smarty->display('user_clips.dwt');
}

/* 删除收藏的商品 */
elseif ($action == 'delete_collection')
{
	include_once(ROOT_PATH . 'includes/lib_clips.php');

	$collection_id = isset($_GET['collection_id']) ? intval($_GET['collection_id']) : 0;

	if ($collection_id > 0)
	{
		$db->query('DELETE FROM ' .$ecs->table('collect_goods'). " WHERE rec_id='$collection_id' AND user_id ='$user_id'" );
	}

	ecs_header("Location: user_c.php?act=collection_list\n");
	exit;
}

/* 添加关注商品 */
elseif ($action == 'add_to_attention')
{
	$rec_id = (int)$_GET['rec_id'];
	if ($rec_id)
	{
		$db->query('UPDATE ' .$ecs->table('collect_goods'). "SET is_attention = 1 WHERE rec_id='$rec_id' AND user_id ='$user_id'" );
	}
	ecs_header("Location: user_c.php?act=collection_list\n");
	exit;
}
/* 取消关注商品 */
elseif ($action == 'del_attention')
{
	$rec_id = (int)$_GET['rec_id'];
	if ($rec_id)
	{
		$db->query('UPDATE ' .$ecs->table('collect_goods'). "SET is_attention = 0 WHERE rec_id='$rec_id' AND user_id ='$user_id'" );
	}
	ecs_header("Location: user_c.php?act=collection_list\n");
	exit;
}
/* 显示留言列表 */
elseif ($action == 'message_list')
{
	include_once(ROOT_PATH . 'includes/lib_clips.php');

	$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

	$order_id = empty($_GET['order_id']) ? 0 : intval($_GET['order_id']);
	$order_info = array();

	/* 获取用户留言的数量 */
	if ($order_id)
	{
		$sql = "SELECT COUNT(*) FROM " .$ecs->table('feedback').
                " WHERE parent_id = 0 AND order_id = '$order_id' AND user_id = '$user_id'";
		$order_info = $db->getRow("SELECT * FROM " . $ecs->table('order_info') . " WHERE order_id = '$order_id' AND user_id = '$user_id'");
		$order_info['url'] = 'user_c.php?act=order_detail&order_id=' . $order_id;
	}
	else
	{
		$sql = "SELECT COUNT(*) FROM " .$ecs->table('feedback').
           " WHERE parent_id = 0 AND user_id = '$user_id' AND user_name = '" . $_SESSION['user_name'] . "' AND order_id=0";
	}

	$record_count = $db->getOne($sql);
	$act = array('act' => $action);

	if ($order_id != '')
	{
		$act['order_id'] = $order_id;
	}

	$pager = get_pager('user_c.php', $act, $record_count, $page, 5);

	$smarty->assign('message_list', get_message_list($user_id, $_SESSION['user_name'], $pager['size'], $pager['start'], $order_id));
	$smarty->assign('pager',        $pager);
	$smarty->assign('order_info',   $order_info);
	$smarty->display('user_clips.dwt');
}

/* 显示评论列表 */
elseif ($action == 'comment_list')
{
	include_once(ROOT_PATH . 'includes/lib_clips.php');

	$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

	/* 获取用户留言的数量 */
	$sql = "SELECT COUNT(*) FROM " .$ecs->table('comment').
           " WHERE parent_id = 0 AND user_id = '$user_id'";
	$record_count = $db->getOne($sql);
	$pager = get_pager('user_c.php', array('act' => $action), $record_count, $page, 5);

	$smarty->assign('comment_list', get_comment_list($user_id, $pager['size'], $pager['start']));
	$smarty->assign('pager',        $pager);
	$smarty->display('user_clips.dwt');
}

/* 添加我的留言 */
elseif ($action == 'act_add_message')
{
	include_once(ROOT_PATH . 'includes/lib_clips.php');

	$message = array(
        'user_id'     => $user_id,
        'user_name'   => $_SESSION['user_name'],
        'user_email'  => $_SESSION['email'],
        'msg_type'    => isset($_POST['msg_type']) ? intval($_POST['msg_type'])     : 0,
        'msg_title'   => isset($_POST['msg_title']) ? trim($_POST['msg_title'])     : '',
        'msg_content' => isset($_POST['msg_content']) ? trim($_POST['msg_content']) : '',
        'order_id'=>empty($_POST['order_id']) ? 0 : intval($_POST['order_id']),
        'upload'      => (isset($_FILES['message_img']['error']) && $_FILES['message_img']['error'] == 0) || (!isset($_FILES['message_img']['error']) && isset($_FILES['message_img']['tmp_name']) && $_FILES['message_img']['tmp_name'] != 'none')
	? $_FILES['message_img'] : array()
	);

	if (add_message($message))
	{
		show_message_c($_LANG['add_message_success'], $_LANG['message_list_lnk'], 'user_c.php?act=message_list&order_id=' . $message['order_id'],'info');
	}
	else
	{
		$err->show($_LANG['message_list_lnk'], 'user_c.php?act=message_list');
	}
}

/* 标签云列表 */
elseif ($action == 'tag_list')
{
	include_once(ROOT_PATH . 'includes/lib_clips.php');

	$good_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

	$smarty->assign('tags',      get_user_tags($user_id));
	$smarty->assign('tags_from', 'user');
	$smarty->display('user_clips.dwt');
}

/* 删除标签云的处理 */
elseif ($action == 'act_del_tag')
{
	include_once(ROOT_PATH . 'includes/lib_clips.php');

	$tag_words = isset($_GET['tag_words']) ? trim($_GET['tag_words']) : '';
	delete_tag($tag_words, $user_id);

	ecs_header("Location: user_c.php?act=tag_list\n");
	exit;

}

/* 显示缺货登记列表 */
elseif ($action == 'booking_list')
{
	include_once(ROOT_PATH . 'includes/lib_clips.php');

	$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

	/* 获取缺货登记的数量 */
	$sql = "SELECT COUNT(*) " .
            "FROM " .$ecs->table('booking_goods'). " AS bg, " .
	$ecs->table('goods') . " AS g " .
            "WHERE bg.goods_id = g.goods_id AND user_id = '$user_id'";
	$record_count = $db->getOne($sql);
	$pager = get_pager('user_c.php', array('act' => $action), $record_count, $page);

	$smarty->assign('booking_list', get_booking_list($user_id, $pager['size'], $pager['start']));
	$smarty->assign('pager',        $pager);
	$smarty->display('user_clips.dwt');
}
/* 添加缺货登记页面 */
elseif ($action == 'add_booking')
{
	include_once(ROOT_PATH . 'includes/lib_clips.php');

	$goods_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
	if ($goods_id == 0)
	{
		show_message_c($_LANG['no_goods_id'], $_LANG['back_page_up'], '', 'error');
	}

	/* 根据规格属性获取货品规格信息 */
	$goods_attr = '';
	if ($_GET['spec'] != '')
	{
		$goods_attr_id = $_GET['spec'];

		$attr_list = array();
		$sql = "SELECT a.attr_name, g.attr_value " .
                "FROM " . $ecs->table('goods_attr') . " AS g, " .
		$ecs->table('attribute') . " AS a " .
                "WHERE g.attr_id = a.attr_id " .
                "AND g.goods_attr_id " . db_create_in($goods_attr_id);
		$res = $db->query($sql);
		while ($row = $db->fetchRow($res))
		{
			$attr_list[] = $row['attr_name'] . ': ' . $row['attr_value'];
		}
		$goods_attr = join(chr(13) . chr(10), $attr_list);
	}
	$smarty->assign('goods_attr', $goods_attr);

	$smarty->assign('info', get_goodsinfo($goods_id));
	$smarty->display('user_clips.dwt');

}

/* 添加缺货登记的处理 */
elseif ($action == 'act_add_booking')
{
	include_once(ROOT_PATH . 'includes/lib_clips.php');

	$booking = array(
        'goods_id'     => isset($_POST['id'])      ? intval($_POST['id'])     : 0,
        'goods_amount' => isset($_POST['number'])  ? intval($_POST['number']) : 0,
        'desc'         => isset($_POST['desc'])    ? trim($_POST['desc'])     : '',
        'linkman'      => isset($_POST['linkman']) ? trim($_POST['linkman'])  : '',
        'email'        => isset($_POST['email'])   ? trim($_POST['email'])    : '',
        'tel'          => isset($_POST['tel'])     ? trim($_POST['tel'])      : '',
        'booking_id'   => isset($_POST['rec_id'])  ? intval($_POST['rec_id']) : 0
	);

	// 查看此商品是否已经登记过
	$rec_id = get_booking_rec($user_id, $booking['goods_id']);
	if ($rec_id > 0)
	{
		show_message_c($_LANG['booking_rec_exist'], $_LANG['back_page_up'], '', 'error');
	}

	if (add_booking($booking))
	{
		show_message_c($_LANG['booking_success'], $_LANG['back_booking_list'], 'user_c.php?act=booking_list',
        'info');
	}
	else
	{
		$err->show($_LANG['booking_list_lnk'], 'user_c.php?act=booking_list');
	}
}

/* 删除缺货登记 */
elseif ($action == 'act_del_booking')
{
	include_once(ROOT_PATH . 'includes/lib_clips.php');

	$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
	if ($id == 0 || $user_id == 0)
	{
		ecs_header("Location: user_c.php?act=booking_list\n");
		exit;
	}

	$result = delete_booking($id, $user_id);
	if ($result)
	{
		ecs_header("Location: user_c.php?act=booking_list\n");
		exit;
	}
}

/* 确认收货 */
elseif ($action == 'affirm_received')
{
	include_once(ROOT_PATH . 'includes/lib_transaction.php');

	$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

	if (affirm_received($order_id, $user_id))
	{
		ecs_header("Location: user_c.php?act=order_list\n");
		exit;
	}
	else
	{
		$err->show($_LANG['order_list_lnk'], 'user_c.php?act=order_list');
	}
}

/* 会员退款申请界面 */
elseif ($action == 'account_raply_c')
{
	//获取剩余余额
	$surplus_amount = get_user_surplus($user_id);
	if (empty($surplus_amount))
	{
		$surplus_amount = 0;
	}
	$smarty->assign('surplus_amount', price_format($surplus_amount, false));

	$smarty->display('user_account_raply_c.dwt');
}

/* 会员预付款界面 */
elseif ($action == 'account_deposit')
{
	include_once(ROOT_PATH . 'includes/lib_clips.php');

	$surplus_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
	$account    = get_surplus_info($surplus_id);

	$smarty->assign('payment', get_online_payment_list(false));
	$smarty->assign('order',   $account);
	$smarty->display('user_transaction.dwt');
}

/* 会员账目明细界面 */
elseif ($action == 'account_detail')
{
	include_once(ROOT_PATH . 'includes/lib_clips.php');

	$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

	$account_type = 'user_money';

	/* 获取记录条数 */
	$sql = "SELECT COUNT(*) FROM " .$ecs->table('account_log').
           " WHERE user_id = '$user_id'" .
           " AND $account_type <> 0 ";
	$record_count = $db->getOne($sql);

	//分页函数
	$pager = get_pager('user_c.php', array('act' => $action), $record_count, $page);

	//获取剩余余额
	$surplus_amount = get_user_surplus($user_id);
	if (empty($surplus_amount))
	{
		$surplus_amount = 0;
	}

	//获取余额记录
	$account_log = array();
	$sql = "SELECT * FROM " . $ecs->table('account_log') .
           " WHERE user_id = '$user_id'" .
           " AND $account_type <> 0 " .
           " ORDER BY log_id DESC";
	$res = $GLOBALS['db']->selectLimit($sql, $pager['size'], $pager['start']);
	while ($row = $db->fetchRow($res))
	{
		$row['change_time'] = local_date($_CFG['date_format'], $row['change_time']);
		$row['type'] = $row[$account_type] > 0 ? $_LANG['account_inc'] : $_LANG['account_dec'];
		$row['user_money'] = price_format(abs($row['user_money']), false);
		$row['frozen_money'] = price_format(abs($row['frozen_money']), false);
		$row['rank_points'] = abs($row['rank_points']);
		$row['pay_points'] = abs($row['pay_points']);
		$row['short_change_desc'] = sub_str($row['change_desc'], 60);
		$row['amount'] = $row[$account_type];
		$account_log[] = $row;
	}

	//模板赋值
	$smarty->assign('surplus_amount', price_format($surplus_amount, false));
	$smarty->assign('account_log',    $account_log);
	$smarty->assign('pager',          $pager);
	$smarty->display('user_transaction.dwt');
}

/* 会员提现申请记录 */
elseif ($action == 'account_log_c')
{
	include_once(ROOT_PATH . 'includes/lib_clips.php');

	$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

	/* 获取记录条数 */
	$sql = "SELECT COUNT(*) FROM " .$ecs->table('user_account').
           " WHERE user_id = '$user_id'" .
           " AND process_type " . db_create_in(array(SURPLUS_SAVE, SURPLUS_RETURN));
	$record_count = $db->getOne($sql);

	//分页函数
	$pager = get_pager('user_c.php', array('act' => $action), $record_count, $page,'10');

	//获取剩余余额
	$surplus_amount = get_user_surplus($user_id);
	if (empty($surplus_amount))
	{
		$surplus_amount = 0;
	}
	$smarty->assign('surplus_amount', price_format($surplus_amount, false));

	//获取余额记录
	$account_log = get_account_log($user_id, $pager['size'], $pager['start']);

	//模板赋值
	$smarty->assign('account_log',    $account_log);
	$smarty->assign('pager',          $pager);
	$smarty->display('user_account_log_c.dwt');
}

/* 对会员余额申请的处理 */
elseif ($action == 'act_account')
{
	include_once(ROOT_PATH . 'includes/lib_clips.php');
	include_once(ROOT_PATH . 'includes/lib_order.php');
	$amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
	if ($amount <= 0)
	{
		show_message_c($_LANG['amount_gt_zero']);
	}

	/* 变量初始化 */
	$surplus = array(
            'user_id'      => $user_id,
            'rec_id'       => !empty($_POST['rec_id'])      ? intval($_POST['rec_id'])       : 0,
            'process_type' => isset($_POST['surplus_type']) ? intval($_POST['surplus_type']) : 0,
            'payment_id'   => isset($_POST['payment_id'])   ? intval($_POST['payment_id'])   : 0,
            'user_note'    => isset($_POST['user_note'])    ? trim($_POST['user_note'])      : '',
            'amount'       => $amount
	);

	/* 退款申请的处理 */
	if ($surplus['process_type'] == 1)
	{
		/* 判断是否有足够的余额的进行退款的操作 */
		$sur_amount = get_user_surplus($user_id);
		if ($amount > $sur_amount)
		{
			$content = $_LANG['surplus_amount_error'];
			show_message_c($content, $_LANG['back_page_up'], '', 'info');
		}

		//插入会员账目明细
		$amount = '-'.$amount;
		$surplus['payment'] = '';
		$surplus['rec_id']  = insert_user_account($surplus, $amount);

		/* 如果成功提交 */
		if ($surplus['rec_id'] > 0)
		{
			$content = $_LANG['surplus_appl_submit'];
			show_message_c($content, $_LANG['back_account_log'], 'user_c.php?act=account_log_c', 'info');
		}
		else
		{
			$content = $_LANG['process_false'];
			show_message_c($content, $_LANG['back_page_up'], '', 'info');
		}
	}
	/* 如果是会员预付款，跳转到下一步，进行线上支付的操作 */
	else
	{
		if ($surplus['payment_id'] <= 0)
		{
			show_message_c($_LANG['select_payment_pls']);
		}

		include_once(ROOT_PATH .'includes/lib_payment.php');

		//获取支付方式名称
		$payment_info = array();
		$payment_info = payment_info($surplus['payment_id']);
		$surplus['payment'] = $payment_info['pay_name'];

		if ($surplus['rec_id'] > 0)
		{
			//更新会员账目明细
			$surplus['rec_id'] = update_user_account($surplus);
		}
		else
		{
			//插入会员账目明细
			$surplus['rec_id'] = insert_user_account($surplus, $amount);
		}

		//取得支付信息，生成支付代码
		$payment = unserialize_config($payment_info['pay_config']);

		//生成伪订单号, 不足的时候补0
		$order = array();
		$order['order_sn']       = $surplus['rec_id'];
		$order['user_name']      = $_SESSION['user_name'];
		$order['surplus_amount'] = $amount;

		//计算支付手续费用
		$payment_info['pay_fee'] = pay_fee($surplus['payment_id'], $order['surplus_amount'], 0);

		//计算此次预付款需要支付的总金额
		$order['order_amount']   = $amount + $payment_info['pay_fee'];

		//记录支付log
		$order['log_id'] = insert_pay_log($surplus['rec_id'], $order['order_amount'], $type=PAY_SURPLUS, 0);

		/* 调用相应的支付方式文件 */
		include_once(ROOT_PATH . 'includes/modules/payment/' . $payment_info['pay_code'] . '.php');

		/* 取得在线支付方式的支付按钮 */
		$pay_obj = new $payment_info['pay_code'];
		$payment_info['pay_button'] = $pay_obj->get_code($order, $payment);

		/* 模板赋值 */
		$smarty->assign('payment', $payment_info);
		$smarty->assign('pay_fee', price_format($payment_info['pay_fee'], false));
		$smarty->assign('amount',  price_format($amount, false));
		$smarty->assign('order',   $order);
		$smarty->display('user_transaction.dwt');
	}
}

/* 删除会员余额 */
elseif ($action == 'cancel_c')
{
	include_once(ROOT_PATH . 'includes/lib_clips.php');

	$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
	if ($id == 0 || $user_id == 0)
	{
		ecs_header("Location: user_c.php?act=account_log_c\n");
		exit;
	}

	$result = del_user_account($id, $user_id);
	if ($result)
	{
		ecs_header("Location: user_c.php?act=account_log_c\n");
		exit;
	}
}

/* 会员通过帐目明细列表进行再付款的操作 */
elseif ($action == 'pay')
{
	include_once(ROOT_PATH . 'includes/lib_clips.php');
	include_once(ROOT_PATH . 'includes/lib_payment.php');
	include_once(ROOT_PATH . 'includes/lib_order.php');

	//变量初始化
	$surplus_id = isset($_GET['id'])  ? intval($_GET['id'])  : 0;
	$payment_id = isset($_GET['pid']) ? intval($_GET['pid']) : 0;

	if ($surplus_id == 0)
	{
		ecs_header("Location: user_c.php?act=account_log\n");
		exit;
	}

	//如果原来的支付方式已禁用或者已删除, 重新选择支付方式
	if ($payment_id == 0)
	{
		ecs_header("Location: user_c.php?act=account_deposit&id=".$surplus_id."\n");
		exit;
	}

	//获取单条会员帐目信息
	$order = array();
	$order = get_surplus_info($surplus_id);

	//支付方式的信息
	$payment_info = array();
	$payment_info = payment_info($payment_id);

	/* 如果当前支付方式没有被禁用，进行支付的操作 */
	if (!empty($payment_info))
	{
		//取得支付信息，生成支付代码
		$payment = unserialize_config($payment_info['pay_config']);

		//生成伪订单号
		$order['order_sn'] = $surplus_id;

		//获取需要支付的log_id
		$order['log_id'] = get_paylog_id($surplus_id, $pay_type = PAY_SURPLUS);

		$order['user_name']      = $_SESSION['user_name'];
		$order['surplus_amount'] = $order['amount'];

		//计算支付手续费用
		$payment_info['pay_fee'] = pay_fee($payment_id, $order['surplus_amount'], 0);

		//计算此次预付款需要支付的总金额
		$order['order_amount']   = $order['surplus_amount'] + $payment_info['pay_fee'];

		//如果支付费用改变了，也要相应的更改pay_log表的order_amount
		$order_amount = $db->getOne("SELECT order_amount FROM " .$ecs->table('pay_log')." WHERE log_id = '$order[log_id]'");
		if ($order_amount <> $order['order_amount'])
		{
			$db->query("UPDATE " .$ecs->table('pay_log').
                       " SET order_amount = '$order[order_amount]' WHERE log_id = '$order[log_id]'");
		}

		/* 调用相应的支付方式文件 */
		include_once(ROOT_PATH . 'includes/modules/payment/' . $payment_info['pay_code'] . '.php');

		/* 取得在线支付方式的支付按钮 */
		$pay_obj = new $payment_info['pay_code'];
		$payment_info['pay_button'] = $pay_obj->get_code($order, $payment);

		/* 模板赋值 */
		$smarty->assign('payment', $payment_info);
		$smarty->assign('order',   $order);
		$smarty->assign('pay_fee', price_format($payment_info['pay_fee'], false));
		$smarty->assign('amount',  price_format($order['surplus_amount'], false));
		$smarty->assign('action',  'act_account');
		$smarty->display('user_transaction.dwt');
	}
	/* 重新选择支付方式 */
	else
	{
		include_once(ROOT_PATH . 'includes/lib_clips.php');

		$smarty->assign('payment', get_online_payment_list());
		$smarty->assign('order',   $order);
		$smarty->assign('action',  'account_deposit');
		$smarty->display('user_transaction.dwt');
	}
}

/* 添加标签(ajax) */
elseif ($action == 'add_tag')
{
	include_once('includes/cls_json.php');
	include_once('includes/lib_clips.php');

	$result = array('error' => 0, 'message' => '', 'content' => '');
	$id     = isset($_POST['id']) ? intval($_POST['id']) : 0;
	$tag    = isset($_POST['tag']) ? json_str_iconv(trim($_POST['tag'])) : '';

	if ($user_id == 0)
	{
		/* 用户没有登录 */
		$result['error']   = 1;
		$result['message'] = $_LANG['tag_anonymous'];
	}
	else
	{
		add_tag($id, $tag); // 添加tag
		clear_cache_files('goods'); // 删除缓存

		/* 重新获得该商品的所有缓存 */
		$arr = get_tags($id);

		foreach ($arr AS $row)
		{
			$result['content'][] = array('word' => htmlspecialchars($row['tag_words']), 'count' => $row['tag_count']);
		}
	}

	$json = new JSON;

	echo $json->encode($result);
	exit;
}

/* 添加收藏商品(ajax) */
elseif ($action == 'collect')
{
	include_once(ROOT_PATH .'includes/cls_json.php');
	$json = new JSON();
	$result = array('error' => 0, 'message' => '');
	$goods_id = $_GET['id'];

	if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == 0)
	{
		$result['error'] = 1;
		$result['message'] = $_LANG['login_please'];
		die($json->encode($result));
	}
	else
	{
		/* 检查是否已经存在于用户的收藏夹 */
		$sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('collect_goods') .
            " WHERE user_id='$_SESSION[user_id]' AND goods_id = '$goods_id'";
		if ($GLOBALS['db']->GetOne($sql) > 0)
		{
			$result['error'] = 1;
			$result['message'] = $GLOBALS['_LANG']['collect_existed'];
			die($json->encode($result));
		}
		else
		{
			$time = gmtime();
			$sql = "INSERT INTO " .$GLOBALS['ecs']->table('collect_goods'). " (user_id, goods_id, add_time)" .
                    "VALUES ('$_SESSION[user_id]', '$goods_id', '$time')";

			if ($GLOBALS['db']->query($sql) === false)
			{
				$result['error'] = 1;
				$result['message'] = $GLOBALS['db']->errorMsg();
				die($json->encode($result));
			}
			else
			{
				$result['error'] = 0;
				$result['message'] = $GLOBALS['_LANG']['collect_success'];
				die($json->encode($result));
			}
		}
	}
}

/* 删除留言 */
elseif ($action == 'del_msg')
{
	$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
	$order_id = empty($_GET['order_id']) ? 0 : intval($_GET['order_id']);

	if ($id > 0)
	{
		$sql = 'SELECT user_id, message_img FROM ' .$ecs->table('feedback'). " WHERE msg_id = '$id' LIMIT 1";
		$row = $db->getRow($sql);
		if ($row && $row['user_id'] == $user_id)
		{
			/* 验证通过，删除留言，回复，及相应文件 */
			if ($row['message_img'])
			{
				@unlink(ROOT_PATH . DATA_DIR . '/feedbackimg/'. $row['message_img']);
			}
			$sql = "DELETE FROM " .$ecs->table('feedback'). " WHERE msg_id = '$id' OR parent_id = '$id'";
			$db->query($sql);
		}
	}
	ecs_header("Location: user_c.php?act=message_list&order_id=$order_id\n");
	exit;
}

/* 删除评论 */
elseif ($action == 'del_cmt')
{
	$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
	if ($id > 0)
	{
		$sql = "DELETE FROM " .$ecs->table('comment'). " WHERE comment_id = '$id' AND user_id = '$user_id'";
		$db->query($sql);
	}
	ecs_header("Location: user_c.php?act=comment_list\n");
	exit;
}

/* 合并订单 */
elseif ($action == 'merge_order')
{
	include_once(ROOT_PATH .'includes/lib_transaction.php');
	include_once(ROOT_PATH .'includes/lib_order.php');
	$from_order = isset($_POST['from_order']) ? trim($_POST['from_order']) : '';
	$to_order   = isset($_POST['to_order']) ? trim($_POST['to_order']) : '';
	if (merge_user_order($from_order, $to_order, $user_id))
	{
		show_message_c($_LANG['merge_order_success'],$_LANG['order_list_lnk'],'user_c.php?act=order_list', 'info');
	}
	else
	{
		$err->show($_LANG['order_list_lnk']);
	}
}
/* 将指定订单中商品添加到购物车 */
elseif ($action == 'return_to_cart')
{
	include_once(ROOT_PATH .'includes/cls_json.php');
	include_once(ROOT_PATH .'includes/lib_transaction.php');
	$json = new JSON();

	$result = array('error' => 0, 'message' => '', 'content' => '');
	$order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
	if ($order_id == 0)
	{
		$result['error']   = 1;
		$result['message'] = $_LANG['order_id_empty'];
		die($json->encode($result));
	}

	if ($user_id == 0)
	{
		/* 用户没有登录 */
		$result['error']   = 1;
		$result['message'] = $_LANG['login_please'];
		die($json->encode($result));
	}

	/* 检查订单是否属于该用户 */
	$order_user = $db->getOne("SELECT user_id FROM " .$ecs->table('order_info'). " WHERE order_id = '$order_id'");
	if (empty($order_user))
	{
		$result['error'] = 1;
		$result['message'] = $_LANG['order_exist'];
		die($json->encode($result));
	}
	else
	{
		if ($order_user != $user_id)
		{
			$result['error'] = 1;
			$result['message'] = $_LANG['no_priv'];
			die($json->encode($result));
		}
	}

	$message = return_to_cart($order_id);

	if ($message === true)
	{
		$result['error'] = 0;
		$result['message'] = $_LANG['return_to_cart_success'];
		die($json->encode($result));
	}
	else
	{
		$result['error'] = 1;
		$result['message'] = $_LANG['order_exist'];
		die($json->encode($result));
	}

}

/* 编辑使用余额支付的处理 */
elseif ($action == 'act_edit_surplus')
{
	/* 检查是否登录 */
	if ($_SESSION['user_id'] <= 0)
	{
		ecs_header("Location: ./\n");
		exit;
	}

	/* 检查订单号 */
	$order_id = intval($_POST['order_id']);
	if ($order_id <= 0)
	{
		ecs_header("Location: ./\n");
		exit;
	}

	/* 检查余额 */
	$surplus = floatval($_POST['surplus']);
	if ($surplus <= 0)
	{
		$err->add($_LANG['error_surplus_invalid']);
		$err->show($_LANG['order_detail'], 'user_c.php?act=order_detail&order_id=' . $order_id);
	}

	include_once(ROOT_PATH . 'includes/lib_order.php');

	/* 取得订单 */
	$order = order_info($order_id);
	if (empty($order))
	{
		ecs_header("Location: ./\n");
		exit;
	}

	/* 检查订单用户跟当前用户是否一致 */
	if ($_SESSION['user_id'] != $order['user_id'])
	{
		ecs_header("Location: ./\n");
		exit;
	}

	/* 检查订单是否未付款，检查应付款金额是否大于0 */
	if ($order['pay_status'] != PS_UNPAYED || $order['order_amount'] <= 0)
	{
		$err->add($_LANG['error_order_is_paid']);
		$err->show($_LANG['order_detail'], 'user_c.php?act=order_detail&order_id=' . $order_id);
	}

	/* 计算应付款金额（减去支付费用） */
	$order['order_amount'] -= $order['pay_fee'];

	/* 余额是否超过了应付款金额，改为应付款金额 */
	if ($surplus > $order['order_amount'])
	{
		$surplus = $order['order_amount'];
	}

	/* 取得用户信息 */
	$user = user_info($_SESSION['user_id']);

	/* 用户帐户余额是否足够 */
	if ($surplus > $user['user_money'] + $user['credit_line'])
	{
		$err->add($_LANG['error_surplus_not_enough']);
		$err->show($_LANG['order_detail'], 'user_c.php?act=order_detail&order_id=' . $order_id);
	}

	/* 修改订单，重新计算支付费用 */
	$order['surplus'] += $surplus;
	$order['order_amount'] -= $surplus;
	if ($order['order_amount'] > 0)
	{
		$cod_fee = 0;
		if ($order['shipping_id'] > 0)
		{
			$regions  = array($order['country'], $order['province'], $order['city'], $order['district']);
			$shipping = shipping_area_info($order['shipping_id'], $regions);
			if ($shipping['support_cod'] == '1')
			{
				$cod_fee = $shipping['pay_fee'];
			}
		}

		$pay_fee = 0;
		if ($order['pay_id'] > 0)
		{
			$pay_fee = pay_fee($order['pay_id'], $order['order_amount'], $cod_fee);
		}

		$order['pay_fee'] = $pay_fee;
		$order['order_amount'] += $pay_fee;
	}

	/* 如果全部支付，设为已确认、已付款 */
	if ($order['order_amount'] == 0)
	{
		if ($order['order_status'] == OS_UNCONFIRMED)
		{
			$order['order_status'] = OS_CONFIRMED;
			$order['confirm_time'] = gmtime();
		}
		$order['pay_status'] = PS_PAYED;
		$order['pay_time'] = gmtime();
	}
	$order = addslashes_deep($order);
	update_order($order_id, $order);

	/* 更新用户余额 */
	$change_desc = sprintf($_LANG['pay_order_by_surplus'], $order['order_sn']);
	log_account_change($user['user_id'], (-1) * $surplus, 0, 0, 0, $change_desc);

	/* 跳转 */
	ecs_header('Location: user_c.php?act=order_detail&order_id=' . $order_id . "\n");
	exit;
}

/* 编辑使用余额支付的处理 */
elseif ($action == 'act_edit_payment')
{
	/* 检查是否登录 */
	if ($_SESSION['user_id'] <= 0)
	{
		ecs_header("Location: ./\n");
		exit;
	}

	/* 检查支付方式 */
	$pay_id = intval($_POST['pay_id']);
	if ($pay_id <= 0)
	{
		ecs_header("Location: ./\n");
		exit;
	}

	include_once(ROOT_PATH . 'includes/lib_order.php');
	$payment_info = payment_info($pay_id);
	if (empty($payment_info))
	{
		ecs_header("Location: ./\n");
		exit;
	}

	/* 检查订单号 */
	$order_id = intval($_POST['order_id']);
	if ($order_id <= 0)
	{
		ecs_header("Location: ./\n");
		exit;
	}

	/* 取得订单 */
	$order = order_info($order_id);
	if (empty($order))
	{
		ecs_header("Location: ./\n");
		exit;
	}

	/* 检查订单用户跟当前用户是否一致 */
	if ($_SESSION['user_id'] != $order['user_id'])
	{
		ecs_header("Location: ./\n");
		exit;
	}

	/* 检查订单是否未付款和未发货 以及订单金额是否为0 和支付id是否为改变*/
	if ($order['pay_status'] != PS_UNPAYED || $order['shipping_status'] != SS_UNSHIPPED || $order['goods_amount'] <= 0 || $order['pay_id'] == $pay_id)
	{
		ecs_header("Location: user_c.php?act=order_detail&order_id=$order_id\n");
		exit;
	}

	$order_amount = $order['order_amount'] - $order['pay_fee'];
	$pay_fee = pay_fee($pay_id, $order_amount);
	$order_amount += $pay_fee;

	$sql = "UPDATE " . $ecs->table('order_info') .
           " SET pay_id='$pay_id', pay_name='$payment_info[pay_name]', pay_fee='$pay_fee', order_amount='$order_amount'".
           " WHERE order_id = '$order_id'";
	$db->query($sql);

	/* 跳转 */
	ecs_header("Location: user_c.php?act=order_detail&order_id=$order_id\n");
	exit;
}

/* 保存订单详情收货地址 */
elseif ($action == 'save_order_address')
{
	include_once(ROOT_PATH .'includes/lib_transaction.php');

	$address = array(
        'consignee' => isset($_POST['consignee']) ? compile_str(trim($_POST['consignee']))  : '',
        'email'     => isset($_POST['email'])     ? compile_str(trim($_POST['email']))      : '',
        'address'   => isset($_POST['address'])   ? compile_str(trim($_POST['address']))    : '',
        'zipcode'   => isset($_POST['zipcode'])   ? compile_str(make_semiangle(trim($_POST['zipcode']))) : '',
        'tel'       => isset($_POST['tel'])       ? compile_str(trim($_POST['tel']))        : '',
        'mobile'    => isset($_POST['mobile'])    ? compile_str(trim($_POST['mobile']))     : '',
        'sign_building' => isset($_POST['sign_building']) ? compile_str(trim($_POST['sign_building'])) : '',
        'best_time' => isset($_POST['best_time']) ? compile_str(trim($_POST['best_time']))  : '',
        'order_id'  => isset($_POST['order_id'])  ? intval($_POST['order_id']) : 0
	);
	if (save_order_address($address, $user_id))
	{
		ecs_header('Location: user_c.php?act=order_detail&order_id=' .$address['order_id']. "\n");
		exit;
	}
	else
	{
		$err->show($_LANG['order_list_lnk'], 'user_c.php?act=order_list');
	}
}

/* 我的红包列表 */
elseif ($action == 'bonus')
{
	include_once(ROOT_PATH .'includes/lib_transaction.php');

	$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
	$record_count = $db->getOne("SELECT COUNT(*) FROM " .$ecs->table('user_bonus'). " WHERE user_id = '$user_id'");

	$pager = get_pager('user_c.php', array('act' => $action), $record_count, $page);
	$bonus = get_user_bouns_list($user_id, $pager['size'], $pager['start']);

	$smarty->assign('pager', $pager);
	$smarty->assign('bonus', $bonus);
	$smarty->display('user_transaction.dwt');
}

/* 我的团购列表 */
elseif ($action == 'group_buy')
{
	include_once(ROOT_PATH .'includes/lib_transaction.php');

	//待议
	$smarty->display('user_transaction.dwt');
}

/* 团购订单详情 */
elseif ($action == 'group_buy_detail')
{
	include_once(ROOT_PATH .'includes/lib_transaction.php');

	//待议
	$smarty->display('user_transaction.dwt');
}

// 用户推荐页面
elseif ($action == 'affiliate')
{
	$goodsid = intval(isset($_REQUEST['goodsid']) ? $_REQUEST['goodsid'] : 0);
	if(empty($goodsid))
	{
		//我的推荐页面

		$page       = !empty($_REQUEST['page'])  && intval($_REQUEST['page'])  > 0 ? intval($_REQUEST['page'])  : 1;
		$size       = !empty($_CFG['page_size']) && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) : 10;

		empty($affiliate) && $affiliate = array();

		if(empty($affiliate['config']['separate_by']))
		{
			//推荐注册分成
			$affdb = array();
			$num = count($affiliate['item']);
			$up_uid = "'$user_id'";
			$all_uid = "'$user_id'";
			for ($i = 1 ; $i <=$num ;$i++)
			{
				$count = 0;
				if ($up_uid)
				{
					$sql = "SELECT user_id FROM " . $ecs->table('users') . " WHERE parent_id IN($up_uid)";
					$query = $db->query($sql);
					$up_uid = '';
					while ($rt = $db->fetch_array($query))
					{
						$up_uid .= $up_uid ? ",'$rt[user_id]'" : "'$rt[user_id]'";
						if($i < $num)
						{
							$all_uid .= ", '$rt[user_id]'";
						}
						$count++;
					}
				}
				$affdb[$i]['num'] = $count;
				$affdb[$i]['point'] = $affiliate['item'][$i-1]['level_point'];
				$affdb[$i]['money'] = $affiliate['item'][$i-1]['level_money'];
			}
			$smarty->assign('affdb', $affdb);

			$sqlcount = "SELECT count(*) FROM " . $ecs->table('order_info') . " o".
        " LEFT JOIN".$ecs->table('users')." u ON o.user_id = u.user_id".
        " LEFT JOIN " . $ecs->table('affiliate_log') . " a ON o.order_id = a.order_id" .
        " WHERE o.user_id > 0 AND (u.parent_id IN ($all_uid) AND o.is_separate = 0 OR a.user_id = '$user_id' AND o.is_separate > 0)";

			$sql = "SELECT o.*, a.log_id, a.user_id as suid,  a.user_name as auser, a.money, a.point, a.separate_type FROM " . $ecs->table('order_info') . " o".
                    " LEFT JOIN".$ecs->table('users')." u ON o.user_id = u.user_id".
                    " LEFT JOIN " . $ecs->table('affiliate_log') . " a ON o.order_id = a.order_id" .
        " WHERE o.user_id > 0 AND (u.parent_id IN ($all_uid) AND o.is_separate = 0 OR a.user_id = '$user_id' AND o.is_separate > 0)".
                    " ORDER BY order_id DESC" ;

			/*
			 SQL解释：

			 订单、用户、分成记录关联
			 一个订单可能有多个分成记录

			 1、订单有效 o.user_id > 0
			 2、满足以下之一：
			 a.直接下线的未分成订单 u.parent_id IN ($all_uid) AND o.is_separate = 0
			 其中$all_uid为该ID及其下线(不包含最后一层下线)
			 b.全部已分成订单 a.user_id = '$user_id' AND o.is_separate > 0

			 */

			$affiliate_intro = nl2br(sprintf($_LANG['affiliate_intro'][$affiliate['config']['separate_by']], $affiliate['config']['expire'], $_LANG['expire_unit'][$affiliate['config']['expire_unit']], $affiliate['config']['level_register_all'], $affiliate['config']['level_register_up'], $affiliate['config']['level_money_all'], $affiliate['config']['level_point_all']));
		}
		else
		{
			//推荐订单分成
			$sqlcount = "SELECT count(*) FROM " . $ecs->table('order_info') . " o".
                    " LEFT JOIN".$ecs->table('users')." u ON o.user_id = u.user_id".
                    " LEFT JOIN " . $ecs->table('affiliate_log') . " a ON o.order_id = a.order_id" .
                    " WHERE o.user_id > 0 AND (o.parent_id = '$user_id' AND o.is_separate = 0 OR a.user_id = '$user_id' AND o.is_separate > 0)";


			$sql = "SELECT o.*, a.log_id,a.user_id as suid, a.user_name as auser, a.money, a.point, a.separate_type,u.parent_id as up FROM " . $ecs->table('order_info') . " o".
                    " LEFT JOIN".$ecs->table('users')." u ON o.user_id = u.user_id".
                    " LEFT JOIN " . $ecs->table('affiliate_log') . " a ON o.order_id = a.order_id" .
                    " WHERE o.user_id > 0 AND (o.parent_id = '$user_id' AND o.is_separate = 0 OR a.user_id = '$user_id' AND o.is_separate > 0)" .
                    " ORDER BY order_id DESC" ;

			/*
			 SQL解释：

			 订单、用户、分成记录关联
			 一个订单可能有多个分成记录

			 1、订单有效 o.user_id > 0
			 2、满足以下之一：
			 a.订单下线的未分成订单 o.parent_id = '$user_id' AND o.is_separate = 0
			 b.全部已分成订单 a.user_id = '$user_id' AND o.is_separate > 0

			 */

			$affiliate_intro = nl2br(sprintf($_LANG['affiliate_intro'][$affiliate['config']['separate_by']], $affiliate['config']['expire'], $_LANG['expire_unit'][$affiliate['config']['expire_unit']], $affiliate['config']['level_money_all'], $affiliate['config']['level_point_all']));

		}

		$count = $db->getOne($sqlcount);

		$max_page = ($count> 0) ? ceil($count / $size) : 1;
		if ($page > $max_page)
		{
			$page = $max_page;
		}

		$res = $db->SelectLimit($sql, $size, ($page - 1) * $size);
		$logdb = array();
		while ($rt = $GLOBALS['db']->fetchRow($res))
		{
			if(!empty($rt['suid']))
			{
				//在affiliate_log有记录
				if($rt['separate_type'] == -1 || $rt['separate_type'] == -2)
				{
					//已被撤销
					$rt['is_separate'] = 3;
				}
			}
			$rt['order_sn'] = substr($rt['order_sn'], 0, strlen($rt['order_sn']) - 5) . "***" . substr($rt['order_sn'], -2, 2);
			$logdb[] = $rt;
		}

		$url_format = "user_c.php?act=affiliate&page=";

		$pager = array(
                    'page'  => $page,
                    'size'  => $size,
                    'sort'  => '',
                    'order' => '',
                    'record_count' => $count,
                    'page_count'   => $max_page,
                    'page_first'   => $url_format. '1',
                    'page_prev'    => $page > 1 ? $url_format.($page - 1) : "javascript:;",
                    'page_next'    => $page < $max_page ? $url_format.($page + 1) : "javascript:;",
                    'page_last'    => $url_format. $max_page,
                    'array'        => array()
		);
		for ($i = 1; $i <= $max_page; $i++)
		{
			$pager['array'][$i] = $i;
		}

		$smarty->assign('url_format', $url_format);
		$smarty->assign('pager', $pager);


		$smarty->assign('affiliate_intro', $affiliate_intro);
		$smarty->assign('affiliate_type', $affiliate['config']['separate_by']);

		$smarty->assign('logdb', $logdb);
	}
	else
	{
		//单个商品推荐
		$smarty->assign('userid', $user_id);
		$smarty->assign('goodsid', $goodsid);

		$types = array(1,2,3,4,5);
		$smarty->assign('types', $types);

		$goods = get_goods_info($goodsid);
		$shopurl = $ecs->url();
		$goods['goods_img'] = (strpos($goods['goods_img'], 'http://') === false && strpos($goods['goods_img'], 'https://') === false) ? $shopurl . $goods['goods_img'] : $goods['goods_img'];
		$goods['goods_thumb'] = (strpos($goods['goods_thumb'], 'http://') === false && strpos($goods['goods_thumb'], 'https://') === false) ? $shopurl . $goods['goods_thumb'] : $goods['goods_thumb'];
		$goods['shop_price'] = price_format($goods['shop_price']);

		$smarty->assign('goods', $goods);
	}

	$smarty->assign('shopname', $_CFG['shop_name']);
	$smarty->assign('userid', $user_id);
	$smarty->assign('shopurl', $ecs->url());
	$smarty->assign('logosrc', 'themes/' . $_CFG['template'] . '/images/logo.gif');

	$smarty->display('user_clips.dwt');
}

//首页邮件订阅ajax操做和验证操作
elseif ($action =='email_list')
{
	$job = $_GET['job'];

	if($job == 'add' || $job == 'del')
	{
		if(isset($_SESSION['last_email_query']))
		{
			if(time() - $_SESSION['last_email_query'] <= 30)
			{
				die($_LANG['order_query_toofast']);
			}
		}
		$_SESSION['last_email_query'] = time();
	}

	$email = trim($_GET['email']);
	$email = htmlspecialchars($email);

	if (!is_email($email))
	{
		$info = sprintf($_LANG['email_invalid'], $email);
		die($info);
	}
	$ck = $db->getRow("SELECT * FROM " . $ecs->table('email_list') . " WHERE email = '$email'");
	if ($job == 'add')
	{
		if (empty($ck))
		{
			$hash = substr(md5(time()), 1, 10);
			$sql = "INSERT INTO " . $ecs->table('email_list') . " (email, stat, hash) VALUES ('$email', 0, '$hash')";
			$db->query($sql);
			$info = $_LANG['email_check'];
			$url = $ecs->url() . "user_c.php?act=email_list&job=add_check&hash=$hash&email=$email";
			send_mail('', $email, $_LANG['check_mail'], sprintf($_LANG['check_mail_content'], $email, $_CFG['shop_name'], $url, $url, $_CFG['shop_name'], local_date('Y-m-d')), 1);
		}
		elseif ($ck['stat'] == 1)
		{
			$info = sprintf($_LANG['email_alreadyin_list'], $email);
		}
		else
		{
			$hash = substr(md5(time()),1 , 10);
			$sql = "UPDATE " . $ecs->table('email_list') . "SET hash = '$hash' WHERE email = '$email'";
			$db->query($sql);
			$info = $_LANG['email_re_check'];
			$url = $ecs->url() . "user_c.php?act=email_list&job=add_check&hash=$hash&email=$email";
			send_mail('', $email, $_LANG['check_mail'], sprintf($_LANG['check_mail_content'], $email, $_CFG['shop_name'], $url, $url, $_CFG['shop_name'], local_date('Y-m-d')), 1);
		}
		die($info);
	}
	elseif ($job == 'del')
	{
		if (empty($ck))
		{
			$info = sprintf($_LANG['email_notin_list'], $email);
		}
		elseif ($ck['stat'] == 1)
		{
			$hash = substr(md5(time()),1,10);
			$sql = "UPDATE " . $ecs->table('email_list') . "SET hash = '$hash' WHERE email = '$email'";
			$db->query($sql);
			$info = $_LANG['email_check'];
			$url = $ecs->url() . "user_c.php?act=email_list&job=del_check&hash=$hash&email=$email";
			send_mail('', $email, $_LANG['check_mail'], sprintf($_LANG['check_mail_content'], $email, $_CFG['shop_name'], $url, $url, $_CFG['shop_name'], local_date('Y-m-d')), 1);
		}
		else
		{
			$info = $_LANG['email_not_alive'];
		}
		die($info);
	}
	elseif ($job == 'add_check')
	{
		if (empty($ck))
		{
			$info = sprintf($_LANG['email_notin_list'], $email);
		}
		elseif ($ck['stat'] == 1)
		{
			$info = $_LANG['email_checked'];
		}
		else
		{
			if ($_GET['hash'] == $ck['hash'])
			{
				$sql = "UPDATE " . $ecs->table('email_list') . "SET stat = 1 WHERE email = '$email'";
				$db->query($sql);
				$info = $_LANG['email_checked'];
			}
			else
			{
				$info = $_LANG['hash_wrong'];
			}
		}
		show_message_c($info, $_LANG['back_home_lnk'], 'index.php');
	}
	elseif ($job == 'del_check')
	{
		if (empty($ck))
		{
			$info = sprintf($_LANG['email_invalid'], $email);
		}
		elseif ($ck['stat'] == 1)
		{
			if ($_GET['hash'] == $ck['hash'])
			{
				$sql = "DELETE FROM " . $ecs->table('email_list') . "WHERE email = '$email'";
				$db->query($sql);
				$info = $_LANG['email_canceled'];
			}
			else
			{
				$info = $_LANG['hash_wrong'];
			}
		}
		else
		{
			$info = $_LANG['email_not_alive'];
		}
		show_message_c($info, $_LANG['back_home_lnk'], 'index.php');
	}
}

/* ajax 发送验证邮件 */
elseif ($action == 'send_hash_mail')
{
	include_once(ROOT_PATH .'includes/cls_json.php');
	include_once(ROOT_PATH .'includes/lib_passport.php');
	$json = new JSON();

	$result = array('error' => 0, 'message' => '', 'content' => '');

	if ($user_id == 0)
	{
		/* 用户没有登录 */
		$result['error']   = 1;
		$result['message'] = $_LANG['login_please'];
		die($json->encode($result));
	}

	if (send_regiter_hash($user_id))
	{
		$result['message'] = $_LANG['validate_mail_ok'];
		die($json->encode($result));
	}
	else
	{
		$result['error'] = 1;
		$result['message'] = $GLOBALS['err']->last_message();
	}

	die($json->encode($result));
}
else if ($action == 'track_packages')
{
	include_once(ROOT_PATH . 'includes/lib_transaction.php');
	include_once(ROOT_PATH .'includes/lib_order.php');

	$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

	$orders = array();

	$sql = "SELECT order_id,order_sn,invoice_no,shipping_id FROM " .$ecs->table('order_info').
            " WHERE user_id = '$user_id' AND shipping_status = '" . SS_SHIPPED . "'";
	$res = $db->query($sql);
	$record_count = 0;
	while ($item = $db->fetch_array($res))
	{
		$shipping   = get_shipping_object($item['shipping_id']);

		if (method_exists ($shipping, 'query'))
		{
			$query_link = $shipping->query($item['invoice_no']);
		}
		else
		{
			$query_link = $item['invoice_no'];
		}

		if ($query_link != $item['invoice_no'])
		{
			$item['query_link'] = $query_link;
			$orders[]  = $item;
			$record_count += 1;
		}
	}
	$pager  = get_pager('user_c.php', array('act' => $action), $record_count, $page);
	$smarty->assign('pager',  $pager);
	$smarty->assign('orders', $orders);
	$smarty->display('user_transaction.dwt');
}
else if ($action == 'order_query')
{
	$_GET['order_sn'] = trim(substr($_GET['order_sn'], 1));
	$order_sn = empty($_GET['order_sn']) ? '' : addslashes($_GET['order_sn']);
	include_once(ROOT_PATH .'includes/cls_json.php');
	$json = new JSON();

	$result = array('error'=>0, 'message'=>'', 'content'=>'');

	if(isset($_SESSION['last_order_query']))
	{
		if(time() - $_SESSION['last_order_query'] <= 10)
		{
			$result['error'] = 1;
			$result['message'] = $_LANG['order_query_toofast'];
			die($json->encode($result));
		}
	}
	$_SESSION['last_order_query'] = time();

	if (empty($order_sn))
	{
		$result['error'] = 1;
		$result['message'] = $_LANG['invalid_order_sn'];
		die($json->encode($result));
	}

	$sql = "SELECT order_id, order_status, shipping_status, pay_status, ".
           " shipping_time, shipping_id, invoice_no, user_id ".
           " FROM " . $ecs->table('order_info').
           " WHERE order_sn = '$order_sn' LIMIT 1";

	$row = $db->getRow($sql);
	if (empty($row))
	{
		$result['error'] = 1;
		$result['message'] = $_LANG['invalid_order_sn'];
		die($json->encode($result));
	}

	$order_query = array();
	$order_query['order_sn'] = $order_sn;
	$order_query['order_id'] = $row['order_id'];
	$order_query['order_status'] = $_LANG['os'][$row['order_status']] . ',' . $_LANG['ps'][$row['pay_status']] . ',' . $_LANG['ss'][$row['shipping_status']];

	if ($row['invoice_no'] && $row['shipping_id'] > 0)
	{
		$sql = "SELECT shipping_code FROM " . $ecs->table('shipping') . " WHERE shipping_id = '$row[shipping_id]'";
		$shipping_code = $db->getOne($sql);
		$plugin = ROOT_PATH . 'includes/modules/shipping/' . $shipping_code . '.php';
		if (file_exists($plugin))
		{
			include_once($plugin);
			$shipping = new $shipping_code;
			$order_query['invoice_no'] = $shipping->query((string)$row['invoice_no']);
		}
		else
		{
			$order_query['invoice_no'] = (string)$row['invoice_no'];
		}
	}

	$order_query['user_id'] = $row['user_id'];
	/* 如果是匿名用户显示发货时间 */
	if ($row['user_id'] == 0 && $row['shipping_time'] > 0)
	{
		$order_query['shipping_date'] = local_date($GLOBALS['_CFG']['date_format'], $row['shipping_time']);
	}
	$smarty->assign('order_query',    $order_query);
	$result['content'] = $smarty->fetch('library/order_query.lbi');
	die($json->encode($result));
}
elseif ($action == 'transform_points')
{
	$rule = array();
	if (!empty($_CFG['points_rule']))
	{
		$rule = unserialize($_CFG['points_rule']);
	}
	$cfg = array();
	if (!empty($_CFG['integrate_config']))
	{
		$cfg = unserialize($_CFG['integrate_config']);
		$_LANG['exchange_points'][0] = empty($cfg['uc_lang']['credits'][0][0])? $_LANG['exchange_points'][0] : $cfg['uc_lang']['credits'][0][0];
		$_LANG['exchange_points'][1] = empty($cfg['uc_lang']['credits'][1][0])? $_LANG['exchange_points'][1] : $cfg['uc_lang']['credits'][1][0];
	}
	$sql = "SELECT user_id, user_name, pay_points, rank_points FROM " . $ecs->table('users')  . " WHERE user_id='$user_id'";
	$row = $db->getRow($sql);
	if ($_CFG['integrate_code'] == 'ucenter')
	{
		$exchange_type = 'ucenter';
		$to_credits_options = array();
		$out_exchange_allow = array();
		foreach ($rule as $credit)
		{
			$out_exchange_allow[$credit['appiddesc'] . '|' . $credit['creditdesc'] . '|' . $credit['creditsrc']] = $credit['ratio'];
			if (!array_key_exists($credit['appiddesc']. '|' .$credit['creditdesc'], $to_credits_options))
			{
				$to_credits_options[$credit['appiddesc']. '|' .$credit['creditdesc']] = $credit['title'];
			}
		}
		$smarty->assign('selected_org', $rule[0]['creditsrc']);
		$smarty->assign('selected_dst', $rule[0]['appiddesc']. '|' .$rule[0]['creditdesc']);
		$smarty->assign('descreditunit', $rule[0]['unit']);
		$smarty->assign('orgcredittitle', $_LANG['exchange_points'][$rule[0]['creditsrc']]);
		$smarty->assign('descredittitle', $rule[0]['title']);
		$smarty->assign('descreditamount', round((1 / $rule[0]['ratio']), 2));
		$smarty->assign('to_credits_options', $to_credits_options);
		$smarty->assign('out_exchange_allow', $out_exchange_allow);
	}
	else
	{
		$exchange_type = 'other';

		$bbs_points_name = $user->get_points_name();
		$total_bbs_points = $user->get_points($row['user_name']);

		/* 论坛积分 */
		$bbs_points = array();
		foreach ($bbs_points_name as $key=>$val)
		{
			$bbs_points[$key] = array('title'=>$_LANG['bbs'] . $val['title'], 'value'=>$total_bbs_points[$key]);
		}

		/* 兑换规则 */
		$rule_list = array();
		foreach ($rule as $key=>$val)
		{
			$rule_key = substr($key, 0, 1);
			$bbs_key = substr($key, 1);
			$rule_list[$key]['rate'] = $val;
			switch ($rule_key)
			{
				case TO_P :
					$rule_list[$key]['from'] = $_LANG['bbs'] . $bbs_points_name[$bbs_key]['title'];
					$rule_list[$key]['to'] = $_LANG['pay_points'];
					break;
				case TO_R :
					$rule_list[$key]['from'] = $_LANG['bbs'] . $bbs_points_name[$bbs_key]['title'];
					$rule_list[$key]['to'] = $_LANG['rank_points'];
					break;
				case FROM_P :
					$rule_list[$key]['from'] = $_LANG['pay_points'];$_LANG['bbs'] . $bbs_points_name[$bbs_key]['title'];
					$rule_list[$key]['to'] =$_LANG['bbs'] . $bbs_points_name[$bbs_key]['title'];
					break;
				case FROM_R :
					$rule_list[$key]['from'] = $_LANG['rank_points'];
					$rule_list[$key]['to'] = $_LANG['bbs'] . $bbs_points_name[$bbs_key]['title'];
					break;
			}
		}
		$smarty->assign('bbs_points', $bbs_points);
		$smarty->assign('rule_list',  $rule_list);
	}
	$smarty->assign('shop_points', $row);
	$smarty->assign('exchange_type',     $exchange_type);
	$smarty->assign('action',     $action);
	$smarty->assign('lang',       $_LANG);
	$smarty->display('user_transaction.dwt');
}
elseif ($action == 'act_transform_points')
{
	$rule_index = empty($_POST['rule_index']) ? '' : trim($_POST['rule_index']);
	$num = empty($_POST['num']) ? 0 : intval($_POST['num']);


	if ($num <= 0 || $num != floor($num))
	{
		show_message_c($_LANG['invalid_points'], $_LANG['transform_points'], 'user_c.php?act=transform_points');
	}

	$num = floor($num); //格式化为整数

	$bbs_key = substr($rule_index, 1);
	$rule_key = substr($rule_index, 0, 1);

	$max_num = 0;

	/* 取出用户数据 */
	$sql = "SELECT user_name, user_id, pay_points, rank_points FROM " . $ecs->table('users') . " WHERE user_id='$user_id'";
	$row = $db->getRow($sql);
	$bbs_points = $user->get_points($row['user_name']);
	$points_name = $user->get_points_name();

	$rule = array();
	if ($_CFG['points_rule'])
	{
		$rule = unserialize($_CFG['points_rule']);
	}
	list($from, $to) = explode(':', $rule[$rule_index]);

	$max_points = 0;
	switch ($rule_key)
	{
		case TO_P :
			$max_points = $bbs_points[$bbs_key];
			break;
		case TO_R :
			$max_points = $bbs_points[$bbs_key];
			break;
		case FROM_P :
			$max_points = $row['pay_points'];
			break;
		case FROM_R :
			$max_points = $row['rank_points'];
	}

	/* 检查积分是否超过最大值 */
	if ($max_points <=0 || $num > $max_points)
	{
		show_message_c($_LANG['overflow_points'], $_LANG['transform_points'], 'user_c.php?act=transform_points' );
	}

	switch ($rule_key)
	{
		case TO_P :
			$result_points = floor($num * $to / $from);
			$user->set_points($row['user_name'], array($bbs_key=>0 - $num)); //调整论坛积分
			log_account_change($row['user_id'], 0, 0, 0, $result_points, $_LANG['transform_points'], ACT_OTHER);
			show_message_c(sprintf($_LANG['to_pay_points'],  $num, $points_name[$bbs_key]['title'], $result_points), $_LANG['transform_points'], 'user_c.php?act=transform_points');

		case TO_R :
			$result_points = floor($num * $to / $from);
			$user->set_points($row['user_name'], array($bbs_key=>0 - $num)); //调整论坛积分
			log_account_change($row['user_id'], 0, 0, $result_points, 0, $_LANG['transform_points'], ACT_OTHER);
			show_message_c(sprintf($_LANG['to_rank_points'], $num, $points_name[$bbs_key]['title'], $result_points), $_LANG['transform_points'], 'user_c.php?act=transform_points');

		case FROM_P :
			$result_points = floor($num * $to / $from);
			log_account_change($row['user_id'], 0, 0, 0, 0-$num, $_LANG['transform_points'], ACT_OTHER); //调整商城积分
			$user->set_points($row['user_name'], array($bbs_key=>$result_points)); //调整论坛积分
			show_message_c(sprintf($_LANG['from_pay_points'], $num, $result_points,  $points_name[$bbs_key]['title']), $_LANG['transform_points'], 'user_c.php?act=transform_points');

		case FROM_R :
			$result_points = floor($num * $to / $from);
			log_account_change($row['user_id'], 0, 0, 0-$num, 0, $_LANG['transform_points'], ACT_OTHER); //调整商城积分
			$user->set_points($row['user_name'], array($bbs_key=>$result_points)); //调整论坛积分
			show_message_c(sprintf($_LANG['from_rank_points'], $num, $result_points, $points_name[$bbs_key]['title']), $_LANG['transform_points'], 'user_c.php?act=transform_points');
	}
}
elseif ($action == 'act_transform_ucenter_points')
{
	$rule = array();
	if ($_CFG['points_rule'])
	{
		$rule = unserialize($_CFG['points_rule']);
	}
	$shop_points = array(0 => 'rank_points', 1 => 'pay_points');
	$sql = "SELECT user_id, user_name, pay_points, rank_points FROM " . $ecs->table('users')  . " WHERE user_id='$user_id'";
	$row = $db->getRow($sql);
	$exchange_amount = intval($_POST['amount']);
	$fromcredits = intval($_POST['fromcredits']);
	$tocredits = trim($_POST['tocredits']);
	$cfg = unserialize($_CFG['integrate_config']);
	if (!empty($cfg))
	{
		$_LANG['exchange_points'][0] = empty($cfg['uc_lang']['credits'][0][0])? $_LANG['exchange_points'][0] : $cfg['uc_lang']['credits'][0][0];
		$_LANG['exchange_points'][1] = empty($cfg['uc_lang']['credits'][1][0])? $_LANG['exchange_points'][1] : $cfg['uc_lang']['credits'][1][0];
	}
	list($appiddesc, $creditdesc) = explode('|', $tocredits);
	$ratio = 0;

	if ($exchange_amount <= 0)
	{
		show_message_c($_LANG['invalid_points'], $_LANG['transform_points'], 'user_c.php?act=transform_points');
	}
	if ($exchange_amount > $row[$shop_points[$fromcredits]])
	{
		show_message_c($_LANG['overflow_points'], $_LANG['transform_points'], 'user_c.php?act=transform_points');
	}
	foreach ($rule as $credit)
	{
		if ($credit['appiddesc'] == $appiddesc && $credit['creditdesc'] == $creditdesc && $credit['creditsrc'] == $fromcredits)
		{
			$ratio = $credit['ratio'];
			break;
		}
	}
	if ($ratio == 0)
	{
		show_message_c($_LANG['exchange_deny'], $_LANG['transform_points'], 'user_c.php?act=transform_points');
	}
	$netamount = floor($exchange_amount / $ratio);
	include_once(ROOT_PATH . './includes/lib_uc.php');
	$result = exchange_points($row['user_id'], $fromcredits, $creditdesc, $appiddesc, $netamount);
	if ($result === true)
	{
		$sql = "UPDATE " . $ecs->table('users') . " SET {$shop_points[$fromcredits]}={$shop_points[$fromcredits]}-'$exchange_amount' WHERE user_id='{$row['user_id']}'";
		$db->query($sql);
		$sql = "INSERT INTO " . $ecs->table('account_log') . "(user_id, {$shop_points[$fromcredits]}, change_time, change_desc, change_type)" . " VALUES ('{$row['user_id']}', '-$exchange_amount', '". gmtime() ."', '" . $cfg['uc_lang']['exchange'] . "', '98')";
		$db->query($sql);
		show_message_c(sprintf($_LANG['exchange_success'], $exchange_amount, $_LANG['exchange_points'][$fromcredits], $netamount, $credit['title']), $_LANG['transform_points'], 'user_c.php?act=transform_points');
	}
	else
	{
		show_message_c($_LANG['exchange_error_1'], $_LANG['transform_points'], 'user_c.php?act=transform_points');
	}
}
/* 清除商品浏览历史 */
elseif ($action == 'clear_history')
{
	setcookie('ECS[history]',   '', 1);
}

/*我的小伙伴*/
elseif ($action == 'weixin_fans')
{

	$page_arr=array();
	$page_arr['act'] = $action;

	$where = '1';
	$where .= ' and parent_id = '.$_SESSION['user_id'];
	$where_con = 'parent_id = '.$_SESSION['user_id'];
	//日期搜索
	if($_GET['start_time'] || $_GET['end_time']){
		$start_time = ($_GET['start_time'])?strtotime($_GET['start_time']):0;

		$end_time = ($_GET['end_time'])?strtotime($_GET['end_time'])+86400:time();

		if($start_time<=$end_time){
			$where .= ' and reg_time>='.$start_time.' and reg_time<='.$end_time;

			$page_arr['start_time'] = $_GET['start_time'];
			$page_arr['end_time'] = $_GET['end_time'];
		}
	}

	//会员等级搜索

	if($_GET['user_rank']){
		if($_GET['user_rank']=='pthy'){
			$user_rank = 0;
		}else{
			$user_rank = intval($_GET['user_rank']);
		}
		$where .= ' and user_rank = '.$user_rank;
		$page_arr['user_rank'] = $_GET['user_rank'];
	}

	//会员名称搜索
	if($_GET['user_name']){
		$where .= ' and concat(`user_name`,`nickname`) like "%'.$_GET['user_name'].'%"';
		$page_arr['user_name'] = $_GET['user_name'];
	}


	/* 获得当前页码 */
	//print_r($_SESSION);
	$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
	$sql_zj3 = "SELECT COUNT(*) FROM " .$ecs->table('users'). " WHERE ".$where;
	$record_count = $db->getOne($sql_zj3);


	//会员总数
	$sql_21 = "SELECT COUNT(*) FROM " .$ecs->table('users'). " WHERE ".$where_con;
	$num_21 = $db->getOne($sql_21);
	$smarty->assign('num_21',  $num_21);

	//加盟商总数
	if($_SESSION['user_rank']==103){
		$where_22 = $where_con.' and user_rank = 102';
		$sql_22 = "SELECT COUNT(*) FROM " .$ecs->table('users'). " WHERE ".$where_22;
		$num_22 = $db->getOne($sql_22);
		$smarty->assign('num_22',  $num_22);
	}

	//vip会员数
	$where_23 = $where_con.' and user_rank = 99';
	$sql_23 = "SELECT COUNT(*) FROM " .$ecs->table('users'). " WHERE ".$where_23;
	$num_23 = $db->getOne($sql_23);
	$smarty->assign('num_23',  $num_23);

	//普通会员数
	$where_24 = $where_con.' and user_rank = 0';
	$sql_24 = "SELECT COUNT(*) FROM " .$ecs->table('users'). " WHERE ".$where_24;
	$num_24 = $db->getOne($sql_24);
	$smarty->assign('num_24',  $num_24);










	$pager  = get_pager('user_c.php', $page_arr, $record_count, $page,'10');

	$sql_zj4 = "SELECT * FROM " .$ecs->table('users'). " WHERE ".$where." order by reg_time desc";

	$res = $db->SelectLimit($sql_zj4, $pager['size'], $pager['start']);

	if ($res !== false)
	{
		$arr = array();
		while ($row = mysql_fetch_assoc($res))
		{
			$arr[] = $row;
		}

	}else{
		$arr = false;
	}

	foreach($arr as &$v){
		$sql_zj5 = "SELECT * FROM " .$ecs->table('weixin_user'). " WHERE  ecuid = '".$v['wxid']."'";
		$re_1 = $db->getRow($sql_zj5);
		$v['wx'] = $re_1;

		$sql_zj_v = "SELECT COUNT(*) FROM " .$ecs->table('users'). " WHERE parent_id = ".$v['user_id'];
		$v['num_fans'] = $db->getOne($sql_zj_v);

		$v['reg_time'] = date('Y-m-d',$v['reg_time']);
	}





	$smarty->assign('act_self',  $action);
	$smarty->assign('user_rank',  $_SESSION['user_rank']);

	$smarty->assign('pager',  $pager);
	$smarty->assign('arr', $arr);
	$smarty->display('weixin_fans1.dwt');
}
/*修改会员名称*/
elseif ($action == 'user_name'){

	$id = $_GET['id'];
	$user_name = $_GET['user_name'];
	$res = $GLOBALS['db']->getAll("select * from".$ecs->table('users')." where user_name ='".$user_name."'");
	if($res){
		echo "用户名已存在！";
		header("Refresh:1;user_c.php?act=weixin_fans");
		exit;
	}else{
		$sql1 = "update ". $ecs->table('users') . " set `user_name`='$user_name' where user_id=".$id;
		$db->query($sql1);
	}
	header('location:user_c.php?act=weixin_fans');
}

/*我的收益*/
elseif ($action == 'my_income')
{

	$page_arr=array();
	$page_arr['act'] = $action;

	$where = '1';
	$where .= ' and order_id != "" and user_id='.$_SESSION['user_id'];
	$where_con = 'user_id='.$_SESSION['user_id'];

	//日期搜索
	if($_GET['start_time'] || $_GET['end_time']){
		$start_time = ($_GET['start_time'])?strtotime($_GET['start_time']):0;

		$end_time = ($_GET['end_time'])?strtotime($_GET['end_time'])+86400:time();

		if($start_time<=$end_time){
			$where .= ' and change_time>='.$start_time.' and change_time<='.$end_time;

			$page_arr['start_time'] = $_GET['start_time'];
			$page_arr['end_time'] = $_GET['end_time'];
		}
	}
    //会员名称搜索
    if($_GET['user_name']){

    }
	/* 获得当前页码 */
	$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
	
	$sql_zj3 = "SELECT COUNT(*) FROM " .$ecs->table('account_log'). " WHERE ".$where;
	$record_count = $db->getOne($sql_zj3);
	
	$pager  = get_pager('user_c.php', $page_arr, $record_count, $page,'10');

	$sql1_1 = "select * from ". $ecs->table('account_log') ." where ".$where;
	$res_1 = $db->SelectLimit($sql1_1, $pager['size'], $pager['start']);
	
	if ($res_1 !== false)
	{
		$arr_1 = array();
		while ($row = mysql_fetch_assoc($res_1))
		{
			$arr_1[] = $row;
		}

	}else{
		$arr_1 = false;
	}

	foreach ($arr_1 as $key => &$value_1) {
		$order_id_1 = $value_1['order_id'];
		$res1_111 = $db->getRow("select * from". $ecs->table('order_info') . "  where order_id='".$order_id_1."'");
		$user_id_1 = $value_1['user_id'];
		$username_1 = $db->getone("select `user_name` from". $ecs->table('users') . "  where user_id='".$user_id_1."'");
		$value_1['add_time'] = date('Y-m-d',$res1_111['add_time']);
		$value_1['order_status'] = $res1_111['order_status'];
		$value_1['goods_amount'] = $res1_111['goods_amount'];
		$value_1['shipping_status'] = $res1_111['shipping_status'];
		$value_1['pay_status'] = $res1_111['pay_status'];
		$value_1['is_gain_rebate'] = $res1_111['is_gain_rebate'];
		$value_1['order_sn'] = $res1_111['order_sn'];
		$value_1['username'] = $username_1;
	}

	$smarty->assign('arr', $arr_1);
	
	$sql1 = "select * from ". $ecs->table('account_log') ." where ".$where_con;
	$res = $db->getAll($sql1);
	foreach ($res as $key => $value) {
		$order_id = $value['order_id'];
		if(!empty($order_id)){
			$res1 = $db->getRow("select * from". $ecs->table('order_info') . "  where order_id='".$order_id."'");
			$user_id = $res1['user_id'];
			$username = $db->getone("select `user_name` from". $ecs->table('users') . "  where user_id='".$user_id."'");
			$value['add_time'] = date('Y-m-d',$res1['add_time']);
			$value['order_status'] = $res1['order_status'];
			$value['goods_amount'] = $res1['goods_amount'];
			$value['shipping_status'] = $res1['shipping_status'];
			$value['pay_status'] = $res1['pay_status'];
			$value['is_gain_rebate'] = $res1['is_gain_rebate'];
			$value['order_sn'] = $res1['order_sn'];
			$value['username'] = $username;
			$money += $value['user_money'];
			// $total_money += $value['goods_amount'];
			$count[] = $value['order_id'];
		}
	}
	$arr = array();
	foreach ($res as $key => $value) {
		$order_id = $value['order_id'];		
		$arr[] = $order_id;
	}
	$arr = array_unique($arr);
	foreach ($arr as $k => $v) {
		$result = $db->getone("select `goods_amount` from". $ecs->table('order_info') . "  where order_id='".$v."'");
		$total_money += $result;
	}

	$count = count(array_unique($count));
	$smarty -> assign('money',$money);
	$smarty -> assign('total_money',$total_money);
	$smarty -> assign('count',$count);

	$smarty->assign('act_self',  $action);
	$smarty->assign('pager',  $pager);
	$smarty->display('weixin_fans2.dwt');
}



/*我发展的会员消费订单*/
elseif ($action == 'order_list_c')
{
	include_once(ROOT_PATH . 'includes/lib_transaction.php');

	$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

	$page_arr_order = array();
	$page_arr_order['act'] = $action;


	$user_id_c = intval($_REQUEST['user_id_c']);
	$page_arr_order['user_id_c'] = $user_id_c;

	$where_order = "1";
	$where_order .= " and user_id = ".$user_id_c;

	//print_r($where_order);

	//日期搜索
	if($_GET['start_time']&&$_GET['end_time']){
		$start_time = strtotime($_GET['start_time']);
		$end_time = strtotime($_GET['end_time'])+86400;
		if($start_time<=$end_time){
			$where_order .= ' and add_time>='.$start_time.' and add_time<='.$end_time;
			$page_arr_order['start_time'] = $_GET['start_time'];
			$page_arr_order['end_time'] = $_GET['end_time'];
		}
		//print_r($start_time);
		//print_r($end_time);
	}

	//print_r($where_order);

	//会员等级搜索

	if($_GET['shipping_status']){
		if($_GET['shipping_status']=='fhzt'){
			$shipping_status = 0;
		}else{
			$shipping_status = intval($_GET['shipping_status']);
		}
		$where_order .= ' and shipping_status = '.$shipping_status;
		$page_arr_order['shipping_status'] = $shipping_status;
	}


	$record_count = $db->getOne("SELECT COUNT(*) FROM " .$ecs->table('order_info'). " WHERE $where_order");
	//订单总数
	$smarty->assign('order_num_4',  $record_count);

	if($_GET['show_name']){
		$show_name = $_GET['show_name'];
	}else{
		$show_name = '';
	}
	$page_arr_order['show_name'] = $show_name;


	//成交订单数
	$orrder_where_1 = "user_id = '$user_id_c' and order_status = 5 and shipping_status = 2 and pay_status = 2";
	$order_sql_1= "SELECT COUNT(*) FROM " .$ecs->table('order_info'). " WHERE ".$orrder_where_1;
	$order_num_1 = $db->getOne($order_sql_1);
	$smarty->assign('order_num_1',  $order_num_1);

	$order_sql_2 = "SELECT gain_rebate_money,(goods_amount + shipping_fee + insure_fee + pay_fee + pack_fee + card_fee + tax - discount) AS total_fee FROM " .$ecs->table('order_info'). " WHERE ".$orrder_where_1;
	$order_arr = $db->getAll($order_sql_2);
	//print_r($order_arr);
	//成交金额
	$order_num_2 = 0;
	//收益金额
	$order_num_3 = 0;
	foreach($order_arr as $order_v){
		$order_num_2 += $order_v['total_fee'];
		$order_num_3 += $order_v['gain_rebate_money'];
	}
	$smarty->assign('order_num_2',  $order_num_2);
	$smarty->assign('order_num_3',  $order_num_3);



	$pager  = get_pager('user_c.php',$page_arr_order, $record_count, $page,'10');

	//print_r($pager);
	//var_dump($user_id);
	$orders = get_user_orders_c($user_id_c, $pager['size'], $pager['start'],$where_order);
	foreach ($orders as $key => $value) {
		$order_id = $value['order_id'];
		$res = $db->getAll('SELECT * FROM ' . $ecs->table('account_log') . ' WHERE order_id ='.$order_id.' and user_id='.$user_id ) ;
		if($res){
			$orders[$key]['fenli'] = $res;
		}

	}


	$smarty->assign('user_rank',  $_SESSION['user_rank']);
	$smarty->assign('user_id_c',  $user_id_c);
	$smarty->assign('act_self',  $action);
	$smarty->assign('show_name',  $show_name);
	$smarty->assign('pager',  $pager);
	$smarty->assign('orders', $orders);
	//var_dump($orders);

	//print_r($orders);

	$smarty->display('user_transaction_c.dwt');
}
/*获取返利*/
//elseif ($action == 'gain_rebate')
//{
//	$user_id_g = $_SESSION['user_id'];
//	$rank = $_SESSION['user_rank'];
//
//	$sql_zj6 = "SELECT * FROM " .$ecs->table('users'). " WHERE  parent_id = ".$user_id_g;
//	$res_g = $db->getAll($sql_zj6);
//
//	if($rank==102){
//		$sql_11 = "SELECT `value` FROM " . $ecs->table('affiliate_config') . " WHERE id = 1";
//	}elseif($rank==103){
//		$sql_11 = "SELECT `value` FROM " . $ecs->table('affiliate_config') . " WHERE id = 2";
//	}else{
//		show_message_c('您不能获取返利','返回上一页','user_c.php?act=weixin_fans','error');
//		exit();
//	}
//	$value = $db->getOne($sql_11);
//
//	$value_1 = $value/100;
//	$money_con=0;//放进users表的余额里面
//	if($res_g){
//		foreach($res_g as $v){
//			$sql_zj7 = "SELECT order_id,(goods_amount + shipping_fee + insure_fee + pay_fee + pack_fee + card_fee + tax - discount) AS total_fee FROM " .$ecs->table('order_info'). " WHERE  user_id = ".$v['user_id']." and is_gain_rebate != 1 and order_status = 5 and shipping_status = 2 and pay_status = 2";
//			$res_g_1 = $db->getAll($sql_zj7);
//				if($res_g_1){
//					foreach($res_g_1 as $vv){
//						$money_con_2=0;//放在订单表里面作为提现依据
//						$money_con_2 = floatval($vv['total_fee']*$value_1);
//						$money_con_2 = number_format($money_con_2,2,".","");
//						//print_r($money_con_2);
//						//echo '<br/>';
//						$money_con += $money_con_2;
//
//						//改变订单里面的状态
//						$sql_8 = "UPDATE " . $ecs->table('order_info') . " SET is_gain_rebate = 1,gain_rebate_money='".$money_con_2."' WHERE order_id = ".$vv['order_id'];
//						$db -> query($sql_8);
//					}
//				}
//		}
//		//echo '<br/>';
//		//print_r($money_con);
//
//
//		//account_log操作
//		if($money_con && $user_id_g){
//			//加入余额
//			$sql_9 = "UPDATE " . $ecs->table('users') . " SET user_money = user_money + ".$money_con." WHERE user_id = ".$user_id_g;
//			$is_re = $db -> query($sql_9);
//
//			//account_log操作
//			$zj_time = time();
//			$sql_31 = "INSERT INTO ".$ecs->table('account_log')."(user_id , user_money, frozen_money , rank_points , pay_points , change_time , change_desc , change_type) VALUES ('$user_id_g','$money_con','0','0','0','$zj_time','返利到余额','99')";
//			$db->query($sql_31);
//		}
//
//		//print_r($sql_31);
//
//		if($is_re){
//			show_message_c('获取成功','返回上一页','user_c.php?act=weixin_fans');
//		}else{
//			show_message_c('无订单可获利','返回上一页','user_c.php?act=weixin_fans','error');
//		}
//
//
//	}
//}
//工厂发货状态
elseif($action == 'goods_send_status'){

	$user_rank = $GLOBALS['db']->getone("select `user_rank` from".$ecs->table('users')."where user_id='".$_SESSION['user_id']."'");

	if($user_rank == 102){
		$facilitator_id = $_SESSION['user_id'];
		$list = get_join_list($facilitator_id);
			
	}elseif($user_rank == 103){
		$parent_facilitator_id = $_SESSION['user_id'];
		$list = get_service_list($parent_facilitator_id);

	}else{
		exit;
	}

	$pager  = get_pager('user_c.php',$page_arr_order, $record_count, $page,'5');
	foreach ($list['item'] as $k8 => $v8) {
		$list['item'][$k8]['add_time'] = date('Y-m-d',$v8['add_time']);
		$list['item'][$k8]['send_time'] = date('Y-m-d',$v8['send_time']);
	}
	$smarty->assign('goods_list', $list['item']);
	//var_dump($list['item']);
	$smarty->assign('user_rank',  $user_rank);
	$smarty->assign('filter',       $list['filter']);
	$smarty->assign('record_count', $list['record_count']);
	$smarty->assign('page_count',   $list['page_count']);
	$smarty->assign('username', $username );
	$smarty ->display('goods_send_status.dwt');
}
//确认工厂发货
elseif($action == 'check_send_status'){

	$id = $_GET['id'];
	$username = $_GET['username'];
	$user_rank = $GLOBALS['db']->getone("select `user_rank` from".$ecs->table('users')."where user_id='".$_SESSION['user_id']."'");

	if($user_rank == 103){
		$fw_time = time();
		$sql = "update ". $ecs->table('factory') . " set `status`=3 ,`fw_time`=".$fw_time." where id=".$id;
		$db->query($sql);
	}elseif($user_rank == 102){
		$jx_time = time();
		$sql = "update ". $ecs->table('factory') . " set `status`=4 ,`jx_time`=".$jx_time." where id=".$id;
		$db->query($sql);
		$order_sn = $GLOBALS['db']->getone("select `order_sn` from".$ecs->table('factory')."where id='".$id."'");
        $status = $GLOBALS['db']->getAll("select `status` from".$ecs->table('factory')."where order_sn='".$order_sn."'");
        $a=1;
        foreach ($status as $key => $value) {
            if($value['status']!=4){
                $a=0;
            }
        }
        if($a){
           $sql2 = "update ". $ecs->table('order_info') . " set `order_status`=5 ,`shipping_status`=1,`pay_status`=2 where order_sn=".$order_sn;
            $db->query($sql2); 
        }
		
	}else{
		exit;
	}
	header('location:user_c.php?act=goods_send_status');
}
elseif($action == 'downimg'){
	//下载
	//二维码远程地址
	$sql_qr = "SELECT `ticket` FROM `wxch_qr` WHERE `type` = 'tj' and `affiliate` = ".$user_id;
	$ticket = $db->getOne($sql_qr);
	$qr_url = "http://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".$ticket;
	//是否已经有二维码了
	$sql_qr_qrimgsrc = "SELECT `qrimgsrc` FROM `wxch_qr` WHERE `type` = 'tj' and `affiliate` = ".$user_id;
	$qrimgsrc = $db->getOne($sql_qr_qrimgsrc);
	if(!$qrimgsrc){
		$qrimg = curl_get_contents($qr_url);
		$time = time().rand(1000,9999);
		$path = './images/upload/qrimg/'.$time.'.jpg';
		@file_put_contents($path, $qrimg);
		$qrimgsrc = '/images/upload/qrimg/'.$time.'.jpg';
		$update_sql = "UPDATE `wxch_qr` SET `qrimgsrc` = '$qrimgsrc' WHERE `type` = 'tj' and `affiliate` = ".$user_id;
		$db->query($update_sql);
	}

	$imgname = $user_info_c['username'].'.jpg';

	downLoad('./'.$qrimgsrc,$imgname);

}

elseif($action == 'download_file'){
	$sql = "SELECT * from ".$ecs->table('article')." WHERE cat_id =16";
	$list = $db->getAll($sql);
	//print_r($list);
	$smarty->assign('list', $list );
	$smarty->display('download_file.dwt');
}

//店员照片列表
elseif ($action == 'store_photos_list')
{
	$user_id=$_SESSION['user_id'];
	
    $sql = 'SELECT image_id,user_id,store_shop FROM ' . $ecs->table('images_upload') . ' WHERE user_id='.$user_id.' and store_shop !=""';
    $store_photos_list = $db->getAll($sql);
    
    $sql = 'SELECT image_id,user_id,store_shops FROM ' . $ecs->table('images_upload') . ' WHERE user_id='.$user_id.' and store_shops !=""';
    $store_photos_lists = $db->getAll($sql);
    $smarty->assign('store_photos_list', $store_photos_list);
    $smarty->assign('store_photos_lists', $store_photos_lists);
	$smarty->assign('surplus_amount', price_format($surplus_amount, false));

	$smarty->display('store_photos_list.dwt');
}
//门店店招照，店铺形象添加
elseif ($action == 'store_photos_add')
{
	
	if(!empty($_FILES['name']['name']))
   	{
   		$user_id=$_SESSION['user_id'];
		$sql = 'SELECT store_shop FROM ' . $ecs->table('images_upload') . ' WHERE user_id='.$user_id.' and store_shop !=""';
		$store_photos_list = $db->getOne($sql);
		if($store_photos_list!=''){
			echo "<center><br/><br/><br/><br/><br/><font color='rad' size='+2'><b>你已有店招！！</b></font></center>";
			echo "<script>setTimeout('location.href=\"user_c.php?act=store_photos_list\"',1000);          </script>";
		}
		else{
   			$name=$_FILES['name']['name'];

		  //图片上传
	        $tmp=strrchr($name,'.');
	        $newname=time().$tmp;
	        $path="upload/".$newname;
	        if(is_uploaded_file($_FILES['name']['tmp_name']))
	        {
	         if(move_uploaded_file($_FILES['name']['tmp_name'],$path))
		    {
			 $sql="insert into `ecs_images_upload` (`user_id`,`store_shop`) values ('".$user_id."','".$newname."')";
			 $query=mysql_query($sql);
			 if($query >0 ){
			 	echo "<center><br/><br/><br/><br/><br/><font color='rad' size='+2'><b>添加成功</b></font></center>";
		     	echo "<script>setTimeout('location.href=\"user_c.php?act=store_photos_list\"',1000);          </script>";
			 }
		    }
			else
			{
			}
	       }
	   }
	} 
	$smarty->assign('surplus_amount', price_format($surplus_amount, false));

	$smarty->display('store_photos_add.dwt');
}
//门店店招照，店铺形象添加
elseif ($action == 'store_photos_adds')
{
	$user_id=$_SESSION['user_id'];
	if(!empty($_FILES['name']['name']))
   	{
   		$name=$_FILES['name']['name'];

		  //图片上传
	        $tmp=strrchr($name,'.');
	        $newname=time().$tmp;
	        $path="upload/".$newname;
	        if(is_uploaded_file($_FILES['name']['tmp_name']))
	        {
	         if(move_uploaded_file($_FILES['name']['tmp_name'],$path))
		    {
			 $sql="insert into `ecs_images_upload` (`user_id`,`store_shops`) values ('".$user_id."','".$newname."')";
			 $query=mysql_query($sql);
			 if($query >0 ){
			 	echo "<center><br/><br/><br/><br/><br/><font color='rad' size='+2'><b>添加成功</b></font></center>";
		     	echo "<script>setTimeout('location.href=\"user_c.php?act=store_photos_list\"',1000);          </script>";
			 }
		    }
			else
			{
			}
	       }
	   }
	   
	$smarty->assign('surplus_amount', price_format($surplus_amount, false));

	$smarty->display('store_photos_adds.dwt');
}
//门店照片修改显示
elseif ($action == 'store_photos_update')
{
	$image_id=$_GET['id'];
	
    $sql = 'SELECT image_id,store_shop FROM ' . $ecs->table('images_upload') . ' WHERE image_id='.$image_id.'';
    $store_photos_list = $db->getAll($sql);
    foreach ($store_photos_list as $store_photos_lists){
    	$image_id=$store_photos_lists['image_id'];
    	$store_shop=$store_photos_lists['store_shop'];
    }
    /*$sql = 'SELECT image_id,user_id,store_shops FROM ' . $ecs->table('images_upload') . ' WHERE user_id='.$user_id.'';
    $store_photos_lists = $db->getAll($sql);*/
    $smarty->assign('image_id', $image_id);
    $smarty->assign('store_shop', $store_shop);
	$smarty->assign('surplus_amount', price_format($surplus_amount, false));

	$smarty->display('store_photos_update.dwt');
}
//门店照片修改显示
elseif ($action == 'store_photos_updates')
{
	$image_id=$_GET['id'];
	
    $sql = 'SELECT image_id,store_shops FROM ' . $ecs->table('images_upload') . ' WHERE image_id='.$image_id.'';
    $store_photos_list = $db->getAll($sql);
    foreach ($store_photos_list as $store_photos_lists){
    	$image_id=$store_photos_lists['image_id'];
    	$store_shops=$store_photos_lists['store_shops'];
    }
    /*$sql = 'SELECT image_id,user_id,store_shops FROM ' . $ecs->table('images_upload') . ' WHERE user_id='.$user_id.'';
    $store_photos_lists = $db->getAll($sql);*/
    $smarty->assign('image_id', $image_id);
    $smarty->assign('store_shops', $store_shops);
	$smarty->assign('surplus_amount', price_format($surplus_amount, false));

	$smarty->display('store_photos_updates.dwt');
}
//门店照片修改执行
elseif ($action == 'store_photos_query')
{
	$image_id=$_POST['name'];
//添加店员形象
	if(!empty($_FILES['name']['name']))
   	{
   		//图片上传
		$sql="select `store_shop` from `ecs_images_upload` where `image_id`='".$image_id."'";
	  	$query=mysql_query($sql);
	  	$rs=mysql_fetch_array($query);
	  	$path='upload/'.$rs['store_shop'];
	     if(file_exists($path))
	    	{
		  	@unlink($path);
		  	clearstatcache();
		    $names=$_FILES['name']['name'];
	        $tmp=strrchr($names,'.');
	        $newname=time().$tmp;
	        $path="upload/".$newname;
	        if(is_uploaded_file($_FILES['name']['tmp_name']))
	        {
	         if(move_uploaded_file($_FILES['name']['tmp_name'],$path))
		    {
			 $db->query('UPDATE ' .$ecs->table('images_upload'). "SET store_shop='$newname' WHERE image_id='$image_id'" );
			 echo "<center><br/><br/><br/><br/><br/><font color='rad' size='+2'><b>修改成功</b></font></center>";
		     echo "<script>setTimeout('location.href=\"user_c.php?act=store_photos_list\"',1000);          </script>";
		    }
			else
			{
			 
			}
	       }
	   }
   	}else{
	   //图片上传
		    $names=$_POST['names'];
			 $db->query('UPDATE ' .$ecs->table('images_upload'). "SET store_shop='$names' WHERE image_id='$image_id'" );
		   if($db > 0)
			{
			 echo "<center><br/><br/><br/><br/><br/><font color='rad' size='+2'><b>修改成功</b></font></center>";
		     echo "<script>setTimeout('location.href=\"user_c.php?act=store_photos_list\"',1000);          </script>";
			}
	   }

	   //门店形象修改
}
//店铺形象修改执行
elseif ($action == 'store_photos_querys')
{
	$image_id=$_POST['name'];
//添加店员形象
	if(!empty($_FILES['name']['name']))
   	{
   		//图片上传
		$sql="select `store_shops` from `ecs_images_upload` where `image_id`='".$image_id."'";
	  	$query=mysql_query($sql);
	  	$rs=mysql_fetch_array($query);
	  	$path='upload/'.$rs['store_shops'];
	     if(file_exists($path))
	    	{
		  	@unlink($path);
		  	clearstatcache();
		    $names=$_FILES['name']['name'];
	        $tmp=strrchr($names,'.');
	        $newname=time().$tmp;
	        $path="upload/".$newname;
	        if(is_uploaded_file($_FILES['name']['tmp_name']))
	        {
	         if(move_uploaded_file($_FILES['name']['tmp_name'],$path))
		    {
			 $db->query('UPDATE ' .$ecs->table('images_upload'). "SET store_shops='$newname' WHERE image_id='$image_id'" );
			 echo "<center><br/><br/><br/><br/><br/><font color='rad' size='+2'><b>修改成功</b></font></center>";
		     echo "<script>setTimeout('location.href=\"user_c.php?act=store_photos_list\"',1000);          </script>";
		    }
			else
			{
			 
			}
	       }
	   }
   	}else{
	   //图片上传
		    $names=$_POST['names'];
			 $db->query('UPDATE ' .$ecs->table('images_upload'). "SET store_shops='$names' WHERE image_id='$image_id'" );
		   if($db > 0)
			{
			 echo "<center><br/><br/><br/><br/><br/><font color='rad' size='+2'><b>修改成功</b></font></center>";
		     echo "<script>setTimeout('location.href=\"user_c.php?act=store_photos_list\"',1000);          </script>";
			}
	   }

	   //门店形象修改
}

//店员的删除
elseif ($action == 'store_photos_del')
{
	$user_id=$_SESSION['user_id'];
	$image_id=$_GET['id'];
    $db->query('DELETE FROM ' .$ecs->table('images_upload'). " WHERE image_id='$image_id' AND user_id ='$user_id'" );
	if($db > 0){
		 echo "<center><br/><br/><br/><br/><br/><font color='rad' size='+2'><b>删除成功</b></font></center>";
		 echo "<script>setTimeout('location.href=\"user_c.php?act=store_photos_list\"',1000);</script>";
	}
	$smarty->assign('surplus_amount', price_format($surplus_amount, false));
}
/* 店员照片 */
elseif ($action == 'store_photos')
{
	 $user_id=$_SESSION['user_id'];
	//$username = isset($_POST['user_id']) ? trim($_POST['user_id']) : '';
	 
	if(!empty($_FILES['name']['name']))
   	{
   		$name=$_FILES['name']['name'];
   		//删除原路径的图片
	   $sql="select `image_id`,`store_shop` from `ecs_images_upload` where `user_id`='".$user_id."'";
	  	$query=mysql_query($sql);
	  	$rs=mysql_fetch_array($query);
	  	$store_shop=$rs['store_shop'];
	   $sql="select `image_id` from `ecs_images_upload` where `store_shop`='".$store_shop."'";
	  	$query=mysql_query($sql);
	  	$row=mysql_fetch_array($query);
	  	$image_id=$row['image_id'];
	  	//判断是否为第一次添加
	  	if($store_shop){
	  		$path='upload/'.$store_shop;
	     if(file_exists($path))
	    	{
		  		@unlink($path);
		  		clearstatcache();
		  //图片上传
		    //$n_photo=$_FILES["file"]["name"];
	        $tmp=strrchr($name,'.');
	        $newname=time().$tmp;
	        $path="upload/".$newname;
	        if(is_uploaded_file($_FILES['name']['tmp_name']))
	        {
	         if(move_uploaded_file($_FILES['name']['tmp_name'],$path))
		    {
			 $sql="update `ecs_images_upload` set `store_shop`='".$newname."' where `user_id`=".$user_id." and `image_id`='".$image_id."'";
		     $query=mysql_query($sql);
		    }
			else
			{
			 echo "<center><br/><br/><br/><br/><br/><font color='rad' size='+2'><b>修改成功</b></font></center>";
		     echo "<script>setTimeout('location.href=\"user_c.php?act=store_photos\"',1000);          </script>";
			}
	       }
	     }
	  	}
	  	if($name){
	  		$tmp=strrchr($name,'.');
	        $newname=time().$tmp;
	        $path="upload/".$newname;
	        if(is_uploaded_file($_FILES['name']['tmp_name']))
	        {
	         if(move_uploaded_file($_FILES['name']['tmp_name'],$path))
		    {
			 $sql="insert into `ecs_images_upload` (`user_id`,`store_shop`) values ('".$user_id."','".$newname."')";
		     $query=mysql_query($sql);
		    }
			else
			{
			 echo "<center><br/><br/><br/><br/><br/><font color='rad' size='+2'><b>修改成功</b></font></center>";
		     echo "<script>setTimeout('location.href=\"user_c.php?act=store_photos\"',1000);          </script>";
			}
	       }
	  	}
	   }
  //第二个图片上传
	if(!empty($_FILES['names']['name']))
   	{
		  //图片上传
		    $names=$_FILES['names']['name'];
	        $tmp=strrchr($names,'.');
	        $newname=time().$tmp;
	        $path="upload/".$newname;
	        if(is_uploaded_file($_FILES['names']['tmp_name']))
	        {
	         if(move_uploaded_file($_FILES['names']['tmp_name'],$path))
		    {
			 $sql="insert into `ecs_images_upload` (`user_id`,`store_shops`) values ('".$user_id."','".$newname."')";
			 $query=mysql_query($sql);
		    }
			else
			{
			 echo "<center><br/><br/><br/><br/><br/><font color='rad' size='+2'><b>修改成功</b></font></center>";
		     echo "<script>setTimeout('location.href=\"user_c.php?act=store_photos\"',1000);          </script>";
			}
	       }
	   }
	$smarty->assign('surplus_amount', price_format($surplus_amount, false));

	$smarty->display('user_store_photos.dwt');
}

//店员列表
elseif ($action == 'shop_assistant_list')
{
	$user_id=$_SESSION['user_id'];
	$shop_explain=$_POST['shop_explain'];
	
    $sql = 'SELECT shop_id,user_id,shop_assistant,shop_explain FROM ' . $ecs->table('shop_assistant') . ' WHERE user_id='.$user_id.'';
    $shop_assistant_list = $db->getAll($sql);
    
    $smarty->assign('shop_assistant_list', $shop_assistant_list);
    
	$smarty->assign('surplus_amount', price_format($surplus_amount, false));
	$smarty->display('user_shop_assistant_list.dwt');
}

//店员的删除
elseif ($action == 'shop_assistant_del')
{
	$user_id=$_SESSION['user_id'];
	$shop_id=$_GET['id'];
    $db->query('DELETE FROM ' .$ecs->table('shop_assistant'). " WHERE shop_id='$shop_id' AND user_id ='$user_id'" );
	if($db > 0){
		 echo "<center><br/><br/><br/><br/><br/><font color='rad' size='+2'><b>删除成功</b></font></center>";
		 echo "<script>setTimeout('location.href=\"user_c.php?act=shop_assistant_list\"',1000);</script>";
	}
	$smarty->assign('surplus_amount', price_format($surplus_amount, false));
}

//店员修改显示
elseif ($action == 'shop_assistant_update')
{
	$shop_id=$_GET['id'];
	
    $sql = 'SELECT shop_id,user_id,shop_assistant,shop_explain FROM ' . $ecs->table('shop_assistant') . ' WHERE shop_id='.$shop_id.'';
    $shop_assistant_update = $db->getAll($sql);
    foreach ($shop_assistant_update as $row){
    	$shop_id=$row['shop_id'];
    	$shop_assistant=$row['shop_assistant'];
    	$shop_explain=$row['shop_explain'];
    }
    $smarty->assign('shop_id', $shop_id);
    $smarty->assign('shop_assistant', $shop_assistant);
    $smarty->assign('shop_explain', $shop_explain);
    
	$smarty->assign('surplus_amount', price_format($surplus_amount, false));
	$smarty->display('user_shop_assistant_update.dwt');
}

//店员修改执行
elseif ($action == 'shop_assistant_update_query')
{
 	$shop_id=$_POST['id'];
	$shop_explain=$_POST['shop_explain'];
	
	//添加店员形象
	if(!empty($_FILES['name']['name']))
   	{
		  //图片上传
		$sql="select `shop_assistant` from `ecs_shop_assistant` where `shop_id`='".$shop_id."'";
	  	$query=mysql_query($sql);
	  	$rs=mysql_fetch_array($query);
	  	//判断是否为第一次添加
	  	$path='upload/'.$rs['shop_assistant'];
	     if(file_exists($path))
	    	{
		  	@unlink($path);
		  	clearstatcache();
		    $names=$_FILES['name']['name'];
	        $tmp=strrchr($names,'.');
	        $newname=time().$tmp;
	        $path="upload/".$newname;
	        if(is_uploaded_file($_FILES['name']['tmp_name']))
	        {
	         if(move_uploaded_file($_FILES['name']['tmp_name'],$path))
		    {
			 $db->query('UPDATE ' .$ecs->table('shop_assistant'). "SET shop_assistant='$newname',shop_explain='$shop_explain' WHERE shop_id='$shop_id'" );
			 echo "<center><br/><br/><br/><br/><br/><font color='rad' size='+2'><b>修改成功</b></font></center>";
		     echo "<script>setTimeout('location.href=\"user_c.php?act=shop_assistant_list\"',1000);          </script>";
		    }
			else
			{
			 
			}
	       }
	   }
   	}else{
	   //图片上传
		    $names=$_POST['names'];
			 $db->query('UPDATE ' .$ecs->table('shop_assistant'). "SET shop_explain='$shop_explain' WHERE shop_id='$shop_id'" );
		if($db > 0)
			{
			 echo "<center><br/><br/><br/><br/><br/><font color='rad' size='+2'><b>修改成功</b></font></center>";
		     echo "<script>setTimeout('location.href=\"user_c.php?act=shop_assistant_list\"',1000);          </script>";
			}
	   }
	$smarty->assign('surplus_amount', price_format($surplus_amount, false));
}
//店员形象
elseif ($action == 'shop_assistant')
{
	$user_id=$_SESSION['user_id'];
	$shop_explain=$_POST['shop_explain'];
	//添加店员形象
	if(!empty($_FILES['name']['name']))
   	{
		  //图片上传
		    $names=$_FILES['name']['name'];
	        $tmp=strrchr($names,'.');
	        $newname=time().$tmp;
	        $path="upload/".$newname;
	        if(is_uploaded_file($_FILES['name']['tmp_name']))
	        {
	         if(move_uploaded_file($_FILES['name']['tmp_name'],$path))
		    {
			 $sql="insert into `ecs_shop_assistant` (`user_id`,`shop_assistant`,`shop_explain`) values ('".$user_id."','".$newname."','".$shop_explain."')";
					$query=mysql_query($sql);
		    }
			else
			{
			 echo "<center><br/><br/><br/><br/><br/><font color='rad' size='+2'><b>t成功</b></font></center>";
		     echo "<script>setTimeout('location.href=\"user_c.php?act=store_photos\"',1000);          </script>";
			}
	       }
	   }
	$smarty->assign('surplus_amount', price_format($surplus_amount, false));

	$smarty->display('user_shop_assistant.dwt');
}
//专享活动列表
elseif ($action == 'vip_activity_list'){
	$user_id=$_SESSION['user_id'];
	$shop_explain=$_POST['shop_explain'];
	
    $sql = 'SELECT vip_id,title,vip_image,event_details FROM ' . $ecs->table('vip_activity') . ' WHERE user_id='.$user_id.'';
    $shop_assistant_list = $db->getAll($sql);
    $smarty->assign('shop_assistant_list', $shop_assistant_list);
    
	$smarty->assign('surplus_amount', price_format($surplus_amount, false));
	$smarty->display('vip_activity_list.dwt');
}
//专享活动删除
elseif ($action == 'vip_activity_del'){
	$user_id=$_GET['id'];
	$shop_explain=$_POST['shop_explain'];
	
    $db->query('DELETE FROM ' .$ecs->table('vip_activity'). " WHERE vip_id='$user_id'");
	if($db > 0){
		
		echo "<center><br/><br/><br/><br/><br/><font color='rad' size='+2'><b>删除成功</b></font></center>";
		echo "<script>setTimeout('location.href=\"user_c.php?act=vip_activity_list\"',1000);          </script>";
	}
    
	$smarty->assign('surplus_amount', price_format($surplus_amount, false));
}
//专享活动修改显示页面
elseif ($action == 'vip_activity_update'){
	$user_id=$_GET['id'];

    $sql = 'SELECT vip_id,title,vip_image,event_details FROM ' . $ecs->table('vip_activity') . ' WHERE vip_id='.$user_id.'';
    $shop_assistant_list = $db->getAll($sql);
    
    foreach ($shop_assistant_list as $row){
    	$vip_id=$row['vip_id'];
    	$title=$row['title'];
    	$vip_image=$row['vip_image'];
    	$event_details=$row['event_details'];
    }
    $smarty->assign('vip_id',$vip_id);
    $smarty->assign('title', $title);
    $smarty->assign('vip_image', $vip_image);
	$smarty->assign('event_details', $event_details);
    
	$smarty->assign('surplus_amount', price_format($surplus_amount, false));
	$smarty->display('user_vip_update.dwt');
}
//专享活动修改执行
elseif ($action == 'vip_activity_update_query'){
	
	$user_id=$_POST['id'];
	$title=$_POST['title'];
	$shop_explain=$_POST['shop_explain'];
if(!empty($_FILES['name']['name']))
   	{
   		//图片上传
		$sql="select `vip_image` from `ecs_vip_activity` where `image_id`='".$user_id."'";
	  	$query=mysql_query($sql);
	  	$rs=mysql_fetch_array($query);
	  	$path='upload/'.$rs['vip_image'];
	     if(file_exists($path))
	    	{
		  	@unlink($path);
		  	clearstatcache();
		    $names=$_FILES['name']['name'];
	        $tmp=strrchr($names,'.');
	        $newname=time().$tmp;
	        $path="upload/".$newname;
	        if(is_uploaded_file($_FILES['name']['tmp_name']))
	        {
	         if(move_uploaded_file($_FILES['name']['tmp_name'],$path))
		    {
			 $db->query('UPDATE ' .$ecs->table('vip_activity'). "SET title='$title',vip_image='$newname',event_details='$shop_explain' WHERE vip_id='$user_id'" );
			 echo "<center><br/><br/><br/><br/><br/><font color='rad' size='+2'><b>修改成功</b></font></center>";
		     echo "<script>setTimeout('location.href=\"user_c.php?act=vip_activity_list\"',1000);          </script>";
		    }
			else
			{
			 
			}
	       }
	   }
   	}else{
			 $db->query('UPDATE ' .$ecs->table('vip_activity'). "SET title='$title',event_details='$shop_explain' WHERE vip_id='$user_id'" );
		   if($db > 0)
			{
			 echo "<center><br/><br/><br/><br/><br/><font color='rad' size='+2'><b>修改成功</b></font></center>";
		     echo "<script>setTimeout('location.href=\"user_c.php?act=vip_activity_list\"',1000);          </script>";
			}
	   }
}
//专享活动添加
elseif ($action == 'vip_activity')
{
	$title=$_POST['title'];
	$user_id=$_SESSION['user_id'];
	$shop_explain=$_POST['shop_explain'];
	//添加专享活动
	if(!empty($_FILES['name']['name']))
   	{
		  //图片上传
		    $names=$_FILES['name']['name'];
	        $tmp=strrchr($names,'.');
	        $newname=time().$tmp;
	        $path="upload/".$newname;
	        if(is_uploaded_file($_FILES['name']['tmp_name']))
	        {
	         if(move_uploaded_file($_FILES['name']['tmp_name'],$path))
		    {
		 			$sql="insert into `ecs_vip_activity` (`user_id`,`title`,`vip_image`,`event_details`) values ('".$user_id."','".$title."','".$newname."','".$shop_explain."')";
					$query=mysql_query($sql);
		    }
			else
			{
			 echo "<center><br/><br/><br/><br/><br/><font color='rad' size='+2'><b>修改成功</b></font></center>";
		     echo "<script>setTimeout('location.href=\"user_c.php?act=store_photos\"',1000);          </script>";
			}
	       }
	   }
	$smarty->assign('surplus_amount', price_format($surplus_amount, false));

	$smarty->display('user_vip_activity.dwt');
}
//评价管理
elseif ($action == 'evaluation_management')
{
	$user_id=$_SESSION['user_id'];
	//①获得第几页
	$page = isset($_REQUEST['page']) && intval($_REQUEST['page']) > 0 ? intval($_REQUEST['page'])  : 1;
	//②分页大小
	$size = 5;
	//③总的记录数，你可以用sql查询出来
	$sql = "SELECT COUNT(*) FROM ecs_comment where user_id='".$user_id."'";
	$record_count = $GLOBALS['db']->getOne($sql);
	//④生成分页
	$pager = get_pager('user_c.php', array(), $record_count, $page,5);
	//⑤赋值到模板

	$sql="select comment_id,user_name,content,from_unixtime(add_time,'%Y%m%d') as add_time,comment_rank,user_id from ecs_comment where user_id='".$user_id."'";
	$query=mysql_query($sql);
	while($rs=mysql_fetch_array($query)){
		$comment_rank=$rs['comment_rank'];
		$comment+=$comment_rank;
		$user=count($rs['user_id']);
		$users+=$user;
		$rss[]=$rs;
	}
	 $comment;//总和
	 $users;//个数
	$hh=round((($users/$comment)*100)*2).'%';//好评率
	$hh1=round((($users/$comment)*100)*2);//好评率
	$smarty->assign('rss', $rss);
	$smarty->assign('hh', $hh);
	$smarty->assign('hh1', $hh1);
	$smarty->assign('pager', $pager);
	$smarty->assign('surplus_amount', price_format($surplus_amount, false));

	$smarty->display('user_evaluation_management.dwt');
}
elseif ($action == 'comment_id')
{
	$user_id=$_GET['id'];
	$sql="DELETE FROM ecs_comment WHERE comment_id = '".$user_id."' ";
	$query=mysql_query($sql);
	if($query >0){
		echo "<center><br/><br/><br/><br/><br/><font color='rad' size='+2'><b>删除成功</b></font></center>";
		echo "<script>setTimeout('location.href=\"user_c.php?act=evaluation_management\"',1000);</script>";
	}
	$smarty->assign('surplus_amount', price_format($surplus_amount, false));
}
//施工团队评价显示页面
elseif ($action == 'construction_team')
{
	$user_id=$_SESSION['user_id'];
	//①获得第几页
	$page = isset($_REQUEST['page']) && intval($_REQUEST['page']) > 0 ? intval($_REQUEST['page'])  : 1;
	//②分页大小
	$size = 5;
	//③总的记录数，你可以用sql查询出来
	$sql = "SELECT COUNT(*) FROM ecs_comment where user_id='".$user_id."'";
	$record_count = $GLOBALS['db']->getOne($sql);
	//④生成分页
	$pager = get_pager('user_c.php', array(), $record_count, $page,5);
	//⑤赋值到模板
	$sqltd="select construction_id,construction_name from user_construction_team where user_id='".$user_id."'";
	$querytd=mysql_query($sqltd);
	while($rstd=mysql_fetch_array($querytd)){
		$construction_id=$rstd['construction_id'];
		$construction_name=$rstd['construction_name'];
	}
	$sql="select comment_id,user_name,content,from_unixtime(add_time,'%Y%m%d') as add_time,comment_rank,construction_id from ecs_comment where user_id='".$user_id."' and construction_id='".$construction_id."'";
	$query=mysql_query($sql);
	while($rs=mysql_fetch_array($query)){
		$comment_rank=$rs['comment_rank'];
		$comment+=$comment_rank;
		$user=count($rs['construction_id']);
		$users+=$user;
		$rss[]=$rs;
	}
	 $comment;//总和
	  $users;//个数
	  $hh=round((($users/$comment)*100)*2).'%';//好评率
	$hh1=round((($users/$comment)*100)*2);//好评率
	$smarty->assign('rss', $rss);
	$smarty->assign('hh', $hh);
	$smarty->assign('hh1', $hh1);
	$smarty->assign('user_id', $user_id);
	$smarty->assign('construction_name', $construction_name);
	$smarty->assign('pager', $pager);
	$smarty->assign('surplus_amount', price_format($surplus_amount, false));

	$smarty->display('user_construction_team.dwt');
}
//施工团队列表页面
elseif ($action == 'construction_team_list')
{
	$user_id=$_SESSION['user_id'];
	$sql = 'SELECT construction_id,construction_name FROM user_construction_team WHERE user_id='.$user_id.'';
    $construction_team_list = $db->getAll($sql);
	$smarty->assign('construction_team_list', $construction_team_list);
	$smarty->display('construction_team_list.dwt');
}
////修改施工团队显示页面
elseif ($action == 'construction_team_update')
{
	$construction_id=$_GET['id'];
	$sql = 'SELECT construction_id,construction_name FROM user_construction_team WHERE construction_id='.$construction_id.'';
    $construction_team_list = $db->getAll($sql);
    foreach($construction_team_list as $row){
    	$construction_id=$row['construction_id'];
    	$construction_name=$row['construction_name'];
    }
	$smarty->assign('construction_id', $construction_id);
	$smarty->assign('construction_name', $construction_name);
	$smarty->display('construction_team_update.dwt');
}
//处理修改施工团队处理执行
elseif ($action == 'construction_team_updates')
{
	$id=$_POST['name'];
	$title=$_POST['title'];
	$db->query("UPDATE user_construction_team SET construction_name='$title' WHERE construction_id='$id'" );
	if($db >0 ){
		echo "<center><br/><br/><br/><br/><br/><font color='rad' size='+2'><b>修改成功</b></font></center>";
		echo "<script>setTimeout('location.href=\"user_c.php?act=construction_team_list\"',1000);          </script>";
	}
	
	$smarty->assign('rss', $rss);
	$smarty->assign('user_id', $user_id);
	$smarty->assign('pager', $pager);
	$smarty->assign('surplus_amount', price_format($surplus_amount, false));
}
//添加施工团队显示页面
elseif ($action == 'construction_team_add')
{
	$user_id=$_GET['id'];
	$smarty->assign('rss', $rss);
	$smarty->assign('user_id', $user_id);
	$smarty->assign('pager', $pager);
	$smarty->assign('surplus_amount', price_format($surplus_amount, false));

	$smarty->display('construction_team_add.dwt');
}
//处理添加施工团队处理
elseif ($action == 'user_construction_team_add')
{
	$user_id=$_GET['id'];
	$title=$_POST['title'];
	$sql="insert into `user_construction_team` (`user_id`,`construction_name`) values ('".$user_id."','".$title."')";
	$query=mysql_query($sql);
	if($query >0 ){
		echo "<center><br/><br/><br/><br/><br/><font color='rad' size='+2'><b>添加成功</b></font></center>";
		echo "<script>setTimeout('location.href=\"user_c.php?act=construction_team_add\"',1000);          </script>";
	}
	
	$smarty->assign('rss', $rss);
	$smarty->assign('user_id', $user_id);
	$smarty->assign('pager', $pager);
	$smarty->assign('surplus_amount', price_format($surplus_amount, false));
}
///删除施工团队
elseif ($action == 'construction_team_del')
{
	$user_id=$_GET['id'];
	$sql="DELETE FROM user_construction_team WHERE construction_id = '".$user_id."' ";
	$query=mysql_query($sql);
	if($query >0){
		echo "<center><br/><br/><br/><br/><br/><font color='rad' size='+2'><b>删除成功</b></font></center>";
		echo "<script>setTimeout('location.href=\"user_c.php?act=construction_team_list\"',1000);</script>";
	}
	$smarty->assign('surplus_amount', price_format($surplus_amount, false));
}
function downLoad($filename,$imgname){
	header('Content-type:image/jpeg');
	header("Content-Disposition:attachment; filename=$imgname");
	//在header确保前面没有任何输出
	@readfile($filename);
	exit;
}


function curl_get_contents($url)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_TIMEOUT, 2);
	curl_setopt($ch, CURLOPT_USERAGENT, _USERAGENT_);
	curl_setopt($ch, CURLOPT_REFERER,_REFERER_);
	curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	$r = curl_exec($ch);
	curl_close($ch);
	return $r;
}

/**
 * 获取订单商品列表
 * @access  public
 * @return  array
 */
//服务商
function get_service_list($parent_facilitator_id)
{
	$where = '1';
	$where .= (!empty($parent_facilitator_id)) ? " AND parent_facilitator_id=" . $parent_facilitator_id : '';
	/* 查询条件 */
	$filter['start_time']     = empty($_REQUEST['start_time']) ? 0 : strtotime(trim($_REQUEST['start_time']));
	$filter['end_time']     = empty($_REQUEST['end_time']) ? 0 : strtotime(trim($_REQUEST['end_time']));
	$filter['status']     = empty($_REQUEST['status']) ? 0 : trim($_REQUEST['status']);
	$filter['send_company']     = empty($_REQUEST['send_company']) ? 0 : trim($_REQUEST['send_company']);
	$filter['keywords']     = empty($_REQUEST['keywords']) ? 0 : trim($_REQUEST['keywords']);
	if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
	{
		$filter['status'] = json_str_iconv($filter['status']);
	}
	$where .= (!empty($filter['start_time'])) ? " AND add_time >=" . $filter['start_time'] : '';
	$where .= (!empty($filter['end_time'])) ? " AND add_time <=" . $filter['end_time'] : '';
	$where .= (!empty($filter['status'])) ? " AND status=" . $filter['status'] : '';
	$where .= (!empty($filter['send_company'])) ? " AND send_company='" . $filter['send_company']."'" : '';
	$where .= (!empty($filter['keywords'])) ? " AND concat(`goods_sn`,`c_address`,`c_name`,`send_sn`) LIKE '%" . $filter['keywords']. "%' " : '';

	$sql = "SELECT count(*) FROM " .$GLOBALS['ecs']->table('factory'). " WHERE $where";
	$filter['record_count'] = $GLOBALS['db']->getOne($sql);

	/* 分页大小 */
	$filter = page_and_size($filter);
	//var_dump($filter);

	/* 获取数据 */
	$arr = array();
	$sql  = "SELECT * FROM " .$GLOBALS['ecs']->table('factory'). " WHERE $where and order_sn>0 ORDER BY id desc, status asc  LIMIT  $filter[start] , $filter[page_size]";

	$res  = $GLOBALS['db']->getAll($sql);

	$filter['keywords'] = stripslashes($filter['keywords']);
	$arr = array('item' => $res, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
	return $arr;
}
//加盟商
function get_join_list($facilitator_id)
{
	$where = '1';
	$where .= (!empty($facilitator_id)) ? " AND facilitator_id=" . $facilitator_id : '';
	/* 查询条件 */
	$filter['start_time']     = empty($_REQUEST['start_time']) ? 0 : strtotime(trim($_REQUEST['start_time']));
	$filter['end_time']     = empty($_REQUEST['end_time']) ? 0 : strtotime(trim($_REQUEST['end_time']));
	$filter['status']     = empty($_REQUEST['status']) ? 0 : trim($_REQUEST['status']);
	$filter['send_company']     = empty($_REQUEST['send_company']) ? 0 : trim($_REQUEST['send_company']);
	$filter['keywords']     = empty($_REQUEST['keywords']) ? 0 : trim($_REQUEST['keywords']);
	if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
	{
		$filter['status'] = json_str_iconv($filter['status']);
	}
	$where .= (!empty($filter['start_time'])) ? " AND add_time >=" . $filter['start_time'] : '';
	$where .= (!empty($filter['end_time'])) ? " AND add_time <=" . $filter['end_time'] : '';
	$where .= (!empty($filter['status'])) ? " AND status=" . $filter['status'] : '';
	$where .= (!empty($filter['send_company'])) ? " AND send_company=" . $filter['send_company'] : '';
	$where .= (!empty($filter['keywords'])) ? " AND concat(`goods_sn`,`c_address`,`c_name`,`send_sn`) LIKE '%" . $filter['keywords']. "%' " : '';

	$sql = "SELECT count(*) FROM " .$GLOBALS['ecs']->table('factory'). " WHERE $where";
	$filter['record_count'] = $GLOBALS['db']->getOne($sql);

	/* 分页大小 */
	$filter = page_and_size($filter);
	//var_dump($filter);

	/* 获取数据 */
	$arr = array();
	$sql  = "SELECT * FROM " .$GLOBALS['ecs']->table('factory'). " WHERE $where and order_sn>0 ORDER BY status  LIMIT  $filter[start] , $filter[page_size]";
	$res  = $GLOBALS['db']->getAll($sql);

	$filter['keywords'] = stripslashes($filter['keywords']);
	$arr = array('item' => $res, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
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
		$filter['page_size'] = 10000;
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
/**
 *
 *
 * @access  public
 * @param
 * @return  void
 */
function make_json_result($content, $message='', $append=array())
{
	make_json_response($content, 0, $message, $append);
}
/**
 * 创建一个JSON格式的数据
 *
 * @access  public
 * @param   string      $content
 * @param   integer     $error
 * @param   string      $message
 * @param   array       $append
 * @return  void
 */
function make_json_response($content='', $error="0", $message='', $append=array())
{
	include_once(ROOT_PATH . 'includes/cls_json.php');

	$json = new JSON;

	$res = array('error' => $error, 'message' => $message, 'content' => $content);

	if (!empty($append))
	{
		foreach ($append AS $key => $val)
		{
			$res[$key] = $val;
		}
	}

	$val = $json->encode($res);

	exit($val);
}
?>