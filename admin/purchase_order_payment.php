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
$exc = new exchange($ecs->table("payable"), $db, 'buy_id', 'goods_name');
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
        $file_name = "应付单".$_REQUEST['start_date'].'_'.$_REQUEST['end_date'] . '_sale';
        $goods_sales_list = get_sale_list(false);

        header("Content-type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=$file_name.xls");

        /* 文件标题 */
        echo ecs_iconv(EC_CHARSET, 'GB2312', $_REQUEST['start_date']. $_LANG['to'] .$_REQUEST['end_date']. $_LANG['sales_list']) . "\t\n";

        /* 商品名称,订单号,商品数量,销售价格,销售日期 */
        echo ecs_iconv(EC_CHARSET, 'GB2312', '工厂名称') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '加盟商') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '商品名称') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '数量') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '单价') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '成本价') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '小计') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '运费') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '总价') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '结算方式') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '采购时间') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '付款时间') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '付款状态') . "\t\n";

        foreach ($goods_sales_list['sale_list_data'] AS $key => $value)
        {
        	if($value['jiesuan_status']==1){$value['jiesuan_status']="日结";}
        	elseif($value['jiesuan_status']==2){$value['jiesuan_status']="月结";}
        	if($value['pay_status']==1){$value['pay_status']="已结算";}else{$value['pay_status']="未结算";}
        	echo ecs_iconv(EC_CHARSET, 'GB2312', $value['suppliers_name']) . "\t";
        	echo ecs_iconv(EC_CHARSET, 'GB2312', $value['user_name']) . "\t";
        	echo ecs_iconv(EC_CHARSET, 'GB2312', $value['goods_name']) . "\t";         
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['goods_number']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['goods_price']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['cost_price']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['subtotal']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['payable']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['zongjia']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['jiesuan_status']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['add_time']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['payment_time']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['pay_status']) . "\t";
            echo "\n";
        }
        exit;
    }
    
    $sale_list_data = get_sale_list();
    $smarty->assign('goods_sales_list', $sale_list_data['sale_list_data']);
    $smarty->assign('filter',       $sale_list_data['filter']);
    $smarty->assign('record_count', $sale_list_data['record_count']);
    $smarty->assign('page_count',   $sale_list_data['page_count']);

    make_json_result($smarty->fetch('purchase_order_payment.htm'), '', array('filter' => $sale_list_data['filter'], 'page_count' => $sale_list_data['page_count']));
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
    /* 赋值到模板 */
    $smarty->assign('filter',       $sale_list_data['filter']);
    $smarty->assign('record_count', $sale_list_data['record_count']);
    $smarty->assign('page_count',   $sale_list_data['page_count']);
    $smarty->assign('goods_sales_list', $sale_list_data['sale_list_data']);
    $smarty->assign('full_page',        1);
    $smarty->assign('start_date',       local_date('Y-m-d', $start_date));
    $smarty->assign('end_date',         local_date('Y-m-d', $end_date));
    $smarty->assign('ur_here',      '付款画面');
    $smarty->assign('cfg_lang',     $_CFG['lang']);
    $smarty->assign('action_link',  array('text' => "下载付款明细",'href'=>'#download'));
	//$smarty->assign('action_link2',  array('text' =>'添加采购订单','href'=>'purchase_order.php?act=order_query'));
    /* 显示页面 */
    assign_query_info();
    $smarty->display('purchase_order_payment.htm');
}
elseif ($_REQUEST['act'] == 'order_query')
{
    /* 检查权限 */
    admin_priv('order_view');

    //查询供货商
	$sql = "SELECT suppliers_id, suppliers_name, suppliers_desc, is_check FROM " .$ecs->table('suppliers'). " ";
    $row = $GLOBALS['db']->getAll($sql);
    /* 模板赋值 */
    $smarty->assign('row', $row);
    $smarty->assign('ur_here', $_LANG['03_order_query']);
    $smarty->assign('action_link', array('href' => 'purchase_order.php?act=list', 'text' => '采购订单'));

    /* 显示模板 */
    assign_query_info();
    $smarty->display('purchase_order_payment_query.htm');
}

/*------------------------------------------------------ */
//--ykq_add_插入采购订单
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'inserts')
{
    /* 检查权限 */
    admin_priv('order_view');
	$_POST['suppliers_id'];//工厂id
	$_POST['goods_name'];//商品名称
	$_POST['goods_attr'];//商品型号
	$_POST['goods_number'];//商品数量
	$_POST['goods_price'];//商品单价
	$_POST['goods_total']=$_POST['goods_price']*$_POST['goods_price'];//商品合计
	
    $sql = 'INSERT INTO ' . $ecs->table('buy') . " (suppliers_id, goods_name, goods_attr, goods_number,goods_price,goods_total,add_time)" .
                " VALUES ('$_POST[suppliers_id]','$_POST[goods_name]', '$_POST[goods_attr]', '$_POST[goods_number]','$_POST[goods_price]','$_POST[goods_total]','".time()."')";
    $db->query($sql);
    if($db > 0){
    	sys_msg('添加成功',0);
    }

  
}

//修改
elseif ($_REQUEST['act'] == 'update')
{
    /* 检查权限 */
    admin_priv('order_view');

    //查询供货商
	$sql = "SELECT buy_id,payable FROM " .$ecs->table('payable'). " where buy_id=$_GET[buy_id] ";
    $row = $GLOBALS['db']->getRow($sql);
    /* 模板赋值 */
    $smarty->assign('row', $row);
    $smarty->assign('form_act', 'edit');
    $smarty->assign('ur_here', $_LANG['03_order_query']);
    $smarty->assign('action_link', array('href' => 'purchase_order.php?act=list', 'text' => '采购订单'));

    /* 显示模板 */
    assign_query_info();
    $smarty->display('purchase_order_payment_query.htm');
}

//修改执行
elseif ($_REQUEST['act'] == 'edit')
{
    /* 检查权限 */
    admin_priv('order_view');
    $_POST['freight'];//运费
    //查询供货商
	$sql = "update ". $ecs->table('payable') . " set `payable`='".$_POST[freight]."' where buy_id='".$_POST[buy_id]."'";
    $db->query($sql);
    if($db>0){
    	sys_msg('修改运费成功',0,array(array('href'=>'purchase_order_payment.php?act=list')));  	
    }

    /* 显示模板 */
    assign_query_info();
}

//确认付款
elseif ($_REQUEST['act'] == 'jiesuan')
{
    /* 检查权限 */
    admin_priv('order_view');

    //查询供货商
	$sql = "SELECT buy_id,payable FROM " .$ecs->table('payable'). " where buy_id=$_GET[buy_id] ";
    $row = $GLOBALS['db']->getRow($sql);
    /* 模板赋值 */
    $smarty->assign('row', $row);
    $smarty->assign('jiesuan', 'jiesuan');
    $smarty->assign('form_act', 'jiesuan_edit');
    $smarty->assign('ur_here', $_LANG['03_order_query']);
    $smarty->assign('action_link', array('href' => 'purchase_order.php?act=list', 'text' => '采购订单'));

    /* 显示模板 */
    assign_query_info();
    $smarty->display('purchase_order_payment_query.htm');
}

//确认付款执行
elseif ($_REQUEST['act'] == 'jiesuan_edit')
{
    /* 检查权限 */
    admin_priv('order_view');
    $_POST['jiesuan'];//结算状态
    
    //确认付款更新状态
    if($_POST['jiesuan']==1){
    	$sql = "update ". $ecs->table('payable') . " set `pay_status`='1',`shipping_status`='1',`jiesuan_status`='".$_POST[jiesuan]."' where buy_id='".$_POST[buy_id]."'";
    	$db->query($sql);
    }else{
    	$sql = "update ". $ecs->table('payable') . " set `pay_status`='2',`shipping_status`='1',`jiesuan_status`='".$_POST[jiesuan]."' where buy_id='".$_POST[buy_id]."'";
    	$db->query($sql);
    }
	
    if($db>0){
    	sys_msg('付款成功',0,array(array('href'=>'purchase_order_payment.php?act=list')));  	
    }

    /* 显示模板 */
    assign_query_info();
}

//确认付款执行
elseif ($_REQUEST['act'] == 'remove')
{
    check_authz_json('ecs_payable');

    $id = intval($_GET['id']);
    //$name = $exc->get_name($id);

    if ($id)
    {
    	$sql = "update ". $ecs->table('payable') . " set `pay_status`='1',`shipping_status`='1' where buy_id='".$id."'";
    	$db->query($sql);
        admin_log(addslashes($name), 'remove', 'ecs_payable');
        $url = 'purchase_order_payment.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);
        ecs_header("Location: $url\n");
        exit;
    }
    else
    {
        make_json_error($_LANG['remove_failed']);
    }
}

//批量执行
elseif ($_REQUEST['act'] == 'batch')
{
    /* 取得要操作的商品编号 */
    $buy_id = !empty($_POST['checkboxes']) ? join(',', $_POST['checkboxes']) : 0;
    $buy_id = db_create_in($buy_id);
    $time=time();
    //确认付款更新状态
	$sql = "update ". $ecs->table('payable') . " set `pay_status`='1',`shipping_status`='1',`jiesuan_status`='2',`payment_time`='$time' where buy_id $buy_id";
    $db->query($sql);
    if($db>0){
    	sys_msg('付款成功',0,array(array('href'=>'purchase_order_payment.php?act=list')));  	
    }

    /* 显示模板 */
    assign_query_info();
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
    
  	$filter['facilitator_id']=empty($_REQUEST['facilitator_id'])?'':trim($_REQUEST['facilitator_id']);
    $filter['jiesuan']=empty($_REQUEST['jiesuan'])?'':trim($_REQUEST['jiesuan']);
    $filter['suppliers_id']=empty($_REQUEST['suppliers_id'])?'':trim($_REQUEST['suppliers_id']);
    
    /* 查询数据的条件 */
    $where = " WHERE  b.add_time >= '".$filter['start_date']."' AND b.add_time < '" . ($filter['end_date'] + 86400) . "'";
	if(!empty($filter['facilitator_id'])){
		   $where.=" and u.user_name ='".$filter['facilitator_id']."'";
	    }
	
	if(!empty($filter['jiesuan'])){
	        $where.=" and jiesuan_status = '".$filter['jiesuan']."'";
	    }
	    
	if(!empty($filter['suppliers_id'])){
	        $where.=" and s.suppliers_id='".$filter['suppliers_id']."'";
	    }
     $sql = "SELECT COUNT(b.buy_id) FROM " .$GLOBALS['ecs']->table('payable') . ' AS b 
           left join ' . $GLOBALS['ecs']->table('suppliers') . ' AS s on b.suppliers_id = s.suppliers_id
           left join '.$GLOBALS['ecs']->table('users') . ' AS u on b.facilitator_id = u.user_id'.
           $where;
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);

    /* 分页大小 */
    $filter = page_and_size($filter);

    $sql = "SELECT b.*,s.suppliers_name,u.user_name FROM " . $GLOBALS['ecs']->table('payable').' AS b
           left join ' . $GLOBALS['ecs']->table('suppliers') . ' AS s on b.suppliers_id = s.suppliers_id
    	   left join '.$GLOBALS['ecs']->table('users') . ' AS u on b.facilitator_id = u.user_id '.
           $where. " ORDER BY b.buy_id DESC";
    if ($is_pagination)
    {
        $sql .= " LIMIT " . $filter['start'] . ', ' . $filter['page_size'];
    }

    $sale_list_data = $GLOBALS['db']->getAll($sql);
    foreach ($sale_list_data as $key => $item)
    {
    	$sale_list_data[$key]['subtotal']  = $sale_list_data[$key]['goods_number']*$sale_list_data[$key]['cost_price'];//小计
    	$sale_list_data[$key]['zongjia']  = $sale_list_data[$key]['subtotal']+$sale_list_data[$key]['payable'];//总价
        $sale_list_data[$key]['add_time']  = date("Y-m-d H:i:s",$sale_list_data[$key]['add_time']);
        $sale_list_data[$key]['payment_time']  = date("Y-m-d H:i:s",$sale_list_data[$key]['payment_time']);
    }
    $arr = array('sale_list_data' => $sale_list_data, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    return $arr;
}
?>