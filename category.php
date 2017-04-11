<?php

/**
 * ECSHOP 商品分类
 * ============================================================================
 * * 版权所有 2008-2015 秦皇岛商之翼网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.68ecshop.com;
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: derek $
 * $Id: category.php 17217 2011-01-19 06:29:08Z derek $
*/

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


/* 初始化分页信息 */
$page = isset($_REQUEST['page'])   && intval($_REQUEST['page'])  > 0 ? intval($_REQUEST['page'])  : 1;
$size = isset($_CFG['page_size'])  && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) : 10;
$brand = isset($_REQUEST['brand']) && intval($_REQUEST['brand']) > 0 ? intval($_REQUEST['brand']) : 0;
$price_max = isset($_REQUEST['price_max']) && intval($_REQUEST['price_max']) > 0 ? intval($_REQUEST['price_max']) : 0;
$price_min = isset($_REQUEST['price_min']) && intval($_REQUEST['price_min']) > 0 ? intval($_REQUEST['price_min']) : 0;
$filter_attr_str = isset($_REQUEST['filter_attr']) ? htmlspecialchars(trim($_REQUEST['filter_attr'])) : '0';

$filter_attr_str = trim(urldecode($filter_attr_str));
$filter_attr_str = preg_match('/^[\d\.]+$/',$filter_attr_str) ? $filter_attr_str : '';
$filter_attr = empty($filter_attr_str) ? '' : explode('.', $filter_attr_str);
$parent_id=isset($_REQUEST['parent_id'])?$_REQUEST['parent_id']:1;

/* 排序、显示方式以及类型 */
$default_display_type = $_CFG['show_order_type'] == '0' ? 'list' : ($_CFG['show_order_type'] == '1' ? 'grid' : 'text');
$default_sort_order_method = $_CFG['sort_order_method'] == '0' ? 'DESC' : 'ASC';
$default_sort_order_type   = $_CFG['sort_order_type'] == '0' ? 'goods_id' : ($_CFG['sort_order_type'] == '1' ? 'shop_price' : 'last_update');

$sort  = (isset($_REQUEST['sort'])  && in_array(trim(strtolower($_REQUEST['sort'])), array('goods_id', 'shop_price', 'last_update', 'salenum'))) ? trim($_REQUEST['sort'])  : $default_sort_order_type;  
 /* 代码增加_start  By  www.68ecshop.com */
$order = (isset($_REQUEST['order']) && in_array(trim(strtoupper($_REQUEST['order'])), array('ASC', 'DESC')))                              ? trim($_REQUEST['order']) : $default_sort_order_method;
$display  = (isset($_REQUEST['display']) && in_array(trim(strtolower($_REQUEST['display'])), array('list', 'grid', 'text'))) ? trim($_REQUEST['display'])  : (isset($_COOKIE['ECS']['display']) ? $_COOKIE['ECS']['display'] : $default_display_type);
$display  = in_array($display, array('list', 'grid', 'text')) ? $display : 'text';
setcookie('ECS[display]', $display, gmtime() + 86400 * 7);



//异步调用

if (!empty($_REQUEST['act']) && $_REQUEST['act'] == 'ajax')
{
	
    include('includes/cls_json.php');
    
    $last = $_POST['last'];
	$amount = $_POST['amount'];
	
	$limit = "limit $last,$amount";

    $json   = new JSON;
    //获取当前分类的子类
    $children_list = $categories[$parent_id]['cat_id'];
    $cat_id=isset($_REQUEST['cat_id'])?isset($_REQUEST['cat_id']):current($categories)['id'];
    $children_id=isset($_REQUEST['children_id'])?$_REQUEST['children_id']:current($categories[$cat_id]['cat_id'])['id'];
    $children = get_children($children_id);
    $smarty->assign('children_list', $children_list);
    $children = get_children($cat_id);
	$goodslist = category_get_goods($children, $brand, $price_min, $price_max, '', $size, $page, $sort, $order, $limit);
	
    foreach($goodslist as $val){
    	$smarty->assign('goods', $val);
    	$res[]['info']  = $GLOBALS['smarty']->fetch('library/goods_list_ajax.lbi');
    }
    /*
    if(count($goodslist)>0){
    	$smarty->assign('goods_list', $goodslist);
    	$res[]['info']  = $GLOBALS['smarty']->fetch('library/goods_list_ajax.lbi');
    }*/
    die($json->encode($res));
}

//爆款新品
if($_REQUEST['act'] && $_REQUEST['act'] == 'starhot'){
	$hot_goods = get_recommend_goods('hot');//爆款商品
	
	$hot_wufang_goods = array();
	$hot_chunzhi_goods = array();
	foreach($hot_goods as $value){
		if($value['material_id'] == 9){
			$hot_wufang_goods[] = $value;
		}elseif($value['material_id'] == 10){
			$hot_chunzhi_goods[] = $value;
		}
	}
	$hot_chunzhi_goods = array_slice($hot_chunzhi_goods,0,6);
	$hot_wufang_goods = array_slice($hot_wufang_goods,0,6);
	
	$smarty->assign('hot_chunzhi_goods',$hot_chunzhi_goods);
	$smarty->assign('hot_wufang_goods',$hot_wufang_goods);
	$smarty->display('starhot.dwt');
	return;
}
/*------------------------------------------------------ */
//-- PROCESSOR
/*------------------------------------------------------ */

/* 页面的缓存ID */
$cache_id = sprintf('%X', crc32($cat_id . '-' . $display . '-' . $sort  .'-' . $order  .'-' . $page . '-' . $size . '-' . $_SESSION['user_rank'] . '-' .
    $_CFG['lang'] .'-'. $brand. '-' . $price_max . '-' .$price_min . '-' . $filter_attr_str));

if (!$smarty->is_cached('category.dwt'))
{
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
    /* 如果页面没有被缓存则重新获取页面的内容 */
    $categories=get_categories_tree();
    for($i=1;$i<=count($categories);$i++){
        if($categories[$i]['name'] == CATEGROY_PLACE){
            $cat_info=$categories[$i];
            $cat_info['ti_name']='cat_id';
        }else if($categories[$i]['name'] == CATEGROY_STYLE){
            $style_info=$categories[$i];
            $style_info['tj_name']='style_id';
        }else if ($categories[$i]['name'] == CATEGROY_MATERIAL){
            $material_info=$categories[$i];
            $material_info['tj_name']='material_id';
        }
         
    } 
    //var_dump($material_info);die();
    $smarty->assign('cat_info',    $cat_info); //场所
    $smarty->assign('style_info',   $style_info);//风格 
    $smarty->assign('material_info',$material_info);
    //获得当前的分类
    $style_id    = isset($_REQUEST['style_id'])?intval($_REQUEST['style_id']):0;
    $material_id = isset($_REQUEST['material_id'])?intval($_REQUEST['material_id']):0;
    $cat_id=isset($_REQUEST['cat_id'])?intval($_REQUEST['cat_id']):0;
    //价格
    $price        = !empty($_REQUEST['price'])        ? intval($_REQUEST['price'])     : 0;
	
    $name         = !empty($_REQUEST['name']) ? $_REQUEST['name'] : '';
    
    //获取当前分类的子类
    $smarty->assign('cat_id', $cat_id);
    $smarty->assign('style_id', $style_id);
    $smarty->assign('price',$price);
    $smarty->assign('material_id', $material_id);
    //得到价格
    if($price == 0){
        $price_num=0;
    }elseif($price == 99){
        $price_num=array('0','100');
    }elseif($price == 149){
        $price_num=array('100','150');
    }elseif($price == 199){
        $price_num=array('150','200');
    }elseif($price == 249){
        $price_num=array('200','250');
    }elseif($price ==299){
        $price_num=array('250','300');
    }elseif($price ==300){
        $price_num=array('300');
    }
    
    $count = get_cagtegory_goods_count($cat_id,$style_id,$material_id,$price_num, $brand, $price_min, $price_max, $ext,$name);
    
    $max_page = ($count> 0) ? ceil($count / $size) : 1;
    if ($page > $max_page)
    {
        $page = $max_page;
    }
    
    $goodslist =get_goods_info_by_cat($cat_id,$style_id,$material_id,$price_num,$price, $size, $page, $sort, $order,$name);
    //var_dump($goodslist['goods_list']);die();
    //$goodslist = category_get_goods($children, $brand, $price_min, $price_max, $ext, $size, $page, $sort, $order);
    //$goodslist['page']=array('1','2');
    $smarty->assign('maxpage',          $max_page);
    $smarty->assign('goods_list',       $goodslist['goods_list']);
    $smarty->assign('pager_list',            $goodslist['page']);
    $smarty->assign('category',         $cat_id);
    $smarty->assign('script_name', 'category');

    assign_pager('category',            $cat_id, $count, $size, $sort, $order, $page, '', $brand, $price_min, $price_max, $display, $filter_attr_str); // 分页
    assign_dynamic('category'); // 动态内容
}
// $smarty->assign('cat_id',$cat_id);
$smarty->assign('brand',$brand);
$smarty->assign('price_min',$price_min);
$smarty->assign('price_max',$price_max);
$smarty->assign('filter_attr_str',$filter_attr_str);
$smarty->display('prolist.dwt');

/*------------------------------------------------------ */
//-- PRIVATE FUNCTION
/*------------------------------------------------------ */

/**
 * 获得分类的信息
 *
 * @param   integer $cat_id
 *
 * @return  void
 */
function get_cat_info($cat_id)
{
    return $GLOBALS['db']->getRow('SELECT cat_name, keywords, cat_desc, style, grade, filter_attr, parent_id FROM ' . $GLOBALS['ecs']->table('category') .
        " WHERE cat_id = '$cat_id'");
}


/**
 * 获得分类下的商品总数
 *
 * @access  public
 * @param   string     $cat_id
 * @return  integer
 */
function get_cagtegory_goods_count($cat_id,$sttyle_id,$matterial_id,$price_num, $brand = 0, $min = 0, $max = 0, $ext='',$name)
{
    $where  = "g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 " ;
    if($cat_id>0){
        $where .=" AND cat_id =$cat_id";
    }
    if($sttyle_id>0){
        $where .=" AND style_id=$sttyle_id";
    }
    if($matterial_id>0){
        $where .=" AND material_id=$matterial_id";
    }
    
    /*20160804搜索框产品名称判断*/
    if(!empty($name)){
    	$where .= "AND g.goods_name LIKE '%$name%'";
    }
    /*20160804END*/
    
    if($price_num != 0){
        if(!empty($price_num[1])){
            $where.=' AND shop_price>'.$price_num[0].' AND shop_price<'.$price_num[1];
        }else {
            $where.=' AND shop_price>'.$price_num[0];
        }
    }
    if ($brand > 0)
    {
        $where .=  " AND g.brand_id = $brand ";
    }

    if ($min > 0)
    {
        $where .= " AND g.shop_price >= $min ";
    }

    if ($max > 0)
    {
        $where .= " AND g.shop_price <= $max ";
    }
    //var_dump($where);
   // echo 'SELECT COUNT(*) FROM ' . $GLOBALS['ecs']->table('goods') . " AS g WHERE $where $ext";die();
    /* 返回商品总数 */
    return $GLOBALS['db']->getOne('SELECT COUNT(*) FROM ' . $GLOBALS['ecs']->table('goods') . " AS g WHERE $where $ext");
}

/**
 * 取得最近的上级分类的grade值
 *
 * @access  public
 * @param   int     $cat_id    //当前的cat_id
 *
 * @return int
 */
function get_parent_grade($cat_id)
{
    static $res = NULL;

    if ($res === NULL)
    {
        $data = read_static_cache('cat_parent_grade');
        if ($data === false)
        {
            $sql = "SELECT parent_id, cat_id, grade ".
                   " FROM " . $GLOBALS['ecs']->table('category');
            $res = $GLOBALS['db']->getAll($sql);
            write_static_cache('cat_parent_grade', $res);
        }
        else
        {
            $res = $data;
        }
    }

    if (!$res)
    {
        return 0;
    }

    $parent_arr = array();
    $grade_arr = array();

    foreach ($res as $val)
    {
        $parent_arr[$val['cat_id']] = $val['parent_id'];
        $grade_arr[$val['cat_id']] = $val['grade'];
    }

    while ($parent_arr[$cat_id] >0 && $grade_arr[$cat_id] == 0)
    {
        $cat_id = $parent_arr[$cat_id];
    }

    return $grade_arr[$cat_id];

}
/*
 *get_goods_info 获得指定分类下面的商品
* @access  public
* @param   string  $children
* @return  array
*/
function get_goods_info_by_cat($cat_id,$sttyle_id,$matterial_id, $price_num,$price, $size, $page, $sort, $order,$name, $extension_code)
{	
    $size=6;
    $where = " WHERE g.is_delete = 0 AND g.is_on_sale = 1 AND g.is_alone_sale = 1";
    
    if($cat_id>0){
        $where .=" AND cat_id =$cat_id";
    }
    if($sttyle_id>0){
        $where .=" AND style_id=$sttyle_id";
    }
  
    
    /*20160804搜索框产品名称判断*/
    if(!empty($name)){
    	$where .= " AND g.goods_name LIKE '%$name%'";
    }
    /*20160804END*/
    
    
    //var_dump($where);
    if($matterial_id>0){
        $where .=" AND material_id=$matterial_id";
    }
    if($price_num != 0){
        if(!empty($price_num[1])){
            $where.=' AND shop_price>'.$price_num[0].' AND shop_price<'.$price_num[1];
        }else {
            $where.=' AND shop_price>'.$price_num[0];
        }
    }
    
    $display = $GLOBALS['display'];
    /* 获得商品列表 */
    $sql = 'SELECT g.style_id,g.material_id,g.cat_id, g.goods_id, g.goods_name, g.goods_name_style, g.market_price, g.is_new, g.is_best, g.is_hot, g.shop_price AS org_price, g.shop_price AS shop_price, g.promote_price, g.goods_type,
            g.promote_start_date, g.promote_end_date, g.goods_brief, g.goods_thumb , g.goods_img ' .
            'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g '." $where ORDER BY $sort $order";
    $res = $GLOBALS['db']->selectLimit($sql, $size, ($page - 1) * $size);
    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
    $record_count = $GLOBALS['db']->getOne("SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('goods')." as g $where $ext ORDER BY $sort $order");
    $pager = get_pager('category.php', array('style_id' => $sttyle_id,'cat_id'=>$cat_id,'material_id'=>$matterial_id,'price'=>$price), $record_count, $page, $size);
    
   // echo $sql;
    $arr = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        if ($row['promote_price'] > 0)
        {
            $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
        }
        else
        {
            $promote_price = 0;
        }

        /* 处理商品水印图片 */
        $watermark_img = '';

        if ($promote_price != 0)
        {
            $watermark_img = "watermark_promote_small";
        }
        elseif ($row['is_new'] != 0)
        {
            $watermark_img = "watermark_new_small";
        }
        elseif ($row['is_best'] != 0)
        {
            $watermark_img = "watermark_best_small";
        }
        elseif ($row['is_hot'] != 0)
        {
            $watermark_img = 'watermark_hot_small';
        }

        if ($watermark_img != '')
        {
            $arr['goods_list'][$row['goods_id']]['watermark_img'] =  $watermark_img;
        }

        $arr['goods_list'][$row['goods_id']]['goods_id']         = $row['goods_id'];
        if($display == 'grid')
        {
            $arr['goods_list'][$row['goods_id']]['goods_name']       = $GLOBALS['_CFG']['goods_name_length'] > 0 ? sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
        }
        else
        {
            $arr['goods_list'][$row['goods_id']]['goods_name']       = $row['goods_name'];
        }
       
        if($row['style_id'] != 0){
            $arr['goods_list'][$row['goods_id']]['style_name']=get_goods_category($row['style_id']);
        }
        if($row['cat_id'] != 0){
            $arr['goods_list'][$row['goods_id']]['cat_name']=get_goods_category($row['cat_id']);
        }
        if($row['material_id'] != 0){
            $arr['goods_list'][$row['goods_id']]['material_name']=get_goods_category($row['material_id']);
        }
        $arr['goods_list'][$row['goods_id']]['origin_name']=get_goods_attr_info($row['goods_id']);
        $arr['goods_list'][$row['goods_id']]['specification']=get_goods_specification($row['goods_id']);
        $arr['goods_list'][$row['goods_id']]['name']             = $row['goods_name'];
        $arr['goods_list'][$row['goods_id']]['goods_brief']      = $row['goods_brief'];
        $arr['goods_list'][$row['goods_id']]['goods_style_name'] = add_style($row['goods_name'],$row['goods_name_style']);
        $arr['goods_list'][$row['goods_id']]['market_price']     = price_format($row['market_price']);
        $arr['goods_list'][$row['goods_id']]['shop_price']       = price_format($row['shop_price']);
        $arr['goods_list'][$row['goods_id']]['type']             = $row['goods_type'];
        $arr['goods_list'][$row['goods_id']]['promote_price']    = ($promote_price > 0) ? price_format($promote_price) : '';
        $arr['goods_list'][$row['goods_id']]['goods_thumb']      =isset($row['goods_thumb'])?"../".get_image_path($row['goods_id'], $row['goods_thumb'], true):'';
        $arr['goods_list'][$row['goods_id']]['goods_img']        = isset($row['goods_img'])?"../".get_image_path($row['goods_id'], $row['goods_img']):'';
        $arr['goods_list'][$row['goods_id']]['url']              = build_uri('goods', array('gid'=>$row['goods_id']), $row['goods_name']);
    }
    $arr['page']=$pager;
    return $arr;
}
//获得属性
function get_goods_category($cat_id){
    $sql="SELECT cat_name FROM ".$GLOBALS['ecs']->table('category')." WHERE cat_id=".$cat_id;
    return $GLOBALS['db']->getOne($sql);
}
//获得产地
function get_goods_attr_info($goods_id){
   //获得产地id
    $attr_id=$GLOBALS['db']->getOne('SELECT attr_id FROM '.$GLOBALS['ecs']->table('attribute').' WHERE attr_name="产地"');
    $sql="SELECT attr_value FROM ".$GLOBALS['ecs']->table('goods_attr')." WHERE attr_id=".$attr_id." AND goods_id=$goods_id";
    return $GLOBALS['db']->getOne($sql);
}
//获得规格
function get_goods_specification($goods_id){
    //获得规格id
    $attr_id=$GLOBALS['db']->getOne('SELECT attr_id FROM '.$GLOBALS['ecs']->table('attribute').' WHERE attr_name="规格"');
    $sql="SELECT attr_value FROM ".$GLOBALS['ecs']->table('goods_attr')." WHERE attr_id=".$attr_id." AND goods_id=$goods_id";
    return $GLOBALS['db']->getOne($sql);
}



?>