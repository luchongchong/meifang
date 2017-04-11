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
$exc = new exchange($ecs->table('bank'), $db, 'id', 'unit_name');
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
    	$aaa="银行系统";
        $file_name = $_REQUEST['start_date'].'_'.$_REQUEST['end_date'] . '_sale';
        $goods_sales_list = get_sale_list(false);
        header("Content-type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=$aaa$file_name.xls");

        /* 文件标题 */
        echo ecs_iconv(EC_CHARSET, 'GB2312', $_REQUEST['start_date']. $_LANG['to'] .$_REQUEST['end_date'].'财务账目明细流水') . "\t\n";

        /* 商品名称,订单号,商品数量,销售价格,销售日期 */
        echo ecs_iconv(EC_CHARSET, 'GB2312', '时间') . "\t";      
        echo ecs_iconv(EC_CHARSET, 'GB2312', '银行名称') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '部门名称') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '费用名称') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '摘要') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '单位名称') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '借贷方向') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '金额(借)') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '金额(贷)') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '余额') . "\t\n";

        foreach ($goods_sales_list['sale_list_data'] AS $key => $value)
        {
        	if($value['to_loan']==1){$value['to_loan']="借";}elseif($value['to_loan']==2){$value['to_loan']="贷";}        	
        	echo ecs_iconv(EC_CHARSET, 'GB2312', $value['time']) . "\t";
        	echo ecs_iconv(EC_CHARSET, 'GB2312', $value['bank_name']) . "\t";
        	echo ecs_iconv(EC_CHARSET, 'GB2312', $value['bank_department']) . "\t";
        	echo ecs_iconv(EC_CHARSET, 'GB2312', $value['cost_name']) . "\t"; 
        	echo ecs_iconv(EC_CHARSET, 'GB2312', $value['column_name']) . "\t";  
        	echo ecs_iconv(EC_CHARSET, 'GB2312', $value['unit_name']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['to_loan']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['money_min']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['money_max']) . "\t";
            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['bank_balance']) . "\t";                         
            echo "\n";
        }
        exit;
    }
    
    $sale_list_data = get_sale_list();
    $smarty->assign('goods_sales_list', $sale_list_data['sale_list_data']);
    $smarty->assign('filter',       $sale_list_data['filter']);
    $smarty->assign('record_count', $sale_list_data['record_count']);
    $smarty->assign('page_count',   $sale_list_data['page_count']);

    make_json_result($smarty->fetch('bank_account.htm'), '', array('filter' => $sale_list_data['filter'], 'page_count' => $sale_list_data['page_count']));
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
    
	$sql = "SELECT * FROM ".$GLOBALS['ecs']->table('bank_name')."";
	$bank_name=$GLOBALS['db']->getAll($sql);
	
    $sale_list_data = get_sale_list();
    /* 赋值到模板 */
    $smarty->assign('subject',      $subject);
	$smarty->assign('person',      $person);
	
	$smarty->assign('bank_name',       $bank_name);
    $smarty->assign('filter',       $sale_list_data['filter']);
    $smarty->assign('record_count', $sale_list_data['record_count']);
    $smarty->assign('page_count',   $sale_list_data['page_count']);
    $smarty->assign('goods_sales_list', $sale_list_data['sale_list_data']);
    $smarty->assign('ur_here',          '银行账目');
    $smarty->assign('full_page',        1);
    $smarty->assign('start_date',       local_date('Y-m-d', $start_date));
    $smarty->assign('end_date',         local_date('Y-m-d', $end_date));
    $smarty->assign('action_link',  array('text' => '下载银行账目明细','href'=>'#download'));
    $smarty->assign('action_link2',  array('text' => '添加银行账目费用','href'=>'bank_account.php?act=add'));

    /* 显示页面 */
    assign_query_info();
    $smarty->display('bank_account.htm');
}
elseif($_REQUEST['act'] == 'add'){
	
	
	//查询费用名称
	$sql = "SELECT * FROM ".$GLOBALS['ecs']->table('bank_info')." where bank_cost_name!=''";
	$bank_cost_name=$GLOBALS['db']->getALL($sql);
	
	//查询部门名称
	$sql = "SELECT * FROM ".$GLOBALS['ecs']->table('bank_info')." where bank_department!=''";
	$bank_department=$GLOBALS['db']->getAll($sql);
	
	//查询银行名称
	$sql = "SELECT * FROM ".$GLOBALS['ecs']->table('bank_name')."";
	$bank_name=$GLOBALS['db']->getAll($sql);
	
    $smarty->assign('form_act',    'insert');
	$smarty->assign('bank_cost_name',      $bank_cost_name);
	$smarty->assign('bank_department',      $bank_department);
	$smarty->assign('bank_name',      $bank_name);
	$smarty->assign('fees',      $fees);
	
	assign_query_info();
    $smarty->display('bank_account_info.htm');
}

elseif($_REQUEST['act'] == 'insert'){
	
	//查询银行名称
	$sql = "SELECT bank_balance FROM ".$GLOBALS['ecs']->table('bank_name')." where id=$_POST[pay_mode]";
	$bank_balance=$GLOBALS['db']->getOne($sql);
	if($_POST['to_loan']==1){
		$bank_balance=$bank_balance+$_POST['money_min'];
		$sql = "UPDATE " .$ecs->table('bank_name'). " SET bank_balance='$bank_balance' WHERE id='$_POST[pay_mode]'";
    	$db->query($sql);
    	$money_min=$_POST['money_min'];
	}
	if($_POST['to_loan']==2){
		$bank_balance=$bank_balance-$_POST['money_min'];
		$sql = "UPDATE " .$ecs->table('bank_name'). " SET bank_balance='$bank_balance' WHERE id='$_POST[pay_mode]'";
    	$db->query($sql);
    	$money_max=$_POST['money_min'];
	}
	$ecs_fee=array(
	'unit_name'=>$_POST['unit_name'],//单位名称
	'pay_mode'=>$_POST['pay_mode'],//银行名称
	'column_name'=>$_POST['column'],//摘要
	'to_loan'=>$_POST['to_loan'],//借贷方向
	'money_min'=>$money_min,//金额借方
	'money_max'=>$money_max,//金额贷方
	'cost_name'=>$_POST['cost_name'],//费用名称
	'bank_department'=>$_POST['bank_department'],//部门
	'time'=>time(),
	'bank_balance'=>$bank_balance
	);
	$db->autoExecute($ecs->table('bank'), $ecs_fee, 'INSERT');
	if($db>0){
		$links=array( array('href' => 'bank_account.php?act=list'));
		sys_msg('添加成功', 0, $links);
	}
}
elseif ($_REQUEST['act'] == 'edit')
{
    $goods_type =$_GET['id'];
    admin_priv('goods_type');
	$sql = "SELECT * FROM ".$GLOBALS['ecs']->table('bank')." where id=$goods_type";
	$goods_type=$GLOBALS['db']->getRow($sql);
	
	//查询费用名称
	$sql = "SELECT * FROM ".$GLOBALS['ecs']->table('bank_info')." where bank_cost_name!=''";
	$bank_cost_name=$GLOBALS['db']->getAll($sql);
	
	//查询部门名称
	$sql = "SELECT * FROM ".$GLOBALS['ecs']->table('bank_info')." where bank_department!=''";
	$bank_department=$GLOBALS['db']->getAll($sql);
	
	//查询银行名称
	$sql = "SELECT * FROM ".$GLOBALS['ecs']->table('bank_name')."";
	$bank_name=$GLOBALS['db']->getAll($sql);
    $smarty->assign('ur_here',     $_LANG['edit_goods_type']);
    $smarty->assign('action_link', array('href'=>'bank_account.php?act=list', 'text' => '账目明细'));
    $smarty->assign('action',      'add');
    $smarty->assign('form_act',    'update');
    $smarty->assign('bank_cost_name',      $bank_cost_name);
	$smarty->assign('bank_department',      $bank_department);
	$smarty->assign('bank_name',      $bank_name);
    $smarty->assign('goods_type',  $goods_type);

    assign_query_info();
    $smarty->display('bank_account_info.htm');
}

elseif ($_REQUEST['act'] == 'update')
{
	//查询银行余额
	$pay_mode                  = intval($_POST['pay_mode']);
	$cat_id                   = intval($_POST['cat_id']);
	
	$sql = "SELECT bank_balance FROM ".$GLOBALS['ecs']->table('bank')." where id=$cat_id";
	$bank_balance=$GLOBALS['db']->getOne($sql);
	
	$sql = "SELECT time FROM ".$GLOBALS['ecs']->table('bank')." where id=$cat_id";
	$time=$GLOBALS['db']->getOne($sql);
	
	
	if($_POST['to_loan']==1){
		$sql = "SELECT id,money_min FROM ".$GLOBALS['ecs']->table('bank')." where pay_mode=$pay_mode and time > $time";
		$money_mins=$GLOBALS['db']->getAll($sql);
		
		$sql = "SELECT money_min FROM ".$GLOBALS['ecs']->table('bank')." where id=$cat_id";
		$money_min=$GLOBALS['db']->getOne($sql);
		
		if($_POST['money_min'] < $money_min){
			$a=$money_min-$_POST['money_min'];
			foreach($money_mins as $key=>$value){
				$sql = "UPDATE " .$ecs->table('bank'). " SET bank_balance=bank_balance-$a WHERE id='$value[id]'";
				$db->query($sql);
			}		
			$bank_balance=$bank_balance-$a;
			$sql = "UPDATE " .$ecs->table('bank_name'). " SET bank_balance=bank_balance-'$a' WHERE id='$_POST[pay_mode]'";
    		$db->query($sql);
		}
		
	    if($_POST['money_min'] > $money_min){
			$a=$_POST['money_min']-$money_min;
	    	foreach($money_mins as $key=>$value){
				$sql = "UPDATE " .$ecs->table('bank'). " SET bank_balance=bank_balance+$a WHERE id='$value[id]'";
				$db->query($sql);
			}
			$bank_balance=$bank_balance+$a;
			$sql = "UPDATE " .$ecs->table('bank_name'). " SET bank_balance=bank_balance+'$a' WHERE id='$_POST[pay_mode]'";
    		$db->query($sql);
		}
    	$money_min=$_POST['money_min'];
        
	}
	
	if($_POST['to_loan']==2){
		$sql = "SELECT id,money_min FROM ".$GLOBALS['ecs']->table('bank')." where pay_mode=$pay_mode and time > $time";
		$money_mins=$GLOBALS['db']->getAll($sql);
		
		$sql = "SELECT money_max FROM ".$GLOBALS['ecs']->table('bank')." where id=$cat_id";
		$money_max=$GLOBALS['db']->getOne($sql);
		
		if($_POST['money_min'] < $money_max){
			$a=$money_max-$_POST['money_min'];
			foreach($money_mins as $key=>$value){
				$sql = "UPDATE " .$ecs->table('bank'). " SET bank_balance=bank_balance+$a WHERE id='$value[id]'";
				$db->query($sql);
			}
			$sql = "UPDATE " .$ecs->table('bank_name'). " SET bank_balance=bank_balance+'$a' WHERE id='$_POST[pay_mode]'";
    		$db->query($sql);
			$bank_balance=$bank_balance+$a;
		}
	    if($_POST['money_min'] > $money_max){
			$a=$_POST['money_min']-$money_max;
	    	foreach($money_mins as $key=>$value){
				$sql = "UPDATE " .$ecs->table('bank'). " SET bank_balance=bank_balance-$a WHERE id='$value[id]'";
				$db->query($sql);
			}
			$bank_balance=$bank_balance-$a;
			$sql = "UPDATE " .$ecs->table('bank_name'). " SET bank_balance=bank_balance-'$a' WHERE id='$_POST[pay_mode]'";
    		$db->query($sql);
		}
    	$money_max=$_POST['money_min'];
	}
    $ecs_fee=array(
	'unit_name'=>$_POST['unit_name'],//单位名称
	'pay_mode'=>$_POST['pay_mode'],//付款方式
	'column_name'=>$_POST['column'],//摘要
	'to_loan'=>$_POST['to_loan'],//借贷方向
	'money_min'=>$money_min,//金额借方
	'money_max'=>$money_max,//金额贷方
	'cost_name'=>$_POST['cost_name'],//费用名称
	'bank_department'=>$_POST['bank_department'],//部门
    'bank_balance'=>$bank_balance
	);
    
    if ($db->autoExecute($ecs->table('bank'), $ecs_fee, 'UPDATE', "id='$cat_id'") !== false)
    {
        $links = array(array('href' => 'bank_account.php?act=list', 'text' => $_LANG['back_list']));
        sys_msg('修改银行账目成功', 0, $links);
    }
    else
    {
        sys_msg($_LANG['edit_goodstype_failed'], 1);
    }
}
elseif ($_REQUEST['act'] == 'remove')
{
    //check_authz_json('bank_account');

    $id = intval($_GET['id']);

    $name = $exc->get_name($id);
    
	$sql = "SELECT * FROM ".$GLOBALS['ecs']->table('bank')." where id=$id";
	$bank=$GLOBALS['db']->getRow($sql);
	
	$sql = "SELECT id FROM ".$GLOBALS['ecs']->table('bank')." where pay_mode=$bank[pay_mode] and time > $bank[time]";
    $money_mins=$GLOBALS['db']->getAll($sql);
    
	if($bank[to_loan]==1){		
		foreach($money_mins as $key=>$value){
				$sql = "UPDATE " .$ecs->table('bank'). " SET bank_balance=bank_balance-$bank[money_min] WHERE id='$value[id]'";
				$db->query($sql);
			}
		$sql = "UPDATE " .$ecs->table('bank_name'). " SET bank_balance=bank_balance-$bank[money_min] WHERE id='$bank[pay_mode]'";
    	$db->query($sql);
	}
	
	if($bank[to_loan]==2){
		foreach($money_mins as $key=>$value){
				$sql = "UPDATE " .$ecs->table('bank'). " SET bank_balance=bank_balance+$bank[money_max] WHERE id='$value[id]'";
				$db->query($sql);
			}
		$sql = "UPDATE " .$ecs->table('bank_name'). " SET bank_balance=bank_balance+$bank[money_max] WHERE id='$bank[pay_mode]'";
    	$db->query($sql);
	}
    if ($exc->drop($id))
    {
        admin_log(addslashes($name), 'remove', 'bank_account');
        $url = 'bank_account.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);
        ecs_header("Location: $url\n");
        exit;
    }
    else
    {
        make_json_error($_LANG['remove_failed']);
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
  	$filter['unit_name']=empty($_REQUEST['unit_name'])?'':trim($_REQUEST['unit_name']);
  	$filter['bank_department']=empty($_REQUEST['bank_department'])?'':trim($_REQUEST['bank_department']);
  	$filter['money']=empty($_REQUEST['money'])?'':trim($_REQUEST['money']);
  	$filter['moneys']=empty($_REQUEST['moneys'])?'':trim($_REQUEST['moneys']);
  	$filter['column_name']=empty($_REQUEST['column_name'])?'':trim($_REQUEST['column_name']);
  	$filter['bank_id']=empty($_REQUEST['bank_name'])?'':$_REQUEST['bank_name'];
    /* 查询数据的条件 */
    $where = " WHERE time >= '".$filter['start_date']."' AND time < '" . ($filter['end_date'] + 86400) . "'";
	if(!empty($filter['unit_name'])){
		   $where.=" and a.unit_name like '%".$filter['unit_name']."%'";
	    }
	if(!empty($filter['bank_department'])){
		   $where.=" and a.bank_department like '%".$filter['bank_department']."%'";
	    }
	if(!empty($filter['money'])){
	        $where.=" and a.money_min >= '".$filter['money']."' and money_min <= '".$filter['moneys']."'";
	    }
	    
	if(!empty($filter['column_name'])){
	        $where.=" and a.column_name like '%".$filter['column_name']."%'";
	    }
	if(!empty($filter['bank_id'])){
	        $where.=" and b.id= '".$filter[bank_id]."'";
	    }  
    $sql = "SELECT COUNT(a.id) FROM " .$GLOBALS['ecs']->table('bank') ."as a left join
    " . $GLOBALS['ecs']->table('bank_name')." as b on a.pay_mode=b.id ".$where;
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);

    /* 分页大小 */
    $filter = page_and_size($filter);

    $sql = "SELECT a.*,b.id as ids,b.bank_name FROM " . $GLOBALS['ecs']->table('bank')." as a left join
    " . $GLOBALS['ecs']->table('bank_name')." as b on a.pay_mode=b.id
    ". $where. " ORDER BY a.id DESC";
    if ($is_pagination)
    {
        $sql .= " LIMIT " . $filter['start'] . ', ' . $filter['page_size'];
    }

    $sale_list_data = $GLOBALS['db']->getAll($sql);
	//print_r($sale_list_data);
    foreach ($sale_list_data as $key => $item)
    {
        $sale_list_data[$key]['sales_price'] = price_format($sale_list_data[$key]['sales_price']);
        $sale_list_data[$key]['time']  = date('Y-m-d H:i:s',$sale_list_data[$key]['time']);
    }
    $arr = array('sale_list_data' => $sale_list_data, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    return $arr;
}
?>