<?php
 /**
 * MEIFANG 订单统计
 * ============================================================================ 
 * 版权所有 2005-2014 上海优辉商务，并保留所有权利。
 * 网站地址: http://www.j345.net
 * ----------------------------------------------------------------------------
 * 优辉网络,共创你我
 * ============================================================================
 * $Author: jack $
 * $Id: order_stats.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_order.php');
require_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/admin/statistic.php');

$smarty->assign('lang', $_LANG);
 admin_priv('order_status');
/* act操作项的初始化 */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'list';
}
else
{
    $_REQUEST['act'] = trim($_REQUEST['act']);
}



/*------------------------------------------------------ */
//-- 发货单列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    /* 检查权限 */
   

    /* 查询 */
    $result = delivery_list();
    /*厂家列表*/
    $factory = $GLOBALS['db']->getAll("select * from".$GLOBALS['ecs']->table('suppliers'));
    /*服务商列表*/
    $parent_facilitator = $GLOBALS['db']->getAll("select * from".$GLOBALS['ecs']->table('users')."where user_rank='103'");
     /*施工门店列表*/
     $facilitator = $GLOBALS['db']->getAll("select * from".$GLOBALS['ecs']->table('users')."where user_rank='102'");
    /* 模板赋值 */
    $smarty->assign('ur_here', $_LANG['11_order_list']);

    $smarty->assign('os_unconfirmed',   OS_UNCONFIRMED);
    $smarty->assign('cs_await_pay',     CS_AWAIT_PAY);
    $smarty->assign('cs_await_ship',    CS_AWAIT_SHIP);
    $smarty->assign('full_page',        1);
    $smarty->assign('factory',   $factory);
    $smarty->assign('parent_facilitator',   $parent_facilitator);
    $smarty->assign('facilitator',   $facilitator);
    $smarty->assign('delivery_list',   $result['delivery']);
    $smarty->assign('filter',       $result['filter']);
    $smarty->assign('record_count', $result['record_count']);
    $smarty->assign('page_count',   $result['page_count']);
    $smarty->assign('sort_update_time', '<img src="images/sort_desc.gif">');

    /* 显示模板 */
    assign_query_info();
    $smarty->display('order_status.htm');
}


/*------------------------------------------------------ */
//-- 搜索、排序、分页
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'delivery_query')
{
    /* 检查权限 */
    admin_priv('delivery_view');

    $result = delivery_list();

    $smarty->assign('delivery_list',   $result['delivery']);
    $smarty->assign('filter',       $result['filter']);
    $smarty->assign('record_count', $result['record_count']);
    $smarty->assign('page_count',   $result['page_count']);

    $sort_flag = sort_flag($result['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);
    make_json_result($smarty->fetch('order_status.htm'), '', array('filter' => $result['filter'], 'page_count' => $result['page_count']));
}

/**
 *  获取发货单列表信息
 *
 * @access  public
 * @param
 *
 * @return void
 */
function delivery_list()
{
    $result = get_filter();
    if ($result === false)
    {
        $aiax = isset($_GET['is_ajax']) ? $_GET['is_ajax'] : 0;

        /* 过滤信息 */
        $filter['goods_attr'] = empty($_REQUEST['goods_attr']) ? '' : trim($_REQUEST['goods_attr']);
        $filter['order_sn'] = empty($_REQUEST['order_sn']) ? '' : trim($_REQUEST['order_sn']);
        $filter['fag'] = empty($_REQUEST['fag']) ? '' : trim($_REQUEST['fag']);
        $filter['fuwu'] = empty($_REQUEST['fuwu']) ? '' : trim($_REQUEST['fuwu']);
        $filter['sg'] = empty($_REQUEST['sg']) ? '' : trim($_REQUEST['sg']);
        $filter['status'] = empty($_REQUEST['status']) ? '' : trim($_REQUEST['status']);

        // $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'update_time' : trim($_REQUEST['sort_by']);
        // $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        $where = 'WHERE 1 ';
        if ($filter['order_sn'])
        {
            $where .= " AND order_sn LIKE '%" . mysql_like_quote($filter['order_sn']) . "%'";
        }
        if ($filter['goods_attr'])
        {
            $where .= " AND goods_attr LIKE '%" . mysql_like_quote($filter['goods_attr']) . "%'";
        }

        if ($filter['status'] == 1 )
        {
            $where .= " AND status  =". $_REQUEST['status'];
        }
        elseif($filter['status'] == 2)
        {
            $where .= " AND status  >= 2";
        }
        elseif($filter['status'] == 3)
        {
            $where .= " AND status  >= 3";
        }
        elseif($filter['status'] == 4){
            $where .= " AND status  >= 4";
        }
        else{
            $where .= " AND status  >= 1";
        }
        if ($filter['fag'])
        {
            $where .= " AND suppliers_id =".$_REQUEST['fag'];
        }
        if ($filter['fuwu'])
        {
            $where .= " AND parent_facilitator_id =".$_REQUEST['fuwu'];
        }
        if ($filter['sg'])
        {
            $where .= " AND facilitator_id =".$_REQUEST['sg'];
        }
        /* 获取管理员信息 */
        $admin_info = admin_info();

        /* 如果管理员属于某个办事处，只列出这个办事处管辖的发货单 */
        if ($admin_info['agency_id'] > 0)
        {
            $where .= " AND agency_id = '" . $admin_info['agency_id'] . "' ";
        }

        /* 如果管理员属于某个供货商，只列出这个供货商的发货单 */
        if ($admin_info['suppliers_id'] > 0)
        {
            $where .= " AND suppliers_id = '" . $admin_info['suppliers_id'] . "' ";
        }

        /* 分页大小 */
        $filter['page'] = empty($_REQUEST['page']) || (intval($_REQUEST['page']) <= 0) ? 1 : intval($_REQUEST['page']);

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
            $filter['page_size'] = 15;
        }

        /* 记录总数 */
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('factory') . $where;
        $filter['record_count']   = $GLOBALS['db']->getOne($sql);
        $filter['page_count']     = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;

        /* 查询 */
        $sql = "SELECT *
                FROM " . $GLOBALS['ecs']->table("factory") . "
                $where order by add_time desc
                 
                LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'] . " ";

        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }

    /* 获取供货商列表 */
    $suppliers_list = get_suppliers_list();
    $_suppliers_list = array();
    foreach ($suppliers_list as $value)
    {
        $_suppliers_list[$value['suppliers_id']] = $value['suppliers_name'];
    }

    $row = $GLOBALS['db']->getAll($sql);

    /* 格式化数据 */
    foreach ($row AS $key => $value)
    {
        $row[$key]['add_time'] = local_date($GLOBALS['_CFG']['time_format'], $value['add_time']);
        $row[$key]['update_time'] = local_date($GLOBALS['_CFG']['time_format'], $value['update_time']);
        if ($value['status'] == 1)
        {
            $row[$key]['status_name'] = $GLOBALS['_LANG']['delivery_status'][1];
        }
        elseif ($value['status'] == 2)
        {
            $row[$key]['status_name'] = $GLOBALS['_LANG']['delivery_status'][2];
        }
        else
        {
        $row[$key]['status_name'] = $GLOBALS['_LANG']['delivery_status'][0];
        }
        $row[$key]['suppliers_name'] = isset($_suppliers_list[$value['suppliers_id']]) ? $_suppliers_list[$value['suppliers_id']] : '';
        $row[$key]['fw_time'] = date('Y-m-d H:i:s',$value['fw_time']);
        $row[$key]['jx_time'] = date('Y-m-d H:i:s',$value['jx_time']);
        //ykq_update_160605_判断发货日是否为空。
        if($value['send_time']){
        	$row[$key]['send_time'] = date('Y-m-d',$value['send_time']);
        }
        else{
        	$row[$key]['send_time']='';
        }
        
        $fuwu_id = $value['parent_facilitator_id'];
        $fuwu = $GLOBALS['db']->getone("select `user_name` from".$GLOBALS['ecs']->table('users')."where user_id='".$fuwu_id."'");

        $row[$key]['parent_facilitator_id'] = $fuwu;
        $jinxiao_id = $value['facilitator_id'];
        $jinxiao = $GLOBALS['db']->getone("select `user_name` from".$GLOBALS['ecs']->table('users')."where user_id='".$jinxiao_id."'");
        $row[$key]['facilitator_id'] = $jinxiao;
    }
    $arr = array('delivery' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
   // var_dump($arr);
    return $arr;
}

/**
 * 取得供货商列表
 * @return array    二维数组
 */
function get_suppliers_list()
{
    $sql = 'SELECT *
            FROM ' . $GLOBALS['ecs']->table('suppliers') . '
            WHERE is_check = 1
            ORDER BY suppliers_name ASC';
    $res = $GLOBALS['db']->getAll($sql);

    if (!is_array($res))
    {
        $res = array();
    }

    return $res;
}