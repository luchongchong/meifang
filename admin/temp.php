<?php
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . '/includes/lib_order.php');
include_once(ROOT_PATH . '/includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);
if ($_REQUEST['act']=='add') {
	$smarty->display('temp_info.htm');
}

if ($_REQUEST['act']=='goadd') {

	$goods_img   = $image->upload_image($_FILES['image_name']);
        if ($goods_img === false)
    {
        sys_msg($image->error_msg(), 1, array(), false);
    }else{
	    $theme = $_POST['temp_name'];
		$designer = $_POST['stylist'];
		$design = $_POST['specification'];
		$display = $_POST['display'];
		$addtime = time();
		$sql = "INSERT INTO ".$ecs->table('yd_subject')." (`id`,`theme`,`designer`,`design`,`number`,`comment`,`link`,`facepath`,`display`,`addtime`) VALUES (null,'$theme','$designer','$design',0,0,0,'$goods_img','$display','$addtime')";
		if($db->query($sql)){
			     $img_url = $_POST['img_urlA'];
				 $cption  = $_POST['cption'];
			     $temp_id = $db->insert_id();
			if(!empty($img_url)){
			    $img_url = $_POST['img_urlA'];
	 			foreach($cption as $k => $v){
	 				foreach($img_url as $j => $i){
	 					if($k == $j){
	 						$sql = "INSERT INTO ".$ecs->table('yd_img')." (`themeid`,`imgpath`,`caption`) VALUES ('$temp_id','$i','$v')";
	 						$db->query($sql);
	 						
	 		}
	 	}
	 }
			}
		if(!empty($_POST['goods_id'])){
			foreach($_POST['goods_id'] as $k => $v){
				$sql = "INSERT INTO ".$ecs->table('yd_goods')." (`themeid`,`goodsid`) VALUES ('$temp_id','$v')";
				$db->query($sql);
			}	
		}
			sys_msg('上传成功', 1, array(), false);

		}
    }

	//$ecs->table('yd_subject') 
}
if($_REQUEST['act']=='list'){
	$smarty->display('zhuti_list.htm');

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
/*	echo '<pre>';
	var_dump($goods_list);
	echo '<pre>';*/
	echo json_encode($goods_list);
}	
?>