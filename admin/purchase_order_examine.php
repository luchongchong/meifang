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
        echo ecs_iconv(EC_CHARSET, 'GB2312', '订单号') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '商品名称') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '商品型号') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '商品数量') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '商品单价') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '进货单价') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '商品总价') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '收货人') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '收货加盟商') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '电话') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '收货地址') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '采购时间') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '快递单号') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '付款状态') . "\t\n";

        foreach ($goods_sales_list['sale_list_data'] AS $key => $value)
        {
        	echo ecs_iconv(EC_CHARSET, 'GB2312', $value['suppliers_name']) . "\t";
        	echo ecs_iconv(EC_CHARSET, 'GB2312', $value['order_sn'].'') . "\t";
        	echo ecs_iconv(EC_CHARSET, 'GB2312', $value['goods_name']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['goods_attr']) . "\t";                
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['goods_number']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['goods_price']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['cost_price']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['total']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['name']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['user_name']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['tel']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['address']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['add_time']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['courier_number']) . "\t";
           	$sql = "SELECT pay_status FROM " .$ecs->table('order_info'). " where order_sn=$value[order_sn]";
	    	$pay_status = $GLOBALS['db']->getOne($sql);
	    	if($pay_status==0){
	    		$value['pay_status']='未付款';
	    	}
	    	if($pay_status==2){
	    		$value['pay_status']='已付款';
	    	}
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

    make_json_result($smarty->fetch('purchase_order_examine.htm'), '', array('filter' => $sale_list_data['filter'], 'page_count' => $sale_list_data['page_count']));
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
    foreach($sale_list_data['sale_list_data'] as $key =>$value){
    	$sql = "SELECT pay_status FROM " .$ecs->table('order_info'). " where order_sn=$value[order_sn]";
    	$pay_status = $GLOBALS['db']->getOne($sql);
    	if($pay_status==0){
    		$sale_list_data['sale_list_data'][$key]['pay_statuss']='未付款';
    	}
    	if($pay_status==2){
    		$sale_list_data['sale_list_data'][$key]['pay_statuss']='已付款';
    	}
    }
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
	//$smarty->assign('action_link2',  array('text' =>'添加采购订单','href'=>'purchase_order.php?act=order_query'));
    /* 显示页面 */
    assign_query_info();
    $smarty->display('purchase_order_examine.htm');
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
    $smarty->assign('ur_here', $_LANG['03_order_query']);
    $smarty->assign('action_link', array('href' => 'purchase_order.php?act=list', 'text' => '采购订单'));

    /* 显示模板 */
    assign_query_info();
    $smarty->display('purchase_order_examine_query.htm');
}

//确认采购
elseif ($_REQUEST['act'] == 'order_query_freight')
{
    /* 检查权限 */
    admin_priv('order_view');

    $_GET['buy_id'];
    //查询供货商
	$sql = "SELECT suppliers_id, suppliers_name, suppliers_desc, is_check FROM " .$ecs->table('suppliers'). " ";
    $row = $GLOBALS['db']->getAll($sql);
    
    //$sql = "SELECT suppliers_id, suppliers_name, suppliers_desc, is_check FROM " .$ecs->table('buy_goods'). " ";
    //$row = $GLOBALS['db']->getAll($sql);
    /* 模板赋值 */
    
    $smarty->assign('buy_id', $_GET['buy_id']);
    $smarty->assign('form_act', 'order_query_freights');
    $smarty->assign('freight', 'freight');
    $smarty->assign('row', $row);
    $smarty->assign('ur_here', $_LANG['03_order_query']);
    $smarty->assign('action_link', array('href' => 'purchase_order.php?act=list', 'text' => '采购订单'));

    /* 显示模板 */
    assign_query_info();
    $smarty->display('purchase_order_query_examine.htm');
}

//确认采购运费执行
elseif ($_REQUEST['act'] == 'order_query_freights')
{
    /* 检查权限 */
    admin_priv('order_view');

    $_POST['freight'];//商品运费
    $_POST['buy_id'];
    
    $sql = "SELECT * FROM " .$ecs->table('buy_goods'). " where id='".$_POST['buy_id']."' ";
    $row = $GLOBALS['db']->getRow($sql);
    
    $sql = "SELECT * FROM " .$ecs->table('buy'). " where buy_id='".$row['buy_id']."' ";
    $rows = $GLOBALS['db']->getRow($sql);
    $sql = 'INSERT INTO ' . $ecs->table('payable') . " (suppliers_id,user_id,facilitator_id,goods_name,goods_number,goods_price,cost_price,goods_total,add_time,payable)" . " 
    VALUES ('$row[suppliers_id]','$rows[user_id]','$rows[facilitator_id]','$row[goods_name]','$row[goods_number]','$row[goods_price]','$row[cost_price]','$row[goods_total]','".time()."','".$_POST[freight]."')";
    $db->query($sql);
    if($db > 0){
    	$sql = "update ". $ecs->table('buy_goods') . " set `status`='1' where id='".$_POST['buy_id']."'";
    	$db->query($sql);
    	sys_msg('采购成功',0);
    }

    /* 显示模板 */
    assign_query_info();
}
elseif ($_REQUEST['act'] == 'update')
{
    /* 检查权限 */
    admin_priv('order_view');

    //查询工厂
    $sql = "SELECT suppliers_id, suppliers_name, suppliers_desc, is_check FROM " .$ecs->table('suppliers'). " ";
    $rows = $GLOBALS['db']->getAll($sql);
    $smarty->assign('rows', $rows);
    
    //查询供货商
	$sql = "SELECT id,suppliers_id,courier_number FROM " .$ecs->table('buy_goods'). " where buy_id=$_GET[buy_id] ";
    $row = $GLOBALS['db']->getRow($sql);
    /* 模板赋值 */
    $smarty->assign('row', $row);
    $smarty->assign('form_act', 'edit');
    $smarty->assign('ur_here', $_LANG['03_order_query']);
    $smarty->assign('action_link', array('href' => 'purchase_order.php?act=list', 'text' => '采购订单'));

    /* 显示模板 */
    assign_query_info();
    $smarty->display('purchase_order_query_examine.htm');
}

//修改执行
elseif ($_REQUEST['act'] == 'edit')
{
    /* 检查权限 */
    admin_priv('order_view');

    $_POST['suppliers_id'];//工厂id
    $_POST['courier_number'];//工厂id
    //查询供货商
	//$sql = "update ". $ecs->table('buy') . " set `goods_attr`='".$_POST[goods_attr]."',`goods_number`='".$_POST[goods_number]."',`goods_price`='".$_POST[goods_price]."',`goods_total`='".$_POST['goods_total']."' where id='".$_POST[buy_id]."'";
    $sql = "update ". $ecs->table('buy_goods') . " set `suppliers_id`='".$_POST[suppliers_id]."',`courier_number`='".$_POST[courier_number]."' where id='".$_POST[id]."'";
    $db->query($sql);
    if($db>0){
    	sys_msg('修改成功',0,array(array('href'=>'purchase_order_examine.php?act=list')));  	
    }

    /* 显示模板 */
    assign_query_info();
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
		   $where.=" and b.goods_attr like '%".$filter['goods_attr']."%'";
	    }
	
	if(!empty($filter['order_sn'])){
	        $where.=" and bs.order_sn like '%".$filter['order_sn']."%'";
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

    $sql = "SELECT b.*,replace(replace(b.goods_attr,char(10),''),char(13),'') as goods_attr,s.suppliers_name,bs.order_sn,bs.facilitator_id,bs.name,bs.tel,bs.address,bs.add_time FROM " . $GLOBALS['ecs']->table('buy_goods').' AS b
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
    	$sql = "SELECT user_name FROM " .$GLOBALS['ecs']->table('users') . " where user_id=$item[facilitator_id]";
        $sale_list_data[$key]['user_name'] = $GLOBALS['db']->getOne($sql);
    	$sale_list_data[$key]['total']  = $sale_list_data[$key]['cost_price']*$sale_list_data[$key]['goods_number'];
        $sale_list_data[$key]['add_time']  = date("Y-m-d H:i:s",$sale_list_data[$key]['add_time']);
    }
    $arr = array('sale_list_data' => $sale_list_data, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    return $arr;
}
?>