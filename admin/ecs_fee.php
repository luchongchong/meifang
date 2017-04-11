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
$exc = new exchange($ecs->table('fee'), $db, 'id', 'expense_account');
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

        /* 商品名称,订单号,商品数量,销售价格,销售日期 */
        echo ecs_iconv(EC_CHARSET, 'GB2312', '日期') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '科目') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '金额') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '备注') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '领取人') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '经办人') . "\t\n";

        foreach ($goods_sales_list['sale_list_data'] AS $key => $value)
        {
        	echo ecs_iconv(EC_CHARSET, 'GB2312', date('Y-m-d',$value['start_date'])) . "\t";
        	echo ecs_iconv(EC_CHARSET, 'GB2312', $value['expense_account']) . "\t";
        	echo ecs_iconv(EC_CHARSET, 'GB2312', $value['fee_money']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['fee_remarks']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['receive_people']) . "\t";                
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['handling_people']) . "\t";
            echo "\n";
        }
        exit;
    }
    
    $sale_list_data = get_sale_list();
    $smarty->assign('goods_sales_list', $sale_list_data['sale_list_data']);
    $smarty->assign('filter',       $sale_list_data['filter']);
    $smarty->assign('record_count', $sale_list_data['record_count']);
    $smarty->assign('page_count',   $sale_list_data['page_count']);

    make_json_result($smarty->fetch('ecs_fee.htm'), '', array('filter' => $sale_list_data['filter'], 'page_count' => $sale_list_data['page_count']));
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
    
    //查询费用科目
	$sql = "SELECT * FROM ".$GLOBALS['ecs']->table('subject')."";
	$subject=$GLOBALS['db']->getAll($sql);
	
	//查询费用相关人
	$sql = "SELECT * FROM ".$GLOBALS['ecs']->table('person')."";
	$person=$GLOBALS['db']->getAll($sql);
	
    $sale_list_data = get_sale_list();
    /* 赋值到模板 */
    $smarty->assign('subject',      $subject);
	$smarty->assign('person',      $person);
	
    $smarty->assign('filter',       $sale_list_data['filter']);
    $smarty->assign('record_count', $sale_list_data['record_count']);
    $smarty->assign('page_count',   $sale_list_data['page_count']);
    $smarty->assign('goods_sales_list', $sale_list_data['sale_list_data']);
    $smarty->assign('ur_here',          '费用列表');
    $smarty->assign('full_page',        1);
    $smarty->assign('start_date',       local_date('Y-m-d', $start_date));
    $smarty->assign('end_date',         local_date('Y-m-d', $end_date));
    //$smarty->assign('ur_here',      $_LANG['sale_list']);
    //$smarty->assign('cfg_lang',     $_CFG['lang']);
    $smarty->assign('action_link',  array('text' => '下载费用明细','href'=>'#download'));
    $smarty->assign('action_link2',  array('text' => '添加费用','href'=>'ecs_fee.php?act=add'));

    /* 显示页面 */
    assign_query_info();
    $smarty->display('ecs_fee.htm');
}
elseif($_REQUEST['act'] == 'add'){
	
	//查询费用科目
	$sql = "SELECT * FROM ".$GLOBALS['ecs']->table('subject')."";
	$subject=$GLOBALS['db']->getAll($sql);
	
	//查询费用相关人
	$sql = "SELECT * FROM ".$GLOBALS['ecs']->table('person')."";
	$person=$GLOBALS['db']->getAll($sql);
	
	$sql = "SELECT *,f.id FROM ".$GLOBALS['ecs']->table('fee')." as f 
	left join ".$GLOBALS['ecs']->table('subject')." as s on f.expense_account=s.id 
	left join ".$GLOBALS['ecs']->table('person')." as p on f.receive_people=p.id 
	left join ".$GLOBALS['ecs']->table('person')." as ps on f.handling_people=ps.id where insert_id=0";
	$fee=$GLOBALS['db']->getAll($sql);
	$fees=array();
	foreach($fee as $key=>$row){
		 $row['start_date']=date('Y-m-d',$row['start_date']);
		$fees[]=$row;
	}
	$smarty->assign('subject',      $subject);
	$smarty->assign('person',      $person);
	$smarty->assign('fees',      $fees);
	
	assign_query_info();
    $smarty->display('ecs_fee_info.htm');
}

elseif($_REQUEST['act'] == 'insert'){
	$ecs_fee=array(
	'start_date'=>strtotime($_POST['start_date']),
	'expense_account'=>$_POST['expense_account'],//费用科目
	'fee_money'=>$_POST['fee_money'],//费用金额
	'fee_remarks'=>$_POST['fee_remarks'],//备注
	'receive_people'=>$_POST['receive_people'],//经办人
	'handling_people'=>$_POST['handling_people']//领取人
	);
	$db->autoExecute($ecs->table('fee'), $ecs_fee, 'INSERT');
	if($db>0){
		$sql = "SELECT * FROM ".$GLOBALS['ecs']->table('subject')."";
	$subject=$GLOBALS['db']->getAll($sql);
	
	//查询费用相关人
	$sql = "SELECT * FROM ".$GLOBALS['ecs']->table('person')."";
	$person=$GLOBALS['db']->getAll($sql);
	
	$sql = "SELECT *,f.id FROM ".$GLOBALS['ecs']->table('fee')." as f 
	left join ".$GLOBALS['ecs']->table('subject')." as s on f.expense_account=s.id 
	left join ".$GLOBALS['ecs']->table('person')." as p on f.receive_people=p.id 
	left join ".$GLOBALS['ecs']->table('person')." as ps on f.handling_people=ps.id where insert_id=0";
	$fee=$GLOBALS['db']->getAll($sql);
	$fees=array();
	foreach($fee as $key=>$row){
		 $row['start_date']=date('Y-m-d',$row['start_date']);
		$fees[]=$row;
	}
	$smarty->assign('subject',      $subject);
	$smarty->assign('person',      $person);
	$smarty->assign('fees',      $fees);
	$smarty->display('ecs_fee_info.htm');
	}
	
}
elseif($_REQUEST['act'] == 'inserts'){
	$row=$_POST['id'];
	foreach($row as $key => $value){
		$sql = "UPDATE " . $ecs->table('fee') . " SET insert_id = 1 WHERE id= " .$value;
        $db->query($sql);
	}
	if($db>0){
		$links=array( array('href' => 'ecs_fee.php?act=list'));
		sys_msg('添加成功', 0, $links);
	}
}
elseif ($_REQUEST['act'] == 'remove')
{
    check_authz_json('ecs_fee');
    $id = intval($_GET['id']);
    $sql = "DELETE FROM ".$GLOBALS['ecs']->table('fee')." WHERE id = '$id'";
    $db->query($sql);
    if($db>0){
	    clear_cache_files();
	    $url = 'ecs_fee.php?act=add';
	    ecs_header("Location: $url\n");
	    exit;
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
  	$filter['expense_account']=empty($_REQUEST['expense_account'])?'':trim($_REQUEST['expense_account']);
    $filter['receive_people']=empty($_REQUEST['receive_people'])?'':$_REQUEST['receive_people'];
    $filter['handling_people']=empty($_REQUEST['handling_people'])?'':$_REQUEST['handling_people'];
    /* 查询数据的条件 */
    $where = " WHERE f.expense_account=s.id and f.receive_people=p.id and f.handling_people=ps.id and insert_id=1 AND f.start_date >= '".$filter['start_date']."' AND f.start_date < '" . ($filter['end_date'] + 86400) . "'";
	if(!empty($filter['expense_account'])){
		   $where.=" and s.id =".$filter['expense_account']."";
	    }
	
	if(!empty($filter['receive_people'])){
	        $where.=" and p.id=".$filter['receive_people'];
	    }
	    
	if(!empty($filter['handling_people'])){
	        $where.=" and ps.id=".$filter['handling_people'];
	    } 
    $sql = "SELECT COUNT(f.id) FROM " .
           $GLOBALS['ecs']->table('fee') . ' AS f,'.
           $GLOBALS['ecs']->table('person') . ' AS p, '.
           $GLOBALS['ecs']->table('person') . ' AS ps, '.
           $GLOBALS['ecs']->table('subject') . ' AS s '.
           $where;
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);

    /* 分页大小 */
    $filter = page_and_size($filter);

    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('fee')." AS f, 
           ".$GLOBALS['ecs']->table('person')." AS p,
    	   ".$GLOBALS['ecs']->table('person')." AS ps,
    	   ".$GLOBALS['ecs']->table('subject')." AS s ".
           $where. " ORDER BY f.id DESC";
    if ($is_pagination)
    {
        $sql .= " LIMIT " . $filter['start'] . ', ' . $filter['page_size'];
    }

    $sale_list_data = $GLOBALS['db']->getAll($sql);
	//print_r($sale_list_data);
    foreach ($sale_list_data as $key => $item)
    {
        $sale_list_data[$key]['sales_price'] = price_format($sale_list_data[$key]['sales_price']);
        $sale_list_data[$key]['sales_time']  = date('Y-m-d',$sale_list_data[$key]['start_date']);
    }
    $arr = array('sale_list_data' => $sale_list_data, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    return $arr;
}
?>