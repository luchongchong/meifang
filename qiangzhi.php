<?php
/*
 * ǽֽ��ʦ
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

/* �������ķ��� ID */
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
	/* �������IDΪ0������Ϊ1 */
	$cat_id=1;
}


//������з���
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


//=====ǽֽ����===
function qiangzhi_count($keywords){
	if($keywords){
		$sql = " where ";
	}
	
}

//======ǽֽ����====
function all_qiangzhi_list($keywords,$page,$size){
	
	
}



