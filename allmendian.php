<?php
/*
 * 全国门店信息
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

/*------------------------------------------------------ */
//-- INPUT
/*------------------------------------------------------ */

/* 获得请求的分类 ID */
if (isset($_REQUEST['id']))
{
	$cat_id = intval($_REQUEST['id']);
}
elseif (isset($_REQUEST['category']))
{
	$cat_id = intval($_REQUEST['category']);
}
else
{
	/* 如果分类ID为0，设置为1 */
	$cat_id=1;
}
$cache_id = sprintf("%x",crc32($page.'-'.$size.'-'.$_CGF['lang']));

if(!$smarty->is_cached("allmendian.dwt")){
	$sort_list=get_categories_tree();
	for ($i=1;$i<=count($sort_list);$i++){
		if($sort_list[$i]['name']==CATEGROY_MATERIAL){
			$material=$sort_list[$i]['cat_id'];
		}elseif ($sort_list[$i]['name']==CATEGROY_PLACE){
			$cait=$sort_list[$i]['cat_id'];
		}
	}
	//获得所有分类
	$smarty->assign('material',$material);
	$smarty->assign('cait',$cait);
	
	
}

if($_REQUEST['act'] && $_REQUEST['act'] == 'region'){
	//三级联动函数
	header('Content-type: text/html; charset=' . EC_CHARSET);
		$id = trim($_REQUEST['id']);
		if($id){
			$sql = "select region_id,region_name from".$GLOBALS['ecs']->table('region') ."where parent_id = $id";
			$region_list = $GLOBALS['db']->getAll($sql);
			$str = "";
			foreach($region_list as $value){
				$str .= "<option value='".$value['region_id']."'>".$value['region_name']."</option>";
			}
			echo $str;
			return;
		}
}

if($_REQUEST['act'] && $_REQUEST['act'] == 'see'){
	$id = isset($_REQUEST['id']) && intval($_REQUEST['id']) > 0 ? intval($_REQUEST['id']) : 0;
	
	$sql = "select * from ".$GLOBALS['ecs']->table('store'). " where store_id=$id";
	$mendian_info = $GLOBALS['db']->getrow($sql);
	//拼接地址
	$data=get_address($mendian_info['region_1'],$mendian_info['region_2'], $mendian_info['region_3']);
	$mendian_info=array_merge($mendian_info,$data);
	
	//获取用户评价
	$comment_list=get_comment_list(COMMENT_TYPE_DIANMIAN,$id);
	$count_commnet = count($comment_list['item']);//var_dump($count_commnet);
	$page_list = ceil($count_commnet/10);
	$smarty->assign('page_list', $page_list);
	$smarty->assign('id', $id);
	//获得评论用户的头像
	for($i=0;$i<count($comment_list['item']);$i++){
		$comment_list['item'][$i]['user_img']=$GLOBALS['db']->getOne('SELECT headimgurl FROM ecs_weixin_user WHERE ecuid='.$comment_list['item'][$i]['user_id']);
	}
	//获得好评率
	$comment_rank=$GLOBALS['db']->getAll(' SELECT count(*) as num,comment_rank from '.$GLOBALS['ecs']->table('comment').' where id_value='.$id.' and comment_type=3 group by comment_rank');
	for($i=0;$i<count($comment_rank);$i++){
		if($comment_rank[$i]['comment_rank'] == 1){
			$positive_feedback_num=$comment_rank[$i]['num'];
		}
		$num+=$comment_rank[$i]['num'];
	}
	$positive_feedback=intval($positive_feedback_num/$num*100);
	$smarty->assign('positive_feedback',$positive_feedback);
	//获得施工队列表
	$construction_team=get_mendian_list();
	$smarty->assign('construction_list',$construction_team);//var_dump($construction_team);exit();
	//获得店员形象
	$assistant=$GLOBALS['db']->getAll('SELECT * FROM '.$GLOBALS['ecs']->table('shop_assistant').' WHERE user_id='.$mendian_info['userid']);
	for($i=0;$i<count($assistant);$i++){
		$assistant[$i]['shop_assistant']="../upload/".$assistant[$i]['shop_assistant'];
	}
	$smarty->assign('assistant_list',$assistant);//var_dump($assistant);
	//获得门店的图片
	$store_img=$GLOBALS['db']->getAll("SELECT  store_shops FROM". $GLOBALS['ecs']->table('images_upload')." WHERE user_id=".$mendian_info['userid']);
	foreach ($store_img as $k=>$img){
		if(empty($img['store_shops'])){
			unset($store_img[$k]);
		}else{
			$store_img[$k]['store_shops'] = "./upload/".$store_img[$k]['store_shops'];
		}
	}
	$store_img = array_values($store_img);
	
	
	
	//==第一张大图
	$smarty->assign('first_img',$store_img[0]['store_shops']);
	$smarty->assign('store_shops',$store_img);
	//获得专享服务
	$exclusive_list=$_LANG['exclusive_list'];
	$smarty->assign('exclusive_list',$exclusive_list);
	$smarty->assign('comment_list',$comment_list);
	
	
	$smarty->assign('mendian_info',$mendian_info);
	$smarty->display("mendian_xiangqing.dwt");
	return;
}
/*
 * 门店详情页，用户评论，分页用ajax
 * */
if($_REQUEST['act'] && $_REQUEST['act'] == 'change'){
	$id = $_REQUEST['id'];
	$page = isset($_REQUEST['page']) && intval($_REQUEST['page']) > 0 ? intval($_REQUEST['page']) :1;
	$comment_list = user_pinglun($id, $page);
	echo $comment_list;
	return;
}

$page = isset($_REQUEST['page']) && intval($_REQUEST['page']) > 0 ?intval($_REQUEST['page']) : 1;
$size = isset($_CFG['page_size']) && intval($_CFG['page_size']) > 0?intval($_CFG['page_size']) :5;
$region_1 = isset($_REQUEST['province'])?intval($_REQUEST['province']) : 0;
$region_2 = isset($_REQUEST['city'])?intval($_REQUEST['city']) : 0;
$region_3 = isset($_REQUEST['district'])?intval($_REQUEST['district']) : 0;
$smarty->assign('region_1',$region_1);
$smarty->assign('region_2',$region_2);
$smarty->assign('region_3',$region_3);

//===分页所需数据
$count = all_store_count($region_1,$region_2,$region_3);
$size = 5;
$pagebanner = get_pager('allmendian.php', array('province'=>$region_1,'city'=>$region_2,'district',$region_3), $count,$page,$size);
$smarty->assign('pager_list', $pagebanner);

//===地图所需数据===
$map_store_list = map_store_list($region_1, $region_2, $region_3);
$map_store_list = json_encode($map_store_list);//var_dump($map_store_list);
$smarty->assign('map_store_list', $map_store_list);

//====所有数据==
$store_list = all_store_list($region_1, $region_2, $region_3,$page,$size);

//======
$smarty->assign('all_store_list', $store_list);
$smarty->assign('store_count', $count);
$smarty->assign('new_store',new_store());//新开门店显示

$smarty->display("allmendian.dwt");

//===新开店的信息四个
function new_store(){
	$sql = "select store_id,name,store_img from".$GLOBALS['ecs']->table('store')."ORDER BY store_id desc limit 0,4";
	$res = $GLOBALS['db'] -> getAll($sql);
	return $res;
}

//===门店总数===
function all_store_count($region_1=0,$region_2=0,$region_3=0){
	if($region_1 > 0){
		$where = "where region_1=$region_1";
	}
	if($region_2 > 0){
		$where .= " AND region_2=$region_2";
	}
	if($region_3 > 0){
		$where .= " AND region_3=$region_3";
	}
	
	$sql = "select COUNT(*) FROM ".$GLOBALS['ecs']->table('store').$where;
	return $GLOBALS['db']->getOne($sql);
}


//=====所有门店，分页显示===
function all_store_list($region_1,$region_2,$region_3,$page,$size){
	$size = 5;
	if($region_1 > 0){
		$where = "where region_1=$region_1";
	}
	if($region_2 > 0){
		$where .= " AND region_2=$region_2";
	}
	if($region_3 > 0){
		$where .= " AND region_3=$region_3";
	}
	
	$start_size = ($page-1)*$size;
	$sql = "SELECT * FROM" .$GLOBALS['ecs']->table('store') . $where." limit  $start_size, $size";
	$arr = $GLOBALS['db'] -> getAll($sql);
	return $arr;
}

//====所有门店====
function map_store_list($region_1,$region_2,$region_3){
	if($region_1 > 0){
		$where = "where region_1=$region_1";
	}
	if($region_2 > 0){
		$where .= " AND region_2=$region_2";
	}
	if($region_3 > 0){
		$where .= " AND region_3=$region_3";
	}
	
	$sql = "select long_1,lat,name,tel,address from" .$GLOBALS['ecs']->table('store').$where;
	$arr = $GLOBALS['db']->getAll($sql);
	$newArr = array();
	foreach($arr as $key=>&$v){
		$newArr[$key]['point'] = $v['long_1'].','.$v['lat'];
		$newArr[$key]['title'] = $v['name'];
		$newArr[$key]['tel'] = $v['tel'];
		$newArr[$key]['address'] = $v['address'];
	}
	return $newArr;
}

//获取地址
function get_address($province_id,$city_id,$district_id){
	$province = $GLOBALS['db']->getOne("SELECT `region_name` FROM " .$GLOBALS['ecs']->table('region'). " WHERE region_id='$province_id'");
	$city = $GLOBALS['db']->getOne("SELECT `region_name` FROM " .$GLOBALS['ecs']->table('region'). " WHERE region_id='$city_id'");
	$district = $GLOBALS['db']->getOne("SELECT `region_name` FROM " .$GLOBALS['ecs']->table('region'). " WHERE region_id='$district_id'");
	 
	$data=array(
			'province' => $province,
			'city'     => $city,
			'district' => $district
	);
	return $data;
}


/**
 * 获取评论列表
 * @access  public
 * @return  array
 */
function get_comment_list($comment_type,$id_value)
{

	/* 查询条件 */
	$filter['keywords']     = empty($_REQUEST['keywords']) ? 0 : trim($_REQUEST['keywords']);
	if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
	{
		$filter['keywords'] = json_str_iconv($filter['keywords']);
	}
	$filter['sort_by']      = empty($_REQUEST['sort_by']) ? 'add_time' : trim($_REQUEST['sort_by']);
	$filter['sort_order']   = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

	$where = (!empty($filter['keywords'])) ? " AND content LIKE '%" . mysql_like_quote($filter['keywords']) . "%' " : '';
	$sql = "SELECT count(*) FROM " .$GLOBALS['ecs']->table('comment'). " WHERE comment_type=$comment_type and id_value=$id_value $where";
	$filter['record_count'] = $GLOBALS['db']->getOne($sql);
	 
	//     /* 分页大小 */
	$filter = page_and_size($filter);
	if($filter['page_count']<$_REQUEST['page']){
		return false;
	}
	/* 获取评论数据 */
	$arr = array();
	$sql  = "SELECT * FROM " .$GLOBALS['ecs']->table('comment'). " WHERE comment_type=$comment_type and id_value=$id_value". $where  .
	" ORDER BY $filter[sort_by] $filter[sort_order] ".
	" LIMIT ". $filter['start'] .", $filter[page_size]";
	$res  = $GLOBALS['db']->query($sql);
	while ($row = $GLOBALS['db']->fetchRow($res))
	{
		$sql = ($row['comment_type'] == 0) ?
		"SELECT goods_name FROM " .$GLOBALS['ecs']->table('goods'). " WHERE goods_id='$row[id_value]'" :
		"SELECT title FROM ".$GLOBALS['ecs']->table('article'). " WHERE article_id='$row[id_value]'";
		$row['title'] = $GLOBALS['db']->getOne($sql);
		$row['add_time'] = local_date($GLOBALS['_CFG']['time_format'], $row['add_time']);
		$arr[] = $row;
	}
	$filter['keywords'] = stripslashes($filter['keywords']);
	$arr = array('item' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
	//var_dump($arr);
	return $arr;
}
function get_mendian_list(){
	 
	/* 查询条件 */
	$filter['keywords']     = empty($_REQUEST['keywords']) ? 0 : trim($_REQUEST['keywords']);
	if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
	{
		$filter['keywords'] = json_str_iconv($filter['keywords']);
	}
	$filter['sort_by']      = empty($_REQUEST['sort_by']) ? 'add_time' : trim($_REQUEST['sort_by']);
	$filter['sort_order']   = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

	$where = (!empty($filter['keywords'])) ? " AND content LIKE '%" . mysql_like_quote($filter['keywords']) . "%' " : '';
	$sql = "SELECT count(*) FROM " .$GLOBALS['ecs']->table('comment'). " user_construction_team $where";

	$filter['record_count'] = $GLOBALS['db']->getOne($sql);

	//     /* 分页大小 */
	$filter = page_and_size($filter);

	 
	if($filter['page_count']<$_REQUEST['page']){
		return false;
	}
	/* 获取评论数据 */
	$arr = array();
	$sql  = "SELECT * FROM  user_construction_team". $where .
	" LIMIT ". $filter['start'] .", $filter[page_size]";
	$res  = $GLOBALS['db']->query($sql);
	while ($row = $GLOBALS['db']->fetchRow($res))
	{
		//获得好评率
		$comment_rank=$GLOBALS['db']->getAll(' SELECT count(*) as num,comment_rank from '.$GLOBALS['ecs']->table('comment').' where id_value='.$row['construction_id'].' and comment_type=3 group by comment_rank');
		for($i=0;$i<count($comment_rank);$i++){
			if($comment_rank[$i]['comment_rank'] == 1){
				$positive_feedback_num=$comment_rank[$i]['num'];
			}
			$num+=$comment_rank[$i]['num'];
		}
		$row['positive']=intval($positive_feedback_num/$num*100);
		$arr[] = $row;
	}
	
	
	$filter['keywords'] = stripslashes($filter['keywords']);
	$arr = array('item' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

	return $arr;
}
/**
 * 分页的信息加入条件的数组
 *
 * @access  public
 * @return  array
 */
function page_and_size($filter)
{
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
		$filter['page_size'] = 10;
	}

	/* 每页显示 */
	$filter['page'] = (empty($_REQUEST['page']) || intval($_REQUEST['page']) <= 0) ? 1 : intval($_REQUEST['page']);

	/* page 总数 */
	$filter['page_count'] = (!empty($filter['record_count']) && $filter['record_count'] > 0) ? ceil($filter['record_count'] / $filter['page_size']) : 1;

	/* 边界处理 */
	if ($filter['page'] > $filter['page_count'])
	{
		$filter['page'] = $filter['page_count'];
	}

	$filter['start'] = ($filter['page'] - 1) * $filter['page_size'];

	return $filter;
}


function user_pinglun($id,$page){
	
	//获取用户评价
	$comment_list=get_comment_list(COMMENT_TYPE_DIANMIAN,$id);
	//获得评论用户的头像
	for($i=0;$i<count($comment_list['item']);$i++){
		$comment_list['item'][$i]['user_img']=$GLOBALS['db']->getOne('SELECT headimgurl FROM ecs_weixin_user WHERE ecuid='.$comment_list['item'][$i]['user_id']);
	}
	
	$arr = '';
	foreach($comment_list['item'] as $comment){
		$arr .= '<dl class="pin1" style="border-bottom:1px solid #ccc;padding-bottom:5px;">';
		$arr .= '<dt><img src='.$comment['user_img'].' width="152" height="152">';
		$arr .= '<span class="weixin">'.$comment['user_name'].'</span></dt>';
		$arr .= '<dd class="one" style="margin-left:5%;">';
		$arr .= '<p style="width:300px;">'.$comment['content'].'</p>';
		$arr .= '<P class="time">'.$comment['add_time'].'<span style="margin-left:200px;"><span style="color:#036eb8;">赞</span>（0）</span></p>';
		$arr .= '</dd></dl>';
	}
	return $arr;
}