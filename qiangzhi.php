<?php
/*
 * 墙纸大师
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

if(isset($_REQUEST['act']) && $_REQUEST['act'] == 'see'){
	$id = isset($_REQUEST['id']) && intval($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
	$sql = "";
}


get_pager("qiangzhi.php", array('keywords'=>$keywords), $record_count);

$smarty->display("qiangzhi.dwt");


//=====墙纸总数===
function qiangzhi_count($keywords){
	if($keywords){
		$sql = " where ";
	}
	
}

//======墙纸总量====
function all_qiangzhi_list($keywords,$page,$size){
	
	
}



