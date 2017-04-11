<?php

/**
 * ECSHOP 商品详情
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
if($_REQUEST['act'] == 'mstj'){
	$sql = "SELECT * FROM ".$ecs->table('yd_subject')." WHERE display='0' ORDER BY id DESC LIMIT 0,6";
	$result = $db->getAll($sql);
	$smarty->assign('result',$result);
	$smarty->assign('imgurl',SITE_URL);
	$smarty->display('mstj.html');
}
if($_REQUEST['act'] == 'mssj'){
	$sql="SELECT * FROM ".$ecs->table('yd_subject')."ORDER BY id DESC";
	$result = $db->getAll($sql);
	$smarty->assign('result',$result);
	$smarty->assign('imgurl',SITE_URL);
	$smarty->display('mssj.html');
}

if($_REQUEST['act'] == 'centent'){
	$id = $_REQUEST['id'];
	$sql = "SELECT * FROM ".$ecs->table('yd_img')."WHERE themeid='$id'";
	$imgpath = $db->getAll($sql);
	$sql = "SELECT g.goods_name,g.shop_price,g.goods_id,g.goods_number FROM ecs_yd_goods AS y,ecs_goods AS g WHERE y.themeid='$id' AND g.goods_id=y.goodsid LIMIT 0,5";
	$shop = $db->getAll($sql);
	$smarty->assign('imgpath',$imgpath);
	$smarty->assign('shop',$shop);
	$smarty->assign('imgurl',SITE_URL);
	$smarty->display('mspay.html');
}


if($_REQUEST['act'] == 'tem_pay'){
	if($_SESSION['user_id'] == 0){
		ecs_header("Location: user.php?act=login\n");
        exit;
	}else{
	$user_id = $_SESSION['user_id'];
	if(!empty($_POST)){
	foreach($_POST as $k => $v){ //先区分开以存在的商品和 不存在的商品
		$goods_id = $k; //获取商品id
		$number  = $v;
		$sql = "SELECT goods_id,user_id,goods_number,goods_price FROM ".$ecs->table('cart')." WHERE user_id = '$user_id' AND goods_id = '$goods_id'"; //判断购物车当中是否 已经存在商品
		if($result = $db->getAll($sql)){
			$be[$k]= $v;
			unset($_POST[$k]);
		}
	}
	foreach($be as $c => $m ){
		$goods_id = $c; //获取商品id
		$number  = $m;
		$sql = "SELECT goods_id,user_id,goods_number,goods_price,market_price FROM ".$ecs->table('cart')." WHERE user_id = '$user_id' AND goods_id = '$goods_id'"; //判断购物车当中是否 已经存在商品
		if($result = $db->getRow($sql)){
			$newnumber = ($number + $result['goods_number']); //和已存在的商品数量叠加
			$newgoodsprice = $result['market_price'] * $newnumber;	//从新计算商品总价格
			$sql = "UPDATE ".$ecs->table('cart')." SET goods_number='$newnumber',goods_price='$newgoodsprice' WHERE user_id = '$user_id' AND goods_id='$goods_id'";
		}
		$db->query($sql); //发送修改购物车数据
	}
	//处理订单表里不存在的数据
	if(!empty($_POST)){
		foreach($_POST as $k => $v){
		$goods_id = $k; //获取商品id
		$number  = $v;
		$sql = "SELECT * FROM ".$ecs->table('goods')." WHERE goods_id = '$goods_id'";//查询商品信息
			if($goods_result = $db->getAll($sql)){
				foreach($goods_result as $l => $o){
					//拼接订单商品
					$cart_list['user_id'] = $user_id; //用户id
					$cart_list['goods_id'] = $goods_id; //商品id
					$cart_list['goods_number'] = $number; //购买数量
					$cart_list['goods_name'] = $o['goods_name']; // 商品名称
					$cart_list['goods_price'] = $o['shop_price'] * $number; // 商品总价格
					$cart_list['market_price'] = $o['shop_price']; // 商品单价
					$cart_list['goods_sn'] = $o['goods_sn'];	//商品编号
					$cart_list['goods_imgpath'] = $o['goods_img'];	//商品图片路径
					$cart_list['cat_id'] = $o['cat_id'];	//商品图片路径
				}
				
			}	
			$cart_sql .="('".$cart_list['rec_id']."','".$cart_list['user_id']."','".$cart_list['session_id']."','".$cart_list['goods_id']."','".$cart_list['goods_sn']."','".$cart_list['product_id']."','".$cart_list['goods_name']."','".$cart_list['market_price']."','".$cart_list['goods_price']."','".$cart_list['goods_number']."','".$cart_list['goods_attr']."','".$cart_list['is_real']."','".$cart_list['extension_code']."','".$cart_list['parent_id']."','".$cart_list['rec_type']."','".$cart_list['is_gift']."','".$cart_list['is_shipping']."','".$cart_list['can_handsel']."','".$cart_list['goods_attr_id']."','".$cart_list['goods_imgpath']."','".$cart_list['cat_id']."'),";		
		
		}
	    $cart_to_sql = "INSERT INTO ".$ecs->table('cart')."(`rec_id`, `user_id`, `session_id`, `goods_id`, `goods_sn`, `product_id`, `goods_name`, `market_price`, `goods_price`, `goods_number`, `goods_attr`, `is_real`, `extension_code`, `parent_id`, `rec_type`, `is_gift`, `is_shipping`, `can_handsel`, `goods_attr_id`, `goods_imgpath`, `cat_id`) VALUES ".trim($cart_sql,',');
	   $db->query($cart_to_sql);
	}
		ecs_header("Location:./tem.php?act=gocart\n");
	}else{
		show_message('请选这您要购买的商品!');
	}

	}

}

//购物车加载
if($_REQUEST['act'] == 'gocart'){
	if($_SESSION['user_id'] == 0){
		ecs_header("Location: user.php?act=login\n");
        exit;
	}else{

	$user_id = $_SESSION['user_id'];
	$sql = "SELECT c.*,r.* FROM ecs_cart c,ecs_category r WHERE c.user_id = '$user_id' AND c.cat_id = r.cat_id";
	dump($db->getAll($sql));
	$smarty->display('themeflow.dwt');
	}
}
?>