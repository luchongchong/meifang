<?php
/*
 * 搭配大师
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

if ((DEBUG_MODE & 2) != 2)
{
	$smarty->caching = true;
}

/*------------------------------------------------------ */
//-- INPUT
/*------------------------------------------------------ */

/* 获得请求的分类 ID */
if (isset($_REQUEST['id']))
{
	$cat_id = intval($_REQUEST['id']);
}
elseif (isset($_REQUEST['category']))
{
	$cat_id = intval($_REQUEST['category']);
}
else
{
	/* 如果分类ID为0，设置为1 */
	$cat_id=1;
}

//获得所有分类
$sort_list=get_categories_tree();
for ($i=1;$i<=count($sort_list);$i++){
	if($sort_list[$i]['name']==CATEGROY_MATERIAL){
		$material=$sort_list[$i]['cat_id'];
	}elseif ($sort_list[$i]['name']==CATEGROY_PLACE){
		$cait=$sort_list[$i]['cat_id'];
	}
}
$smarty->assign('material',$material);
$smarty->assign('cait',$cait);


$keywords = isset($_REQUEST['keywords']) ? $_REQUEST['keywords'] : "";
$page = isset($_REQUEST['page']) && intval($_REQUEST['page']) > 0 ? intval($_REQUEST['page']) : 1;

$record_count = all_dapei_count($keywords);//总数
$pager_list = get_pager("dapei.php", array('keywords'=>$keywords), $record_count,$page,$size=8);//分页
$all_dapei_list = all_dapei_list($keywords,$page,$size);//===数据

//====搭配结算==
if(isset($_REQUEST['act']) && $_REQUEST['act'] == 'see'){
	$id = isset($_REQUEST['id']) && intval($_REQUEST['id']) > 0 ?intval($_REQUEST['id']) : 0;
	$sql = "select * from ".$GLOBALS['ecs']->table('yd_subject')." where id=$id";
	$dapei_info = $GLOBALS['db'] ->getrow($sql);
	
	$smarty->assign('dapei_info', $dapei_info);
	$smarty->display("dapei_jiesuan.dwt");
	return;
}


$smarty->assign('keywords', $keywords);
$smarty->assign('all_dapei_list', $all_dapei_list);
$smarty->assign('pager_list', $pager_list);
$smarty->display("dapei.dwt");

//=====总数
function all_dapei_count($keywords){
	if(!empty($keywords)){
		$where = " WHERE theme like '%$keywords%' OR design LIKE '%$keywords%'";
	}
	
	$sql = "SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('yd_subject').$where;
	$count = $GLOBALS['db']->getOne($sql);
	return $count;
}

//====产品列表===
function all_dapei_list($keywords = '',$page=1,$size = 8){
	if(!empty($keywords)){
		$keywords = trim($keywords);
		$keywords = htmlspecialchars($keywords);
		$where = "where theme like '%$keywords%' OR design LIKE '%$keywords%'";
		//$where = " AND where theme like '%$keywords%' OR design LIKE '%$keywords%'";
	}
	
	$start_size = ($page - 1) * $size;
	$sql = "select * from".$GLOBALS['ecs']->table('yd_subject').$where."order by id desc limit $start_size , $size";
	//$sql = "select ys.id,ys.theme,ys.design,yd.dianzan,yd.yuedu from ecs_yd_subject as ys left join ecs_yd_dz as yd where ys.id = yd.id ".$where. "order by ys.id desc limit $start_size , $size";
	$arr = $GLOBALS['db']->getAll($sql);
	
	
	return $arr;
}