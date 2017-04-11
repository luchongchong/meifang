<?php

/**
 * ECSHOP 销售明细列表程序
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: sale_list.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_order.php');
require_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/admin/statistic.php');
$smarty->assign('lang', $_LANG);

if (isset($_REQUEST['act']) && ($_REQUEST['act'] == 'query' ||  $_REQUEST['act'] == 'download'))
{
    /* 检查权限 */
    check_authz_json('sale_order_stats');
    if (strstr($_REQUEST['start_date'], '-') === false)
    {
        $_REQUEST['start_date'] = local_date('Y-m-d', $_REQUEST['start_date']);
        $_REQUEST['end_date'] = local_date('Y-m-d', $_REQUEST['end_date']);
    }
    /*------------------------------------------------------ */
    //--Excel文件下载
    /*------------------------------------------------------ */
    if ($_REQUEST['act'] == 'download')
    {
        $file_name = $_REQUEST['start_date'].'_'.$_REQUEST['end_date'] . '_sale';
        $goods_sales_list = get_sale_list(false);
        header("Content-type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=$file_name.xls");

        /* 文件标题 */
        echo ecs_iconv(EC_CHARSET, 'GB2312', $_REQUEST['start_date']. $_LANG['to'] .$_REQUEST['end_date']. $_LANG['sales_list']) . "\t\n";

        /* 订单号	店铺号	店主名	金额	支付方式	支付日期*/
        echo ecs_iconv(EC_CHARSET, 'GB2312', $_LANG['order_sn']) . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '店铺号') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '店主名') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '金额') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '支付方式') . "\t";
        echo ecs_iconv(EC_CHARSET, 'utf-8', '支付日期') . "\t\n";

        foreach ($goods_sales_list['sale_list_data'] AS $key => $value)
        {
            echo ecs_iconv(EC_CHARSET, 'GB2312', '[ ' . $value['order_sn'] . ' ]') . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['shop_no']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['name']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['money_paid']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['pay_name']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'utf-8', $value['pay_time']) . "\t";
            echo "\n";
        }
        exit;
    }
    $sale_list_data = get_sale_list();
    $smarty->assign('goods_sales_list', $sale_list_data['sale_list_data']);
    $smarty->assign('filter',       $sale_list_data['filter']);
    $smarty->assign('record_count', $sale_list_data['record_count']);
    $smarty->assign('page_count',   $sale_list_data['page_count']);

    make_json_result($smarty->fetch('collect_detail.htm'), '', array('filter' => $sale_list_data['filter'], 'page_count' => $sale_list_data['page_count']));
}
/*------------------------------------------------------ */
//--商品明细列表
/*------------------------------------------------------ */
else
{
    /* 权限判断 */
    admin_priv('sale_order_stats');
    /* 时间参数 */
    if (!isset($_REQUEST['start_date']))
    {
        $start_date = local_strtotime('-7 days');
    }
    if (!isset($_REQUEST['end_date']))
    {
        $end_date = local_strtotime('today');
    }
    //查询支付方式
    $sql = "SELECT pay_id, pay_code, pay_name FROM ".$ecs->table('payment').
        " WHERE enabled=1";
    $pm = $db->getAll($sql);
    $sale_list_data = get_sale_list();
    /* 赋值到模板 */
    $smarty->assign('pm',       $pm);
    $smarty->assign('filter',       $sale_list_data['filter']);
    $smarty->assign('record_count', $sale_list_data['record_count']);
    $smarty->assign('page_count',   $sale_list_data['page_count']);
    $smarty->assign('goods_sales_list', $sale_list_data['sale_list_data']);
    //$smarty->assign('ur_here',          $_LANG['collect_detail']);
    $smarty->assign('full_page',        1);
    $smarty->assign('start_date',       local_date('Y-m-d', $start_date));
    $smarty->assign('end_date',         local_date('Y-m-d', $end_date));
    //$smarty->assign('ur_here',      $_LANG['collect_detail']);
    $smarty->assign('cfg_lang',     $_CFG['lang']);
    $smarty->assign('action_link',  array('text' => $_LANG['down_saless'],'href'=>'#download'));

    /* 显示页面 */
    assign_query_info();
    $smarty->display('collect_detail.htm');
}
/*------------------------------------------------------ */
//--获取销售明细需要的函数
/*------------------------------------------------------ */
/**
 * 取得销售明细数据信息
 * @param   bool  $is_pagination  是否分页
 * @return  array   销售明细数据
 */
function get_sale_list($is_pagination = true){

    /* 时间参数 */
    $filter['start_date'] = empty($_REQUEST['start_date']) ? local_strtotime('-7 days') : local_strtotime($_REQUEST['start_date']);
    $filter['end_date'] = empty($_REQUEST['end_date']) ? local_strtotime('today') : local_strtotime($_REQUEST['end_date']);
  	$filter['name']=empty($_REQUEST['name'])?'':trim($_REQUEST['name']);
    $filter['status']=empty($_REQUEST['status'])?'':$_REQUEST['status'];
    
    /* 查询数据的条件 */
    $where =" WHERE a.pay_time >= '".$filter['start_date']."' AND a.pay_time < '" . ($filter['end_date'] + 86400) . "'";
    $where .= " and a.pay_status=2";
 	if(!empty($filter['name'])){
	   $where.=" and b.name like'%".$filter['name']."%'";
    }

    if(!empty($filter['status'])){
        $where.=" and a.pay_id=".$filter['status'];
    }
    $sql = "SELECT COUNT(a.order_sn) FROM " .
        $GLOBALS['ecs']->table('order_info') . ' AS a'.
		 " left join ecs_store b on a.shop_no=b.shop_no LEFT JOIN ecs_users u on a.user_id=u.user_id".
        $where;
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);
    /* 分页大小 */
    $filter = page_and_size($filter);

    $sql='select a.user_id,u.user_name,a.pay_id,b.shop_no,b.name,a.order_sn,a.pay_name,a.money_paid,a.pay_time'.
        " FROM ".$GLOBALS['ecs']->table('order_info')." AS a".
        " left join ecs_store b on a.shop_no=b.shop_no LEFT JOIN ecs_users u on a.user_id=u.user_id".
        $where." order by a.pay_time DESC";
    if ($is_pagination)
    {
        $sql .= " LIMIT " . $filter['start'] . ', ' . $filter['page_size'];
    }

    $sale_list_data = $GLOBALS['db']->getAll($sql);

    foreach ($sale_list_data as $key => $item)
    {
        $sale_list_data[$key]['sales_price'] = price_format($sale_list_data[$key]['sales_price']);
        $sale_list_data[$key]['pay_time']  = local_date($GLOBALS['_CFG']['time_format'], $sale_list_data[$key]['pay_time']);
    }
    $arr = array('sale_list_data' => $sale_list_data, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    return $arr;
}
?>