<?php

/**
 * MEIFANG 返利进程
 * ============================================================================ 
 * 版权所有 2005-2014 上海优辉商务，并保留所有权利。
 * 网站地址: http://www.j345.net
 * ----------------------------------------------------------------------------
 * 优辉网络,共创你我
 * ============================================================================
 * $Author: liubo $
 * $Id: lib_goods.php 17217 2011-01-19 06:29:08Z liubo $
*/

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}
/**
 * 分利
 */
function rebate($order_id){
    
    //进行返利操作
    // 1,获取订单商品信息
    $sql_1 = "SELECT * FROM ".$GLOBALS['ecs']->table('order_goods')." as og left join ".$GLOBALS['ecs']->table('goods')." as goods on og.goods_id=goods.goods_id where og.order_id = ".$order_id;
    
    $order_goods = $GLOBALS['db']->getRow($sql_1);
    // 获取分利配置ID
    $affiliate_config_id = $order_goods['affiliate_id'];
 
    // ================数据准备 start========================
//    服务商      通过安装用户查找(6%)
//    介绍开店  通过安装用户查找(无介绍id，这部分分利给安装)(5%)
//    销售门店  通过订单查找(20%)
//    安装门店  通过订单查找(20%)
//    经销商      通过购买者id查找(5%)
//    上级会员  通过购买者id查找(10%)
    
    // 2,在订单信息中需要获取购买者ID，销售门店（经销商）编号，安装门店编号，订单金额
    
    // 获取订单信息
    $sql_2 = "SELECT * FROM ".$GLOBALS['ecs']->table('order_info')." where is_gain_rebate=0 and order_status=1 and shipping_status=2 and pay_status=2 and order_id = ".$order_id;
    $result_2 = $GLOBALS['db']->getAll($sql_2);
    $order_info = $result_2[0];
    // 销售门店编号
    $shop_no = $order_info['shop_no'];
    // 安装门店编号
    $construction_no = $order_info['construction_no'];
    // 购买者用户ID
    $user_id = $order_info['user_id'];
    // 商品的总金额
    $goods_amount = $order_info['goods_amount'];
    // 3,上级会员和经销商  通过购买者id查找用户表
    // 获取用户信息（自己）
    $sql_3 = "SELECT * FROM ".$GLOBALS['ecs']->table('users')." where user_id = ".$user_id;
    $result_3 = $GLOBALS['db']->getAll($sql_3);
    //上级会员  通过购买者id查找(10%)
    $shangji_id = $result_3[0]['parent_id'];
 
    //经销商      通过购买者id查找(5%)
    $dingjijingxiaoshang_id = $result_3[0]['sell_id'];
    $dingjijingxiaoshang_id = $dingjijingxiaoshang_id;
    // 4,安装门店  通过订单查找(20%)
    $sql_4 = "SELECT * FROM ".$GLOBALS['ecs']->table('store')." where shop_no  = ".$construction_no;
    $result_4 = $GLOBALS['db']->getAll($sql_4);
    // 施工队用户ID
    $anzhuangmendian_id = $result_4[0]['userid'];
    
    // 5,销售门店  通过订单查找(20%)
    $sql_5 = "SELECT * FROM ".$GLOBALS['ecs']->table('store')." where shop_no = ".$shop_no;
    $result_5 = $GLOBALS['db']->getAll($sql_5);
    
    // 门店（经销商）用户ID
    $xiaoshoumendian_id = $result_5[0]['userid'];
    $sql_51 = "SELECT * FROM ".$GLOBALS['ecs']->table('users')." where user_id = ".$anzhuangmendian_id;
    $result_51 = $GLOBALS['db']->getAll($sql_51);
    // 服务商      通过安装用户查找(6%)
    $fuwushang_id = $result_51[0]['service_id'];
    // 介绍开店  通过安装用户查找(无介绍id，这部分分利给安装)(5%)
    $introduce_id = $result_51[0]['introduce_id'];
    
    // 有无介绍人标志
    $has_jieshaoren_flg = true;
    //(无介绍id，这部分分利给安装)(5%)
    if(empty($introduce_id)){
        $has_jieshaoren_flg = false;
    	$introduce_id = $anzhuangmendian_id;
    }
    // 6,获取各个对象的分成比例
    $sql_6 = "SELECT * FROM ".$GLOBALS['ecs']->table('affiliate_config')." where id = ".$affiliate_config_id;
    $result_6 = $GLOBALS['db']->getAll($sql_6);
    $affiliate_config = $result_6[0];
    // 所属顶级经销商
	$bili_dingjijinxiaoshang = $affiliate_config['top_sell'];
    // 所属顶级服务商
	$bili_dingjifuwushang = $affiliate_config['top_service'];
    // 所属上级
    $bili_shangji = $affiliate_config['parent'];
    // 服务服务商(安装门店)
    $bili_fuwufuwushang = $affiliate_config['install'];
    // 发货经销商(销售门店)
    $bili_fahuojingxiaoshang = $affiliate_config['sell'];
    //开店介绍人
    $bili_jieshaoren = $affiliate_config['introduce'];
    
    // ================数据准备 end========================
    // ================计算金额，更新账户和日志 start========================
    // 所属顶级经销商分利金额(5%)
    fenli($dingjijingxiaoshang_id, $goods_amount, $bili_dingjijinxiaoshang, '经销商粉丝'.$bili_dingjijinxiaoshang, $order_id);
    // 所属顶级服务商分利金额(6%)
    fenli($fuwushang_id, $goods_amount, $bili_dingjifuwushang, '服务商'.$bili_dingjifuwushang, $order_id);
    // 所属上级分利金额(10%)
    fenli($shangji_id, $goods_amount, $bili_shangji, '下级购物'.$bili_shangji, $order_id);
    // 服务服务商分利金额(20%)
    fenli($anzhuangmendian_id, $goods_amount, $bili_fuwufuwushang, '销售收益'.$bili_fuwufuwushang, $order_id);
   
    // 发货经销商分利金额(20%)
    fenli($xiaoshoumendian_id, $goods_amount, $bili_fahuojingxiaoshang, '施工收益'.$bili_fahuojingxiaoshang, $order_id);
    
    if($has_jieshaoren_flg){
    	$charge_desc = '介绍开店';
    }else{
    	$charge_desc = '无介绍人，施工收益';
    }
    // 开店介绍人分利金额(5%)
    fenli($introduce_id, $goods_amount, $bili_jieshaoren, $charge_desc.$bili_jieshaoren, $order_id);
    // ================计算金额，更新账户和日志  end========================
    // ================更新订单状态 start========================
    //7,改变订单里面的状态
    $sql_7 = "UPDATE " . $GLOBALS['ecs']->table('order_info') . " SET is_gain_rebate = 1 WHERE order_id = ".$order_id;
    $GLOBALS['db'] -> query($sql_7);
    // ================更新订单状态 end========================
}

/*分利*/
function fenli($user_id, $goods_amount, $bili, $charge_desc, $order_id){
    $fenlijine = $goods_amount * $bili * 0.01;
    $fenlijine=floatval($fenlijine);
    $fenlijine=number_format($fenlijine,2,".","");
    
    //更新用户余额
    $sql1 = "UPDATE " . $GLOBALS['ecs']->table('users') . " SET user_money = user_money + ".$fenlijine." WHERE user_id = ".$user_id;
    
    $GLOBALS['db'] -> query($sql1);
    $zj_time=time();
    //插入日志表
    $sql_2 = "INSERT INTO ".$GLOBALS['ecs']->table('account_log')." (user_id , user_money, frozen_money , rank_points , pay_points , change_time , change_desc , change_type, order_id) VALUES ('$user_id','$fenlijine','0','0','0','$zj_time','$charge_desc','99','$order_id')";
    $GLOBALS['db']->query($sql_2);
    
}


?>
