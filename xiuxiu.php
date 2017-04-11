<?php
/*
 * Ä§·¨»»×°
 *
 *
 *
 * */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
if ((DEBUG_MODE & 2) != 2)
{
	$smarty->caching = true;
}

if(!$smarty->is_cached("xiuxiu.dwt")){
	$smarty->display("xiuxiu.dwt");
}
