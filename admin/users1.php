<?php

/**
 * MEIFANG 会员管理程序
 * ============================================================================ 
 * 版权所有 2005-2014 上海优辉商务，并保留所有权利。
 * 网站地址: http://www.j345.net
 * ----------------------------------------------------------------------------
 * 优辉网络,共创你我
 * ============================================================================
 * $Author: liubo $
 * $Id: users.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/*------------------------------------------------------ */
//-- 用户帐号列表
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'list')
{
    /* 检查权限 */
    admin_priv('users_manage');
    $sql = "SELECT rank_id, rank_name, min_points FROM ".$ecs->table('user_rank')." ORDER BY min_points ASC ";

    $rs = $db->query($sql);

    $ranks = array();
    while ($row = $db->FetchRow($rs))
    {
        $ranks[$row['rank_id']] = $row['rank_name'];
    }
    $smarty->assign('user_ranks',   $ranks);
    $smarty->assign('ur_here',      $_LANG['03_users_list']);
    $smarty->assign('action_link',  array('text' => $_LANG['04_users_add'], 'href'=>'users.php?act=add'));

    $parent_ids=$_REQUEST['user_id'];
    $_SESSION['parent_id']=$parent_ids;

    $user_list = user_list_c($parent_ids);
 

    foreach($user_list['user_list'] as &$v){
        //微信二维码
        $v['qid'] = '';
        $v['qr_url'] = '';
        
        if($v['user_rank']==99 || $v['user_rank']==102 || $v['user_rank']==103){
            $sql_qr = "SELECT `qid`,`ticket` FROM `wxch_qr` WHERE `type` = 'tj' and `affiliate` = ".$v['user_id'];

            $res = $db->getRow($sql_qr);

            if($res){
                $v['qid'] = $res['qid'];
                $v['qr_url'] = "http://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".$res['ticket']; 
            }

        }

        //所属下级 
        $v['junior_id']='';   
        if($v['user_rank']==99 || $v['user_rank']==102 || $v['user_rank']==103){
            $sql_junior = "SELECT `user_id`,`user_name`,`user_rank`,`reg_time` FROM ".$ecs->table('users')." WHERE `parent_id` = ".$v['user_id'];

            $res = $db->getRow($sql_junior);

            if ($res) {
                $v['junior_id'] = $res['user_id'];
                $v['junior_data'] = $res;
            }
        }

        
        //微信昵称
        if($v['wxid']){
            $sql_nickname = "SELECT `nickname` FROM `wxch_user` WHERE `wxid` = '".$v['wxid']."'";
            $nickname = $db->getOne($sql_nickname);
            $v['wx_nickname'] = $nickname;
        }else{
            $v['wx_nickname'] = '';
        }
        
        //所属上级
        $v['parent_name']='';
        if($v['parent_id']){
            $sql_parent_name = "SELECT `user_name` FROM ".$ecs->table('users')." WHERE `user_id` = ".$v['parent_id'];
            $parent_name = $db->getOne($sql_parent_name);
            $v['parent_name'] = $parent_name;
            
        }
        
    }

    // echo "<pre>";
    // print_r($user_list['user_list']);die;
    $smarty->assign('user_list',    $user_list['user_list']);
    $smarty->assign('filter',       $user_list['filter']);
    $smarty->assign('record_count', $user_list['record_count']);
    $smarty->assign('page_count',   $user_list['page_count']);
    $smarty->assign('full_page',    1);
    $smarty->assign('sort_user_id', '<img src="images/sort_desc.gif">');

    assign_query_info();
    $smarty->display('users1_list.htm');
}

/*------------------------------------------------------ */
//-- ajax返回用户列表
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
     $parent_ids=$_SESSION['parent_id'];
    $user_list = user_list_c($parent_ids);
    
 	foreach($user_list['user_list'] as &$v){
    	$v['qid'] = '';
    	$v['qr_url'] = '';
    	
    	if($v['user_rank']==99 || $v['user_rank']==102 || $v['user_rank']==103){
    		$sql_qr = "SELECT `qid`,`ticket` FROM `wxch_qr` WHERE `type` = 'tj' and `affiliate` = ".$v['user_id'];
    		$res = $db->getRow($sql_qr);
    		if($res){
    			$v['qid'] = $res['qid'];
    			$v['qr_url'] = "http://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".$res['ticket']; 
    		}
    	}  

        //所属下级 
        $v['junior_id']='';   
        if($v['user_rank']==99 || $v['user_rank']==102 || $v['user_rank']==103){
            $sql_junior = "SELECT `user_id`,`user_name`,`user_rank`,`reg_time` FROM ".$ecs->table('users')." WHERE `parent_id` = ".$v['user_id'];

            $res = $db->getRow($sql_junior);

            if ($res) {
                $v['junior_id'] = $res['user_id'];
                $v['junior_data'] = $res;
            }
        } 
        	
 		//微信昵称
        if($v['wxid']){
            $sql_nickname = "SELECT `nickname` FROM `wxch_user` WHERE `wxid` = '".$v['wxid']."'";
            $nickname = $db->getOne($sql_nickname);
            $v['wx_nickname'] = $nickname;
        }else{
            $v['wx_nickname'] = '';
        }
        
        //所属上级
        $v['parent_name']='';
        if($v['parent_id']){
            $sql_parent_name = "SELECT `user_name` FROM ".$ecs->table('users')." WHERE `user_id` = ".$v['parent_id'];
            $parent_name = $db->getOne($sql_parent_name);
            $v['parent_name'] = $parent_name;
            
        }
        
    }

    $smarty->assign('user_list',    $user_list['user_list']);
    $smarty->assign('filter',       $user_list['filter']);
    $smarty->assign('record_count', $user_list['record_count']);
    $smarty->assign('page_count',   $user_list['page_count']);

    $sort_flag  = sort_flag($user_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('users1_list.htm'), '', array('filter' => $user_list['filter'], 'page_count' => $user_list['page_count']));
}

/*------------------------------------------------------ */
//-- 添加会员帐号
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add')
{
    /* 检查权限 */
    admin_priv('users_manage');

    $user = array(  'rank_points'   => $_CFG['register_points'],
                    'pay_points'    => $_CFG['register_points'],
                    'sex'           => 0,
                    'credit_line'   => 0
                    );
    /* 取出注册扩展字段 */
    $sql = 'SELECT * FROM ' . $ecs->table('reg_fields') . ' WHERE type < 2 AND display = 1 AND id != 6 ORDER BY dis_order, id';
    $extend_info_list = $db->getAll($sql);
    $smarty->assign('extend_info_list', $extend_info_list);

    $smarty->assign('ur_here',          $_LANG['04_users_add']);
    $smarty->assign('action_link',      array('text' => $_LANG['03_users_list'], 'href'=>'users.php?act=list'));
    $smarty->assign('form_action',      'insert');
    $smarty->assign('user',             $user);
    $smarty->assign('special_ranks',    get_rank_list(true));

    assign_query_info();
    $smarty->display('user_info.htm');
}

/*------------------------------------------------------ */
//-- 添加会员帐号
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'insert')
{
    /* 检查权限 */
    admin_priv('users_manage');
    $username = empty($_POST['username']) ? '' : trim($_POST['username']);
    $password = empty($_POST['password']) ? '' : trim($_POST['password']);
    $email = empty($_POST['email']) ? '' : trim($_POST['email']);
    $usercity = empty($_POST['usercity']) ? '' :trim($_POST['usercity']);
    $sex = empty($_POST['sex']) ? 0 : intval($_POST['sex']);
    $sex = in_array($sex, array(0, 1, 2)) ? $sex : 0;
    $birthday = $_POST['birthdayYear'] . '-' .  $_POST['birthdayMonth'] . '-' . $_POST['birthdayDay'];
    $rank = empty($_POST['user_rank']) ? 0 : intval($_POST['user_rank']);
    $credit_line = empty($_POST['credit_line']) ? 0 : floatval($_POST['credit_line']);

    $users =& init_users();
    
    $re = $users->add_user($username, $password, $email);
    //print_r($re);
    //exit();

    if (!$re)
    {
        /* 插入会员数据失败 */
        if ($users->error == ERR_INVALID_USERNAME)
        {
            $msg = $_LANG['username_invalid'];
        }
        elseif ($users->error == ERR_USERNAME_NOT_ALLOW)
        {
            $msg = $_LANG['username_not_allow'];
        }
        elseif ($users->error == ERR_USERNAME_EXISTS)
        {
            $msg = $_LANG['username_exists'];
        }
        elseif ($users->error == ERR_INVALID_EMAIL)
        {
            $msg = $_LANG['email_invalid'];
        }
        elseif ($users->error == ERR_EMAIL_NOT_ALLOW)
        {
            $msg = $_LANG['email_not_allow'];
        }
        elseif ($users->error == ERR_EMAIL_EXISTS)
        {
            $msg = $_LANG['email_exists'];
        }
        else
        {
            //die('Error:'.$users->error_msg());
        }
        sys_msg($msg, 1);
    }

    /* 注册送积分 */
    if (!empty($GLOBALS['_CFG']['register_points']))
    {
        log_account_change($_SESSION['user_id'], 0, 0, $GLOBALS['_CFG']['register_points'], $GLOBALS['_CFG']['register_points'], $_LANG['register_points']);
    }

    /*把新注册用户的扩展信息插入数据库*/
    $sql = 'SELECT id FROM ' . $ecs->table('reg_fields') . ' WHERE type = 0 AND display = 1 ORDER BY dis_order, id';   //读出所有扩展字段的id
    $fields_arr = $db->getAll($sql);

    $extend_field_str = '';    //生成扩展字段的内容字符串
    $user_id_arr = $users->get_profile_by_name($username);
    foreach ($fields_arr AS $val)
    {
        $extend_field_index = 'extend_field' . $val['id'];
        if(!empty($_POST[$extend_field_index]))
        {
            $temp_field_content = strlen($_POST[$extend_field_index]) > 100 ? mb_substr($_POST[$extend_field_index], 0, 99) : $_POST[$extend_field_index];
            $extend_field_str .= " ('" . $user_id_arr['user_id'] . "', '" . $val['id'] . "', '" . $temp_field_content . "'),";
        }
    }
    $extend_field_str = substr($extend_field_str, 0, -1);

    if ($extend_field_str)      //插入注册扩展数据
    {
        $sql = 'INSERT INTO '. $ecs->table('reg_extend_info') . ' (`user_id`, `reg_field_id`, `content`) VALUES' . $extend_field_str;
        $db->query($sql);
    }

    /* 更新会员的其它信息 */
    $other =  array();
    $other['user_city'] = $usercity;
    $other['credit_line'] = $credit_line;
    $other['user_rank']  = $rank;
    $other['sex']        = $sex;
    $other['birthday']   = $birthday;
    $other['reg_time'] = local_strtotime(local_date('Y-m-d H:i:s'));

    $other['msn'] = isset($_POST['extend_field1']) ? htmlspecialchars(trim($_POST['extend_field1'])) : '';
    $other['qq'] = isset($_POST['extend_field2']) ? htmlspecialchars(trim($_POST['extend_field2'])) : '';
    $other['office_phone'] = isset($_POST['extend_field3']) ? htmlspecialchars(trim($_POST['extend_field3'])) : '';
    $other['home_phone'] = isset($_POST['extend_field4']) ? htmlspecialchars(trim($_POST['extend_field4'])) : '';
    $other['mobile_phone'] = isset($_POST['extend_field5']) ? htmlspecialchars(trim($_POST['extend_field5'])) : '';

    $db->autoExecute($ecs->table('users'), $other, 'UPDATE', "user_name = '$username'");

    /* 记录管理员操作 */
    admin_log($_POST['username'], 'add', 'users');
    
    if($rank == 99 || $rank == 102 || $rank == 103){
		//进行二维码添加
		//查询用户id
		$sql_user_id = "select `user_id` from ".$ecs->table('users')." where user_name = '".$username."'";
		$user_id_zj = $db->getOne($sql_user_id);
		$arr['scene'] = $username;
		$arr['affiliate'] = $user_id_zj;
		add_qr($arr,$db);
	}

    /* 提示信息 */
    $link[] = array('text' => $_LANG['go_back'], 'href'=>'users.php?act=list');
    sys_msg(sprintf($_LANG['add_success'], htmlspecialchars(stripslashes($_POST['username']))), 0, $link);

}

/*------------------------------------------------------ */
//-- 编辑用户帐号
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'edit')
{
    /* 检查权限 */
    admin_priv('users_manage');

    $sql = "SELECT u.user_name, u.sex,u.user_city, u.birthday, u.pay_points, u.rank_points, u.user_rank , u.user_money, u.frozen_money, u.credit_line, u.parent_id, u2.user_name as parent_username, u.qq, u.msn, u.office_phone, u.home_phone, u.mobile_phone".
        " FROM " .$ecs->table('users'). " u LEFT JOIN " . $ecs->table('users') . " u2 ON u.parent_id = u2.user_id WHERE u.user_id='$_GET[id]'";

    $row = $db->GetRow($sql);
    $row['user_name'] = addslashes($row['user_name']);
    $users  =& init_users();
    $user   = $users->get_user_info($row['user_name']);

    $sql = "SELECT u.user_id, u.sex, u.user_city,u.birthday, u.pay_points, u.rank_points, u.user_rank , u.user_money, u.frozen_money, u.credit_line, u.parent_id, u2.user_name as parent_username, u.qq, u.msn,
    u.office_phone, u.home_phone, u.mobile_phone".
        " FROM " .$ecs->table('users'). " u LEFT JOIN " . $ecs->table('users') . " u2 ON u.parent_id = u2.user_id WHERE u.user_id='$_GET[id]'";

    $row = $db->GetRow($sql);

    if ($row)
    {
        $user['user_id']        = $row['user_id'];
        $user['user_city']      = $row['user_city'];
        $user['sex']            = $row['sex'];
        $user['birthday']       = date($row['birthday']);
        $user['pay_points']     = $row['pay_points'];
        $user['rank_points']    = $row['rank_points'];
        $user['user_rank']      = $row['user_rank'];
        $user['user_money']     = $row['user_money'];
        $user['frozen_money']   = $row['frozen_money'];
        $user['credit_line']    = $row['credit_line'];
        $user['formated_user_money'] = price_format($row['user_money']);
        $user['formated_frozen_money'] = price_format($row['frozen_money']);
        $user['parent_id']      = $row['parent_id'];
        $user['parent_username']= $row['parent_username'];
        $user['qq']             = $row['qq'];
        $user['msn']            = $row['msn'];
        $user['office_phone']   = $row['office_phone'];
        $user['home_phone']     = $row['home_phone'];
        $user['mobile_phone']   = $row['mobile_phone'];
    }
    else
    {
          $link[] = array('text' => $_LANG['go_back'], 'href'=>'users.php?act=list');
          sys_msg($_LANG['username_invalid'], 0, $links);
//        $user['sex']            = 0;
//        $user['pay_points']     = 0;
//        $user['rank_points']    = 0;
//        $user['user_money']     = 0;
//        $user['frozen_money']   = 0;
//        $user['credit_line']    = 0;
//        $user['formated_user_money'] = price_format(0);
//        $user['formated_frozen_money'] = price_format(0);
     }

    /* 取出注册扩展字段 */
    $sql = 'SELECT * FROM ' . $ecs->table('reg_fields') . ' WHERE type < 2 AND display = 1 AND id != 6 ORDER BY dis_order, id';
    $extend_info_list = $db->getAll($sql);

    $sql = 'SELECT reg_field_id, content ' .
           'FROM ' . $ecs->table('reg_extend_info') .
           " WHERE user_id = $user[user_id]";
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
            case 1:     $extend_info_list[$key]['content'] = $user['msn']; break;
            case 2:     $extend_info_list[$key]['content'] = $user['qq']; break;
            case 3:     $extend_info_list[$key]['content'] = $user['office_phone']; break;
            case 4:     $extend_info_list[$key]['content'] = $user['home_phone']; break;
            case 5:     $extend_info_list[$key]['content'] = $user['mobile_phone']; break;
            default:    $extend_info_list[$key]['content'] = empty($temp_arr[$val['id']]) ? '' : $temp_arr[$val['id']] ;
        }
    }

    $smarty->assign('extend_info_list', $extend_info_list);

    /* 当前会员推荐信息 */
    $affiliate = unserialize($GLOBALS['_CFG']['affiliate']);
    $smarty->assign('affiliate', $affiliate);

    empty($affiliate) && $affiliate = array();

    if(empty($affiliate['config']['separate_by']))
    {
        //推荐注册分成
        $affdb = array();
        $num = count($affiliate['item']);
        $up_uid = "'$_GET[id]'";
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
                    $count++;
                }
            }
            $affdb[$i]['num'] = $count;
        }
        if ($affdb[1]['num'] > 0)
        {
            $smarty->assign('affdb', $affdb);
        }
    }


    assign_query_info();
    $smarty->assign('ur_here',          $_LANG['users_edit']);
    $smarty->assign('action_link',      array('text' => $_LANG['03_users_list'], 'href'=>'users.php?act=list&' . list_link_postfix()));
    $smarty->assign('user',             $user);
    $smarty->assign('form_action',      'update');
    $smarty->assign('qid',      $_GET['qid']);
    $smarty->assign('special_ranks',    get_rank_list(true));
    $smarty->display('user_info.htm');
}

/*------------------------------------------------------ */
//-- 编辑用户名
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'edit_name')
{
    /* 检查权限 */
    admin_priv('users_manage');		
	$user_id = $_REQUEST['id'];	
	//加盟商和服务商
	$parent_sql = "select user_id,user_name,user_rank from ".$ecs->table('users')." where user_rank = 103 and user_id != ".$user_id;
	$parent_arr = $db->getAll($parent_sql);
	//print_r($parent_arr);
	
	//用户信息
	$user_sql = "select user_id,user_rank,user_name,parent_id from ".$ecs->table('users')." where user_id = ".$user_id;
	$user = $db->getRow($user_sql);
	
	$smarty->assign('user',      $user);
	$smarty->assign('parent_arr',      $parent_arr);
    $smarty->assign('ur_here',          '编辑会员名称');
    $smarty->assign('form_action',      'update_name');
	$smarty->assign('qid',      $_GET['qid']);
    $smarty->display('user_info_name.htm');
}
/*------------------------------------------------------ */
//-- 更新用户帐号
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'update_name')
{
    /* 检查权限 */
    admin_priv('users_manage');
	
    $user_id = empty($_POST['id']) ? '' : trim($_POST['id']);
	$user_name = empty($_POST['user_name']) ? '' : trim($_POST['user_name']);
	$parent_id = empty($_POST['parent_id']) ? 0 : trim($_POST['parent_id']);
	$qid = empty($_POST['qid']) ? '' : trim($_POST['qid']);
	//print_r($_POST);
	
	$sql_sel = "select count(*) from ".$ecs->table('users')." where user_id != ".$user_id." and user_name = '".$user_name."'";
	$re = $db->getOne($sql_sel);
	if($re){
		sys_msg('用户名已存在', 1);
	}else{
		$sql="UPDATE ".$ecs->table('users')."SET parent_id=".$parent_id.", user_name='".$user_name."' WHERE user_id = ".$user_id;
		$db->query($sql);	
	}

    /* 记录管理员操作 */
    admin_log($user_name, 'edit', 'users');

    /* 提示信息 */
    $links[0]['text']    = $_LANG['goto_list'];
    $links[0]['href']    = 'users.php?act=list&' . list_link_postfix();
    $links[1]['text']    = $_LANG['go_back'];
    $links[1]['href']    = 'javascript:history.back()';
    
    //二维码
	if($qid){
		$update_sql = "UPDATE  `wxch_qr` SET  `scene` =  '$user_name' WHERE  `qid` = ".$qid;
		$db->query($update_sql);
	}
    sys_msg($_LANG['update_success'], 0, $links);
	
}

/*------------------------------------------------------ */
//-- 更新用户帐号
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'update')
{
    /* 检查权限 */
    admin_priv('users_manage');
    $username = empty($_POST['username']) ? '' : trim($_POST['username']);
    $password = empty($_POST['password']) ? '' : trim($_POST['password']);
    $email = empty($_POST['email']) ? '' : trim($_POST['email']);
    $sex = empty($_POST['sex']) ? 0 : intval($_POST['sex']);
    $sex = in_array($sex, array(0, 1, 2)) ? $sex : 0;
    $birthday = $_POST['birthdayYear'] . '-' .  $_POST['birthdayMonth'] . '-' . $_POST['birthdayDay'];
    $rank = empty($_POST['user_rank']) ? 0 : intval($_POST['user_rank']);
    $credit_line = empty($_POST['credit_line']) ? 0 : floatval($_POST['credit_line']);

    $users  =& init_users();

    if (!$users->edit_user(array('username'=>$username, 'password'=>$password, 'email'=>$email, 'gender'=>$sex, 'bday'=>$birthday ), 1))
    {
        if ($users->error == ERR_EMAIL_EXISTS)
        {
            $msg = $_LANG['email_exists'];
        }
        else
        {
            $msg = $_LANG['edit_user_failed'];
        }
        sys_msg($msg, 1);
    }
    if(!empty($password))
    {
			$sql="UPDATE ".$ecs->table('users'). "SET `ec_salt`='0' WHERE user_name= '".$username."'";
			$db->query($sql);
	}
    /* 更新用户扩展字段的数据 */
    $sql = 'SELECT id FROM ' . $ecs->table('reg_fields') . ' WHERE type = 0 AND display = 1 ORDER BY dis_order, id';   //读出所有扩展字段的id
    $fields_arr = $db->getAll($sql);
    $user_id_arr = $users->get_profile_by_name($username);
    $user_id = $user_id_arr['user_id'];

    foreach ($fields_arr AS $val)       //循环更新扩展用户信息
    {
        $extend_field_index = 'extend_field' . $val['id'];
        if(isset($_POST[$extend_field_index]))
        {
            $temp_field_content = strlen($_POST[$extend_field_index]) > 100 ? mb_substr($_POST[$extend_field_index], 0, 99) : $_POST[$extend_field_index];

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


    /* 更新会员的其它信息 */
    $other =  array();
    $other['credit_line'] = $credit_line;
    $other['user_rank'] = $rank;

    $other['msn'] = isset($_POST['extend_field1']) ? htmlspecialchars(trim($_POST['extend_field1'])) : '';
    $other['qq'] = isset($_POST['extend_field2']) ? htmlspecialchars(trim($_POST['extend_field2'])) : '';
    $other['office_phone'] = isset($_POST['extend_field3']) ? htmlspecialchars(trim($_POST['extend_field3'])) : '';
    $other['home_phone'] = isset($_POST['extend_field4']) ? htmlspecialchars(trim($_POST['extend_field4'])) : '';
    $other['mobile_phone'] = isset($_POST['extend_field5']) ? htmlspecialchars(trim($_POST['extend_field5'])) : '';

    $db->autoExecute($ecs->table('users'), $other, 'UPDATE', "user_name = '$username'");

    /* 记录管理员操作 */
    admin_log($username, 'edit', 'users');

    /* 提示信息 */
    $links[0]['text']    = $_LANG['goto_list'];
    $links[0]['href']    = 'users.php?act=list&' . list_link_postfix();
    $links[1]['text']    = $_LANG['go_back'];
    $links[1]['href']    = 'javascript:history.back()';
    
    //二维码 
	$qid = empty($_POST['qid']) ? '' : trim($_POST['qid']);
	if($qid){
		//修改二维码
//		if($rank == 99 || $rank == 102 || $rank == 103){	
//			$arr['id'] = $_POST['qid'];//二维码id		
//			$arr['scene'] = $username;
//			$arr['affiliate'] = $_POST['id'];
//			edit_qr($arr,$db);
//		}
	}else{
		//新增二维码
		if($rank == 99 || $rank == 102 || $rank == 103){	
			$arr['scene'] = $username;
			$arr['affiliate'] = $_POST['id'];//会员id
			add_qr($arr,$db);
		}		
	}
    
    

    sys_msg($_LANG['update_success'], 0, $links);

}

/*------------------------------------------------------ */
//-- 批量删除会员帐号
/*------------------------------------------------------ */

elseif ($_REQUEST['type'] == 'batch_remove')
{
    /* 检查权限 */
    admin_priv('users_drop');

    if (isset($_POST['checkboxes']))
    {
        $sql = "SELECT user_name FROM " . $ecs->table('users') . " WHERE user_id " . db_create_in($_POST['checkboxes']);
        $col = $db->getCol($sql);
        $usernames = implode(',',addslashes_deep($col));
        $count = count($col);
        /* 通过插件来删除用户 */
        $users =& init_users();
        $users->remove_user($col);

        admin_log($usernames, 'batch_remove', 'users');

        $lnk[] = array('text' => $_LANG['go_back'], 'href'=>'users.php?act=list');
        sys_msg(sprintf($_LANG['batch_remove_success'], $count), 0, $lnk);
    }
    else
    {
        $lnk[] = array('text' => $_LANG['go_back'], 'href'=>'users.php?act=list');
        sys_msg($_LANG['no_select_user'], 0, $lnk);
    }
}
elseif ($_POST['type'] == 'vip_update')
{
	/* 检查权限 */
	admin_priv('users_drop');
	if (isset($_POST['checkboxes']))
	{
		$sql = "SELECT user_id,user_name FROM " . $ecs->table('users') . " WHERE user_rank=0 and user_id " . db_create_in($_POST['checkboxes']);
		$col = $db->getCol($sql);
		if($col){
			foreach($col as $cols){
				$db->query('UPDATE ' .$ecs->table('users'). " SET user_rank = '99' WHERE user_id = '$cols'");
				$sql_user_id = "select `user_id` from ".$ecs->table('users')." where user_id = '".$cols."'";
				$user_id_zj = $db->getOne($sql_user_id);
				$arr['affiliate'] = $user_id_zj;
				add_qr($arr);
			}
		}
		$usernames = implode(',',addslashes_deep($col));
		$count = count($col);
		/* 通过插件来删除用户 */
		//$users =& init_users();
		//$users->remove_user($col);

		admin_log($usernames, 'batch_remove', 'users');

		$lnk[] = array('text' => $_LANG['go_back'], 'href'=>'users.php?act=list');
		sys_msg(sprintf("成功跟新会员", $count), 0, $lnk);
	}
	else
	{
		$lnk[] = array('text' => $_LANG['go_back'], 'href'=>'users.php?act=list');
		sys_msg($_LANG['no_select_user'], 0, $lnk);
	}
}

/* 编辑用户名 */
elseif ($_REQUEST['act'] == 'edit_username')
{
    /* 检查权限 */
    check_authz_json('users_manage');

    $username = empty($_REQUEST['val']) ? '' : json_str_iconv(trim($_REQUEST['val']));
    $id = empty($_REQUEST['id']) ? 0 : intval($_REQUEST['id']);

    if ($id == 0)
    {
        make_json_error('NO USER ID');
        return;
    }

    if ($username == '')
    {
        make_json_error($GLOBALS['_LANG']['username_empty']);
        return;
    }

    $users =& init_users();

    if ($users->edit_user($id, $username))
    {
        if ($_CFG['integrate_code'] != 'ECSHOP')
        {
            /* 更新商城会员表 */
            $db->query('UPDATE ' .$ecs->table('users'). " SET user_name = '$username' WHERE user_id = '$id'");
        }

        admin_log(addslashes($username), 'edit', 'users');
        make_json_result(stripcslashes($username));
    }
    else
    {
        $msg = ($users->error == ERR_USERNAME_EXISTS) ? $GLOBALS['_LANG']['username_exists'] : $GLOBALS['_LANG']['edit_user_failed'];
        make_json_error($msg);
    }
}

/*------------------------------------------------------ */
//-- 编辑email
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_email')
{
    /* 检查权限 */
    check_authz_json('users_manage');

    $id = empty($_REQUEST['id']) ? 0 : intval($_REQUEST['id']);
    $email = empty($_REQUEST['val']) ? '' : json_str_iconv(trim($_REQUEST['val']));

    $users =& init_users();

    $sql = "SELECT user_name FROM " . $ecs->table('users') . " WHERE user_id = '$id'";
    $username = $db->getOne($sql);


    if (is_email($email))
    {
        if ($users->edit_user(array('username'=>$username, 'email'=>$email)))
        {
            admin_log(addslashes($username), 'edit', 'users');

            make_json_result(stripcslashes($email));
        }
        else
        {
            $msg = ($users->error == ERR_EMAIL_EXISTS) ? $GLOBALS['_LANG']['email_exists'] : $GLOBALS['_LANG']['edit_user_failed'];
            make_json_error($msg);
        }
    }
    else
    {
        make_json_error($GLOBALS['_LANG']['invalid_email']);
    }
}

/*------------------------------------------------------ */
//-- 删除会员帐号
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'remove')
{
    /* 检查权限 */
    admin_priv('users_drop');

    $sql = "SELECT user_name FROM " . $ecs->table('users') . " WHERE user_id = '" . $_GET['id'] . "'";
    $username = $db->getOne($sql);
    /* 通过插件来删除用户 */
    $users =& init_users();
    $users->remove_user($username); //已经删除用户所有数据

    /* 记录管理员操作 */
    admin_log(addslashes($username), 'remove', 'users');

    /* 提示信息 */
    $link[] = array('text' => $_LANG['go_back'], 'href'=>'users.php?act=list');
    sys_msg(sprintf($_LANG['remove_success'], $username), 0, $link);
}

/*------------------------------------------------------ */
//--  收货地址查看
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'address_list')
{
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $sql = "SELECT a.*, c.region_name AS country_name, p.region_name AS province, ct.region_name AS city_name, d.region_name AS district_name ".
           " FROM " .$ecs->table('user_address'). " as a ".
           " LEFT JOIN " . $ecs->table('region') . " AS c ON c.region_id = a.country " .
           " LEFT JOIN " . $ecs->table('region') . " AS p ON p.region_id = a.province " .
           " LEFT JOIN " . $ecs->table('region') . " AS ct ON ct.region_id = a.city " .
           " LEFT JOIN " . $ecs->table('region') . " AS d ON d.region_id = a.district " .
           " WHERE user_id='$id'";
    $address = $db->getAll($sql);
    $smarty->assign('address',          $address);
    assign_query_info();
    $smarty->assign('ur_here',          $_LANG['address_list']);
    $smarty->assign('action_link',      array('text' => $_LANG['03_users_list'], 'href'=>'users.php?act=list&' . list_link_postfix()));
    $smarty->display('user_address_list.htm');
}

/*------------------------------------------------------ */
//-- 脱离推荐关系
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'remove_parent')
{
    /* 检查权限 */
    admin_priv('users_manage');

    $sql = "UPDATE " . $ecs->table('users') . " SET parent_id = 0 WHERE user_id = '" . $_GET['id'] . "'";
    $db->query($sql);

    /* 记录管理员操作 */
    $sql = "SELECT user_name FROM " . $ecs->table('users') . " WHERE user_id = '" . $_GET['id'] . "'";
    $username = $db->getOne($sql);
    admin_log(addslashes($username), 'edit', 'users');

    /* 提示信息 */
    $link[] = array('text' => $_LANG['go_back'], 'href'=>'users.php?act=list');
    sys_msg(sprintf($_LANG['update_success'], $username), 0, $link);
}

/*------------------------------------------------------ */
//-- 查看用户推荐会员列表
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'aff_list')
{
    /* 检查权限 */
    admin_priv('users_manage');
    $smarty->assign('ur_here',      $_LANG['03_users_list']);

    $auid = $_GET['auid'];
    $user_list['user_list'] = array();

    $affiliate = unserialize($GLOBALS['_CFG']['affiliate']);
    $smarty->assign('affiliate', $affiliate);

    empty($affiliate) && $affiliate = array();

    $num = count($affiliate['item']);
    $up_uid = "'$auid'";
    $all_count = 0;
    for ($i = 1; $i<=$num; $i++)
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
                $count++;
            }
        }
        $all_count += $count;

        if ($count)
        {
            $sql = "SELECT user_id, user_name, '$i' AS level, email, is_validated, user_money, frozen_money, rank_points, pay_points, reg_time ".
                    " FROM " . $GLOBALS['ecs']->table('users') . " WHERE user_id IN($up_uid)" .
                    " ORDER by level, user_id";
            $user_list['user_list'] = array_merge($user_list['user_list'], $db->getAll($sql));
        }
    }

    $temp_count = count($user_list['user_list']);
    for ($i=0; $i<$temp_count; $i++)
    {
        $user_list['user_list'][$i]['reg_time'] = local_date($_CFG['date_format'], $user_list['user_list'][$i]['reg_time']);
    }

    $user_list['record_count'] = $all_count;

    $smarty->assign('user_list',    $user_list['user_list']);
    $smarty->assign('record_count', $user_list['record_count']);
    $smarty->assign('full_page',    1);
    $smarty->assign('action_link',  array('text' => $_LANG['back_note'], 'href'=>"users.php?act=edit&id=$auid"));

    assign_query_info();
    $smarty->display('affiliate_list.htm');
}


/**
 *  返回用户列表数据
 *
 * @access  public
 * @param
 *
 * @return void
 */
function user_list_c($parent_id)
{
    $result = get_filter();
    if ($result === false)
    {
        /* 过滤条件 */
        $filter['keywords'] = empty($_REQUEST['keywords']) ? '' : trim($_REQUEST['keywords']);
        if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
        {
            $filter['keywords'] = json_str_iconv($filter['keywords']);
        }
        $filter['rank'] = empty($_REQUEST['rank']) ? 0 : intval($_REQUEST['rank']);
        $filter['pay_points_gt'] = empty($_REQUEST['pay_points_gt']) ? 0 : intval($_REQUEST['pay_points_gt']);
        $filter['pay_points_lt'] = empty($_REQUEST['pay_points_lt']) ? 0 : intval($_REQUEST['pay_points_lt']);

        $filter['sort_by']    = empty($_REQUEST['sort_by'])    ? 'user_id' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC'     : trim($_REQUEST['sort_order']);

        $ex_where = ' WHERE 1 ';
        if ($filter['keywords'])
        {
            $ex_where .= " AND user_name LIKE '%" . mysql_like_quote($filter['keywords']) ."%'";
        }
        if ($filter['rank'])
        {
            $sql = "SELECT min_points, max_points, special_rank FROM ".$GLOBALS['ecs']->table('user_rank')." WHERE rank_id = '$filter[rank]'";
            $row = $GLOBALS['db']->getRow($sql);
            if ($row['special_rank'] > 0)
            {
                /* 特殊等级 */
                $ex_where .= " AND user_rank = '$filter[rank]' ";
            }
            else
            {
                $ex_where .= " AND rank_points >= " . intval($row['min_points']) . " AND rank_points < " . intval($row['max_points']);
            }
        }
        if ($filter['pay_points_gt'])
        {
             $ex_where .=" AND pay_points >= '$filter[pay_points_gt]' ";
        }
        if ($filter['pay_points_lt'])
        {
            $ex_where .=" AND pay_points < '$filter[pay_points_lt]' ";
        }

        $ex_where .= " and parent_id=".$parent_id;

        $filter['record_count'] = $GLOBALS['db']->getOne("SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('users') . $ex_where);

        

        /* 分页大小 */
        $filter = page_and_size($filter);
        $sql = "SELECT user_id, user_rank, user_name,parent_id, email, is_validated, user_money, frozen_money, rank_points, pay_points, reg_time,nickname".
                " FROM " . $GLOBALS['ecs']->table('users') .' as u LEFT JOIN '.$GLOBALS['ecs']->table('weixin_user').' as w on u.user_id=w.ecuid '. $ex_where .
                " ORDER by " . $filter['sort_by'] . ' ' . $filter['sort_order'] .
                " LIMIT " . $filter['start'] . ',' . $filter['page_size'];

        $filter['keywords'] = stripslashes($filter['keywords']);
        //echo $sql;die();
        set_filter($filter, $sql);
    }
    else
    {   
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }

    $user_list = $GLOBALS['db']->getAll($sql);

    $count = count($user_list);
    for ($i=0; $i<$count; $i++)
    {
        $user_list[$i]['reg_time'] = local_date($GLOBALS['_CFG']['date_format'], $user_list[$i]['reg_time']);
    }

    $arr = array('user_list' => $user_list, 'filter' => $filter,
        'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}

//生成二维码
function add_qr($arr)
{
    $scene = $arr['scene'];
    $user_rank=$arr['rank'];
    $type = 'tj';
    $affiliate = intval($arr['affiliate']);
    $function = 'tuijian_self';
    $dateline = time();
    $endtime = time()+3600*24*30;
    if($user_rank == '99'){
        $action_name = 'QR_SCENE';
        $expire_seconds =3600*24*30;
    }else{
        $action_name = 'QR_LIMIT_STR_SCENE';
    }
    $ret = $GLOBALS['db']->getRow("SELECT * FROM `wxch_qr` WHERE `action_name` = 'QR_LIMIT_SCENE' ORDER BY `scene_id` DESC");
    $scene_id = $affiliate;
    //检测是否已经添加过
    $is_add_qr = $GLOBALS['db']->getRow("SELECT * FROM `wxch_qr` WHERE `function` = 'tuijian_self' and affiliate = ".$affiliate);

    if($user_rank == '99'){
	    $json_arr = array('expire_seconds'=>$expire_seconds,'action_name'=>$action_name,'action_info'=>array('scene'=>array('scene_id'=>$scene_id)));
	}else{
	    $json_arr = array('action_name'=>$action_name,'action_info'=>array('scene'=>array('scene_str'=>"$scene_id")));
	}
    $data = json_encode($json_arr);
    $access_token=getAccessToken();
    if(strlen($access_token) >= 64) 
	{   
	    if($user_rank == '99'){
	        $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$access_token;
	    }else{
    		$url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$access_token;
	   }
	   $res_json = curl_grab_page($url, $data);
	   $json = json_decode($res_json);
	}
	$ticket = $json->ticket;
	//access_token无效重新获取
	if(!$ticket){
		$ret = $GLOBALS['db']->getRow("SELECT `access_token` FROM `ecs_weixin_config` WHERE `id` = 1");
		$access_token = $ret['access_token'];
		if(strlen($access_token) >= 64) 
		{
    		if($user_rank == '99'){
    	        $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$access_token;
    	    }else{
        		$url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$access_token;
    	    }
			$res_json = curl_grab_page($url, $data);
			$json = json_decode($res_json);
		}
		$ticket = $json->ticket;
	}
	
	//print_r($ticket);
	if($ticket)
	{
		$ticket_url = urlencode($ticket);
		$ticket_url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$ticket_url;
		$qrimg = curl_get_contents($ticket_url);
		
		//print_r($qrimg);
		//exit();
		$time = time();
		$path = '../images/upload/'.$time.'.jpg';
		$qr_path = '/images/upload/'.$time.'.jpg';
		$qr_path = '';
		$is_update='false';
		if($is_add_qr){
		    if($user_rank == '99'){
		            $is_update = 'true';
		    }else{
		        return false;
		    }
		
		}
		if($is_update == 'true'){
		   
		   $insert_sql = "UPDATE `wxch_qr` SET endtime='".$endtime."', ticket='".$ticket."' WHERE affiliate=".$affiliate;
		}else{
		    $insert_sql = "INSERT INTO `wxch_qr` (`type`,`action_name`,`ticket`, `scene_id`, `scene` ,`qr_path`,`function`,`affiliate`,`endtime`,`dateline`) VALUES
		    ('$type','$action_name', '$ticket',$scene_id, '$scene' ,'$qr_path','$function','$affiliate','$endtime','$dateline')";
		}
		$GLOBALS['db']->query($insert_sql);
	}
}
//获取微信AccessToken
function getAccessToken() {
    $ret = $GLOBALS['db'] -> getRow("SELECT * FROM `ecs_weixin_config` WHERE `id` = 1");
    $appid = $ret['appid'];
    $appsecret = $ret['appsecret'];
    $dateline = $ret['dateline'];
    $time = time();
    //if (($time - $dateline) > 6000) {
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";
        $ret_json = curl_get_contents($url);
        $ret = json_decode($ret_json);
        if ($ret -> access_token) {
            $GLOBALS['db'] -> query("UPDATE `ecs_weixin_config` SET `access_token` = '$ret->access_token',`dateline` = '$time' WHERE `ecs_weixin_config`.`id` =1;");
        }
    //}
    $ret = $GLOBALS['db'] -> getOne("SELECT `access_token` FROM `ecs_weixin_config` WHERE `id` = 1");
    return $ret;
}

//$arr['scene']会员名称
//$arr['affiliate']会员id
//$arr['id']二维码id
function edit_qr($arr,$db)
{
    $id = $arr['id'];
	$scene = $arr['scene'];
	$affiliate = intval($arr['affiliate']);
	$function = 'tuijian_self';
	$dateline = time();
	$endtime = 0;
	if($endtime) 
	{
		$endtime = time()+60*60*$endtime;
	}
	$ticket = $db->getOne("SELECT `ticket` FROM `wxch_qr` WHERE `qid` = '$id' ");
	if($ticket)
	{
		$ticket_url = urlencode($ticket);
		$ticket_url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$ticket_url;
		$qrimg = curl_get_contents($ticket_url);
		$time = time();
		$path = '../images/upload/'.$time.'.jpg';
		@file_put_contents($path, $qrimg);
		$qr_path = '/images/upload/'.$time.'.jpg';
//			if(@filesize(ROOT_PATH.$qr_path)<10240) 
//			{
			$qr_path = '';
//			}
		
		$update_sql = "UPDATE  `wxch_qr` SET  `scene` =  '$scene',`function` = '$function',`qr_path` = '$qr_path',`affiliate` = '$affiliate',`endtime` = '$endtime',`dateline` = '$dateline' WHERE  `qid` ='$id';";
		$GLOBALS['db']->query($update_sql);
		
	}
}


function htmltowei($contents) 
{
	$contents = strip_tags($contents,'<br>');
	$contents = str_replace('<br />',"\r\n",$contents);
	$contents = str_replace('&quot;','"',$contents);
	$contents = str_replace('&nbsp;','',$contents);
	return $contents;
}
function access_token($db) 
{
	$ret =$GLOBALS['db']->getRow("SELECT * FROM `wxch_config` WHERE `id` = 1");
	$appid = $ret['appid'];
	$appsecret = $ret['appsecret'];
	$dateline = $ret['dateline'];
	$time = time();
	if(($time - $dateline) > 7200) 
	{
		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";
		$ret_json = curl_get_contents($url);
		$ret = json_decode($ret_json);
		if($ret->access_token)
		{
			$GLOBALS['db']->query("UPDATE `wxch_config` SET `access_token` = '$ret->access_token',`dateline` = '$time' WHERE `wxch_config`.`id` =1;");
		}
	}
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
function curl_grab_page($url,$data,$proxy='',$proxystatus='',$ref_url='') 
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
	curl_setopt($ch, CURLOPT_TIMEOUT, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	if ($proxystatus == 'true') 
	{
		curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, TRUE);
		curl_setopt($ch, CURLOPT_PROXY, $proxy);
	}
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	if(!empty($ref_url))
	{
		curl_setopt($ch, CURLOPT_HEADER, TRUE);
		curl_setopt($ch, CURLOPT_REFERER, $ref_url);
	}
	curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	ob_start();
	return curl_exec ($ch);
	ob_end_clean();
	curl_close ($ch);
	unset($ch);
}

?>