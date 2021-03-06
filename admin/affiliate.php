<?php

/**
 * ECSHOP 程序说明
 * ===========================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ==========================================================
 * $Author: liubo $
 * $Id: affiliate.php 17217 2011-01-19 06:29:08Z liubo $
 */

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
admin_priv('affiliate');
$config = get_affiliate();

/*------------------------------------------------------ */
//-- 分成管理页
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    assign_query_info();
    if (empty($_REQUEST['is_ajax']))
    {
        $smarty->assign('full_page', 1);
    }
    $affiliate=$GLOBALS['db']->getAll('SELECT * FROM '.$GLOBALS['ecs']->table('affiliate_config'));
    $smarty->assign('affiliate_list',$affiliate);
    $smarty->assign('ur_here', $_LANG['affiliate_ck']);
    $smarty->assign('remove_confirm',$_LANG['remove_confirm']);
    $smarty->assign('config', $config);
    $smarty->display('affiliate_c.htm');
}
/**
 * funciton:分利管理模块下添加分利明细的列表
 * author:royallu
 * time:20160525
 * */
elseif($_REQUEST['act']=='details')
{

    $list = affiliate_details_list();
   // var_dump($list);exit();
    $smarty->assign('ur_here', $_LANG['affiliate_detail']);
    $smarty->assign('affiliate_details_list',$list['affiliate_details_list']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);
    $smarty->assign('full_page',    1);

    $smarty->display('affiliate_details.htm');
}

/**  author:royallu
 *   function:ajax添加分利明细数据返回
 *   time:20160524
 * */
else if ($_REQUEST['act'] == 'query')
{
    $list = affiliate_details_list();
    $smarty->assign('affiliate_details_list', $list['affiliate_details_list']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);

    make_json_result($smarty->fetch('affiliate_details.htm'),'',array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}
//增加策略
elseif($_REQUEST['act'] == 'add_c'){
    assign_query_info();
    if (empty($_REQUEST['is_ajax']))
    {
        $smarty->assign('full_page', 1);
    }
  // $smarty->assign('lang',$_LANG['log_action']);
   $smarty->assign('ur_here', $_LANG['affiliate']);
   $smarty->assign('config', $config);
   $smarty->assign('act','add_z');
   $smarty->display('affiliate_add.htm');
}
//执行添加
elseif ($_REQUEST['act'] == 'add_z'){
    
    //接收参数
    $othe['affiliate_name']= !empty($_REQUEST['affiliate_name'])?htmlspecialchars(trim($_REQUEST['affiliate_name'])) : '';
    //查询策略名字是否存在
    $res=$GLOBALS['db']->getOne('SELECT COUNT(*) AS num FROM '.$GLOBALS['ecs']->table('affiliate_config').'WHERE affiliate_name="'.$othe['affiliate_name'].'"');
    if($res){
            $links[]=array('text' => $_LANG['affiliate'], 'href' => 'affiliate.php?act=add_c');
             sys_msg($_LANG['affilate_name'], 0 ,$links);
            exit;
    }
    $othe['top_sell']    = isset($_REQUEST['top_sell'])?intval($_REQUEST['top_sell']):0;
    $othe['top_service'] = isset($_REQUEST['top_service'])?intval($_REQUEST['top_service']):0;
    $othe['parent']     = isset($_REQUEST['parent'])?intval($_REQUEST['parent']):0;
    $othe['install']    = isset($_REQUEST['install'])?intval($_REQUEST['install']):0;
    $othe['sell']     = isset($_REQUEST['sell'])?intval($_REQUEST['sell']):0;
    $othe['introduce']   = isset($_REQUEST['introduce'])?intval($_REQUEST['introduce']):0;
    $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('affiliate_config'),$othe,'INSERT');
    /* 提示信息 */
    $links[] = array('text' => $_LANG['affiliate'], 'href' => 'affiliate.php?act=list');
    sys_msg($_LANG['add_ok'], 0 ,$links);
}
//编辑数据
elseif ($_REQUEST['act'] == 'edit'){
    $id=$_REQUEST['id'];
    $affiliate=$GLOBALS['db']->getRow('SELECT * FROM '.$GLOBALS['ecs']->table('affiliate_config').' WHERE id='.$id);
    
    assign_query_info();
    if (empty($_REQUEST['is_ajax']))
    {
        $smarty->assign('full_page', 1);
    }
    $smarty->assign('affiliate',$affiliate);
    $smarty->assign('ur_here', $_LANG['affiliate']);
    $smarty->assign('act','update_c');
    $smarty->display('affiliate_add.htm');
}
//执行修改
elseif($_REQUEST['act'] == 'update_c'){
    $id=$_REQUEST['id'];
     //接收参数
    $othe['affiliate_name']= !empty($_REQUEST['affiliate_name'])?htmlspecialchars(trim($_REQUEST['affiliate_name'])) : '';
    $othe['top_sell']    = isset($_REQUEST['top_sell'])?intval($_REQUEST['top_sell']):0;
    $othe['top_service'] = isset($_REQUEST['top_service'])?intval($_REQUEST['top_service']):0;
    $othe['parent']     = isset($_REQUEST['parent'])?intval($_REQUEST['parent']):0;
    $othe['install']    = isset($_REQUEST['install'])?intval($_REQUEST['install']):0;
    $othe['sell']     = isset($_REQUEST['sell'])?intval($_REQUEST['sell']):0;
    $othe['introduce']   = isset($_REQUEST['introduce'])?intval($_REQUEST['introduce']):0;
    $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('affiliate_config'),$othe,'UPDATE',"id = '$id'");
    
    $links[] = array('text' => $_LANG['affiliate'], 'href' => 'affiliate.php?act=list');
    sys_msg($_LANG['edit_ok'], 0 ,$links);
}
//删除
elseif($_REQUEST['act']=='remove'){

    $id=$_REQUEST['id'];
    $GLOBALS['db']->query('DELETE from'.$GLOBALS['ecs']->table('affiliate_config').' WHERE id='.$id);
    $links[] = array('text' => $_LANG['affiliate'], 'href' => 'affiliate.php?act=list');
    sys_msg($_LANG['remove'], 0 ,$links);
}
/*------------------------------------------------------ */
//-- 增加下线分配方案
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add_z')
{
    if (count($config['item']) < 5)
    {
        //下线不能超过5层
        $_POST['level_point'] = (float)$_POST['level_point'];
        $_POST['level_money'] = (float)$_POST['level_money'];
        $maxpoint = $maxmoney = 100;
        foreach ($config['item'] as $key => $val)
        {
            $maxpoint -= $val['level_point'];
            $maxmoney -= $val['level_money'];
        }
        $_POST['level_point'] > $maxpoint && $_POST['level_point'] = $maxpoint;
        $_POST['level_money'] > $maxmoney && $_POST['level_money'] = $maxmoney;
        if (!empty($_POST['level_point']) && strpos($_POST['level_point'],'%') === false)
        {
            $_POST['level_point'] .= '%';
        }
        if (!empty($_POST['level_money']) && strpos($_POST['level_money'],'%') === false)
        {
            $_POST['level_money'] .= '%';
        }
        $items = array('level_point'=>$_POST['level_point'],'level_money'=>$_POST['level_money']);
        $links[] = array('text' => $_LANG['affiliate'], 'href' => 'affiliate.php?act=list');
        $config['item'][] = $items;
        $config['on'] = 1;
        $config['config']['separate_by'] = 0;

        put_affiliate($config);
    }
    else
    {
       make_json_error($_LANG['level_error']);
    }

    ecs_header("Location: affiliate.php?act=query\n");
    exit;
}
/*------------------------------------------------------ */
//-- 修改配置
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'updata')
{

    $separate_by = (intval($_POST['separate_by']) == 1) ? 1 : 0;

    $_POST['expire'] = (float) $_POST['expire'];
    $_POST['level_point_all'] = (float)$_POST['level_point_all'];
    $_POST['level_money_all'] = (float)$_POST['level_money_all'];
    $_POST['level_money_all'] > 100 && $_POST['level_money_all'] = 100;
    $_POST['level_point_all'] > 100 && $_POST['level_point_all'] = 100;

    if (!empty($_POST['level_point_all']) && strpos($_POST['level_point_all'],'%') === false)
    {
        $_POST['level_point_all'] .= '%';
    }
    if (!empty($_POST['level_money_all']) && strpos($_POST['level_money_all'],'%') === false)
    {
        $_POST['level_money_all'] .= '%';
    }
    $_POST['level_register_all'] = intval($_POST['level_register_all']);
    $_POST['level_register_up'] = intval($_POST['level_register_up']);
    $temp = array();
    $temp['config'] = array('expire'                => $_POST['expire'],        //COOKIE过期数字
                            'expire_unit'           => $_POST['expire_unit'],   //单位：小时、天、周
                            'separate_by'           => $separate_by,            //分成模式：0、注册 1、订单
                            'level_point_all'       =>$_POST['level_point_all'],    //积分分成比
                            'level_money_all'       =>$_POST['level_money_all'],    //金钱分成比
                            'level_register_all'    =>$_POST['level_register_all'], //推荐注册奖励积分
                            'level_register_up'     =>$_POST['level_register_up']   //推荐注册奖励积分上限
          );
    $temp['item'] = $config['item'];
    $temp['on'] = 1;
    put_affiliate($temp);
    $links[] = array('text' => $_LANG['affiliate'], 'href' => 'affiliate.php?act=list');
    sys_msg($_LANG['edit_ok'], 0 ,$links);
}
/*------------------------------------------------------ */
//-- 推荐开关
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'on')
{

    $on = (intval($_POST['on']) == 1) ? 1 : 0;

    $config['on'] = $on;
    put_affiliate($config);
    $links[] = array('text' => $_LANG['affiliate'], 'href' => 'affiliate.php?act=list');
    sys_msg($_LANG['edit_ok'], 0 ,$links);
}
/*------------------------------------------------------ */
//-- Ajax修改设置
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_point')
{

    /* 取得参数 */
    $key = trim($_POST['id']) - 1;
    $val = (float)trim($_POST['val']);
    $maxpoint = 100;
    foreach ($config['item'] as $k => $v)
    {
        if ($k != $key)
        {
            $maxpoint -= $v['level_point'];
        }
    }
    $val > $maxpoint && $val = $maxpoint;
    if (!empty($val) && strpos($val,'%') === false)
    {
        $val .= '%';
    }
    $config['item'][$key]['level_point'] = $val;
    $config['on'] = 1;
    put_affiliate($config);
    make_json_result(stripcslashes($val));
}
/*------------------------------------------------------ */
//-- Ajax修改设置
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_money')
{
    $key = trim($_POST['id']) - 1;
    $val = (float)trim($_POST['val']);
    $maxmoney = 100;
    foreach ($config['item'] as $k => $v)
    {
        if ($k != $key)
        {
            $maxmoney -= $v['level_money'];
        }
    }
    $val > $maxmoney && $val = $maxmoney;
    if (!empty($val) && strpos($val,'%') === false)
    {
        $val .= '%';
    }
    $config['item'][$key]['level_money'] = $val;
    $config['on'] = 1;
    put_affiliate($config);
    make_json_result(stripcslashes($val));
}
/*------------------------------------------------------ */
//-- 删除下线分成
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'del')
{
    $key = trim($_GET['id']) - 1;
    unset($config['item'][$key]);
    $temp = array();
    foreach ($config['item'] as $key => $val)
    {
        $temp[] = $val;
    }
    $config['item'] = $temp;
    $config['on'] = 1;
    $config['config']['separate_by'] = 0;
    put_affiliate($config);
    ecs_header("Location: affiliate.php?act=list\n");
    exit;
}elseif ($_REQUEST['act'] == 'inventory'){
    //获得分利清单
    $order_son=isset($_REQUEST['order_sn'])?$_REQUEST['order_sn']:'';
    $user_name=isset($_REQUEST['user_name'])?$_REQUEST['user_name']:'';
    $smarty->assign('order_sn',$order_son);
    $smarty->assign('user_name',$user_name);
    $logdb = get_affiliate_ck($order_son,$user_name);
    $smarty->assign('full_page',  1);
    $smarty->assign('ur_here', '分成清单');
    $smarty->assign('logdb',        $logdb['logdb']);
    $logdb['filter']['next']=$logdb['filter']['page']+1;
    $logdb['filter']['prev']=$logdb['filter']['page']-1;
    $smarty->assign('filter',       $logdb['filter']);
    $smarty->assign('record_count', $logdb['record_count']);
    $smarty->assign('page_count',   $logdb['page_count']);
    assign_query_info();
    $smarty->display('affiliate_ck_list.htm');
}

function get_affiliate()
{
    $config = unserialize($GLOBALS['_CFG']['affiliate']);
    empty($config) && $config = array();

    return $config;
}

function put_affiliate($config)
{
    $temp = serialize($config);
    $sql = "UPDATE " . $GLOBALS['ecs']->table('shop_config') .
           "SET  value = '$temp'" .
           "WHERE code = 'affiliate'";
    $GLOBALS['db']->query($sql);
    clear_all_files();
}
function get_affiliate_ck($order_son,$user_name)
{

    $affiliate = unserialize($GLOBALS['_CFG']['affiliate']);
    empty($affiliate) && $affiliate = array();
    $separate_by = $affiliate['config']['separate_by'];
    $where='';
    if($order_son){
        $where.=' AND order_info.order_sn='.$order_son;
    }
    if($user_name){
        $where.=' AND user.user_name='.$user_name;
    }
    $sqladd = '';
    if (isset($_REQUEST['status']))
    {
        $sqladd = ' AND o.is_separate = ' . (int)$_REQUEST['status'];
        $filter['status'] = (int)$_REQUEST['status'];
    }
    if (isset($_REQUEST['order_sn']))
    {
        $sqladd = ' AND o.order_sn LIKE \'%' . trim($_REQUEST['order_sn']) . '%\'';
        $filter['order_sn'] = $_REQUEST['order_sn'];
    }
    if (isset($_GET['auid']))
    {
        $sqladd = ' AND a.user_id=' . $_GET['auid'];
    }


        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('account_log') ." as account LEFT JOIN ".$GLOBALS['ecs']->table('order_info').
                " as order_info ON account.order_id=order_info.order_id LEFT JOIN ".$GLOBALS['ecs']->table('users') ." as user ON account.user_id=user.user_id WHERE change_type=99".$where;
   


    $filter['record_count'] = $GLOBALS['db']->getOne($sql);
    $logdb = array();
    /* 分页大小 */
    $filter = page_and_size($filter);
        $sql = "SELECT order_info.order_sn,account.user_money,account.change_desc,account.user_id,account.change_time,user.user_name FROM ".$GLOBALS['ecs']->table('account_log') ." as account LEFT JOIN ".$GLOBALS['ecs']->table('order_info').
                " as order_info ON account.order_id=order_info.order_id LEFT JOIN ".$GLOBALS['ecs']->table('users') ." as user ON account.user_id=user.user_id WHERE account.change_type=99".$where." ORDER BY account.order_id DESC" .
                " LIMIT " . $filter['start'] . ",$filter[page_size]";

  //  echo $sql;die();

    $query = $GLOBALS['db']->query($sql);
    while ($rt = $GLOBALS['db']->fetch_array($query))
    {
        if(empty($separate_by) && $rt['up'] > 0)
        {
            //按推荐注册分成
            $rt['separate_able'] = 1;
        }
        elseif(!empty($separate_by) && $rt['parent_id'] > 0)
        {
            //按推荐订单分成
            $rt['separate_able'] = 1;
        }
        if(!empty($rt['suid']))
        {
            //在affiliate_log有记录
            $rt['info'] = sprintf($GLOBALS['_LANG']['separate_info2'], $rt['suid'], $rt['auser'], $rt['money'], $rt['point']);
            if($rt['separate_type'] == -1 || $rt['separate_type'] == -2)
            {
                //已被撤销
                $rt['is_separate'] = 3;
                $rt['info'] = "<s>" . $rt['info'] . "</s>";
            }
        }
        $logdb[] = $rt;
    }
    $arr = array('logdb' => $logdb, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
 
    return $arr;
}
function affiliate_details_list()
{
    $result = get_filter();
    if ($result === false)
    {
        /* 过滤信息 */
        $filter['order_sn'] = empty($_REQUEST['order_sn']) ? '' : trim($_REQUEST['order_sn']);
        if (!empty($_GET['is_ajax']) && $_GET['is_ajax'] == 1)
        {
            $_REQUEST['user_name'] = json_str_iconv($_REQUEST['user_name']);

        }
        $filter['user_name'] = empty($_REQUEST['user_name']) ? '' : trim($_REQUEST['user_name']);
       //降序
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC'     : trim($_REQUEST['sort_order']);

        $where = ' where a.user_id=b.user_id and a.order_id=c.order_id';
       // var_dump($filter);exit();
        if ($filter['order_sn']!=''&&$filter['user_name']!='')
        {
            $where .= " AND c.order_sn LIKE '%" . mysql_like_quote($filter['order_sn']) . "%'";
            $where .= " AND b.user_name LIKE '%" . mysql_like_quote($filter['user_name']) . "%'";
         //   $where .='order by c.order_sn desc';
        }
        if($filter['order_sn']!=''){
            $where .= " AND c.order_sn LIKE '%" . mysql_like_quote($filter['order_sn']) . "%'";
        }
        if($filter['user_name']!=''){
            $where .= " AND b.user_name LIKE '%" . mysql_like_quote($filter['user_name']) . "%'";

        }
//        var_dump($where);exit();
        /* 记录总数 */
        $sql='SELECT COUNT(*) FROM ecs_account_log a,ecs_users b,ecs_order_info c'.$where;
        $filter['record_count']   = $GLOBALS['db']->getOne($sql);
        $filter = page_and_size($filter);
      // var_dump($filter);exit();
        //获取数据
        $sql="select c.order_sn, a.user_id ,b.user_name,a.user_money ,a.change_time, change_desc".
            " from ecs_account_log a,ecs_users b,ecs_order_info c".$where.
            " ORDER by order_sn" . $filter['sort_by'] . ' ' . $filter['sort_order'] .
            " LIMIT " . $filter['start'] . ',' . $filter['page_size'];
	 //     var_dump($sql);exit();
        foreach (array('order_sn', 'user_name',) AS $val)
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
    $arr = array();
    $res = $GLOBALS['db']->getAll($sql);
    /* 格式话数据 */
    foreach ($res AS $key => $value)
    {
        $res[$key]['change_time'] = date('Y-m-d H:i', $value['change_time']);
    }

    $arr = array('affiliate_details_list' => $res, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    return $arr;
}
?>
