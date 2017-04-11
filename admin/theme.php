<?php
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . '/includes/lib_order.php');
include_once(ROOT_PATH . '/includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);
//主题列表展示
if($_REQUEST['act']=='list'){

	$list = theme_list();
	$smarty->assign('ur_here',          $_LANG['01_theme_list']);
	$smarty->assign('action_link',  array('text' => $_LANG['01_theme_add'], 'href'=>'theme.php?act=add'));
	//$smarty->assign('imgurl',SITE_URL);
	$smarty->assign('theme_list',$list['theme_list']);
	$smarty->assign('filter',       $list['filter']);
	$smarty->assign('record_count', $list['record_count']);
	$smarty->assign('page_count',   $list['page_count']);
	$smarty->assign('full_page',    1);
	$smarty->display('zhuti_list.htm');

}
//query分页
if ($_REQUEST['act'] == 'query')
{

	$list = theme_list();
	$smarty->assign('theme_list',$list['theme_list']);
	$smarty->assign('filter',       $list['filter']);
	$smarty->assign('record_count', $list['record_count']);
	$smarty->assign('page_count',   $list['page_count']);

	make_json_result($smarty->fetch('zhuti_list.htm'),'',array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}

if ($_REQUEST['act']=='add'||$_REQUEST['act']=='edit') {

	$is_add = $_REQUEST['act'] == 'add'; // 添加还是编辑的标识
	if($is_add){
		$code='add';
		$smarty->assign('code',$code);
		$smarty->assign('ur_here',          $_LANG['04_theme_add']);
		$smarty->assign('form_action','theme.php?act=insert');
		$smarty->display('zhuti_form.htm');
	}
	//编辑操作
	if(!$is_add){
		$code='edit';
		$id=$_REQUEST['theme_id'];
		$sql="SELECT * FROM ".$ecs->table('yd_subject')." WHERE id='$id'";
		$result=$db->getRow($sql);

		$cycle_img_sql="SELECT * FROM ".$ecs->table('yd_img')." WHERE themeid='$id'";
		$result_cycle_img=$db->getAll($cycle_img_sql);
		if(empty($result_cycle_img)){
			sys_msg('商品相册无数据', 1, array(), false);
		}
		//商品
		$goods_sql="SELECT * FROM ".$ecs->table('yd_goods')." WHERE themeid='$id'";
		$result_goods=$db->getALL($goods_sql);
		foreach ($result_goods as $k=>$v){
			$sql = "SELECT goods_id,goods_name,cat_id,brand_id,shop_price FROM ".$ecs->table('goods')."WHERE goods_id=".$v['goodsid'];

			$goods_list[] = $db->getRow($sql);
		}
		foreach($goods_list as $k => $v){
			$sql = "SELECT cat_name FROM ".$ecs->table('category')." WHERE cat_id ='".$v['cat_id']."'";
			$cat_name = $db->getOne($sql);
			$goods_list[$k]['cat_name'] = $cat_name;
		}

		$smarty->assign('code',$code);
		$smarty->assign('goods_list',$goods_list);		  	//主题产品
		$smarty->assign('result_cycle_img',$result_cycle_img);	//轮播图
		$smarty->assign('result',$result);					 	//主题基本数据
		$smarty->assign('theme_id',$id);
		$smarty->assign('form_action','theme.php?act=update');

		$smarty->display('zhuti_form.htm');
	}

}
/**
 * 编辑主题商品
 */
if($_REQUEST['act']=='edit_goods'){
	$theme_id=$_REQUEST['theme_id'];
	//商品
	$goods_sql="SELECT * FROM ".$ecs->table('yd_goods')." WHERE themeid='$theme_id'";

	$result_goods=$db->getALL($goods_sql);
	foreach ($result_goods as $k=>$v){
		$sql = "SELECT goods_id,goods_name,cat_id,brand_id,shop_price FROM ".$ecs->table('goods')."WHERE goods_id=".$v['goodsid'];

		$goods_list[] = $db->getRow($sql);
	}
	if(empty($goods_list)){
		sys_msg('该主题下没有添加产品', 1, array(), false);
	}
	foreach($goods_list as $k => $v){
		$sql = "SELECT cat_name FROM ".$ecs->table('category')." WHERE cat_id ='".$v['cat_id']."'";
		$cat_name = $db->getOne($sql);
		$goods_list[$k]['cat_name'] = $cat_name;
	}

	$smarty->assign('themeid',         $theme_id);
	$smarty->assign('ur_here',         '主题商品');
	$smarty->assign('action_link',  array('text' => '返回主题列表', 'href'=>'theme.php?act=list'));
	$smarty->assign('goods_list',$goods_list);		  	//主题产品
	$smarty->assign('form_action','theme.php?act=add_goods');
	$smarty->assign('full_page',    1);
	$smarty->display('zhuti_goods.htm');

}
/**
 * 商品添加
 * */
if($_REQUEST['act'] == 'add_goods') {
	$theme=$_REQUEST['themeid'];
	if(!empty($_POST['goods_id'])){
		foreach($_POST['goods_id'] as $k => $v){
			$sql = "INSERT INTO ".$ecs->table('yd_goods')." (`themeid`,`goodsid`) VALUES ('$theme','$v')";
			$db->query($sql);
		}
	}
	ecs_header("Location:./theme.php?act=edit_goods&theme_id=$theme\n");
}

/**
 * 商品删除
*/
if($_REQUEST['act'] == 'del_goods'){
	
	$sql="DELETE FROM ".$ecs->table('yd_goods')." WHERE themeid='".$_REQUEST['themeid']."' AND goodsid='".$_REQUEST['goodsid']."'";
	if($db->query($sql)){
		echo '<script>alert("删除成功");window.location.href=document.referrer;</script>';
	}
}
if ($_REQUEST['act']=='insert'||$_REQUEST['act']=='update') {

	$is_add = $_REQUEST['act'] == 'insert';//数据插入
	if($is_add) {
		$goods_img = $image->upload_image($_FILES['image_name']);
		if ($goods_img === false) {
			sys_msg($image->error_msg(), 1, array(), false);

		} else {
			$theme = $_POST['temp_name'];
			$designer = $_POST['stylist'];
			$design = $_POST['specification'];
			$display = $_POST['display'];
			$addtime = time();
			$sql = "INSERT INTO " . $ecs->table('yd_subject') . " (`id`,`theme`,`designer`,`design`,`number`,`comment`,`link`,`facepath`,`display`,`addtime`) VALUES (null,'$theme','$designer','$design',0,0,0,'$goods_img','$display','$addtime')";
			if ($db->query($sql)) {
				$img_url = $_POST['img_urlA'];
				$cption = $_POST['cption'];
				$temp_id = $db->insert_id();
				if (!empty($img_url)) {
					$img_url = $_POST['img_urlA'];
					foreach ($cption as $k => $v) {
						foreach ($img_url as $j => $i) {
							if ($k == $j) {
								$sql = "INSERT INTO " . $ecs->table('yd_img') . " (`id`,`themeid`,`imgpath`,`caption`) VALUES (null,'$temp_id','$i','$v')";
								$db->query($sql);

							}
						}
					}
				}
				if (!empty($_POST['goods_id'])) {
					foreach ($_POST['goods_id'] as $k => $v) {
						$sql = "INSERT INTO " . $ecs->table('yd_goods') . " (`themeid`,`goodsid`) VALUES ('$temp_id','$v')";
						$db->query($sql);
					}
				}
				sys_msg('主题添加成功', 1, array(), false);

			}
		}
	}
	else{
		/*主题更新操作 update*/
		$themeid=$_POST['theme_id'];
		$theme = $_POST['temp_name'];
		$designer = $_POST['stylist'];
		$design = $_POST['specification'];
		$display = $_POST['display'];
		$addtime = time();
		$sql = "UPDATE " . $ecs->table('yd_subject') .
			   "SET theme  = '$theme',designer ='$designer',design='$design',display='$display',addtime='$addtime'" .
				"where id='$themeid'";
		if($db->query($sql)){
			$img_url = $_POST['img_urlA'];
			$cption = $_POST['cption'];
			//$temp_id =$themeid;
			//var_dump($image_url);exit();
			if (!empty($img_url)) {
				$img_url = $_POST['img_urlA'];
				foreach ($cption as $k => $v) {
					foreach ($img_url as $j => $i) {
						if ($k == $j) {
							//$sql = "INSERT INTO " . $ecs->table('yd_img') . " (`id`,`themeid`,`imgpath`,`caption`) VALUES (null,'$temp_id','$i','$v')";
							$sql = "UPDATE " . $ecs->table('yd_img') .
								"SET imgpath  = '$i',caption ='$v'" .
								"where themeid='$themeid'";
							$db->query($sql);
						}
					}
				}
			}
		}
	}
}
//主题商品查询
if($_REQUEST['act'] == 'goodsc'){
	$text = $_REQUEST['text'];
	$sql = "SELECT goods_id,goods_name,cat_id,brand_id,shop_price FROM ".$ecs->table('goods')."WHERE goods_name like '%$text%'";
	$goods_list = $db->getAll($sql);
	foreach($goods_list as $k => $v){
		$sql = "SELECT cat_name FROM ".$ecs->table('category')." WHERE cat_id ='".$v['cat_id']."'";
		$cat_name = $db->getOne($sql);
		$goods_list[$k]['cat_name'] = $cat_name;
	}
	echo json_encode($goods_list);
}	

//删除主题
if($_REQUEST['act']=='del'){
	$id=$_REQUEST['theme_id'];
	$sql="DELETE FROM ".$ecs->table('yd_subject')." WHERE id='$id'";
	$db->query($sql);
	$link[] = array('text' => $_LANG['go_back'], 'href'=>'theme.php?act=list');
	sys_msg('删除主题成功', 0 ,$link);
}

//编辑主题修改数据库
if ($_REQUEST['act']=='update') {
	$id=$_REQUEST['id'];
	$theme=$_POST['theme'];
	$designer=$_POST['designer'];
	$design=$_POST['design'];
	$number=$_POST['number'];
	$comment=$_POST['comment'];
	$link=$_POST['link'];
	$sql="UPDATE ".$ecs->table('yd_subject')."set theme='$theme',designer='$designer',design='$design',number='$number',comment='$comment',link='$link' WHERE id='$id'";
	$db->query($sql);
	ecs_header("Location:./theme.php?act=list\n");
}
//点击切换显示不显示
if($_REQUEST['act']=='display'){
	$id=$_REQUEST['id'];
	$display=$_REQUEST['display'];
	if($display=='0'){
		$sql="UPDATE".$ecs->table('yd_subject')."set display='1' WHERE id='$id'";
	}else{
		$sql="UPDATE".$ecs->table('yd_subject')."set display='0' WHERE id='$id'";
	}
	$db->query($sql);
	ecs_header("Location:./temp.php?act=list\n");
}


if($_REQUEST['act'] == 'img_delete'){
	$sql="DELETE FROM ".$ecs->table('yd_img')." WHERE id='".$_REQUEST['id']."'";
	if($db->query($sql)){
		ecs_header("Location:./temp.php?act=bjztsp&id=".$_REQUEST['themeid']."\n");
	}
}

if($_REQUEST['act'] == 'updateimg'){
	if(!empty($_POST['theme_id'])){
		foreach($_POST['theme_id'] as $id => $caption){
			 $sql = "UPDATE ".$ecs->table('yd_img')."SET caption='".$caption."' WHERE id='".$id."'";
			 $db->query($sql);
		}
	}
	if(!empty($_POST['img_urlA'])){
		foreach($_POST['img_urlA'] as $k => $imgpath){
			$sql = "INSERT INTO ".$ecs->table('yd_img')." (`id`,`themeid`,`imgpath`,`caption`) VALUES(null,'".$_POST['id']."','$imgpath',null)";
			$db->query($sql);
		} 
	}

	if(!empty($_FILES['facepath']['name'])){
		$goods_img   = $image->upload_image($_FILES['facepath']);
        if ($goods_img === false)
    	{
        sys_msg($image->error_msg(), 1, array(), false);
    	}else{
    		$sql = "UPDATE ".$ecs->table('yd_subject')."SET facepath='".$goods_img."' WHERE id='".$_POST['id']."'";
    		$db->query($sql);
    	}
	}
	echo '<script>alert("更新成功");window.location.href=document.referrer;</script>';
}

if($_REQUEST['act'] == 'goodslist'){
	//$sql = "SELECT g.goods_name,g.shop_price,g.goods_id,g.goods_number FROM ecs_yd_goods AS y,ecs_goods AS g WHERE y.themeid='".$_REQUEST['id']."' AND g.goods_id=y.goodsid LIMIT 0,5";
	$sql = "SELECT g.goods_name,g.shop_price,g.goods_id,g.goods_number,g.cat_id FROM ".$ecs->table('yd_goods')." y join ".$ecs->table('goods')." g WHERE y.themeid='".$_REQUEST['id']."' AND g.goods_id=y.goodsid LIMIT 0,5";
	$goods_list = $db->getAll($sql);
	foreach($goods_list as $k => $v){
		$sql = "SELECT cat_name FROM ".$ecs->table('category')." WHERE cat_id ='".$v['cat_id']."'";
		$cat_name = $db->getOne($sql);
		$goods_list[$k]['cat_name'] = $cat_name;
	}

	$smarty->assign('goods_list',$goods_list);
	$smarty->assign('themeid',$_REQUEST['id']);
	$smarty->display('theme_goods_list.dwt');
}

if($_REQUEST['act'] == 'xgsp'){
	$themeid = $_POST['themeid'];
	$sql = "SELECT goodsid FROM ".$ecs->table('yd_goods')." WHERE themeid='$themeid'";
	$goodsid = $db->getAll($sql);
	$goods_id = $_POST['goods_id'];
	$newgoods_id = array();
	foreach($goods_id as $k => $v){
		foreach($goodsid as $j => $g){
			if($v == $g['goodsid']){
				unset($goods_id[$k]);
			}else{
				$newgoods_id[$k] = $v;
			}
		}
	}

	foreach($newgoods_id as $k => $v){
		$sql = "INSERT INTO ".$ecs->table('yd_goods')." (`themeid`,`goodsid`) VALUES('".$themeid."','".$v['goodsid']."')";
		$db->query($sql);
	}
	echo '<script>alert("更新成功");window.location.href=document.referrer;</script>';
}
function theme_list()
{
	//$sql="SELECT * FROM ".$ecs->table('yd_subject')."ORDER BY id DESC";
	//$result=$db->getALL($sql);
	$result = get_filter();
	if ($result === false)
	{
		/* 过滤信息 */
		if (!empty($_GET['is_ajax']) && $_GET['is_ajax'] == 1)
		{
			$_REQUEST['theme_name'] 	= json_str_iconv($_REQUEST['theme_name']);
			$_REQUEST['theme_designer'] = json_str_iconv($_REQUEST['theme_designer']);

		}
		$filter['theme_name'] = empty($_REQUEST['theme_name']) ? '' : trim($_REQUEST['theme_name']);
		$filter['theme_designer'] = empty($_REQUEST['theme_designer']) ? '' : trim($_REQUEST['theme_designer']);
		$where = ' where';
		// var_dump($filter);exit();
		if ($filter['theme_designer']&&$filter['theme_name'])
		{
			$where .= " designer LIKE '%" . mysql_like_quote($filter['theme_designer']) . "%'";
			$where .= " AND theme LIKE '%" . mysql_like_quote($filter['theme_name']) . "%'";

		}else{
			/**
			 * TODO:
			 * */
			$where.=' 1 ';
		}
		/* 记录总数 */
		$sql='SELECT COUNT(*) FROM ecs_yd_subject'.$where;
		$filter['record_count']   = $GLOBALS['db']->getOne($sql);
		$filter = page_and_size($filter);
		//获取数据
		$where .='order by id desc';
		$sql="select * FROM ecs_yd_subject"."$where LIMIT $filter[start],$filter[page_size]";
		foreach (array('theme_name', 'theme_designer',) AS $val)
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
		$res[$key]['addtime'] = local_date('Y-m-d H:i', $value['addtime']);
	}
	$arr = array('theme_list' => $res, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
	return $arr;
}
?>