<?php

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');

/*------------------------------------------------------ */
//-- 粉丝数据排名
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'list')
{

    $list = user_fans_data_rank_list();
    $sql = "SELECT rank_id, rank_name, min_points FROM ".$ecs->table('user_rank')." ORDER BY min_points ASC ";
    $rs = $db->query($sql);
    $ranks = array();
    while ($row = $db->FetchRow($rs))
    {	
    	//$ranks[$row['rank_id']] = $row['rank_name'];
        if($row['rank_id']!=99) {
            $ranks[$row['rank_id']] = $row['rank_name'];
        }
    }
    $ranks[0] = "导出粉丝";
 
    foreach($list['user_list'] as &$v){
        //微信昵称
        $sql_nickname = "SELECT `nickname` FROM `ecs_weixin_user` WHERE `ecuid` = '".$v['user_id']."'";
        $nickname = $db->getOne($sql_nickname);
        $v['wx_nickname'] = $nickname;
            //vip数量
            $v['vip_number']=$db->getOne("SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('users') . "WHERE user_rank='99' and parent_id=".$v['user_id']);

           //粉丝数量
            //$v['fans_number']=$db->getOne("SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('users') . "WHERE user_rank!='99' and user_rank='102' and user_rank='103' and parent_id=".$v['user_id']);
            $v['fans_number']=$db->getOne("SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('users') . "WHERE user_rank!='99' and parent_id=".$v['user_id']);
            
            if($v['user_rank'] == 102){
            	//会员总数
            	$v['user_allnumber']=$db->getOne("SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('users') . "WHERE sell_id=".$v['user_id']);
            }elseif($v['user_rank'] == 103){
            	$v['user_allnumber']=$db->getOne("SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('users') . "WHERE service_id=".$v['user_id']);
            }
          
            
            $v['vip_helpnumber']=$v['user_allnumber']- $v['fans_number']-$v['vip_number'];
    }
	
    $smarty->assign('start_date',          date("2016-01-01"));
    $smarty->assign('end_date',            date("Y-m-d",time()));
    $smarty->assign('user_ranks',   $ranks);            //(服务商/加盟商)
    $smarty->assign('user_list',    $list['user_list']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);
    $smarty->assign('full_page',    1);
    $smarty->display('user_fans_data_rank.htm');
}
/* 导出订单功能开始 */
/**
 *  author:royallu
 *  function:会员数据导出成excel
 */
elseif (isset($_POST['export']))
{
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
	
    //用户等级条件
    $user_rank=$_REQUEST['user_rank'];
    //===查询时间段===
    $start_time = strtotime($_REQUEST['start_time']);
    $end_time = strtotime($_REQUEST['end_time']); 
    
    if(empty($start_time)){
    	$start_time = strtotime("2016-01-01");
    }
    if(empty($end_time)){
    	$end_time = time();
    }
    
    if($user_rank == 0){
    	$phpExcel->setActiveSheetIndex(0)
    	->setCellValue('A3', '会员编号')
    	->setCellValue('B3', '会员名称')
    	->setCellValue('C3', '会员等级')
    	->setCellValue('D3', '会员昵称')
    	->setCellValue('E3', '所属上级')
    	->setCellValue('F3', '注册时间');
    	
    	//会员数据
    	$list = user_fans_list($user_rank,$start_time,$end_time);
    }else{
    	$phpExcel->setActiveSheetIndex(0)
    	->setCellValue('A3', '会员编号')
    	->setCellValue('B3', '会员名称')
    	->setCellValue('C3', '会员等级')
    	->setCellValue('D3', '会员昵称')
    	->setCellValue('E3', 'VIP数量')
    	->setCellValue('F3', '粉丝数量')
    	->setCellValue('G3', '会员总数')
    	->setCellValue('H3', 'VIP帮助吸粉数');
    	
    	//会员数据
    	$list = user_fans_data_rank_list_where_user_rank($user_rank,$start_time,$end_time);
    }
   
   
   
    $sql = "SELECT rank_id, rank_name, min_points FROM ".$ecs->table('user_rank')." ORDER BY min_points ASC ";
    $rs = $db->query($sql);
    $ranks = array();
    while ($row = $db->FetchRow($rs))
    {
        if($row['rank_id']!=99) {
            $ranks[$row['rank_id']] = $row['rank_name'];
        }
    }
   
    foreach($list['user_list'] as &$v){
        //微信昵称
        $sql_nickname = "SELECT `nickname` FROM `ecs_weixin_user` WHERE `ecuid` = '".$v['user_id']."'";
        $nickname = $db->getOne($sql_nickname);
        $v['wx_nickname'] = $nickname;
        
        if($v['user_rank'] == 0 || $v['user_rank'] == 99){
        	//上级推荐人
        	$v['parent_name']=$db->getOne("SELECT user_name FROM " . $GLOBALS['ecs']->table('users') . "WHERE user_rank!='0' and user_id=".$v['parent_id']);
        	//粉丝数量
        	$v['reg_time']= date("Y-m-d H:i:s",$v['reg_time']);
        	
        	//将数据设置到excel表中
        }else{
        	//vip数量
        	$v['vip_number']=$db->getOne("SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('users') . "WHERE user_rank='99' and parent_id=".$v['user_id']);
        	//粉丝数量
        	$v['fans_number']=$db->getOne("SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('users') . "WHERE user_rank!='99' and parent_id=".$v['user_id']);
          
          	if($v['user_rank'] == 102){
            	//会员总数
            	$v['user_allnumber']=$db->getOne("SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('users') . "WHERE sell_id=".$v['user_id']);
            }elseif($v['user_rank'] == 103){
            	$v['user_allnumber']=$db->getOne("SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('users') . "WHERE service_id=".$v['user_id']);
            }
        	//  $v['vip_helpnumber']=$v['user_allnumber']-$v['fans_number']-$v['vip_number'];
        	$v['vip_helpnumber']=$v['user_allnumber']- $v['fans_number']-$v['vip_number'];
        	//将数据设置到excel表中
        }
       
    }
  
    $cell_index = 4;
    foreach ($list['user_list'] as $v) {
        if($v['user_rank']=='102'){
            $v['user_rank_name']='加盟商';
        }
        if($v['user_rank']=='103'){
            $v['user_rank_name']='服务商';
        }
    	if($v['user_rank']=='0'){
            $v['user_rank_name']='普通会员';
        }
        if($v['user_rank']=='99'){
        	$v['user_rank_name']='vIP会员';
        }
        if($v['user_rank'] == 0 || $v['user_rank'] == 99){
        	$phpExcel->setActiveSheetIndex(0)
        	->setCellValue('A'.$cell_index, $v['user_id']."")
        	->setCellValue('B'.$cell_index, $v['user_name'])
        	->setCellValue('C'.$cell_index, $v['user_rank_name'])
        	->setCellValue('D'.$cell_index, $v['wx_nickname'])
        	->setCellValue('E'.$cell_index, $v['parent_name'])
        	->setCellValue('F'.$cell_index, $v['reg_time']);
        	
        }else{
       		 $phpExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$cell_index, $v['user_id']."")
                    ->setCellValue('B'.$cell_index, $v['user_name'])
                    ->setCellValue('C'.$cell_index, $v['user_rank_name'])
                    ->setCellValue('D'.$cell_index, $v['wx_nickname'])
                    ->setCellValue('E'.$cell_index, $v['vip_number'])
                    ->setCellValue('F'.$cell_index, $v['fans_number'])
                    ->setCellValue('G'.$cell_index, $v['user_allnumber'])
                    ->setCellValue('H'.$cell_index, $v['vip_helpnumber']);
        }
        //$phpExcel->setActiveSheetIndex(0)->getStyle('A'.$cell_index)->applyFromArray(array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT)));
        $cell_index++;
    }
// Redirect output to a client’s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="会员.xls"');
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
/* 导出订单功能结束 */
/**
 *   author:royallu
 *   function:ajax搜索
 * */
else if ($_REQUEST['act'] == 'query')
{
	
    $list = user_fans_data_rank_list();
    $sql = "SELECT rank_id, rank_name, min_points FROM ".$ecs->table('user_rank')." ORDER BY min_points ASC ";
    $rs = $db->query($sql);
    $ranks = array();
    while ($row = $db->FetchRow($rs))
    {
        if($row['rank_id']!=99&&$row['rank_id']!=1) {
            $ranks[$row['rank_id']] = $row['rank_name'];
        }
    }
    foreach($list['user_list'] as &$v){
        //微信昵称
        $sql_nickname = "SELECT `nickname` FROM `ecs_weixin_user` WHERE `ecuid` = '".$v['user_id']."'";
        $nickname = $db->getOne($sql_nickname);
        $v['wx_nickname'] = $nickname;
        //vip数量
        $v['vip_number']=$db->getOne("SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('users') . "WHERE user_rank='99' and parent_id=".$v['user_id']);

        //粉丝数量
        //$v['fans_number']=$db->getOne("SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('users') . "WHERE user_rank!='99' and user_rank='102' or user_rank='103' and parent_id=".$v['parent_id']);
      	$v['fans_number'] = $db->getOne("SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('users') . "WHERE user_rank != '99' and parent_id = ".$v['user_id']);
      		if($v['user_rank'] == 102){
            	//会员总数
            	$v['user_allnumber']=$db->getOne("SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('users') . "WHERE sell_id=".$v['user_id']);
            }elseif($v['user_rank'] == 103){
            	$v['user_allnumber']=$db->getOne("SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('users') . "WHERE service_id=".$v['user_id']);
            }
        
        $v['vip_helpnumber']=$v['user_allnumber']- $v['fans_number']-$v['vip_number'];
    }
   // var_dump($list['user_list']);exit();
    $smarty->assign('user_ranks',   $ranks);            //(服务商/加盟商)
    $smarty->assign('user_list',    $list['user_list']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);

    make_json_result($smarty->fetch('user_fans_data_rank.htm'),'',array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}
function user_fans_data_rank_list_where_user_rank($user_rank,$start_time,$end_time){

//    $sql = "SELECT user_id, user_name, email,parent_id, is_validated, user_money, frozen_money,user_rank,rank_points, pay_points, reg_time ".
//        " FROM " . $GLOBALS['ecs']->table('users').
//        " WHERE user_rank=".$user_rank;

    $sql="select a.user_id, a.user_name, a.email,a.parent_id, a.is_validated, a.user_money, a.frozen_money,a.user_rank,a.rank_points, a.pay_points, a.reg_time,b.cnt from ecs_users  a left join
                  (select service_id,count(user_id) as cnt from ecs_users  group by service_id ) as b
                  on a.user_id=b.service_id 
                  where reg_time between $start_time and $end_time and  user_rank=$user_rank 
                  order by b.cnt desc";
   
    $res = $GLOBALS['db']->getAll($sql);
    $arr = array('user_list' => $res);
    return $arr;
}
function user_fans_data_rank_list()
{
    $result = get_filter();
    if ($result === false)
    {
        /* 过滤信息 */
        $filter['user_rank'] = empty($_REQUEST['user_rank']) ? '' : trim($_REQUEST['user_rank']);
        
        if($filter['user_rank']=='103'){
            //服务商
            $sql="select a.user_id, a.user_name, a.email,a.parent_id, a.is_validated, a.user_money, a.frozen_money,a.user_rank,a.rank_points, a.pay_points, a.reg_time,b.cnt from ecs_users  a left join
                  (select service_id,count(user_id) as cnt from ecs_users  group by service_id ) as b
                  on a.user_id=b.service_id 
                  where user_rank=103 
                  order by b.cnt desc";

        }
        if($filter['user_rank']=='102'||empty($filter['user_rank'])){
            //加盟商
            $sql="select a.user_id, a.user_name, a.email,a.parent_id, a.is_validated, a.user_money, a.frozen_money,a.user_rank,a.rank_points, a.pay_points, a.reg_time,b.cnt from ecs_users  a left join
                  (select sell_id,count(user_id) as cnt from ecs_users  group by sell_id ) as b
                  on a.user_id=b.sell_id
                   where user_rank=102 
                  order by b.cnt desc";
        }

      //  $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC'     : trim($_REQUEST['sort_order']);
        $where = ' WHERE 1 ';
        //var_dump($filter['user_rank']);exit();
        if (empty($filter['user_rank'])||$filter['user_rank']=='102')
        {
            $where.=" AND user_rank=".'102';
        }
        if($filter['user_rank']=='103'){
            $where.=" AND user_rank=".'103';
        }
        /* 记录总数 */
        $filter['record_count'] = $GLOBALS['db']->getOne("SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('users') . $where);
     // var_dump($filter['record_count']);exit();
        $filter = page_and_size($filter);
//        $sql = "SELECT user_id, user_name, email,parent_id, is_validated, user_money, frozen_money,user_rank,rank_points, pay_points, reg_time ".
//            " FROM " . $GLOBALS['ecs']->table('users') . $where .
//            //" ORDER by " . $filter['sort_by'] . ' ' . $filter['sort_order'] .
//            " LIMIT " . $filter['start'] . ',' . $filter['page_size'];
        $sql.=  " LIMIT " . $filter['start'] . ',' . $filter['page_size'];
    //  var_dump($sql);exit();
        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }
    $arr = array();
    $res = $GLOBALS['db']->getAll($sql);
//    var_dump($res);exit();
    $arr = array('user_list' => $res, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    return $arr;


}

//========导出粉丝======
function user_fans_list($user_rank,$start_time,$end_time){
	$user_rank = trim($user_rank);
	if($user_rank == 0){
		$where = " where (reg_time between $start_time and $end_time) and  (user_rank = 0 OR user_rank = 99)";
	}
	
	$sql = "select user_id,user_name,parent_id,user_rank,reg_time from ".$GLOBALS['ecs']->table('users').$where." order by user_id desc";
	
	
	$res = $GLOBALS['db']->getAll($sql);
	$arr = array('user_list' => $res);
	return $arr;
}

?>