<?php

/**
 * ECSHOP 商品分类
 * ============================================================================
 * * 版权所有 2008-2015 秦皇岛商之翼网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.68ecshop.com;
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: derek $
 * $Id: category.php 17217 2011-01-19 06:29:08Z derek $
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

if (!empty($_REQUEST['act']) && $_REQUEST['act'] == 'fenlei')
{
	if($_GET[page]){
		$page=$_GET[page];
	}else{
		$page=1;
	};
	$cat_id=$_GET['cat_id'];
	$sql = "SELECT cat_name FROM " . $GLOBALS['ecs']->table('category') . " WHERE cat_id = '$cat_id'";
	$cat_name = $GLOBALS['db']->getOne($sql);
	
	$sql = "SELECT count(*) FROM " . $GLOBALS['ecs']->table('goods_cat') . " WHERE cat_id = '$cat_id'";
	//$sql = "SELECT count(*) FROM " . $GLOBALS['ecs']->table('goods') . " WHERE cat_id= '$cat_id' and is_delete=0";
	$count = $GLOBALS['db']->getOne($sql);
	$pagesize=20;
	$pagemax=ceil($count/$pagesize);
	$sql = "SELECT g.goods_id,g.goods_thumb,g.goods_name,g.shop_price,g.market_price FROM " . $GLOBALS['ecs']->table('goods_cat') . " as c left join
	" . $GLOBALS['ecs']->table('goods') . " as g on c.goods_id=g.goods_id WHERE c.cat_id= '$cat_id' and is_delete=0 LIMIT  ".(($page-1)*$pagesize).",$pagesize";
	
	$goods_id = $GLOBALS['db']->getAll($sql);
	$goods_list=array();
	foreach($goods_id as $key => $value){
		$goods_list[]=$value;
/*		$sql = "SELECT * FROM " . $GLOBALS['ecs']->table('goods') . " WHERE goods_id= '$value[goods_id]' and is_delete=0";
		$goods = $GLOBALS['db']->getAll($sql);
		foreach($goods as $key =>$value){
			$goods_list[]=$value;
		}*/
	}
	if($page<$pagemax)
	{
		$next=$page+1;
	}
	else
	{
		$next=$pagemax;
	}
	if($page==1)
	{
		$up=$page;
	}
	else
	{
		$up=$page-1;
	}
/*	foreach($goods_id as $key => $value){
		$sql = "SELECT * FROM " . $GLOBALS['ecs']->table('goods') . " WHERE goods_id= '$value[goods_id]' and is_delete=0";
		$goods = $GLOBALS['db']->getAll($sql);
		foreach($goods as $key =>$value){
			$goods_list[]=$value;
		}
	}*/
	//print_r($goods_list);
	$smarty->assign('cat_id',    $cat_id);//
	$smarty->assign('up',    $up);//上一页
    $smarty->assign('next',    $next);//下一页
	$smarty->assign('goods_list', $goods_list);
	$smarty->assign('cat_name', $cat_name);
	$smarty->display('categorys.dwt');
}
?>