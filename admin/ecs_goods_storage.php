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
require_once(ROOT_PATH . '/' . ADMIN_PATH . '/includes/lib_goods.php');
require_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/admin/statistic.php');
$smarty->assign('lang', $_LANG);

$exc = new exchange($ecs->table('due_info'), $db, 'rec_id', 'goods_name');
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
        echo ecs_iconv(EC_CHARSET, 'GB2312', $_REQUEST['start_date']. $_LANG['to'] .$_REQUEST['end_date']. "入库明细表") . "\t\n";

        /* 商品名称,订单号,商品数量,销售价格,销售日期 */
        echo ecs_iconv(EC_CHARSET, 'GB2312', '应付单号') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '商品名称') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '商品编号') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '商品型号') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '商品数量') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '商品单价') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '商品总价') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '厂家名称') . "\t\n";

        foreach ($goods_sales_list['sale_list_data'] AS $key => $value)
        {
        	echo ecs_iconv(EC_CHARSET, 'GB2312', $value['order_sn']) . "\t";
        	echo ecs_iconv(EC_CHARSET, 'GB2312', $value['goods_name']) . "\t";
        	echo ecs_iconv(EC_CHARSET, 'GB2312', $value['goods_sn']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['goods_attr']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['goods_amount']) . "\t";                
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['goods_price']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['goods_amount']*$value['goods_price']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['suppliers_name']) . "\t";
            echo "\n";
        }
        exit;
    }
    
    $sale_list_data = get_sale_list();
    $smarty->assign('goods_sales_list', $sale_list_data['sale_list_data']);
    $smarty->assign('filter',       $sale_list_data['filter']);
    $smarty->assign('record_count', $sale_list_data['record_count']);
    $smarty->assign('page_count',   $sale_list_data['page_count']);

    make_json_result($smarty->fetch('ecs_goods_storage.htm'), '', array('filter' => $sale_list_data['filter'], 'page_count' => $sale_list_data['page_count']));
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
	$sql = "SELECT brand_id,brand_name FROM ".$GLOBALS['ecs']->table('brand')." where is_show=1";
	$brand_list=$GLOBALS['db']->getAll($sql);
	
    $sale_list_data = get_sale_list();
    /* 赋值到模板 */
    $smarty->assign('brand_list',      $brand_list);
    $smarty->assign('subject',      $subject);
	$smarty->assign('person',      $person);
	$smarty->assign('cat_list',     cat_list(0, $cat_id));
    $smarty->assign('filter',       $sale_list_data['filter']);
    $smarty->assign('record_count', $sale_list_data['record_count']);
    $smarty->assign('page_count',   $sale_list_data['page_count']);
    $smarty->assign('goods_sales_list', $sale_list_data['sale_list_data']);
    $smarty->assign('ur_here',          '商品入库');
    $smarty->assign('full_page',        1);
    $smarty->assign('start_date',       local_date('Y-m-d', $start_date));
    $smarty->assign('end_date',         local_date('Y-m-d', $end_date));
    //$smarty->assign('ur_here',      $_LANG['sale_list']);
    //$smarty->assign('cfg_lang',     $_CFG['lang']);
    $smarty->assign('action_link',  array('text' => '下载费用明细','href'=>'#download'));
    $smarty->assign('action_link2',  array('text' => '添加商品入库','href'=>'ecs_goods_storage.php?act=add'));

    /* 显示页面 */
    assign_query_info();
    $smarty->display('ecs_goods_storage.htm');
}
elseif($_REQUEST['act'] == 'add'){
	$goods_type=array();
	
	if($_GET['id']){
		
		$sql = "SELECT * FROM ".$GLOBALS['ecs']->table('due_info')." where rec_id=$_GET[id]";
		$fee=$GLOBALS['db']->getAll($sql);	
		foreach($fee as $key=>$row){
			$goods_type[goods_name]=$row['goods_name'];
			$goods_type[goods_num]=$row['goods_num'];
		}
	}
	
	//ykq_add查询供货商
	$sql = "SELECT suppliers_id, suppliers_name, suppliers_desc, is_check FROM ".$GLOBALS['ecs']->table('suppliers')."";
	$suppliers=$GLOBALS['db']->getAll($sql);
	
	$smarty->assign('suppliers',      $suppliers);
	$smarty->assign('id',    $_GET['id']);
	$smarty->assign('form_act',    'insert');
	$smarty->assign('subject',      $subject);
	$smarty->assign('person',      $person);
	$smarty->assign('goods_type',      $goods_type);
	assign_query_info();
    $smarty->display('ecs_goods_storage_info.htm');
}

elseif($_REQUEST['act'] == 'insert'){
	
	if($_POST['goods_name']){
		$sql = "SELECT `goods_id`,`goods_sn` FROM " . $GLOBALS['ecs']->table('goods')."where goods_name ='".$_POST['goods_name']."'";
    	$goods= $GLOBALS['db']->getRow($sql);
	}
	//修改
	$order_sn = get_order_sn(); 
	if($_POST['id']){
		$ecs_fee=array(
		'goods_sn'=>$goods['goods_sn'],
		'goods_attr'=>$_POST['goods_attr'],
		'goods_name'=>$_POST['goods_name'],
		'goods_amount'=>$_POST['goods_num'],
		'goods_price'=>$_POST['goods_price']
		);
		$db->autoExecute($ecs->table('due_info'), $ecs_fee, 'UPDATE',"rec_id='$_POST[id]'");
		if($db>0){
			$sql = "UPDATE " . $ecs->table('goods') . " SET goods_number = goods_number+$_POST[goods_num] WHERE goods_id = '$goods[goods_id]'";
    		$db->query($sql);	
			sys_msg('修改成功', 0, $links);
		}
	}
	//添加
	
	$ecs_fee=array(
	'order_sn'=>$order_sn,
	'goods_sn'=>$goods['goods_sn'],
	'goods_attr'=>$_POST['goods_attr'],
	'goods_name'=>$_POST['goods_name'],
	'goods_amount'=>$_POST['goods_num'],
	'goods_price'=>$_POST['goods_price'],
	'suppliers_id'=>$_POST['suppliers_id']
	);
    $db->autoExecute($ecs->table('due_info'), $ecs_fee, 'INSERT');
	if($db>0){
		$sql = "UPDATE " . $ecs->table('goods') . " SET goods_number = goods_number+$_POST[goods_num] WHERE goods_id = '$goods[goods_id]'";
    	$db->query($sql);	
		sys_msg('添加成功', 0, $links);
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
    check_authz_json('goods_storage');
    $id = intval($_GET['id']);
    $sql = "DELETE FROM ".$GLOBALS['ecs']->table('goods_storage')." WHERE id = '$id'";
    $db->query($sql);
    if($db>0){
	    clear_cache_files();
	    $url = 'ecs_goods_storage.php?act=query';
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
	//$filter['cat_id']  = empty($_REQUEST['cat_id']) ? 0 : intval($_REQUEST['cat_id']);
	$filter['cat_id']=empty($_REQUEST['cat_id'])?'':$_REQUEST['cat_id'];
	$filter['brand_id']=empty($_REQUEST['brand_id'])?'':$_REQUEST['brand_id'];
    $filter['start_date'] = empty($_REQUEST['start_date']) ? local_strtotime('-7 days') : local_strtotime($_REQUEST['start_date']);
    $filter['end_date'] = empty($_REQUEST['end_date']) ? time() : local_strtotime($_REQUEST['end_date']);
  	$filter['goods_name']=empty($_REQUEST['goods_name'])?'':trim($_REQUEST['goods_name']);
  	$filter['goods_attr']=empty($_REQUEST['goods_attr'])?'':trim($_REQUEST['goods_attr']);
  	$filter['suppliers_name']=empty($_REQUEST['suppliers_name'])?'':trim($_REQUEST['suppliers_name']);
    /* 查询数据的条件 */
    //$where = " WHERE  time >= '".$filter['start_date']."' AND time < '" . ($filter['end_date'] + 86400) . "'";
    $where .="where 1";
    $arr=array();
    if($filter['cat_id']){
    		$get_cat_list=get_cat_list($filter['cat_id']);
        	$sql = "SELECT goods_id FROM " .$GLOBALS['ecs']->table('goods_cat'). " WHERE cat_id in ($get_cat_list)";
	       	$cat=$GLOBALS['db']->getAll($sql);
	       	foreach($cat as $key => $value){
	       		$arr[]=$value[goods_id];
	       	}
	       	$cats=implode(",",$arr);
        	$where.= " AND g.goods_id in ($cats) ";
        }
	if(!empty($filter['goods_name'])){
		   $where.=" and d.goods_name ='".$filter['goods_name']."'";
	    }
	if(!empty($filter['goods_attr'])){
		   $where.=" and d.goods_attr ='".$filter['goods_attr']."'";
	    }
	if(!empty($filter['brand_id'])){
		   $where.=" and b.brand_id ='".$filter['brand_id']."'";
	    }
	if(!empty($filter['suppliers_name'])){
		   $where.=" and s.suppliers_name ='".$filter['suppliers_name']."'";
	    }
    $sql = "SELECT COUNT(rec_id) FROM " .$GLOBALS['ecs']->table('due_info') . " as d
    left join " .$GLOBALS['ecs']->table('goods') . " as g on d.goods_name=g.goods_name
    left join " .$GLOBALS['ecs']->table('brand') . " as b on b.brand_id=g.brand_id
    left join " .$GLOBALS['ecs']->table('suppliers') . "as s on d.suppliers_id=s.suppliers_id
    $where";
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);

    /* 分页大小 */
    $filter = page_and_size($filter);

    $sql = "SELECT d.order_sn,d.goods_name,d.goods_sn,d.goods_attr,d.goods_amount,d.goods_price,s.suppliers_name FROM " . $GLOBALS['ecs']->table('due_info')." as d 
    left join " .$GLOBALS['ecs']->table('goods') . "as g on d.goods_name=g.goods_name 
    left join " .$GLOBALS['ecs']->table('brand') . "as b on b.brand_id=g.brand_id
    left join " .$GLOBALS['ecs']->table('suppliers') . "as s on d.suppliers_id=s.suppliers_id
    $where ORDER BY rec_id DESC";
    if ($is_pagination)
    {
        $sql .= " LIMIT " . $filter['start'] . ', ' . $filter['page_size'];
    }

    $sale_list_data = $GLOBALS['db']->getAll($sql);
    foreach ($sale_list_data as $key => $item)
    {
        //$sale_list_data[$key]['sales_price'] = price_format($sale_list_data[$key]['sales_price']);
        $sale_list_data[$key]['time']  = date('Y-m-d',$sale_list_data[$key]['time']);
    }
    $arr = array('sale_list_data' => $sale_list_data, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    return $arr;
}
?>