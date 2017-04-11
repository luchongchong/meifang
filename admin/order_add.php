<?php

/**
 * MEIFANG 订单管理
 * ============================================================================
 * 版权所有 2005-2010 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ECSHOP.com；
 * ----------------------------------------------------------------------------
 * 优辉网络,共创你我
 * ============================================================================
 * $Author: yehuaixiao $
 * $Id: order_add.php 17219 2011-01-27 10:49:19Z yehuaixiao $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_order.php');
require_once(ROOT_PATH . 'includes/lib_goods.php');

/*------------------------------------------------------ */
//-- 订单查询
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'step_post')
{

    /* 检查权限 */
    admin_priv('order_edit');

    /* 取得参数 step */
    $step_list = array('user', 'edit_goods', 'add_goods', 'goods', 'consignee', 'shipping', 'payment', 'other', 'money', 'invoice');
    $step = isset($_REQUEST['step']) && in_array($_REQUEST['step'], $step_list) ? $_REQUEST['step'] : 'user';

    /* 取得参数 order_id */
    $order_id = isset($_REQUEST['order_id']) ? intval($_REQUEST['order_id']) : 0;
    if ($order_id > 0)
    {
        $old_order = order_info($order_id);
    }

    /* 取得参数 step_act 添加还是编辑 */
    $step_act = isset($_REQUEST['step_act']) ? $_REQUEST['step_act'] : 'add';

    /* 插入订单信息 */
    if ('user' == $step)
    {

        /* 取得参数：user_id */
        $user_id = ($_POST['anonymous'] == 1) ? 0 : intval($_POST['user']);

        /* 插入新订单，状态为无效 */
        $order = array(
            'user_id'           => $user_id,
            'add_time'          => gmtime(),
            'order_status'      => OS_INVALID,
            'shipping_status'   => SS_UNSHIPPED,
            'pay_status'        => PS_UNPAYED,
            'from_ad'           => 0,
            'referer'           => $_LANG['admin']
        );

        do
        {
            $order['order_sn'] = get_order_sn();
            if ($db->autoExecute($ecs->table('order_info'), $order, 'INSERT', '', 'SILENT'))
            {
                break;
            }
            else
            {
                if ($db->errno() != 1062)
                {
                    die($db->error());
                }
            }
        }
        while (true); // 防止订单号重复

        echo $order_id = $db->insert_id();

        /* todo 记录日志 */
        admin_log($order['order_sn'], 'add', 'order');

        /* 插入 pay_log */
        $sql = 'INSERT INTO ' . $ecs->table('pay_log') . " (order_id, order_amount, order_type, is_paid)" .
                " VALUES ('$order_id', 0, '" . PAY_ORDER . "', 0)";
        $db->query($sql);

        /* 下一步 */
        ecs_header("Location: order_add.php?act=" . $step_act . "&order_id=" . $order_id . "&step=goods\n");
        exit;
    }

    /* 添加商品 */
    elseif ('add_goods' == $step)
    {

        if(empty($order_id)){
            sys_msg("请先输入用户名！");
            exit();
        }

        /* 取得参数 */
        $goods_id = intval($_POST['goodslist']);
        $goods_price = $_POST['add_price'] != 'user_input' ? floatval($_POST['add_price']) : floatval($_POST['input_price']);

        $wallpaper_price = floatval($_POST['wallpaper_price']);
    
        $goods_attr = '0';
        for ($i = 0; $i < $_POST['spec_count']; $i++)
        {
            if (is_array($_POST['spec_' . $i]))
            {
                $temp_array = $_POST['spec_' . $i];
                $temp_array_count = count($_POST['spec_' . $i]);
                for ($j = 0; $j < $temp_array_count; $j++)
                {
                    if($temp_array[$j]!==NULL)
                    {
                        $goods_attr .= ',' . $temp_array[$j];
                    }
                }
            }
            else
            {
                if($_POST['spec_' . $i]!==NULL)
                {
                    $goods_attr .= ',' . $_POST['spec_' . $i];
                }
            }
        }
        $goods_number = $_POST['add_number'];
        $attr_list = $goods_attr;

        $goods_attr = explode(',',$goods_attr);
        $k   =   array_search(0,$goods_attr);
        unset($goods_attr[$k]);


        $sql = "SELECT attr_value ".
            'FROM ' . $GLOBALS['ecs']->table('goods_attr') .
            "WHERE goods_attr_id in($attr_list)";
       $res = $db->query($sql);
        while ($row = $db->fetchRow($res))
        {
            $attr_value[] = $row['attr_value'];
        }

        $attr_value = implode(",",$attr_value);

        $sql = "SELECT * FROM " .$GLOBALS['ecs']->table('products'). " WHERE goods_id = '$goods_id' LIMIT 0, 1";
        $prod = $GLOBALS['db']->getRow($sql);


        if (is_spec($goods_attr) && !empty($prod))
        {
            $product_info = get_products_info($_REQUEST['goodslist'], $goods_attr);
        }

        //商品存在规格 是货品 检查该货品库存
        if (is_spec($goods_attr) && !empty($prod))
        {
            if (!empty($goods_attr))
            {
                /* 取规格的货品库存 */
                if ($goods_number > $product_info['product_number'])
                {
                    $url = "order_add.php?act=" . $step_act . "&order_id=" . $order_id . "&step=goods";

                    echo '<a href="'.$url.'">'.$_LANG['goods_num_err'] .'</a>';
                    exit;

                    return false;
                }
            }
        }

        if(is_spec($goods_attr) && !empty($prod))
        {
        /* 插入订单商品 */
            $sql = "INSERT INTO " . $ecs->table('order_goods') .
                        "(order_id, goods_id, goods_name, goods_sn, product_id, goods_number, market_price, " .
                        "goods_price,wallpaper_price, goods_attr, is_real, extension_code, parent_id, is_gift, goods_attr_id) " .
                    "SELECT '$order_id', goods_id, goods_name, goods_sn, " .$product_info['product_id'].", ".
                        "'$goods_number', market_price, '$goods_price','$wallpaper_price', '" .$attr_value . "', " .
                        "is_real, extension_code, 0, 0 , '".implode(',',$goods_attr)."' " .
                    "FROM " . $ecs->table('goods') .
                    " WHERE goods_id = '$goods_id' LIMIT 1";
        }
        else
        {
             $sql = "INSERT INTO " . $ecs->table('order_goods') .
                    " (order_id, goods_id, goods_name, goods_sn, " .
                    "goods_number, market_price, goods_price,wallpaper_price, goods_attr, " .
                    "is_real, extension_code, parent_id, is_gift)" .
                "SELECT '$order_id', goods_id, goods_name, goods_sn, " .
                    "'$goods_number', market_price, '$goods_price','$wallpaper_price', '" . $attr_value. "', " .
                    "is_real, extension_code, 0, 0 " .
                "FROM " . $ecs->table('goods') .
                " WHERE goods_id = '$goods_id' LIMIT 1";
        }
        $db->query($sql);

        /* 如果使用库存，且下订单时减库存，则修改库存 */
        if ($_CFG['use_storage'] == '1' && $_CFG['stock_dec_time'] == SDT_PLACE)
        {

            //（货品）
            if (!empty($product_info['product_id']))
            {
                $sql = "UPDATE " . $ecs->table('products') . "
                                    SET product_number = product_number - " . $goods_number . "
                                    WHERE product_id = " . $product_info['product_id'];

                $db->query($sql);
            }


            $sql = "UPDATE " . $ecs->table('goods') .
                    " SET `goods_number` = goods_number - '" . $goods_number . "' " .
                    " WHERE `goods_id` = '" . $goods_id . "' LIMIT 1";
            $db->query($sql);
        }

        /* 更新商品总金额和订单总金额 */
        update_order($order_id, array('goods_amount' => order_amount($order_id)));
        update_order_amount($order_id);

        /* 更新 pay_log */
        update_pay_log($order_id);

        /* todo 记录日志 */
        $sn = $old_order['order_sn'];
        $new_order = order_info($order_id);
        if ($old_order['total_fee'] != $new_order['total_fee'])
        {
            $sn .= ',' . sprintf($_LANG['order_amount_change'], $old_order['total_fee'], $new_order['total_fee']);
        }
        admin_log($sn, 'edit', 'order');

        /* 跳回订单商品 */
        ecs_header("Location: order_add.php?act=" . $step_act . "&order_id=" . $order_id . "&step=goods\n");
        exit;
    }


    /* 确认商品信息 提交 */
    elseif ('edit_goods' == $step)
    {

        if (isset($_POST['rec_id']))
        {
            foreach ($_POST['rec_id'] AS $key => $rec_id)
            {
              $sql = "SELECT goods_number ".
                      'FROM ' . $GLOBALS['ecs']->table('goods') .
                      "WHERE goods_id =".$_POST['goods_id'][$key];
                /* 取得参数 */
                $wallpaper_price = floatval($_POST['wallpaper_price'][$key]);
                $goods_price = floatval($_POST['goods_price'][$key]);

              $goods_number = intval($_POST['goods_number'][$key]);
              $goods_attr = $_POST['goods_attr'][$key];
              $product_id = intval($_POST['product_id'][$key]);
              if($product_id)
                {

                   $sql = "SELECT product_number ".
                          'FROM ' . $GLOBALS['ecs']->table('products') .
                          " WHERE product_id =".$_POST['product_id'][$key];
                }
              $goods_number_all = $db->getOne($sql);
              if($goods_number_all>=$goods_number)
              {
                /* 修改 */
                $sql = "UPDATE " . $ecs->table('order_goods') .
                        " SET goods_price = '$goods_price', " .
                    "wallpaper_price = '$wallpaper_price', " .
                        "goods_number = '$goods_number', " .
                        "goods_attr = '$goods_attr' " .
                        "WHERE rec_id = '$rec_id' LIMIT 1";
                $db->query($sql);
              }
              else
              {
               sys_msg($_LANG['goods_num_err']);


              }
            }

            /* 更新商品总金额和订单总金额 */
            $goods_amount = order_amount($order_id);
            update_order($order_id, array('goods_amount' => $goods_amount));
            update_order_amount($order_id);


        /* 取得订单信息 */
        $order_info = order_info($order_id);
        $region_id_list = array($order_info['country'], $order_info['province'], $order_info['city'], $order_info['district']);

        /* 保存订单 */
        $shipping_id = $_POST['shipping'];

        $shipping = shipping_area_info($shipping_id, $region_id_list);
        $weight_amount = order_weight_price($order_id);
        $shipping_fee = shipping_fee($shipping['shipping_code'], $shipping['configure'], $weight_amount['weight'], $weight_amount['amount'], $weight_amount['number']);
        $order = array(
            'shipping_id' => $shipping_id,
            'shipping_name' => addslashes($shipping['shipping_name']),
            'shipping_fee' => $shipping_fee
        );


            if(!empty($_POST['consignee'])){
                $order['consignee'] = $_POST['consignee'];
            }

            if(!empty($_POST['address'])){
                $order['address'] = $_POST['address'];
            }

            if(!empty($_POST['mobile'])){
                $order['mobile'] = $_POST['mobile'];
            }    


        /* 计算保价费 */
        $order['insure_fee'] = 0;
        update_order($order_id, $order);
        update_order_amount($order_id);

        /* 更新 pay_log */
        update_pay_log($order_id);


        /* 取得支付信息 */
        $pay_id = $_POST['payment'];
        $payment = payment_info($pay_id);

        /* 计算支付费用 */
        $order_amount = order_amount($order_id);
        if ($payment['is_cod'] == 1)
        {
            $order = order_info($order_id);
            $region_id_list = array(
                $order['country'], $order['province'], $order['city'], $order['district']
            );
            $shipping = shipping_area_info($order['shipping_id'], $region_id_list);
            $pay_fee = pay_fee($pay_id, $order_amount, $shipping['pay_fee']);
        }
        else
        {
            $pay_fee = pay_fee($pay_id, $order_amount);
        }

        /* 保存订单 */
        $order = array(
            'pay_id' => $pay_id,
            'pay_name' => addslashes($payment['pay_name']),
            'pay_fee' => $pay_fee,
            'order_status' => OS_CONFIRMED
        );
        

        update_order($order_id, $order);
        update_order_amount($order_id);

        /* 更新 pay_log */
        update_pay_log($order_id);


        $link[] = array('href' => 'order_add.php?act=add', 'text' => "添加订单");
        sys_msg('添加订单成功！', 0, $link);


        }

        /* 跳回订单商品 */
        ecs_header("Location: order_add.php?act=" . $step_act . "&order_id=" . $order_id . "&step=goods\n");
        exit;
    }


    /* 商品  确认订单 */
    elseif ('goods' == $step)
    {

        /* 取得订单信息 */
        $order_info = order_info($order_id);
        $region_id_list = array($order_info['country'], $order_info['province'], $order_info['city'], $order_info['district']);

        /* 保存订单 */
        $shipping_id = $_POST['shipping'];

        $shipping = shipping_area_info($shipping_id, $region_id_list);
        $weight_amount = order_weight_price($order_id);
        $shipping_fee = shipping_fee($shipping['shipping_code'], $shipping['configure'], $weight_amount['weight'], $weight_amount['amount'], $weight_amount['number']);
        $order = array(
            'shipping_id' => $shipping_id,
            'shipping_name' => addslashes($shipping['shipping_name']),
            'shipping_fee' => $shipping_fee
        );

        /* 计算保价费 */
        $order['insure_fee'] = 0;
        update_order($order_id, $order);
        update_order_amount($order_id);

        /* 更新 pay_log */
        update_pay_log($order_id);


        /* 取得支付信息 */
        $pay_id = $_POST['payment'];
        $payment = payment_info($pay_id);

        /* 计算支付费用 */
        $order_amount = order_amount($order_id);
        if ($payment['is_cod'] == 1)
        {
            $order = order_info($order_id);
            $region_id_list = array(
                $order['country'], $order['province'], $order['city'], $order['district']
            );
            $shipping = shipping_area_info($order['shipping_id'], $region_id_list);
            $pay_fee = pay_fee($pay_id, $order_amount, $shipping['pay_fee']);
        }
        else
        {
            $pay_fee = pay_fee($pay_id, $order_amount);
        }

        /* 保存订单 */
        $order = array(
            'pay_id' => $pay_id,
            'pay_name' => addslashes($payment['pay_name']),
            'pay_fee' => $pay_fee
        );
        update_order($order_id, $order);
        update_order_amount($order_id);

        /* 更新 pay_log */
        update_pay_log($order_id);

        /* 完成 */
        ecs_header("Location: order_add.php?act=info&order_id=" . $order_id . "\n");
        exit;

    }
    /* 保存收货人信息 */
    elseif ('consignee' == $step)
    {

    }
    /* 保存配送信息 */
    elseif ('shipping' == $step)
    {


    }
    elseif ('other' == $step)
    {


    }
    elseif ('money' == $step)
    {

        /* 取得订单信息 */
        $old_order = order_info($order_id);
        if ($old_order['user_id'] > 0)
        {
            /* 取得用户信息 */
            $user = user_info($old_order['user_id']);
        }

        /* 保存信息 */
        $order['goods_amount']  = $old_order['goods_amount'];
        $order['discount']      = isset($_POST['discount']) && floatval($_POST['discount']) >= 0 ? round(floatval($_POST['discount']), 2) : 0;
        $order['tax']           = round(floatval($_POST['tax']), 2);
        $order['shipping_fee']  = isset($_POST['shipping_fee']) && floatval($_POST['shipping_fee']) >= 0 ? round(floatval($_POST['shipping_fee']), 2) : 0;
        $order['insure_fee']    = isset($_POST['insure_fee']) && floatval($_POST['insure_fee']) >= 0 ? round(floatval($_POST['insure_fee']), 2) : 0;
        $order['pay_fee']       = floatval($_POST['pay_fee']) >= 0 ? round(floatval($_POST['pay_fee']), 2) : 0;
        $order['pack_fee']      = isset($_POST['pack_fee']) && floatval($_POST['pack_fee']) >= 0 ? round(floatval($_POST['pack_fee']), 2) : 0;
        $order['card_fee']      = isset($_POST['card_fee']) && floatval($_POST['card_fee']) >= 0 ? round(floatval($_POST['card_fee']), 2) : 0;

        $order['money_paid']    = $old_order['money_paid'];
        $order['surplus']       = 0;
        //$order['integral']      = 0;
        $order['integral']=intval($_POST['integral']) >= 0 ? intval($_POST['integral']) : 0;
        $order['integral_money']= 0;
        $order['bonus_id']      = 0;
        $order['bonus']         = 0;

        /* 计算待付款金额 */
        $order['order_amount']  = $order['goods_amount'] - $order['discount']
                                + $order['tax']
                                + $order['shipping_fee']
                                + $order['insure_fee']
                                + $order['pay_fee']
                                + $order['pack_fee']
                                + $order['card_fee']
                                - $order['money_paid'];
        if ($order['order_amount'] > 0)
        {
            if ($old_order['user_id'] > 0)
            {
                /* 如果选择了红包，先使用红包支付 */
                if ($_POST['bonus_id'] > 0)
                {
                    /* todo 检查红包是否可用 */
                    $order['bonus_id']      = $_POST['bonus_id'];
                    $bonus                  = bonus_info($_POST['bonus_id']);
                    $order['bonus']         = $bonus['type_money'];

                    $order['order_amount']  -= $order['bonus'];
                }

                /* 使用红包之后待付款金额仍大于0 */
                if ($order['order_amount'] > 0)
                {
                    if($old_order['extension_code']!='exchange_goods')
                    {
                    /* 如果设置了积分，再使用积分支付 */
                       if (isset($_POST['integral']) && intval($_POST['integral']) > 0)
                         {
                           /* 检查积分是否足够 */
                           $order['integral']          = intval($_POST['integral']);
                           $order['integral_money']    = value_of_integral(intval($_POST['integral']));
                           if ($old_order['integral'] + $user['pay_points'] < $order['integral'])
                                {
                                   sys_msg($_LANG['pay_points_not_enough']);
                                }

                           $order['order_amount'] -= $order['integral_money'];
                         }
                    }
                    else
                    {
                      if (intval($_POST['integral']) > $user['pay_points']+$old_order['integral'])
                        {
                          sys_msg($_LANG['pay_points_not_enough']);
                        }

                    }
                    if ($order['order_amount'] > 0)
                    {
                        /* 如果设置了余额，再使用余额支付 */
                        if (isset($_POST['surplus']) && floatval($_POST['surplus']) >= 0)
                        {
                            /* 检查余额是否足够 */
                            $order['surplus'] = round(floatval($_POST['surplus']), 2);
                            if ($old_order['surplus'] + $user['user_money'] + $user['credit_line'] < $order['surplus'])
                            {
                                sys_msg($_LANG['user_money_not_enough']);
                            }

                            /* 如果红包和积分和余额足以支付，把待付款金额改为0，退回部分积分余额 */
                            $order['order_amount'] -= $order['surplus'];
                            if ($order['order_amount'] < 0)
                            {
                                $order['surplus']       += $order['order_amount'];
                                $order['order_amount']  = 0;
                            }
                        }
                    }
                    else
                    {
                        /* 如果红包和积分足以支付，把待付款金额改为0，退回部分积分 */
                        $order['integral_money']    += $order['order_amount'];
                        $order['integral']          = integral_of_value($order['integral_money']);
                        $order['order_amount']      = 0;
                    }
                }
                else
                {
                    /* 如果红包足以支付，把待付款金额设为0 */
                    $order['order_amount'] = 0;
                }
            }
        }

        update_order($order_id, $order);

        /* 更新 pay_log */
        update_pay_log($order_id);

        /* todo 记录日志 */
        $sn = $old_order['order_sn'];
        $new_order = order_info($order_id);
        if ($old_order['total_fee'] != $new_order['total_fee'])
        {
            $sn .= ',' . sprintf($_LANG['order_amount_change'], $old_order['total_fee'], $new_order['total_fee']);
        }
        admin_log($sn, 'edit', 'order');

        /* 如果余额、积分、红包有变化，做相应更新 */
        if ($old_order['user_id'] > 0)
        {
            $user_money_change = $old_order['surplus'] - $order['surplus'];
            if ($user_money_change != 0)
            {
                log_account_change($user['user_id'], $user_money_change, 0, 0, 0, sprintf($_LANG['change_use_surplus'], $old_order['order_sn']));
            }

            $pay_points_change = $old_order['integral'] - $order['integral'];
            if ($pay_points_change != 0)
            {
                log_account_change($user['user_id'], 0, 0, 0, $pay_points_change, sprintf($_LANG['change_use_integral'], $old_order['order_sn']));
            }

            if ($old_order['bonus_id'] != $order['bonus_id'])
            {
                if ($old_order['bonus_id'] > 0)
                {
                    $sql = "UPDATE " . $ecs->table('user_bonus') .
                            " SET used_time = 0, order_id = 0 " .
                            "WHERE bonus_id = '$old_order[bonus_id]' LIMIT 1";
                    $db->query($sql);
                }

                if ($order['bonus_id'] > 0)
                {
                    $sql = "UPDATE " . $ecs->table('user_bonus') .
                            " SET used_time = '" . gmtime() . "', order_id = '$order_id' " .
                            "WHERE bonus_id = '$order[bonus_id]' LIMIT 1";
                    $db->query($sql);
                }
            }
        }

        if (isset($_POST['finish']))
        {
            /* 完成 */
            if ($step_act == 'add')
            {
                /* 订单改为已确认，（已付款） */
                $arr['order_status'] = OS_CONFIRMED;
                $arr['confirm_time'] = gmtime();
                if ($order['order_amount'] <= 0)
                {
                    $arr['pay_status']  = PS_PAYED;
                    $arr['pay_time']    = gmtime();
                }
                update_order($order_id, $arr);
            }

            /* 初始化提示信息和链接 */
            $msgs   = array();
            $links  = array();

            /* 如果已付款，检查金额是否变动，并执行相应操作 */
            $order = order_info($order_id);
            handle_order_money_change($order, $msgs, $links);

            /* 显示提示信息 */
            if (!empty($msgs))
            {
                sys_msg(join(chr(13), $msgs), 0, $links);
            }
            else
            {
                ecs_header("Location: order_add.php?act=info&order_id=" . $order_id . "\n");
                exit;
            }
        }
    }

}

/*------------------------------------------------------ */
//-- 修改订单（载入页面）
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'add' || $_REQUEST['act'] == 'edit')
{

    /* 检查权限 */
    admin_priv('order_edit');

    /* 取得参数 order_id */
    $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
    $smarty->assign('order_id', $order_id);

    /* 取得参数 step */
    $step_list = array('user', 'goods', 'consignee', 'shipping', 'payment', 'other', 'money');
    $step = isset($_GET['step']) && in_array($_GET['step'], $step_list) ? $_GET['step'] : 'user';
    $smarty->assign('step', $step);

    /* 取得参数 act */
    $act = $_GET['act'];
    $smarty->assign('ur_here',$_LANG['add_order']);
    $smarty->assign('step_act', $act);

    /* 取得订单信息 */
    if ($order_id > 0)
    {

        $order = order_info($order_id);
        /* 发货单格式化 */
        $order['invoice_no'] = str_replace('<br>', ',', $order['invoice_no']);

        /* 如果已发货，就不能修改订单了（配送方式和发货单号除外） */
        if ($order['shipping_status'] == SS_SHIPPED || $order['shipping_status'] == SS_RECEIVED)
        {
            if ($step != 'shipping')
            {
                sys_msg($_LANG['cannot_edit_order_shipped']);
            }
            else
            {
                $step = 'invoice';
                $smarty->assign('step', $step);
            }
        }

        $smarty->assign('order', $order);
    }
    else
    {
        if ($act != 'add' || $step != 'user')
        {
            die('invalid params');
        }
    }

    /* 选择会员 */
    if ('user' == $step)
    {
    }

    /* 增删改商品 */
    elseif ('goods' == $step)
    {

        /* 取得订单商品 */
        $goods_list = order_goods($order_id);
        if (!empty($goods_list))
        {
            foreach ($goods_list AS $key => $goods)
            {
//                if(!empty($goods['wallpaper_price'])){
//                    $goods_list[$key]['rows'] = 1;
//                }
                /* 计算属性数 */
                $attr = $goods['goods_attr'];
                if ($attr == '')
                {
                    $goods_list[$key]['rows'] = 1;
                }
                else
                {
                    $goods_list[$key]['rows'] = count(explode(chr(13), $attr));
                }
            }
        }

        $smarty->assign('goods_list', $goods_list);

        /* 取得商品总金额 */
        $smarty->assign('goods_amount', order_amount($order_id));
        
        /* 取得收货地址列表 */
        if ($order['user_id'] > 0)
        {
        	$address=address_list($order['user_id']);
            $smarty->assign('address_list', $address);

            //$address_id = isset($_REQUEST['address_id']) ? intval($_REQUEST['address_id']) : 0;
            $address_id=$address[0]['address_id'];

            if ($address_id > 0)
            {
                $address = address_info($address_id);
                if ($address)
                {
                    $order['consignee']     = $address['consignee'];
                    $order['country']       = $address['country'];
                    $order['province']      = $address['province'];
                    $order['city']          = $address['city'];
                    $order['district']      = $address['district'];
                    $order['email']         = $address['email'];
                    $order['address']       = $address['address'];
                    $order['zipcode']       = $address['zipcode'];
                    $order['tel']           = $address['tel'];
                    $order['mobile']        = $address['mobile'];
                    $order['sign_building'] = $address['sign_building'];
                    $order['best_time']     = $address['best_time'];
                    $smarty->assign('order', $order);
                }
            }
        }
        //收货人地址
		
        $user_id=getUserId($order_id);
		$sql = "SELECT u.user_id, a.address_name, a.address, a.tel FROM " . $GLOBALS['ecs']->table('users') . "as u 
		left join " . $GLOBALS['ecs']->table('user_address') . " as a on u.address_id=a.address_id".
                " WHERE u.user_id ='$user_id'";
		$address = $GLOBALS['db']->getRow($sql);
		$smarty->assign('address', $address);
        update_order($order_id, $order);


        /* 取得可用的配送方式列表 */
        $region_id_list = array(
            $order['country'], $order['province'], $order['city'], $order['district']
        );
        
      
        $shipping_list = available_shipping_list($region_id_list);

        /* 取得配送费用 */
        $total = order_weight_price($order_id);
        foreach ($shipping_list AS $key => $shipping)
        {
            $shipping_fee = shipping_fee($shipping['shipping_code'],
                unserialize($shipping['configure']), $total['weight'], $total['amount'], $total['number']);
            $shipping_list[$key]['shipping_fee'] = $shipping_fee;
            $shipping_list[$key]['format_shipping_fee'] = price_format($shipping_fee);
            //$shipping_list[$key]['free_money'] = price_format($shipping['configure']['free_money']);
        }
        $smarty->assign('shipping_list', $shipping_list);
        
        /* 取得可用的支付方式列表 */
        if (exist_real_goods($order_id))
        {
            /* 存在实体商品 */
            $region_id_list = array(
                $order['country'], $order['province'], $order['city'], $order['district']
            );
            $shipping_area = shipping_area_info($order['shipping_id'], $region_id_list);
            $pay_fee = ($shipping_area['support_cod'] == 1) ? $shipping_area['pay_fee'] : 0;

            $payment_list = available_payment_list($shipping_area['support_cod'], $pay_fee);
        }
        else
        {
            /* 不存在实体商品 */
            $payment_list = available_payment_list(false);
        }

        $smarty->assign('payment_list', $payment_list);
        		
    	        
    }


    /* 显示模板 */
    assign_query_info();
    $smarty->display('order_add.htm');
}

/*------------------------------------------------------ */
//-- 处理
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'process')
{
    /* 取得参数 func */
    $func = isset($_GET['func']) ? $_GET['func'] : '';

    /* 删除订单商品 */
    if ('drop_order_goods' == $func)
    {

        /* 检查权限 */
        admin_priv('order_edit');

        /* 取得参数 */
        $rec_id = intval($_GET['rec_id']);
        $step_act = $_GET['step_act'];
        $order_id = intval($_GET['order_id']);

        /* 如果使用库存，且下订单时减库存，则修改库存 */
        if ($_CFG['use_storage'] == '1' && $_CFG['stock_dec_time'] == SDT_PLACE)
        {
             $goods = $db->getRow("SELECT goods_id, goods_number FROM " . $ecs->table('order_goods') . " WHERE rec_id = " . $rec_id );
             $sql = "UPDATE " . $ecs->table('goods') .
                    " SET `goods_number` = goods_number + '" . $goods['goods_number'] . "' " .
                    " WHERE `goods_id` = '" . $goods['goods_id'] . "' LIMIT 1";
             $db->query($sql);
        }

        /* 删除 */
        $sql = "DELETE FROM " . $ecs->table('order_goods') .
                " WHERE rec_id = '$rec_id' LIMIT 1";
        $db->query($sql);

        /* 更新商品总金额和订单总金额 */
        update_order($order_id, array('goods_amount' => order_amount($order_id)));
        update_order_amount($order_id);

        /* 跳回订单商品 */
        ecs_header("Location: order_add.php?act=" . $step_act . "&order_id=" . $order_id . "&step=goods\n");
        exit;
    }

    /* 取消刚添加或编辑的订单 */
    elseif ('cancel_order' == $func)
    {

        $step_act = $_GET['step_act'];
        $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
        if ($step_act == 'add')
        {
            /* 如果是添加，删除订单，返回订单列表 */
            if ($order_id > 0)
            {
                $sql = "DELETE FROM " . $ecs->table('order_info') .
                        " WHERE order_id = '$order_id' LIMIT 1";
                $db->query($sql);
            }
            ecs_header("Location: order_add.php?act=list\n");
            exit;
        }
        else
        {
            /* 如果是编辑，返回订单信息 */
            ecs_header("Location: order_add.php?act=info&order_id=" . $order_id . "\n");
            exit;
        }
    }

    /* 编辑订单时由于订单已付款且金额减少而退款 */
    elseif ('refund' == $func)
    {
     
        /* 处理退款 */
        $order_id       = $_REQUEST['order_id'];
        $refund_type    = $_REQUEST['refund'];
        $refund_note    = $_REQUEST['refund_note'];
        $refund_amount  = $_REQUEST['refund_amount'];
        $order          = order_info($order_id);
        order_refund($order, $refund_type, $refund_note, $refund_amount);

        /* 修改应付款金额为0，已付款金额减少 $refund_amount */
        update_order($order_id, array('order_amount' => 0, 'money_paid' => $order['money_paid'] - $refund_amount));

        /* 返回订单详情 */
        ecs_header("Location: order_add.php?act=info&order_id=" . $order_id . "\n");
        exit;
    }

    /* 载入退款页面 */
    elseif ('load_refund' == $func)
    {
        $refund_amount = floatval($_REQUEST['refund_amount']);
        $smarty->assign('refund_amount', $refund_amount);
        $smarty->assign('formated_refund_amount', price_format($refund_amount));

        $anonymous = $_REQUEST['anonymous'];
        $smarty->assign('anonymous', $anonymous); // 是否匿名

        $order_id = intval($_REQUEST['order_id']);
        $smarty->assign('order_id', $order_id); // 订单id

        /* 显示模板 */
        $smarty->assign('ur_here', $_LANG['refund']);
        assign_query_info();
        $smarty->display('order_refund.htm');
    }

    else
    {
        die('invalid params');
    }
}


elseif ($_REQUEST['act'] == 'json')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON();

    $func = $_REQUEST['func'];
    if ($func == 'get_goods_info')
    {
        /* 取得商品信息 */
        $goods_id = $_REQUEST['goods_id'];
        $sql = "SELECT goods_id, c.cat_name, goods_sn, goods_name, b.brand_name, " .
                "goods_number, market_price, shop_price, promote_price, " .
                "promote_start_date, promote_end_date, goods_brief, goods_type, is_promote " .
                "FROM " . $ecs->table('goods') . " AS g " .
                "LEFT JOIN " . $ecs->table('brand') . " AS b ON g.brand_id = b.brand_id " .
                "LEFT JOIN " . $ecs->table('category') . " AS c ON g.cat_id = c.cat_id " .
                " WHERE goods_id = '$goods_id'";
        $goods = $db->getRow($sql);
        $today = gmtime();
        $goods['goods_price'] = ($goods['is_promote'] == 1 &&
            $goods['promote_start_date'] <= $today && $goods['promote_end_date'] >= $today) ?
            $goods['promote_price'] : $goods['shop_price'];

        /* 取得会员价格 */
        $sql = "SELECT p.user_price, r.rank_name " .
                "FROM " . $ecs->table('member_price') . " AS p, " .
                    $ecs->table('user_rank') . " AS r " .
                "WHERE p.user_rank = r.rank_id " .
                "AND p.goods_id = '$goods_id' ";
        $goods['user_price'] = $db->getAll($sql);

        /* 取得商品属性 */
        $sql = "SELECT a.attr_id, a.attr_name, g.goods_attr_id, g.attr_value, g.attr_price, a.attr_input_type, a.attr_type " .
                "FROM " . $ecs->table('goods_attr') . " AS g, " .
                    $ecs->table('attribute') . " AS a " .
                "WHERE g.attr_id = a.attr_id " .
                "AND g.goods_id = '$goods_id' ";
        $goods['attr_list'] = array();
        $res = $db->query($sql);
        while ($row = $db->fetchRow($res))
        {
            $goods['attr_list'][$row['attr_id']][] = $row;
        }
        $goods['attr_list'] = array_values($goods['attr_list']);

       // var_dump($goods);exit();
        echo $json->encode($goods);
    }
}



/*------------------------------------------------------ */
//-- 根据关键字和id搜索用户
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'search_users')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON();

    $id_name = empty($_GET['id_name']) ? '' : json_str_iconv(trim($_GET['id_name']));

    $result = array('error'=>0, 'message'=>'', 'content'=>'');
    if ($id_name != '')
    {
        $sql = "SELECT user_id, user_name FROM " . $GLOBALS['ecs']->table('users') .
                " WHERE user_id LIKE '%" . mysql_like_quote($id_name) . "%'" .
                " OR user_name LIKE '%" . mysql_like_quote($id_name) . "%'" .
                " LIMIT 20";
        $res = $GLOBALS['db']->query($sql);

         $result['userlist'] = array();
        while ($row = $GLOBALS['db']->fetchRow($res))
        {
        	 $sql = "SELECT * FROM " . $ecs->table('user_address') . " WHERE user_id = '$row[user_id]'";
        	 $address=$db->getAll($sql);
             $result['userlist'][] = array('user_id' => $row['user_id'], 'user_name' => $row['user_name'],'address'=>$address);
        }
    }
    else
    {
        $result['error'] = 1;
        $result['message'] = 'NO KEYWORDS!';
    }
    // 地址信息
		     $sql = "SELECT * FROM " . $ecs->table('user_address') . " WHERE user_id = '$row[user_id]'";
		     $smarty->assign('address_list', $db->getAll($sql));
    die($json->encode($result));
}

/*------------------------------------------------------ */
//-- 根据关键字搜索商品
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'search_goods')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON();

    $keyword = empty($_GET['keyword']) ? '' : json_str_iconv(trim($_GET['keyword']));

    $result = array('error'=>0, 'message'=>'', 'content'=>'');

    if ($keyword != '')
    {
        $sql = "SELECT goods_id, goods_name, goods_sn FROM " . $GLOBALS['ecs']->table('goods') .
                " WHERE is_delete = 0" .
                " AND is_on_sale = 1" .
                " AND is_alone_sale = 1" .
                " AND (goods_id LIKE '%" . mysql_like_quote($keyword) . "%'" .
                " OR goods_name LIKE '%" . mysql_like_quote($keyword) . "%'" .
                " OR goods_sn LIKE '%" . mysql_like_quote($keyword) . "%')" .
                " LIMIT 20";
        $res = $GLOBALS['db']->query($sql);

        $result['goodslist'] = array();
        while ($row = $GLOBALS['db']->fetchRow($res))
        {
            $result['goodslist'][] = array('goods_id' => $row['goods_id'], 'name' => $row['goods_id'] . '  ' . $row['goods_name'] . '  ' . $row['goods_sn']);
        }
    }
    else
    {
        $result['error'] = 1;
        $result['message'] = 'NO KEYWORDS';
    }
    die($json->encode($result));
}

/**
 * 退回余额、积分、红包（取消、无效、退货时），把订单使用余额、积分、红包设为0
 * @param   array   $order  订单信息
 */
function return_user_surplus_integral_bonus($order)
{
    /* 处理余额、积分、红包 */
    if ($order['user_id'] > 0 && $order['surplus'] > 0)
    {
        $surplus = $order['money_paid'] < 0 ? $order['surplus'] + $order['money_paid'] : $order['surplus'];
        log_account_change($order['user_id'], $surplus, 0, 0, 0, sprintf($GLOBALS['_LANG']['return_order_surplus'], $order['order_sn']));
        $GLOBALS['db']->query("UPDATE ". $GLOBALS['ecs']->table('order_info') . " SET `order_amount` = '0' WHERE `order_id` =". $order['order_id']);
    }

    if ($order['user_id'] > 0 && $order['integral'] > 0)
    {
        log_account_change($order['user_id'], 0, 0, 0, $order['integral'], sprintf($GLOBALS['_LANG']['return_order_integral'], $order['order_sn']));
    }

    if ($order['bonus_id'] > 0)
    {
        unuse_bonus($order['bonus_id']);
    }

    /* 修改订单 */
    $arr = array(
        'bonus_id'  => 0,
        'bonus'     => 0,
        'integral'  => 0,
        'integral_money'    => 0,
        'surplus'   => 0
    );
    update_order($order['order_id'], $arr);
}


/**
 * 更新订单对应的 pay_log
 * 如果未支付，修改支付金额；否则，生成新的支付log
 * @param   int     $order_id   订单id
 */
function update_pay_log($order_id)
{
    $order_id = intval($order_id);
    if ($order_id > 0)
    {
        $sql = "SELECT order_amount FROM " . $GLOBALS['ecs']->table('order_info') .
                " WHERE order_id = '$order_id'";
        $order_amount = $GLOBALS['db']->getOne($sql);
        if (!is_null($order_amount))
        {
            $sql = "SELECT log_id FROM " . $GLOBALS['ecs']->table('pay_log') .
                    " WHERE order_id = '$order_id'" .
                    " AND order_type = '" . PAY_ORDER . "'" .
                    " AND is_paid = 0";
            $log_id = intval($GLOBALS['db']->getOne($sql));
            if ($log_id > 0)
            {
                /* 未付款，更新支付金额 */
                $sql = "UPDATE " . $GLOBALS['ecs']->table('pay_log') .
                        " SET order_amount = '$order_amount' " .
                        "WHERE log_id = '$log_id' LIMIT 1";
            }
            else
            {
                /* 已付款，生成新的pay_log */
                $sql = "INSERT INTO " . $GLOBALS['ecs']->table('pay_log') .
                        " (order_id, order_amount, order_type, is_paid)" .
                        "VALUES('$order_id', '$order_amount', '" . PAY_ORDER . "', 0)";
            }
            $GLOBALS['db']->query($sql);
        }
    }
}


/**
 * 更新订单总金额
 * @param   int     $order_id   订单id
 * @return  bool
 */
function update_order_amount($order_id)
{
    include_once(ROOT_PATH . 'includes/lib_order.php');
    //更新订单总金额
    $sql = "UPDATE " . $GLOBALS['ecs']->table('order_info') .
            " SET order_amount = " . order_due_field() .
            " WHERE order_id = '$order_id' LIMIT 1";

    return $GLOBALS['db']->query($sql);
}

function getUserId($order_id){
	$sql = "select user_id from ".$GLOBALS['ecs']->table('order_info'). " where order_id=" .$order_id;

	$res = $GLOBALS['db']->getone($sql);
	return $res;

}

?>