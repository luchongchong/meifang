<?php

/**
 * ECSHOP 用户二维码关注
 * ============================================================================
 * * 版权所有 2008-2015 秦皇岛商之翼网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.68ecshop.com;
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: derek $
 * $Id: goods.php 17217 2011-01-19 06:29:08Z derek $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

$id=isset($_GET['id'])?$_GET['id']:0;

$wxidinfo = $GLOBALS['db']->getRow("SELECT * FROM ".$ecs->table('weixin_user')." WHERE ecuid = '$id'");
$sql_qr = "SELECT `ticket` FROM `wxch_qr` WHERE `type` = 'tj' and `affiliate` = ".$id;

$ticket = $db->getRow($sql_qr);
$ticket = $ticket['ticket'];

$qr_url = "http://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".$ticket;
$smarty->assign('qr_url', $qr_url);
$smarty->assign('user', $wxidinfo);

$url=SITE_URL.'mobile/user.php?act=myqrcode&id='.$user_id;
$back_url=SITE_URL.'mobile/user.php?act=point_share&user_id='.$_SESSION['user_id'];
$title=$wxidinfo['nickname']."邀请你加入美房美邦。。。";
$desc = '美房美邦';
//print_r($back_url);
include_once('includes/cls_share.php');
$cls_share = new cls_share();
$re_share = $cls_share->cls_shares($title,$url,$qr_url,$desc,$back_url);
$smarty->assign('re_share',  $re_share);
$smarty->display('qr_weixin.dwt');


?>