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
 * $Id: user_p.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');



/* 用户登录界面 */
if ($_REQUEST['act'] == '')
{
//    var_dump($smarty);die();
    $smarty->display('user_passport_p.dwt');
}


/* 处理会员的登录 */
elseif ($_REQUEST['act'] == 'act_login')
{	
  
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? md5(trim($_POST['password'])) : '';
    $back_act = isset($_POST['back_act']) ? trim($_POST['back_act']) : '';	
 
    $sql_zj1 = "select * from ".$ecs->table('suppliers')." where username = '".$username."'";

    $re = $db->getRow($sql_zj1);
	$psd = $re['password'];	
	if ( $psd == $password)
    {
    	$username = $_POST['username'];
    	//exit;
    	header('location:user_p.php?act=list&username='.$username);	
    }
    else
    {
    	
        $_SESSION['login_fail'] ++ ;
        show_message_c($_LANG['p_login_failure'], $_LANG['relogin_lnk'], 'user_p.php', 'error');
    }
}
/* 退出会员中心 */
elseif ($_REQUEST['act'] == 'logout')
{
	header('location:user_p.php');
}


/*确认发货*/
elseif ($_REQUEST['act'] == 'check')
{
	
	$send_company = $_GET['send_company']?$_GET['send_company']:'';
   	$send_sn = $_GET['send_sn']?$_GET['send_sn']:'';
    $send_time = time();
    $id = $_GET['id'];
    $username = $_GET['username'];
    $result = $GLOBALS['db']->getRow("select `send_status`,`order_sn` from".$ecs->table('factory')."where id='".$id."'");
    $send_status = $result['send_status'];
    $order_sn = $result['order_sn'];
    $fw_time = time();
    $jx_time = time();
    if($send_status == 1){
        $sql1 = "update ". $ecs->table('factory') . " set `status`=4,`fw_time`='$fw_time',`jx_time`='$jx_time',`send_sn`='$send_sn',`send_company`='$send_company',`send_time`='$send_time' where id=".$id;
        $db->query($sql1);
        $sql2 = "update ". $ecs->table('order_info') . " set `order_status`=5 ,`shipping_status`=1,`pay_status`=2 where order_sn=".$order_sn;
        $db->query($sql2);
        
    }else{
        $sql1 = "update ". $ecs->table('factory') . " set `status`=2,`send_sn`='$send_sn',`send_company`='$send_company',`send_time`='$send_time' where id=".$id;
        $db->query($sql1);
        $sql2 = "update ". $ecs->table('order_info') . " set `shipping_status`=1 where order_sn=".$order_sn;
        $db->query($sql2);    
    }
    header('location:user_p.php?act=list&username='.$username);	
}

/*------------------------------------------------------ */
//-- 翻页、搜索、排序   工厂列表页
/*------------------------------------------------------ */
else if ($_REQUEST['act'] == 'list')
{
	//var_dump($_GET);
	$username = isset($_GET['username']) ? trim($_GET['username']) : '';
	$status = isset($_GET['status']) ? trim($_GET['status']) : '';
	$sql = "select `suppliers_id` from".$ecs->table('suppliers')."where username='".$username."'" ;
    $suppliers_id = $db->getOne($sql);   
    $list = get_123_list($suppliers_id);
    //var_dump($list); 
    foreach ($list['item'] as $k8 => $v8) {
    		$list['item'][$k8]['add_time'] = date('Y-m-d',$v8['add_time']);
    		$list['item'][$k8]['send_time'] = date('Y-m-d',$v8['send_time']);
    	}  
    
    //var_dump($list['item']);
    foreach ($list['item'] as $key => $value) {
        $goos_attr = $value['goods_attr'];
        $list['item'][$key]['goods_attr'] = substr($goos_attr,7);
    }
    $smarty->assign('goods_list', $list['item']);
    //var_dump($list['item']); 
    $smarty->assign('filter',       $list['filter']);
    //var_dump( $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);
    $smarty->assign('username', $username );
    $smarty->display('123.dwt');
}
/**
 * 获取订单商品列表
 * @access  public
 * @return  array
 */
function get_123_list($suppliers_id)
{
    $where = '1';
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
    $where .= (!empty($suppliers_id)) ? " AND suppliers_id=" . $suppliers_id : '';
    $sql = "SELECT count(*) FROM " .$GLOBALS['ecs']->table('factory'). " WHERE $where";
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);

    /* 分页大小 */
    $filter = page_and_size($filter);
    //var_dump($filter);

    /* 获取数据 */
    $arr = array();
    $sql  = "SELECT * FROM " .$GLOBALS['ecs']->table('factory'). " WHERE $where and pay_status='2' ORDER BY  add_time DESC,status ASC  LIMIT  $filter[start] , $filter[page_size]";
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
