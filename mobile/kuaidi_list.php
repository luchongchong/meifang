<?php
/**
 * 查询物流信息
 */
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'kuaidi/kuaidi.php');

if($_SESSION['user_id'] == 0)
{
	show_message('您还没有登录！','去登录','user.php'); 
}
else
{
	$user_id = $_SESSION['user_id']; 
}

$order_id = intval($_REQUEST['order_id']);

if($order_id > 0)
{
	$sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('order_info') . " WHERE order_id = '$order_id' AND user_id = '$user_id'";
	$count = $GLOBALS['db']->getOne($sql);
	if($count > 0)
	{
		 $sql = "SELECT shipping_name,invoice_no FROM " . $GLOBALS['ecs']->table('order_info') . " WHERE order_id = '$order_id'";
		 $order = $GLOBALS['db']->getRow($sql);
		 $kuaidi = new Express();
		 $result = $kuaidi->getorder($order['shipping_name'],$order['invoice_no']);
		 $smarty->assign('order',$order);
		 $smarty->assign('kuaidi_list',$result['data']);
	} 
	else
	{
		show_message('您没有权限查看此物流信息！'); 
	}
}
else
{
	Header("Location: index.php\n"); 
}

$smarty->display('kuaidi_list.dwt');

?>