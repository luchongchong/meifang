<?php

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
if ((DEBUG_MODE & 2) != 2)
{
	$smarty->caching = true;
}

if(!$smarty->is_cached("allmendian.dwt")){
	$sort_list=get_categories_tree();
	for ($i=1;$i<=count($sort_list);$i++){
		if($sort_list[$i]['name']==CATEGROY_MATERIAL){
			$material=$sort_list[$i]['cat_id'];
		}elseif ($sort_list[$i]['name']==CATEGROY_PLACE){
			$cait=$sort_list[$i]['cat_id'];
		}
	}
	//获得所有分类
	$smarty->assign('material',$material);
	$smarty->assign('cait',$cait);


}
$smarty->display('liucheng.dwt');