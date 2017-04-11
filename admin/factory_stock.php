<?php
/**
 *  author:royallu
 *  function:进货单列表
 *  time:20161110
 * */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_order.php');
require_once(ROOT_PATH . 'includes/lib_goods.php');

/*------------------------------------------------------ */
//-- 进货单查询
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'order_query')
{
    /* 检查权限 */
    //admin_priv('order_view');

    /* 载入配送方式 */
    $smarty->assign('shipping_list', shipping_list());

    /* 载入支付方式 */
    $smarty->assign('pay_list', payment_list());

    /* 载入国家 */
    $smarty->assign('country_list', get_regions());

    /* 载入订单状态、付款状态、发货状态 */
    $smarty->assign('os_list', get_status_list('order'));
    $smarty->assign('ps_list', get_status_list('payment'));
    $smarty->assign('ss_list', get_status_list('shipping'));

    /* 模板赋值 */
    $smarty->assign('ur_here', $_LANG['03_order_query']);
    $smarty->assign('action_link', array('href' => 'order.php?act=list', 'text' => $_LANG['02_order_list']));

    /* 显示模板 */
    assign_query_info();
    $smarty->display('order_query.htm');
}

/*------------------------------------------------------ */
//-- 进货单列表
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'list')
{
    /* 检查权限 */
  //  admin_priv('order_view');

    /* 模板赋值 */
    $smarty->assign('ur_here', $_LANG['factory_stock']);
   // $smarty->assign('action_link', array('href' => 'factory_stock.php?act=order_query', 'text' => $_LANG['factory_stock']));

    $smarty->assign('status_list', $_LANG['cs']);   // 订单状态

    $smarty->assign('os_unconfirmed',   OS_UNCONFIRMED);
    $smarty->assign('cs_await_pay',     CS_AWAIT_PAY);
    $smarty->assign('cs_await_ship',    CS_AWAIT_SHIP);
    $smarty->assign('full_page',        1);

    $stock_list = stock_list();
//    foreach ($order_list['orders'] as $key => $value) {
//
//        $order_sn = $value['order_sn'];
//        $result = $db->getRow("SELECT `parent_facilitator_id`,`facilitator_id` FROM " .$ecs->table('factory'). " WHERE order_sn='".$order_sn."'");
//        $parent_id = $result['parent_facilitator_id'];
//        $id =  $result['facilitator_id'];
//        $parent_name = $db->getRow("SELECT `user_name` FROM " .$ecs->table('users'). " WHERE user_id='".$parent_id."'");
//        $name = $db->getRow("SELECT `user_name` FROM " .$ecs->table('users'). " WHERE user_id='".$id."'");
//        $order_list['orders'][$key]['parent_name'] = $parent_name['user_name'];
//        $order_list['orders'][$key]['name'] = $name['user_name'];
//    }

    $smarty->assign('stock_list',   $stock_list['stocks']);
    $smarty->assign('filter',       $stock_list['filter']);
    $smarty->assign('record_count', $stock_list['record_count']);
    $smarty->assign('page_count',   $stock_list['page_count']);
    $smarty->assign('sort_order_time', '<img src="images/sort_desc.gif">');

    $start_date = date("Y-m-d H:i:s",mktime(0,0,0,date("m"),date("d"),date("Y")));
    $end_date = date("Y-m-d H:i:s",time());

    $smarty->assign('start_date',$start_date);
    $smarty->assign('end_date',$end_date);

    /* 显示模板 */
    assign_query_info();
    $smarty->display('factory_stock.htm');
}

elseif ($_REQUEST['act'] == 'order_query')
{
    /* 检查权限 */
    admin_priv('order_view');

    /* 模板赋值 */
    $smarty->assign('ur_here', $_LANG['02_order_list']);
    $smarty->assign('action_link', array('href' => 'order.php?act=order_query', 'text' => $_LANG['03_order_query']));

    $smarty->assign('status_list', $_LANG['cs']);   // 订单状态

    $smarty->assign('os_unconfirmed',   OS_UNCONFIRMED);
    $smarty->assign('cs_await_pay',     CS_AWAIT_PAY);
    $smarty->assign('cs_await_ship',    CS_AWAIT_SHIP);
    $smarty->assign('full_page',        1);

    $order_list = order_list();
    foreach ($order_list['orders'] as $key => $value) {

        $order_sn = $value['order_sn'];
        $result = $db->getRow("SELECT `parent_facilitator_id`,`facilitator_id` FROM " .$ecs->table('factory'). " WHERE order_sn='".$order_sn."'");
        $parent_id = $result['parent_facilitator_id'];
        $id =  $result['facilitator_id'];
        $parent_name = $db->getRow("SELECT `user_name` FROM " .$ecs->table('users'). " WHERE user_id='".$parent_id."'");
        $name = $db->getRow("SELECT `user_name` FROM " .$ecs->table('users'). " WHERE user_id='".$id."'");
        $order_list['orders'][$key]['parent_name'] = $parent_name['user_name'];
        $order_list['orders'][$key]['name'] = $name['user_name'];
    }

    $smarty->assign('order_list',   $order_list['orders']);
    $smarty->assign('filter',       $order_list['filter']);
    $smarty->assign('record_count', $order_list['record_count']);
    $smarty->assign('page_count',   $order_list['page_count']);
    $smarty->assign('sort_order_time', '<img src="images/sort_desc.gif">');

    $start_date = date("Y-m-d H:i:s",mktime(0,0,0,date("m"),date("d"),date("Y")));
    $end_date = date("Y-m-d H:i:s",time());

    $smarty->assign('start_date',$start_date);
    $smarty->assign('end_date',$end_date);

    /* 显示模板 */
    assign_query_info();
    $smarty->display('order_list.htm');
}


/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    /* 检查权限 */
    admin_priv('order_view');

    $stock_list = stock_list();

//    foreach ($order_list['orders'] as $key => $value) {
//
//        $order_sn = $value['order_sn'];
//        $result = $db->getRow("SELECT `parent_facilitator_id`,`facilitator_id` FROM " .$ecs->table('factory'). " WHERE order_sn='".$order_sn."'");
//        $parent_id = $result['parent_facilitator_id'];
//        $id =  $result['facilitator_id'];
//        $parent_name = $db->getRow("SELECT `user_name` FROM " .$ecs->table('users'). " WHERE user_id='".$parent_id."'");
//        $name = $db->getRow("SELECT `user_name` FROM " .$ecs->table('users'). " WHERE user_id='".$id."'");
//        $order_list['orders'][$key]['parent_name'] = $parent_name['user_name'];
//        $order_list['orders'][$key]['name'] = $name['user_name'];
//    }
    $smarty->assign('stock_list',   $stock_list['stocks']);
    $smarty->assign('filter',       $stock_list['filter']);
    $smarty->assign('record_count', $stock_list['record_count']);
    $smarty->assign('page_count',   $stock_list['page_count']);
//
//    $smarty->assign('order_list',   $order_list['orders']);
//    $smarty->assign('filter',       $order_list['filter']);
//    $smarty->assign('record_count', $order_list['record_count']);
//    $smarty->assign('page_count',   $order_list['page_count']);
    $sort_flag  = sort_flag($stock_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);
    make_json_result($smarty->fetch('factory_stock.htm'), '', array('filter' => $stock_list['filter'], 'page_count' => $stock_list['page_count']));
}
/*  author:royallu
 *  func:edit step=goods
 *  time:20161115
 * */
elseif ($_REQUEST['act'] == 'add' || $_REQUEST['act'] == 'edit')
{
    /* 检查权限 */
    admin_priv('order_edit');

    /* 取得参数 order_id */
    $order_sn = isset($_GET['order_sn']) ? intval($_GET['order_sn']) : 0;
    $smarty->assign('order_sn', $order_sn);

    /* 取得参数 step */
    $step_list = array('user', 'goods', 'consignee', 'shipping', 'payment', 'other', 'money');
    $step = isset($_GET['step']) && in_array($_GET['step'], $step_list) ? $_GET['step'] : 'user';
    $smarty->assign('step', $step);

    /* 取得参数 act */
    $act = $_GET['act'];
    $smarty->assign('ur_here',$_LANG['add_order']);
    $smarty->assign('step_act', $act);

    /* 选择会员 */
    if ('user' == $step)
    {
        // 无操作
    }

    /* 增删改商品 */
    elseif ('goods' == $step)
    {
        /* 取得订单商品 */
        $sql='select * from ecs_due_info where order_sn='.$order_sn;

        $goods_list = $GLOBALS['db']->getAll($sql);;
        if (!empty($goods_list))
        {
            foreach ($goods_list AS $key => $goods)
            {
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
//        /* 取得商品总金额 */
//        $smarty->assign('goods_amount', order_amount($order_id));
    }



    /* 显示模板 */
    assign_query_info();
    $smarty->display('stock_step.htm');
}
/*------------------------------------------------------ */
//-- 修改订单（处理提交）
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'step_post')
{
    /* 检查权限 */
    admin_priv('order_edit');

    /* 取得参数 step */
    $step_list = array('user', 'edit_goods', 'add_goods', 'goods', 'consignee', 'shipping', 'payment', 'other', 'money', 'invoice');
    $step = isset($_REQUEST['step']) && in_array($_REQUEST['step'], $step_list) ? $_REQUEST['step'] : 'user';

    /* 取得参数 order_sn */
    $order_sn = isset($_REQUEST['order_sn']) ? intval($_REQUEST['order_sn']) : 0;
//    if ($order_id > 0)
//    {
//        $old_order = order_info($order_id);
//    }

    /* 取得参数 step_act 添加还是编辑 */
    $step_act = isset($_REQUEST['step_act']) ? $_REQUEST['step_act'] : 'add';


    /* 编辑商品信息 */
    if ('edit_goods' == $step)
    {
        if (isset($_POST['rec_id']))
        {
            foreach ($_POST['rec_id'] AS $key => $rec_id)
            {
                $sql = "SELECT goods_number ".
                    'FROM ' . $GLOBALS['ecs']->table('goods') .
                    "WHERE goods_id =".$_POST['goods_id'][$key];
                /* 取得参数 */
                $goods_price = floatval($_POST['goods_price'][$key]);
                $goods_number = intval($_POST['goods_number'][$key]);
                $goods_attr = $_POST['goods_attr'][$key];
//                $product_id = intval($_POST['product_id'][$key]);
//                if($product_id)
//                {
//
//                    $sql = "SELECT product_number ".
//                        'FROM ' . $GLOBALS['ecs']->table('products') .
//                        " WHERE product_id =".$_POST['product_id'][$key];
//                }

                $goods_number_all = $db->getOne($sql);

                if($goods_number_all>=$goods_number)
                {
                    /* 修改 */
                    $sql = "UPDATE " . $ecs->table('due_info') .
                        " SET goods_price = '$goods_price', " .
                        "goods_amount = '$goods_number', " .
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
           // $goods_amount = order_amount($order_id);
           // update_order($order_id, array('goods_amount' => $goods_amount));
          //  update_order_amount($order_id);

            /* 更新 pay_log */
            //update_pay_log($order_id);

            /* todo 记录日志 */
          //  $sn = $old_order['order_sn'];
          //  $new_order = order_info($order_id);
//            if ($old_order['total_fee'] != $new_order['total_fee'])
//            {
//                $sn .= ',' . sprintf($_LANG['order_amount_change'], $old_order['total_fee'], $new_order['total_fee']);
//            }
//            admin_log($sn, 'edit', 'order');
        }

        /* 跳回订单商品 */
        ecs_header("Location: factory_stock.php?act=" . $step_act . "&order_sn=" . $order_sn . "&step=goods\n");
        exit;
    }

    /* 商品 */
    elseif ('goods' == $step)
    {
        /* 下一步 */
        if (isset($_POST['next']))
        {
            ecs_header("Location: factory_stock.php?act=" . $step_act . "&order_sn=" . $order_sn . "&step=consignee\n");
            exit;
        }
        /* 完成 */
        elseif (isset($_POST['finish']))
        {
            /* 初始化提示信息和链接 */
            $msgs   = array();
            $links  = array();

            /* 如果已付款，检查金额是否变动，并执行相应操作 */
         //   $order = order_info($order_id);
          //  handle_order_money_change($order, $msgs, $links);

            /* 显示提示信息 */
            if (!empty($msgs))
            {
                sys_msg(join(chr(13), $msgs), 0, $links);
            }
            else
            {
                /* 跳转到订单详情 */
                ecs_header("Location: factory_stock.php?act=info&order_sn=" . $order_sn . "\n");
                exit;
            }
        }
    }
}
/*------------------------------------------------------ */
//-- 进货单详情页面
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'info')
{
    $order_sn=$_REQUEST['order_sn'];
    $sql = "SELECT rec_id,order_sn,goods_sn,goods_name,goods_attr,goods_amount,goods_price FROM " . $ecs->table('due_info')." where order_sn=".$order_sn;
    $smarty->assign('order_sn', $order_sn);
    $smarty->assign('due_info_list', $db->getAll($sql));
    /* 显示模板 */
    assign_query_info();
    $smarty->display('factory_stock_info.htm');
}
/*------------------------------------------------------ */
//-- 确定付款
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'operate')
{

    /* 确认 */
    if (isset($_POST['confirm']))
    {
       $order_sn=$_REQUEST['order_sn'];
        $sql = "UPDATE " . $ecs->table('due') .
            " SET status = 1 " .
//            "WHERE order_sn = '$order_sn' LIMIT 1";
        "WHERE order_sn = '$order_sn'";

        $db->query($sql);

        ecs_header("Location: factory_stock.php?act=list");
        exit;
    }

}
/*------------------------------------------------------ */
//-- 导出订单
/*------------------------------------------------------ */
elseif(isset($_REQUEST['export'])){
    require_once dirname(__FILE__) . '/Classes/PHPExcel.php';
    require_once dirname(__FILE__) . '/Classes/PHPExcel/IOFactory.php';

    error_reporting(E_ALL);
    ini_set('display_errors', TRUE);
    ini_set('display_startup_errors', TRUE);
    ini_set('memory_limit', '1024M');
    ini_set('max_execution_time', '0');
    // echo json_encode($parent_arr);

    // Create new PHPExcel object
    $phpExcel = new PHPExcel();


    //===查询时间段===
    $start_time = strtotime($_REQUEST['start_time']);
    $end_time = strtotime($_REQUEST['end_time']);
    $t = time();
    if(empty($start_time)){
        $start_time = mktime(0,0,0,date("m",$t),date("d",$t),date("Y",$t));
    }
    if(empty($end_time)){
        $end_time = mktime(23,59,59,date("m",$t),date("d",$t),date("Y",$t));
    }


    $phpExcel->setActiveSheetIndex(0)
        ->setCellValue('A3', '应付单号')
        ->setCellValue('B3', '商品供货商')
        ->setCellValue('C3', '订单号')
        ->setCellValue('D3', '应付货款')
        ->setCellValue('E3', '配送费')
        ->setCellValue('F3', '物流单号')
        ->setCellValue('G3', '付款状态')
        ->setCellValue('H3', '备注信息')
        ->setCellValue('I3', '创建时间')

    ;

    //订单数据
    $list = export_order($start_time, $end_time);
    $cell_index = 4;
    foreach($list as $v){
        $phpExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$cell_index, $v['id'])
            ->setCellValue('B'.$cell_index, $v['due_to'])
            ->setCellValue('C'.$cell_index, "'".$v['order_sn'])
            ->setCellValue('D'.$cell_index, $v['amount'])
            ->setCellValue('E'.$cell_index, $v['shipping_fee'])
            ->setCellValue('F'.$cell_index, $v['shipping_sn'])
            ->setCellValue('G'.$cell_index, $v['status'])
            ->setCellValue('H'.$cell_index, $v['comment'])
            ->setCellValue('I'.$cell_index, $v['createtime']);
        $cell_index++;
    }


    // Redirect output to a client’s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="订单.xls"');
    header('Cache-Control: max-age=0');
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');

    // If you're serving to IE over SSL, then the following may be needed
    header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
    header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header ('Pragma: public'); // HTTP/1.0
    $objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel5');
    $objWriter->setPreCalculateFormulas(false);
    $objWriter->save('php://output');
}
function stock_list()
{
    $result = get_filter();
    if ($result === false)
    {
        /* 过滤信息 */
        $filter['order_sn'] = empty($_REQUEST['order_sn']) ? '' : trim($_REQUEST['order_sn']);
        $filter['due_to'] = empty($_REQUEST['due_to']) ? '' : trim($_REQUEST['due_to']);
        $filter['status'] = isset($_REQUEST['status']) ? intval($_REQUEST['status']) : -1;

        $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'createtime' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
        $filter['start_time'] = empty($_REQUEST['start_time']) ? '' : (strpos($_REQUEST['start_time'], '-') > 0 ?  local_strtotime($_REQUEST['start_time']) : $_REQUEST['start_time']);
        $filter['end_time'] = empty($_REQUEST['end_time']) ? '' : (strpos($_REQUEST['end_time'], '-') > 0 ?  local_strtotime($_REQUEST['end_time']) : $_REQUEST['end_time']);

        $where = 'WHERE 1 ';
        if ($filter['order_sn'])
        {
            $where .= " AND o.order_sn LIKE '%" . mysql_like_quote($filter['order_sn']) . "%'";
        }
        if ($filter['due_to'])
        {
            $where .= " AND o.due_to LIKE '%" . mysql_like_quote($filter['due_to']) . "%'";
        }

        if ($filter['status'] != -1)
        {
            $where .= " AND o.status = '$filter[status]'";
        }
        if ($filter['start_time'])
        {
            $where .= " AND o.createtime >= '$filter[start_time]'";
        }
        if ($filter['end_time'])
        {
            $where .= " AND o.createtime <= '$filter[end_time]'";
        }

        /* 分页大小 */
        $filter['page'] = empty($_REQUEST['page']) || (intval($_REQUEST['page']) <= 0) ? 1 : intval($_REQUEST['page']);

        if (isset($_REQUEST['page_size']) && intval($_REQUEST['page_size']) > 0)
        {
            $filter['page_size'] = intval($_REQUEST['page_size']);
        }
        elseif (isset($_COOKIE['ECSCP']['page_size']) && intval($_COOKIE['ECSCP']['page_size']) > 0)
        {
            $filter['page_size'] = intval($_COOKIE['ECSCP']['page_size']);
        }
        else
        {
            $filter['page_size'] = 15;
        }

        /* 记录总数 */
            $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('due') . " AS o ". $where;


        $filter['record_count']   = $GLOBALS['db']->getOne($sql);
        $filter['page_count']     = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;

        /* 查询 */
        $sql = "SELECT o.id,o.due_to, o.order_sn, o.amount, o.shipping_fee, o.shipping_sn, o.status," .
            " o.comment, o.createtime" .
            " FROM " . $GLOBALS['ecs']->table('due') . " AS o " .$where .
            " ORDER BY $filter[sort_by] $filter[sort_order] ".
            " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ",$filter[page_size]";
        foreach (array('order_sn', 'due_sn', 'due_to', 'shipping_sn') AS $val)
        {
            $filter[$val] = stripslashes($filter[$val]);
        }
        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }

    $row = $GLOBALS['db']->getAll($sql);

    /* 格式话数据 */
    foreach ($row AS $key => $value)
    {
        $row[$key]['formated_amount'] = price_format($value['amount']);
        $row[$key]['formated_shipping_fee'] = price_format($value['shipping_fee']);
        $row[$key]['createtime'] = local_date('Y-m-d H:i:s', $value['createtime']);

    }
    $arr = array('stocks' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}
/*
 * 导出订单---20160916---
 * */
function export_order($start_time,$end_time){
    $res = "select d.id,d.due_to,d.order_sn,d.amount,d.shipping_fee,d.shipping_sn,d.status,d.comment,d.createtime from ".
        $GLOBALS['ecs']->table('due'). "AS d ".
        " where  d.createtime between ".$start_time . " and ".$end_time." order by d.id desc";

    $arr = $GLOBALS['db']->getAll($res);

    foreach($arr as $key=>$rows){
//        $arr[$key]['goods_attr_name'] = $GLOBALS['db']->getOne("select attr_value from ".$GLOBALS['ecs']->table('goods_attr')." where goods_attr_id=".$rows['goods_attr_id']);
//        $arr[$key]['attr_name'] = $GLOBALS['db']->getOne("select attr_value from ".$GLOBALS['ecs']->table('goods_attr')." where attr_id=232 and goods_id=".$rows['goods_id']);
//        //$arr[$key]['user_name'] = $GLOBALS['db']->getOne("select user_name from ".$GLOBALS['ecs']->table('users')." where user_id=".$rows['facilitator_id']);
//        $rows['facilitator_id'] = $GLOBALS['db']->getOne("select facilitator_id from ".$GLOBALS['ecs']->table('factory')." where order_sn=".$rows['order_sn']);
//        $arr[$key]['user_name']= $GLOBALS['db']->getOne("select user_name from ".$GLOBALS['ecs']->table('users')." where user_id=".$rows['facilitator_id']);
        $arr[$key]['createtime'] = date("Y-m-d H:i:s",$rows['createtime']);
//        $arr[$key]['goods_price'] = abs($rows['goods_price']);
//        $arr[$key]['all_price'] = abs($rows['goods_price'] * $rows['goods_number']);
    }
    return $arr;
}

?>