<?php
/**
 * MEIFANG 程序说明
 * =========================================================== 
 * 版权所有 2005-2014 上海优辉商务，并保留所有权利。
 * 网站地址: http://www.j345.net
 * ----------------------------------------------------------
 * 优辉网络,共创你我
 * ==========================================================
 * $Author: liubo $
 */

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . '/includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);
//门店列表
if ($_REQUEST['act'] == 'list')
{
	$list = get_store_list();
    foreach ($list['item'] as $key => $value) {
    	$userid = $value['userid'];
    	$parent_id = $value['parent_id'];
    	$sql = "SELECT `user_name` FROM " . $GLOBALS['ecs']->table('users')."where user_id =".$userid;
	    $res= $GLOBALS['db']->getAll($sql);
	    $referrals_id=$value['referrals_id'];
        $sql = "SELECT `user_name` FROM " . $GLOBALS['ecs']->table('users')."where user_id =".$referrals_id;
        $res3= $GLOBALS['db']->getAll($sql);
	    $sql1 = "SELECT `user_name` FROM " . $GLOBALS['ecs']->table('users')."where user_id =".$parent_id;
	    $res1= $GLOBALS['db']->getAll($sql1);
	    $list['item'][$key]['username'] = $res[0]['user_name'];
	    $list['item'][$key]['parent_name'] = $res1[0]['user_name'];
	    $list['item'][$key]['referrals_name']=$res3[0]['user_name'];
    }
    $smarty->assign('store_list', $list['item']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);
	
	$smarty->assign('full_page',    1);
	$smarty->display('store_list.htm');
	
}
else if ($_REQUEST['act'] == 'add')//添加门店页面
{	
	$smarty->assign('step',add);
	$smarty->assign('user_list_1',     get_user_list_1());
	$smarty->assign('user_list_2',     get_user_list_2());
	$smarty->assign('country_list',       get_regions());
	$smarty->assign('lang',              $_LANG);
	$smarty->assign('province_list',       get_regions(1,1));
	$smarty->display('store_add.htm');
}
else if ($_REQUEST['act'] == 'add_exe')//添加门店执行
{
	$arr=array();
	$arr['name']=$_POST['name'];
	$arr['tel']=$_POST['tel'];
	$arr['parent_id']=$_POST['parent_id'];
	$arr['referrals_id']=$_POST['referrals_id'];
	$arr['userid']=$_POST['userid'];
	$arr['shop_no']=$_POST['shop_no'];
	$arr['long_1']=$_POST['long'];
	$arr['lat']=$_POST['lat'];
	$arr['shop_weixin']=$_POST['shop_weixin'];
	//ykq_add_门店经纬度必须有值
	if($arr['long_1']=='' || $arr['lat']==''){
			sys_msg('经纬度必须填写，请重新编辑',1,$link);
			exit;
	}
	$sql = "SELECT * FROM " . $GLOBALS['ecs']->table('store')."where `shop_no`='".$_POST['shop_no']."'";
	$res= $GLOBALS['db']->getAll($sql);
	
	if($res){
		sys_msg('门店号已存在，请重新编辑',1,$link);
		exit;
	}
	if($arr['userid'] == $arr['referrals_id'] ){
	    sys_msg('门店店主和门店介绍人不能是同一个人，请重新编辑!',1,$link);
	    exit;
	}
	//$arr['store_img']=upload_img($image);
	if(!empty($_FILES['store_img']['name'])){
		$arr['store_img']=upload_img($image);
	}
	
	$arr['address']  =$_POST['address'];
	$arr['region_1'] =$_POST['province'];
	$arr['region_2'] =$_POST['city'];
	$arr['region_3'] =$_POST['district'];
	
	$res = $db->autoExecute($ecs->table('store'),$arr,'INSERT');
	if($res)
	{
	    //门店介绍人更新到user表里面
	    $GLOBALS['db']->query('UPDATE ecs_users SET introduce_id ='.$arr['referrals_id'].' WHERE user_id='.$arr['userid']);
		$link[0]['text'] = '返回到门店列表页';
	    $link[0]['href'] = '?act=list';
		sys_msg('添加成功',0,$link);
	}
	else
	{
		sys_msg('添加失败',1,$link);
	}
}
else if ($_REQUEST['act'] == 'edit')//编辑门店页面
{
	$store_id = $_GET['store_id'];
	$sql = "select * from {$ecs->table('store')} where store_id={$store_id}";
	$store_d = $db->getRow($sql);
	
	$user_id = $store_d['userid'];
	$referrals_id=$store_d['referrals_id'];//ykq_add_门店推荐人
	$parent_id = $store_d['parent_id'];
	
	$sql1 = "select `user_name` from {$ecs->table('users')} where user_id={$user_id}";
	$username = $db->getRow($sql1);
	//获得推荐的门店名字
	$sql3 = "select `user_name` from {$ecs->table('users')} where user_id={$referrals_id}";
	$referrals_name = $db->getRow($sql3);

	$sql2 = "select `user_name` from {$ecs->table('users')} where user_id={$parent_id}";
	$parent_name = $db->getRow($sql2);
	//默认省市县
	$region_id_1 = $store_d['region_1'];
	$name_1 = get_regions_name($region_id_1);
	$region_id_2 = $store_d['region_2'];
	$name_2 = get_regions_name($region_id_2);
	$region_id_3 = $store_d['region_3'];
	$name_3 = get_regions_name($region_id_3);
	$smarty->assign('name_1',$name_1);
	$smarty->assign('name_2',$name_2);
	$smarty->assign('name_3',$name_3);
	$smarty->assign('referrals_name',$referrals_name);
	$smarty->assign('username',$username);
	$smarty->assign('parent_name',$parent_name);
	$smarty->assign('store_d',$store_d);	
	$smarty->assign('is_edit',1);
	$smarty->assign('step',edit);
	
	$smarty->assign('lang',         $_LANG);
	$smarty->assign('user_list_1',     get_user_list_1());
	$smarty->assign('user_list_2',     get_user_list_2());
	$smarty->assign('country_list',       get_regions());
	$smarty->assign('province_list',       get_regions(1,1));
	$smarty->display('store_add.htm');
}
else if ($_REQUEST['act'] == 'edit_exe')//编辑执行
{

	$store_id = $_POST['store_id'];
	$shop_no = $GLOBALS['db']->getone("select `shop_no` from".$ecs->table('store')."where store_id='". $store_id."'");
	$arr=array();
	$arr['name']=$_POST['name'];
	$arr['tel']=$_POST['tel'];
	$arr['parent_id']=$_POST['parent_id'];
	$arr['userid']=$_POST['userid'];
	$arr['referrals_id']=$_POST['referrals_id'];//ykq_add_门店介绍人缺失
	$arr['shop_no']=$_POST['shop_no'];
	$arr['long_1']=$_POST['long'];
	$arr['lat']=trim($_POST['lat']);
	
	//$arr['city']=$_POST['city'];
	//ykq_add_门店经纬度必须有值
	if($arr['long_1']=='' || $arr['lat']==''){

			sys_msg('经纬度必须填写，请重新编辑',1,$link);
			exit;

	}
	if($shop_no != $arr['shop_no']){
		$sql = "SELECT * FROM " . $GLOBALS['ecs']->table('store')."where `shop_no`='".$_POST['shop_no']."'";
		$res= $GLOBALS['db']->getAll($sql);
		if($res){
			sys_msg('门店号已存在，请重新编辑',1,$link);
			exit;
		}
	}
	
	
	if(!empty($_FILES['store_img']['name'])){
		$arr['store_img']=upload_img($image);
	}
	
	
	$arr['address']=$_POST['address'];
	$arr['region_1']=$_POST['province'];
	$arr['region_2']=$_POST['city'];
	$arr['region_3']=$_POST['district'];
	$arr['shop_weixin'] = $_POST['shop_weixin'];
	$res = $db->autoExecute($ecs->table('store'),$arr,'UPDATE',"store_id={$store_id}");
	if($res)
	{
	     //门店介绍人更新到user表里面
	    $GLOBALS['db']->query('UPDATE ecs_users SET introduce_id ='.$arr['referrals_id'].' WHERE user_id='.$arr['userid']);
		$link[0]['text'] = '返回到门店列表页';
	    $link[0]['href'] = '?act=list';
		sys_msg('编辑成功',0,$link);
	}
	else
	{
		sys_msg('编辑失败',1,$link);
	}
}
// else if($_REQUEST['act'] == 'clerkadd'){
//     //店员添加
//     //$smarty->assign('step',);
//     //$smarty->assign('user_list_1',     get_user_list_1());
//     //$smarty->assign('user_list_2',     get_user_list_2());
//     //$smarty->assign('country_list',       get_regions());
//     //$smarty->assign('lang',              $_LANG);
//     //$smarty->assign('province_list',       get_regions(1,1));
//     $smarty->display('store_add.htm');
    
// }
/*------------------------------------------------------ */
//-- 翻页、搜索、排序
/*------------------------------------------------------ */
else if ($_REQUEST['act'] == 'query')
{
    $list = get_store_list();
    foreach ($list['item'] as $key => $value) {
	    $userid = $value['userid'];
    	$parent_id = $value['parent_id'];
    	$sql = "SELECT `user_name` FROM " . $GLOBALS['ecs']->table('users')."where user_id =".$userid;
	    $res= $GLOBALS['db']->getAll($sql);
	    $sql1 = "SELECT `user_name` FROM " . $GLOBALS['ecs']->table('users')."where user_id =".$parent_id;
	    $res1= $GLOBALS['db']->getAll($sql1);
	    $list['item'][$key]['username'] = $res[0]['user_name'];
	    $list['item'][$key]['parent_name'] = $res1[0]['user_name'];
    }

    $smarty->assign('store_list', $list['item']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);

    make_json_result($smarty->fetch('store_list.htm'),'',array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}

elseif ($_REQUEST['act'] == 'remove')
{
	
    $id = intval($_GET['id']);

    $sql = "DELETE FROM " .$ecs->table('store'). " WHERE store_id = '$id'";
    $res = $db->query($sql);
	$url = 'store.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);
    ecs_header("Location: $url\n");
    exit;
}



function get_regions_store($type = 0)
{
    $sql = 'SELECT region_id, parent_id, region_name FROM ' . $GLOBALS['ecs']->table('region') .
            " WHERE region_type = '$type' ";

    return $GLOBALS['db']->GetAll($sql);
}

/*查询region_name*/
function get_regions_name($region_id)
{
    $sql = 'SELECT region_name FROM ' . $GLOBALS['ecs']->table('region') .
            " WHERE region_id = '$region_id' ";

    return $GLOBALS['db']->GetOne($sql);
}

/**
 * 获取评论列表
 * @access  public
 * @return  array
 */
function get_store_list()
{
	$where = '1';
    /* 查询条件 */
    $filter['keywords']     = empty($_REQUEST['keywords']) ? 0 : trim($_REQUEST['keywords']);
    if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
    {
        $filter['keywords'] = json_str_iconv($filter['keywords']);
    }

    $where .= (!empty($filter['keywords'])) ? " AND concat(`name`,`shop_no`) LIKE '%" . $filter['keywords']. "%' " : '';

    $sql = "SELECT count(*) FROM " .$GLOBALS['ecs']->table('store'). " WHERE $where";
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);

    /* 分页大小 */
    $filter = page_and_size($filter);

    /* 获取门店数据 */
    $arr = array();
    $sql  = "SELECT * FROM " .$GLOBALS['ecs']->table('store'). " WHERE $where  ORDER BY store_id desc LIMIT  $filter[start] , $filter[page_size]";
    $res  = $GLOBALS['db']->getAll($sql);
	foreach($res as &$v){
		//省份
		$sql_1 = "select `region_name` FROM ".$GLOBALS['ecs']->table('region')." where region_id = ".$v['region_1'];
		$v['region_1_name'] = $GLOBALS['db']->getOne($sql_1);
		//城市
		$sql_2 = "select `region_name` FROM ".$GLOBALS['ecs']->table('region')." where region_id = ".$v['region_2'];
		$v['region_2_name'] = $GLOBALS['db']->getOne($sql_2);
		//县区
		$sql_3 = "select `region_name` FROM ".$GLOBALS['ecs']->table('region')." where region_id = ".$v['region_3'];
		$v['region_3_name'] = $GLOBALS['db']->getOne($sql_3);
	}

    $filter['keywords'] = stripslashes($filter['keywords']);
    $arr = array('item' => $res, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}
/**
 * 取得会员用户名和id列表
 * @return  array   会员等级列表
 */
//加盟商
function get_user_list_1()
{
    $sql = "SELECT `user_id`,`user_name` FROM " . $GLOBALS['ecs']->table('users')." where user_rank = 102 order by user_name ";

    return $GLOBALS['db']->getAll($sql);
}
//服务商
function get_user_list_2()
{
    $sql = "SELECT `user_id`,`user_name` FROM " . $GLOBALS['ecs']->table('users')." where user_rank = 103 order by user_name";

    return $GLOBALS['db']->getAll($sql);
}

//图片上传
function upload_img($image){
    /* 检查图片：如果有错误，检查尺寸是否超过最大值；否则，检查文件类型 */
    if (isset($_FILES['store_img']['error'])) // php 4.2 版本才支持 error
    {
        // 最大上传文件大小
        $php_maxsize = ini_get('upload_max_filesize');
        $htm_maxsize = '2M';
    
        // 门店
        if ($_FILES['store_img']['error'] == 0)
        {
            if (!$image->check_img_type($_FILES['store_img']['type']))
            {
                sys_msg($_LANG['invalid_store_img'], 1, array(), false);
            }
        }
        elseif ($_FILES['store_img']['error'] == 1)
        {
            sys_msg(sprintf($_LANG['store_img_too_big'], $php_maxsize), 1, array(), false);
        }
        elseif ($_FILES['store_img']['error'] == 2)
        {
            sys_msg(sprintf($_LANG['store_img_too_big'], $htm_maxsize), 1, array(), false);
        }
    
    
       
    }
    /* 4.1版本 */
    else
    {
        // 商品图片
        if ($_FILES['store_img']['tmp_name'] != 'none')
        {
            if (!$image->check_img_type($_FILES['store_img']['type']))
            {
    
                sys_msg($_LANG['invalid_store_img'], 1, array(), false);
            }
        }
    
    }
    
    /* 插入还是更新的标识 */
    $is_insert = $_REQUEST['act'] == 'insert';
    
    /* 处理门店 */
    $store_img        = '';  // 初始化商品图片
    
    // 如果上传了商门店图片，相应处理
    if (($_FILES['store_img']['tmp_name'] != '' && $_FILES['store_img']['tmp_name'] != 'none') or (($_POST['store_img_url'] != $_LANG['lab_picture_url'] && $_POST['store_img_url'] != 'http://') && $is_url_store_img = 1))
    {
        if ($_REQUEST['goods_id'] > 0)
        {
            /* 删除原来的图片文件 */
            $sql = "SELECT goods_thumb, store_img, original_img " .
                    " FROM " . $ecs->table('store') .
                    " WHERE goods_id = '$_REQUEST[goods_id]'";
            $row = $GLOBALS['db']->getRow($sql);
            if ($row['store_img'] != '' && is_file('../' . $row['store_img']))
            {
                @unlink('../' . $row['store_img']);
            }
            
        }
    
        if (empty($is_url_store_img))
        {
            $original_img   = $image->upload_image($_FILES['store_img']); // 原始图片
        }
        elseif ($_POST['store_img_url'])
        {
    
            if(preg_match('/(.jpg|.png|.gif|.jpeg)$/',$_POST['store_img_url']) && copy(trim($_POST['store_img_url']), ROOT_PATH . 'temp/' . basename($_POST['store_img_url'])))
            {
                $original_img = 'temp/' . basename($_POST['store_img_url']);
            }
    
        }
    
        if ($original_img === false)
        {
            sys_msg($image->error_msg(), 1, array(), false);
        }
        $store_img      = $original_img;   // 商品图片
    
        /* 复制一份相册图片 */
        /* 添加判断是否自动生成相册图片 */
        if ($_CFG['auto_generate_gallery'])
        {
            $img        = $original_img;   // 相册图片
            $pos        = strpos(basename($img), '.');
            $newname    = dirname($img) . '/' . $image->random_filename() . substr(basename($img), $pos);
            if (!copy('../' . $img, '../' . $newname))
            {
                sys_msg('fail to copy file: ' . realpath('../' . $img), 1, array(), false);
            }
            $img        = $newname;
    
            $gallery_img    = $img;
            $gallery_thumb  = $img;
        }
    
        // 如果系统支持GD，缩放商品图片，且给门店图片加水印
        if (  $image->gd_version() > 0 && $image->check_img_function($_FILES['store_img']['type']) || $is_url_store_img)
        {
    
            if (empty($is_url_store_img))
            {
                // 如果设置大小不为0，缩放图片
                if ($_CFG['image_width'] != 0 || $_CFG['image_height'] != 0)
                {
                    $store_img = $image->make_thumb('../'. $store_img , $GLOBALS['_CFG']['image_width'],  $GLOBALS['_CFG']['image_height']);
                    if ($store_img === false)
                    {
                        sys_msg($image->error_msg(), 1, array(), false);
                    }
                }
    
                /* 添加判断是否自动生成相册图片 */
                if ($_CFG['auto_generate_gallery'])
                {
                    $newname    = dirname($img) . '/' . $image->random_filename() . substr(basename($img), $pos);
                    if (!copy('../' . $img, '../' . $newname))
                    {
                        sys_msg('fail to copy file: ' . realpath('../' . $img), 1, array(), false);
                    }
                    $gallery_img        = $newname;
                }
    
                // 加水印
                if (intval($_CFG['watermark_place']) > 0 && !empty($GLOBALS['_CFG']['watermark']))
                {
                    if ($image->add_watermark('../'.$store_img,'',$GLOBALS['_CFG']['watermark'], $GLOBALS['_CFG']['watermark_place'], $GLOBALS['_CFG']['watermark_alpha']) === false)
                    {
                        sys_msg($image->error_msg(), 1, array(), false);
                    }
                    /* 添加判断是否自动生成相册图片 */
                    if ($_CFG['auto_generate_gallery'])
                    {
                        if ($image->add_watermark('../'. $gallery_img,'',$GLOBALS['_CFG']['watermark'], $GLOBALS['_CFG']['watermark_place'], $GLOBALS['_CFG']['watermark_alpha']) === false)
                        {
                            sys_msg($image->error_msg(), 1, array(), false);
                        }
                    }
                }
            }
    
        }
    }
    
    
    
    
    /* 删除下载的外链原图 */
    if (!empty($is_url_store_img))
    {
        unlink(ROOT_PATH . $original_img);
        empty($newname) || unlink(ROOT_PATH . $newname);
        $url_store_img = $store_img = $original_img = htmlspecialchars(trim($_POST['store_img_url']));
    }
    
     return $store_img;
}

?>