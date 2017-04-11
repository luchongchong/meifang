<?php
/**
 * MEIFANG 程序说明
 * =========================================================== 
 * 版权所有 2005-2014 上海优辉商务，并保留所有权利。
 * 网站地址: http://www.j345.net
 * ----------------------------------------------------------
 * 优辉网络,共创你我
 * ==========================================================
 * $Author: liubo $
 */

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
//门店列表
if ($_REQUEST['act'] == 'list')
{
	
	$list = get_construction_list();
	foreach ($list['item'] as $key => $value) {
    	$userid = $value['userid'];
    	$sql = "SELECT `user_name` FROM " . $GLOBALS['ecs']->table('users')."where user_id =".$userid;
    	$res= $GLOBALS['db']->getAll($sql);
	}
	$list['item'][$key]['username'] = $res[0]['user_name'];
    $smarty->assign('construction_list', $list['item']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);
	
	$smarty->assign('full_page',    1);
	$smarty->display('construction_list.htm');
	
}
else if ($_REQUEST['act'] == 'add')//添加施工队
{	
	$smarty->assign('step',add);
	$smarty->assign('user_list_1',     get_user_list_1());
	$smarty->assign('country_list',       get_regions());
	$smarty->assign('lang',              $_LANG);
	$smarty->assign('province_list',       get_regions(1,1));
	$smarty->display('construction_add.htm');
}
else if ($_REQUEST['act'] == 'add_exe')//添加门店执行
{
	$arr=array();
	$arr['construction_name']=$_POST['construction_name'];
	$arr['phone']=$_POST['phone'];
	$arr['userid']=$_POST['userid'];
	$sql = "SELECT * FROM " . $GLOBALS['ecs']->table('construction')."where `construction_name`='".$_POST['construction_name']."'";
	$res= $GLOBALS['db']->getAll($sql);
	if($res){
		sys_msg('施工队名已存在，请重新编辑',1,$link);
		exit;
	}
	$arr['address']=$_POST['address'];
	$arr['region_1']=$_POST['province'];
	$arr['region_2']=$_POST['city'];
	$arr['region_3']=$_POST['district'];
	$res = $db->autoExecute($ecs->table('construction'),$arr,'INSERT');
	if($res)
	{
		$link[0]['text'] = '返回到施工队列表页';
	    $link[0]['href'] = '?act=list';
		sys_msg('添加成功',0,$link);
	}
	else
	{
		sys_msg('添加失败',1,$link);
	}
}
else if ($_REQUEST['act'] == 'edit')//编辑门店页面
{        
	$construction_id = $_GET['construction_id'];
	$sql = "select * from {$ecs->table('construction')} where construction_id={$construction_id}";
	$construction_d = $db->getRow($sql);
	$user_id = $construction_d['userid'];
	$sql1 = "select `user_name` from {$ecs->table('users')} where user_id={$user_id}";
	$username = $db->getRow($sql1);
	//默认省市县
	$region_id_1 = $store_d['region_1'];
	$name_1 = get_regions_name($region_id_1);
	$region_id_2 = $store_d['region_2'];
	$name_2 = get_regions_name($region_id_2);
	$region_id_3 = $store_d['region_3'];
	$name_3 = get_regions_name($region_id_3);
	$smarty->assign('name_1',$name_1);
	$smarty->assign('name_2',$name_2);
	$smarty->assign('name_3',$name_3);
	$smarty->assign('username',$username);
	$smarty->assign('construction_d',$construction_d);	
	$smarty->assign('is_edit',1);
	$smarty->assign('step',edit);
	
	$smarty->assign('lang',         $_LANG);
	$smarty->assign('user_list_1',     get_user_list_1());
	$smarty->assign('country_list',       get_regions());
	$smarty->assign('province_list',       get_regions(1,1));
	$smarty->display('construction_add.htm');
}
else if ($_REQUEST['act'] == 'edit_exe')//编辑执行
{

	$construction_id = $_POST['construction_id'];
	$construction_name = $GLOBALS['db']->getone("select `construction_name` from".$ecs->table('construction')."where construction_id='". $construction_id."'");
	$arr=array();
	$arr['construction_name']=$_POST['construction_name'];
	$arr['tel']=$_POST['tel'];
	$arr['userid']=$_POST['userid'];
	$arr['long_1']=$_POST['long'];
	$arr['lat']=trim($_POST['lat']);
//	$arr['city']=$_POST['city'];
	if($construction_name != $arr['construction_name']){
		$sql = "SELECT * FROM " . $GLOBALS['ecs']->table('construction')."where `construction_name`='".$_POST['construction_name']."'";
		$res= $GLOBALS['db']->getAll($sql);
		if($res){
			sys_msg('施工队已存在，请重新编辑',1,$link);
			exit;
		}
	}	
	$arr['address']=$_POST['address'];
	$arr['region_1']=$_POST['province'];
	$arr['region_2']=$_POST['city'];
	$arr['region_3']=$_POST['district'];
	
	$res = $db->autoExecute($ecs->table('construction'),$arr,'UPDATE',"construction_id={$construction_id}");
	if($res)
	{
		$link[0]['text'] = '返回到施工队列表页';
	    $link[0]['href'] = '?act=list';
		sys_msg('编辑成功',0,$link);
	}
	else
	{
		sys_msg('编辑失败',1,$link);
	}
}
// else if($_REQUEST['act'] == 'clerkadd'){
//     //店员添加
//     //$smarty->assign('step',);
//     //$smarty->assign('user_list_1',     get_user_list_1());
//     //$smarty->assign('user_list_2',     get_user_list_2());
//     //$smarty->assign('country_list',       get_regions());
//     //$smarty->assign('lang',              $_LANG);
//     //$smarty->assign('province_list',       get_regions(1,1));
//     $smarty->display('store_add.htm');
    
// }
/*------------------------------------------------------ */
//-- 翻页、搜索、排序
/*------------------------------------------------------ */
else if ($_REQUEST['act'] == 'query')
{
    
    $list = get_construction_list();
    foreach ($list['item'] as $key => $value) {
	    $userid = $value['userid'];
    	$parent_id = $value['parent_id'];
    	$sql = "SELECT `user_name` FROM " . $GLOBALS['ecs']->table('users')."where user_id =".$userid;
	    $res= $GLOBALS['db']->getAll($sql);
	    $list['item'][$key]['username'] = $res[0]['user_name'];
    }

    $smarty->assign('construction_list', $list['item']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);
    make_json_result($smarty->fetch('construction_list.htm'),'',array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}

elseif ($_REQUEST['act'] == 'remove')
{
	
    $id = intval($_GET['id']);

    $sql = "DELETE FROM " .$ecs->table('construction'). " WHERE construction_id = '$id'";
    $res = $db->query($sql);
	$url = 'construction.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);
    ecs_header("Location: $url\n");
    exit;
}



function get_regions_store($type = 0)
{
    $sql = 'SELECT region_id, parent_id, region_name FROM ' . $GLOBALS['ecs']->table('region') .
            " WHERE region_type = '$type' ";

    return $GLOBALS['db']->GetAll($sql);
}

/*查询region_name*/
function get_regions_name($region_id)
{
    $sql = 'SELECT region_name FROM ' . $GLOBALS['ecs']->table('region') .
            " WHERE region_id = '$region_id' ";

    return $GLOBALS['db']->GetOne($sql);
}

/**
 * 获取施工队列表
 * @access  public
 * @return  array
 */
function get_construction_list()
{
	$where = '1';
    /* 查询条件 */
    $filter['keywords']     = empty($_REQUEST['keywords']) ? 0 : trim($_REQUEST['keywords']);
    if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
    {
        $filter['keywords'] = json_str_iconv($filter['keywords']);
    }

    $where .= (!empty($filter['keywords'])) ? " AND concat(`name`,`shop_no`) LIKE '%" . $filter['keywords']. "%' " : '';

    $sql = "SELECT count(*) FROM " .$GLOBALS['ecs']->table('construction'). " WHERE $where";
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);

    /* 分页大小 */
    $filter = page_and_size($filter);
        
    /* 获取门店数据 */
    $arr = array();
    $sql  = "SELECT * FROM " .$GLOBALS['ecs']->table('construction'). " WHERE $where  ORDER BY construction_id desc LIMIT  $filter[start] , $filter[page_size]";
    $res  = $GLOBALS['db']->getAll($sql);
    foreach($res as &$v){
        //省份
        $sql_1 = "select `region_name` FROM ".$GLOBALS['ecs']->table('region')." where region_id = ".$v['region_1'];
        $v['region_1_name'] = $GLOBALS['db']->getOne($sql_1);
        //城市
        $sql_2 = "select `region_name` FROM ".$GLOBALS['ecs']->table('region')." where region_id = ".$v['region_2'];
        $v['region_2_name'] = $GLOBALS['db']->getOne($sql_2);
        //县区
        $sql_3 = "select `region_name` FROM ".$GLOBALS['ecs']->table('region')." where region_id = ".$v['region_3'];
        $v['region_3_name'] = $GLOBALS['db']->getOne($sql_3);
    }
    $filter['keywords'] = stripslashes($filter['keywords']);
    $arr = array('item' => $res, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    return $arr;
}
/**
 * 取得会员用户名和id列表
 * @return  array   会员等级列表
 */
//加盟商
function get_user_list_1()
{
    $sql = "SELECT `user_id`,`user_name` FROM " . $GLOBALS['ecs']->table('users')."where user_rank = 102 ";

    return $GLOBALS['db']->getAll($sql);
}

?>