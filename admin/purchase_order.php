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
        $file_name = "采购订单".$_REQUEST['start_date'].'_'.$_REQUEST['end_date'] . '_sale';
        $goods_sales_list = get_sale_list(false);

        header("Content-type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=$file_name.xls");

        /* 文件标题 */
        echo ecs_iconv(EC_CHARSET, 'GB2312', $_REQUEST['start_date']. $_LANG['to'] .$_REQUEST['end_date']. $_LANG['sales_list']) . "\t\n";

        /* 商品名称,订单号,商品数量,销售价格,销售日期 */
        echo ecs_iconv(EC_CHARSET, 'GB2312', '工厂名称') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '商品名称') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '型号') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '数量') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '单价') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '小计') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '采购时间') . "\t\n";

        foreach ($goods_sales_list['sale_list_data'] AS $key => $value)
        {
        	echo ecs_iconv(EC_CHARSET, 'GB2312', $value['suppliers_name']) . "\t";
        	echo ecs_iconv(EC_CHARSET, 'GB2312', $value['goods_name']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['goods_attr']) . "\t";                
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['goods_number']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['goods_price']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['goods_total']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['add_time']) . "\t";
            echo "\n";
        }
        exit;
    }
    
    $sale_list_data = get_sale_list();
    
    $smarty->assign('goods_sales_list', $sale_list_data['sale_list_data']);
    $smarty->assign('filter',       $sale_list_data['filter']);
    $smarty->assign('record_count', $sale_list_data['record_count']);
    $smarty->assign('page_count',   $sale_list_data['page_count']);

    make_json_result($smarty->fetch('purchase_order.htm'), '', array('filter' => $sale_list_data['filter'], 'page_count' => $sale_list_data['page_count']));
}
/*------------------------------------------------------ */
//--商品明细列表
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'list')
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
    
    $sql = "SELECT suppliers_id, suppliers_name, suppliers_desc, is_check FROM " .$ecs->table('suppliers'). " ";
    $row = $GLOBALS['db']->getAll($sql);
    
    /* 模板赋值 */
    $smarty->assign('row', $row);
    $sale_list_data = get_sale_list();
    //print_r($sale_list_data);
    /* 赋值到模板 */
    $smarty->assign('filter',       $sale_list_data['filter']);
    $smarty->assign('record_count', $sale_list_data['record_count']);
    $smarty->assign('page_count',   $sale_list_data['page_count']);
    $smarty->assign('goods_sales_list', $sale_list_data['sale_list_data']);
    $smarty->assign('full_page',        1);
    $smarty->assign('start_date',       local_date('Y-m-d', $start_date));
    $smarty->assign('end_date',         local_date('Y-m-d', $end_date));
    $smarty->assign('ur_here',      '采购订单');
    $smarty->assign('cfg_lang',     $_CFG['lang']);
    $smarty->assign('action_link',  array('text' => $_LANG['down_sales'],'href'=>'#download'));
	$smarty->assign('action_link2',  array('text' =>'添加采购订单','href'=>'purchase_order.php?act=order_query'));
    /* 显示页面 */
    assign_query_info();
    $smarty->display('purchase_order.htm');
}
if ($_REQUEST['act'] == 'order_query')
{
    /* 检查权限 */
    admin_priv('order_view');

    //查询供货商
	$sql = "SELECT suppliers_id, suppliers_name, suppliers_desc, is_check FROM " .$ecs->table('suppliers'). " ";
    $row = $GLOBALS['db']->getAll($sql);
    /* 模板赋值 */
    $smarty->assign('row', $row);
    $smarty->assign('form_act', 'inserts');
    $smarty->assign('ur_here', $_LANG['03_order_query']);
    $smarty->assign('action_link', array('href' => 'purchase_order.php?act=list', 'text' => '采购订单'));

    /* 显示模板 */
    assign_query_info();
    $smarty->display('purchase_order_query.htm');
}

/*------------------------------------------------------ */
//--ykq_add_插入采购订单
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'inserts')
{
    /* 检查权限 */
    admin_priv('order_view');

	$_POST['suppliers_id'];//工厂id
	$arr=array();
	
	$_POST['goods_name'];//商品名称
	foreach($_POST['goods_name'] as $key => $value){
       $arr[$key][goods_name]=$value;
    }
    
    $_POST['goods_attr'];//商品型号
    foreach($_POST['goods_attr'] as $key =>$value){
    	$arr[$key][goods_attr]=$value;
    }
    
    $_POST['goods_number'];//商品数量
	foreach($_POST['goods_number'] as $key =>$value){
    	$arr[$key][goods_number]=$value;
    }
    
	$_POST['goods_price'];//商品价格
	foreach($_POST['goods_price'] as $key =>$value){
    	$arr[$key][goods_price]=$value;
    }
    
    $_POST['goods_totals'];//商品小计
	foreach($_POST['goods_totals'] as $key =>$value){
    	$arr[$key][goods_totals]=$value;
    }
    $_POST['goods_total'];//商品总价
    $order_sn = get_order_sn();
    $sql = 'INSERT INTO ' . $ecs->table('buy') . " (suppliers_id,order_sn,goods_total,add_time)" .
                " VALUES ('$_POST[suppliers_id]','$order_sn','$_POST[goods_total]','".time()."')";
    $db->query($sql);
    $buy_id = $db->insert_id();
    foreach($arr as $key => $value){
    	$sql = 'INSERT INTO ' . $ecs->table('buy_goods') . " (buy_id,goods_name,goods_attr,goods_number,goods_price,goods_total)" .
                " VALUES ('$buy_id','$value[goods_name]','$value[goods_attr]','$value[goods_number]','$value[goods_price]','$value[goods_totals]')";
    	$db->query($sql);
    }
    if($db > 0){
    	sys_msg('采购成功',0);
    }

  
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
    
  	$filter['order_sn']=empty($_REQUEST['order_sn'])?'':trim($_REQUEST['order_sn']);
    $filter['goods_attr']=empty($_REQUEST['goods_attr'])?'':trim($_REQUEST['goods_attr']);
    $filter['suppliers_id']=empty($_REQUEST['suppliers_id'])?'':trim($_REQUEST['suppliers_id']);
    
    /* 查询数据的条件 */
    $where = " WHERE b.pay_status=2 and bs.add_time >= '".$filter['start_date']."' AND bs.add_time < '" . ($filter['end_date'] + 86400) . "'";
	if(!empty($filter['goods_attr'])){
		   $where.=" and b.goods_attr ='".$filter['goods_attr']."'";
	    }
	
	if(!empty($filter['order_sn'])){
	        $where.=" and b.order_sn like '%".$filter['order_sn']."%'";
	    }
	    
	if(!empty($filter['suppliers_id'])){
	        $where.=" and s.suppliers_id='".$filter['suppliers_id']."'";
	    }
     $sql = "SELECT COUNT(b.buy_id) FROM " .$GLOBALS['ecs']->table('buy_goods') . ' AS b 
           left join ' . $GLOBALS['ecs']->table('suppliers') . ' AS s on b.suppliers_id = s.suppliers_id
           left join '.$GLOBALS['ecs']->table('buy') . ' AS bs on b.buy_id = bs.buy_id'.
           $where;
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);

    /* 分页大小 */
    $filter = page_and_size($filter);

    $sql = "SELECT b.*,s.suppliers_name,bs.order_sn,bs.name,bs.tel,bs.address,bs.add_time FROM " . $GLOBALS['ecs']->table('buy_goods').' AS b
           left join ' . $GLOBALS['ecs']->table('suppliers') . ' AS s on b.suppliers_id = s.suppliers_id
    	   left join '.$GLOBALS['ecs']->table('buy') . ' AS bs on b.buy_id = bs.buy_id '.
           $where. " ORDER BY b.buy_id DESC";
    if ($is_pagination)
    {
        $sql .= " LIMIT " . $filter['start'] . ', ' . $filter['page_size'];
    }

    $sale_list_data = $GLOBALS['db']->getAll($sql);
    foreach ($sale_list_data as $key => $item)
    {
        $sale_list_data[$key]['add_time']  = date("Y-m-d H:i:s",$sale_list_data[$key]['add_time']);
    }
    $arr = array('sale_list_data' => $sale_list_data, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    return $arr;
}
?>