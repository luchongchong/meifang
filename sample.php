<?php
/**
 * ECSHOP 样本页面
 * $Author: derek $
 * $Id: search.php 17217 2011-01-19 06:29:08Z derek $
 */

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
$cache_id = sprintf('%X', crc32($_SESSION['user_rank'] . '-' . $_CFG['lang']));
if (!$smarty->is_cached('sample.dwt', $cache_id)){

    $smarty->display('sample.dwt', $cache_id);
}
//if (!function_exists("htmlspecialchars_decode"))
//{
//
//    function htmlspecialchars_decode($string, $quote_style = ENT_COMPAT)
//    {
//        return strtr($string, array_flip(get_html_translation_table(HTML_SPECIALCHARS, $quote_style)));
//    }
//}
//
//if (empty($_GET['encode']))
//{
//    $string = array_merge($_GET, $_POST);
//    if (get_magic_quotes_gpc())
//    {
//        require(dirname(__FILE__) . '/includes/lib_base.php');
//        //require(dirname(__FILE__) . '/includes/lib_common.php');
//
//        $string = stripslashes_deep($string);
//    }
//    $string['search_encode_time'] = time();
//    $string = str_replace('+', '%2b', base64_encode(serialize($string)));
//    //header("Location: sample.php?encode=$string\n");
//    header("Location: sample.php?encode=$string\n");
//
//    exit;
//}
//else
//{
//
//    $string = base64_decode(trim($_GET['encode']));
//    if ($string !== false)
//    {
//        $string = unserialize($string);
//        if ($string !== false)
//        {
//            /* 用户在重定向的情况下当作一次访问 */
//            if (!empty($string['search_encode_time']))
//            {
//                if (time() > $string['search_encode_time'] + 2)
//                {
//                    define('INGORE_VISIT_STATS', true);
//                }
//            }
//            else
//            {
//                define('INGORE_VISIT_STATS', true);
//            }
//        }
//        else
//        {
//            $string = array();
//        }
//    }
//    else
//    {
//        $string = array();
//    }
//}
//
//
//$_REQUEST = array_merge($_REQUEST, addslashes_deep($string));
//
//$_REQUEST['act'] = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : '';
//
///*------------------------------------------------------ */
////-- 高级搜索
///*------------------------------------------------------ */
//if ($_REQUEST['act'] == 'advanced_search')
//{
//    $goods_type = !empty($_REQUEST['goods_type']) ? intval($_REQUEST['goods_type']) : 0;
//    $attributes = get_seachable_attributes($goods_type);
//    $smarty->assign('goods_type_selected', $goods_type);
//    $smarty->assign('goods_type_list',     $attributes['cate']);
//    $smarty->assign('goods_attributes',    $attributes['attr']);
//
//    assign_template();
//    assign_dynamic('search');
//    $position = assign_ur_here(0, $_LANG['advanced_search']);
//    $smarty->assign('page_title', $position['title']);    // 页面标题
//    $smarty->assign('ur_here',    $position['ur_here']);  // 当前位置
//    $cat=get_categories_tree();
//    $smarty->assign('categories', $cat); // 分类树
//    $smarty->assign('helps',      get_shop_help());       // 网店帮助
//    $smarty->assign('top_goods',  get_top10());           // 销售排行
//    $smarty->assign('promotion_info', get_promotion_info());
//    $smarty->assign('cat_list',   cat_list(0, 0, true, 2, false));
//    $smarty->assign('brand_list', get_brand_list());
//    $smarty->assign('action',     'form');
//    $smarty->assign('use_storage', $_CFG['use_storage']);
//
//    $smarty->display('search.dwt');
//
//    exit;
//}
//// /*------------------------------------------------------ */
//// //-- 搜索结果
//// /*------------------------------------------------------ */
// else
// {
//     /* 初始化分页信息 */
//    $page = isset($_REQUEST['page'])   && intval($_REQUEST['page'])  > 0 ? intval($_REQUEST['page'])  : 1;
//    $size =$size = isset($_CFG['page_size'])  && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) : 32;
//    $filter_attr_str = trim(urldecode($filter_attr_str));
//    $filter_attr_str = preg_match('/^[\d\.]+$/',$filter_attr_str) ? $filter_attr_str : '';
//    $filter_attr = empty($filter_attr_str) ? '' : explode('.', $filter_attr_str);
//    $size = 12;
//     //扩展字段
//    //场所
//    $cat_id      = !empty($_REQUEST['cat_id'])        ? intval($_REQUEST['cat_id'])      : 0;
//    //风格
//    $style_id    = !empty($_REQUEST['style_id'])      ? intval($_REQUEST['style_id'])    : 0;
//    //材质
//    $material_id = !empty($_REQUEST['material_id'])   ? intval($_REQUEST['material_id']) : 0;
//    //产地
//    $origin      = !empty($_REQUEST['origin'])        ?htmlspecialchars(trim($_REQUEST['origin']))     : 0;
//    //价格
//    $price        = !empty($_REQUEST['price'])        ? intval($_REQUEST['price'])     : 0;
//   //var_dump($_REQUEST['cat_id']);var_dump($_REQUEST['material_id']);var_dump($_REQUEST['style_id']);
//    //var_Dump($_REQUEST);exit;
//    //echo 'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
//     /* 排序、显示方式以及类型 */
//    $default_display_type = $_CFG['show_order_type'] == '0' ? 'list' : ($_CFG['show_order_type'] == '1' ? 'grid' : 'album');
//    $default_sort_order_method = $_CFG['sort_order_method'] == '0' ? 'DESC' : 'ASC';
//    $default_sort_order_type   = $_CFG['sort_order_type'] == '0' ? 'goods_id' : ($_CFG['sort_order_type'] == '1' ? 'shop_price' : 'last_update');
//
//    $sort  = (isset($_REQUEST['sort'])  && in_array(trim(strtolower($_REQUEST['sort']))   , array('goods_id', 'shop_price', 'last_update', 'click_count', 'sales_count'))) ? trim($_REQUEST['sort'])  : $default_sort_order_type; // 增加按人气、按销量排序 by wang
//    $order = (isset($_REQUEST['order']) && in_array(trim(strtoupper($_REQUEST['order'])), array('ASC', 'DESC'))) ? trim($_REQUEST['order']) : $default_sort_order_method;
//    $display  = (isset($_REQUEST['display']) && in_array(trim(strtolower($_REQUEST['display'])), array('list', 'grid', 'album'))) ? trim($_REQUEST['display'])  : (isset($_COOKIE['ECS']['display']) ? $_COOKIE['ECS']['display'] : $default_display_type);
//    $display  = in_array($display, array('list', 'grid', 'album')) ? $display : 'album'; // by wang
//    setcookie('ECS[display]', $display, gmtime() + 86400 * 7);
//     //echo $cat_id;
//    $_REQUEST['keywords']   = !empty($_REQUEST['keywords'])   ? htmlspecialchars(trim($_REQUEST['keywords']))     : '';
//    $_REQUEST['brand']      = !empty($_REQUEST['brand'])      ? intval($_REQUEST['brand'])      : 0;
//    $_REQUEST['category']   = !empty($_REQUEST['category'])   ? intval($_REQUEST['category'])   : 0;
//    $_REQUEST['min_price']  = !empty($_REQUEST['min_price'])  ? intval($_REQUEST['min_price'])  : 0;
//    $_REQUEST['max_price']  = !empty($_REQUEST['max_price'])  ? intval($_REQUEST['max_price'])  : 0;
//    $_REQUEST['goods_type'] = !empty($_REQUEST['goods_type']) ? intval($_REQUEST['goods_type']) : 0;
//    $_REQUEST['sc_ds']      = !empty($_REQUEST['sc_ds']) ? intval($_REQUEST['sc_ds']) : 0;
//    $_REQUEST['outstock']   = !empty($_REQUEST['outstock']) ? 1 : 0;
//    $sort_list=get_categories_tree();
//    for ($i=1;$i<=count($sort_list);$i++){
//        if($sort_list[$i]['name']==CATEGROY_MATERIAL){
//            $material=$sort_list[$i]['cat_id'];
//        }elseif ($sort_list[$i]['name']==CATEGROY_PLACE){
//            $cait=$sort_list[$i]['cat_id'];
//        }
//    }
//
//    //获得所有分类
//    $smarty->assign('material',$material);
//    $smarty->assign('cait',$cait);
//    $smarty->assign('cat_id',$cat_id);
//    $smarty->assign('style_id',$style_id);
//    $smarty->assign('material_id',$material_id);
//    $smarty->assign('origin',$origin);
//    $smarty->assign('price',$price);
//    //得到所选产地名称
//    $origin_name=get_regions_name($origin);
//    //得到价格
//    if($price == 0){
//        $price_num=array('0');
//    }elseif($price == 49){
//        $price_num=array('0','50');
//    }elseif($price == 99){
//        $price_num=array('50','100');
//    }elseif($price == 199){
//        $price_num=array('100','200');
//    }elseif($price == 200){
//        $price_num=array('200');
//    }
//    //echo $price,'<hr/>';var_dump($price_num);exit;
//    $count = get_cagtegory_goods_count($cat_id,$style_id, $material_id,$origin_name,$price_num,$brand, $price_min, $price_max, $ext);
//
//    $max_page = ($count> 0) ? ceil($count / $size) : 1;
//    if ($page > $max_page)
//    {
//        $page = $max_page;
//    }
//    $pager_list = get_pager("sample.php", array('cat_id'=>$cat_id,'style_id'=>$style_id,'material_id'=>$material_id,'origin'=>$origin,'price'=>$price), $count,$page,$size=12);
//   $smarty->assign('pager_list', $pager_list);
//    /**
//     * 异步显示信息
//     */
//    if ($_REQUEST['act'] == 'asynclist') {
//        $asyn_last = intval($_REQUEST['last']) + 1;
//        $size = $_REQUEST['amount'];
//        $page = ($asyn_last > 0) ? ceil($asyn_last / $size) : 1;
//    }
//
//    $goodslist =get_goods_info_by_cat($cat_id,$style_id, $material_id,$origin_name,$price_num,$size, $page, $sort, $order);
//     //var_dump($goodslist);die();
//    if ($_REQUEST['act'] == 'asynclist') {
//        $sayList = array();
//        if (is_array($goodslist)) {
//            foreach ($goodslist as $vo) {
//                $sayList[] = array(
//                               'pro-inner' => '
//		                       <li> <a href="sample_info.php?id='.$vo['goods_id'].'" > <img src="'.$config['site_url'].$vo['goods_thumb'].'" alt="'.$vo['goods_name'].'">
//		                      <p>'.$vo['name'].'</p>
//                              <p>'.$vo['shop_price'].'</p>
//                              </a></li>'
//                );
//
//            }
//        }
//
//        echo json_encode($sayList);
//        exit;
//    }
//    /*
//     * 异步显示商品列表 by wang end
//    */
//
////     if($display == 'grid')
////     {
////         if(count($goodslist) % 2 != 0)
////         {
////             $goodslist[] = array();
////         }
////     }
//    $smarty->assign('sample',         $cat_id);
//    //echo  $cat_id, $count, $size, $sort, $order, $page, '', $brand, $price_min, $price_max, $display, $filter_attr_str;die();
//    assign_pager('sample',            $cat_id,$style_id, $material_id, $count, $size, $sort, $order, $page, '', $brand, $price_min, $price_max, $display, $filter_attr_str); // 分页
////     if($display == 'grid')
////     {
////         if(count($goodslist) % 2 != 0)
////         {
////             $goodslist[] = array();
////         }
////     }
//    //var_dump(get_categories_tree()[1]['cat_id']);die();
//    $categories=get_categories_tree();
//    for($i=1;$i<=count($categories);$i++){
//       $categories[$i]['idr']=$i+1;
//       if($categories[$i]['name'] == CATEGROY_PLACE){
//           $categories[$i]['tj_name']='cat_id';
//       }else if($categories[$i]['name'] == CATEGROY_STYLE){
//           $categories[$i]['tj_name']='style_id';
//       }else if ($categories[$i]['name'] == CATEGROY_MATERIAL){
//           $categories[$i]['tj_name']='material_id';
//       }
//
//    }
//
//    $smarty->assign('categories',      $categories); // 分类树
//
//  //获得地区
//  $smarty->assign('provite_list',get_regions(1,1));
//  //获取选择条件
//  $smarty->assign('select',get_categories_tree());
//  //获得样本信息
//  $smarty->assign('sample_list',$goodslist);
////echo $material_id,'<br/>',$cat_id,'<br/>',$style_id,'<br/>',$origin,'<br/>',$price,'<hr/>',$count;
//
//  $smarty->display('sample.dwt');
//}
//
///**
// * 获得分类下的商品总数
// *
// * @access  public
// * @param   string     $cat_id
// * @return  integer
// */
//function get_cagtegory_goods_count($cat_id, $style_id, $material_id,$origin_name,$price_num,$brand = 0, $min = 0, $max = 0, $ext='')
//{
//    $where="";
//    if(!empty($origin_name)){
//        $where.='left join '.$GLOBALS['ecs']->table('goods_attr')." as attr on g.goods_id=attr.goods_id WHERE attr.attr_value='".$origin_name."' AND";
//    }else{
//        $where.=" WHERE ";
//    }
//    $where .= "  g.is_delete = 0 AND is_on_sale = 1 AND is_alone_sale = 1 AND g.is_sample=1";
//    if($cat_id>0){
//        $where.=" AND g.cat_id=$cat_id";
//    }
//
//    if($style_id>0){
//        $where.=" AND g.style_id=$style_id";
//    }
//    if($material_id>0){
//        $where.=" AND g.material_id=$material_id";
//    }
//    if($price_num != 0){
//        if(!empty($price_num[1])){
//            $where.=' AND shop_price>='.$price_num[0].' AND shop_price<'.$price_num[1];
//        }else {
//            $where.=' AND shop_price>='.$price_num[0];
//        }
//    }
//    if ($brand > 0)
//    {
//        $where .=  " AND g.brand_id = $brand ";
//    }
//
//    if ($min > 0)
//    {
//        $where .= " AND g.shop_price >= $min ";
//    }
//
//    if ($max > 0)
//    {
//        $where .= " AND g.shop_price <= $max ";
//    }
//     //echo 'SELECT COUNT(*) FROM ' . $GLOBALS['ecs']->table('goods') . " AS g  $where $ext";die();
//    /* 返回商品总数 */
//    return $GLOBALS['db']->getOne('SELECT COUNT(*) FROM ' . $GLOBALS['ecs']->table('goods') . " AS g  $where $ext");
//}
///*
// *get_goods_info 获得指定分类下面的商品
//* @access  public
//* @param   string  $children
//* @return  array
//*/
//function get_goods_info_by_cat($cat_id, $style_id, $material_id,$origin_name,$price_num, $size, $page, $sort, $order)
//{
//    $where='';
//    if(!empty($origin_name)){
//        $where.='left join '.$GLOBALS['ecs']->table('goods_attr')." as attr on g.goods_id=attr.goods_id WHERE attr.attr_value='".$origin_name."' AND";
//    }else{
//        $where.=" WHERE ";
//    }
//    $where .= " g.is_delete = 0 AND is_on_sale = 1 AND is_alone_sale = 1 AND g.is_sample=1";
//    if($cat_id>0){
//        $where.=" AND g.cat_id=$cat_id";
//    }
//    if($style_id>0){
//        $where.=" AND g.style_id=$style_id";
//    }
//    if($material_id>0){
//        $where.=" AND g.material_id=$material_id";
//    }
//    if($price_num != 0){
//        if(!empty($price_num[1])){
//            $where.=' AND shop_price>='.$price_num[0].' AND shop_price<'.$price_num[1];
//        }else {
//            $where.=' AND shop_price>='.$price_num[0];
//        }
//    }
//    $display = $GLOBALS['display'];
//
//    /* 获得商品列表 */
//    $sql = 'SELECT g.goods_id, g.goods_name, g.goods_name_style, g.market_price, g.is_new, g.is_best, g.is_hot, g.shop_price AS org_price, g.shop_price AS shop_price, g.promote_price, g.goods_type,
//            g.promote_start_date, g.promote_end_date, g.goods_brief, g.goods_thumb , g.goods_img ' .
//            'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g '." $where ORDER BY $sort $order";
//    $res = $GLOBALS['db']->selectLimit($sql, $size, ($page - 1) * $size);
//    //echo $sql;
//    $arr = array();
//    while ($row = $GLOBALS['db']->fetchRow($res))
//    {
//        if ($row['promote_price'] > 0)
//        {
//            $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
//        }
//        else
//        {
//            $promote_price = 0;
//        }
//
//        /* 处理商品水印图片 */
//        $watermark_img = '';
//
//        if ($promote_price != 0)
//        {
//            $watermark_img = "watermark_promote_small";
//        }
//        elseif ($row['is_new'] != 0)
//        {
//            $watermark_img = "watermark_new_small";
//        }
//        elseif ($row['is_best'] != 0)
//        {
//            $watermark_img = "watermark_best_small";
//        }
//        elseif ($row['is_hot'] != 0)
//        {
//            $watermark_img = 'watermark_hot_small';
//        }
//
//        if ($watermark_img != '')
//        {
//            $arr[$row['goods_id']]['watermark_img'] =  $watermark_img;
//        }
//
//        $arr[$row['goods_id']]['goods_id']         = $row['goods_id'];
//        if($display == 'grid')
//        {
//            $arr[$row['goods_id']]['goods_name']       = $GLOBALS['_CFG']['goods_name_length'] > 0 ? sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
//        }
//        else
//        {
//            $arr[$row['goods_id']]['goods_name']       = $row['goods_name'];
//        }
//        $arr[$row['goods_id']]['name']             = $row['goods_name'];
//        $arr[$row['goods_id']]['goods_brief']      = $row['goods_brief'];
//        $arr[$row['goods_id']]['goods_style_name'] = add_style($row['goods_name'],$row['goods_name_style']);
//        $arr[$row['goods_id']]['market_price']     = price_format($row['market_price']);
//        $arr[$row['goods_id']]['shop_price']       = price_format($row['shop_price']);
//        $arr[$row['goods_id']]['type']             = $row['goods_type'];
//        $arr[$row['goods_id']]['promote_price']    = ($promote_price > 0) ? price_format($promote_price) : '';
//        $arr[$row['goods_id']]['goods_thumb']      =isset($row['goods_thumb'])?"../".get_image_path($row['goods_id'], $row['goods_thumb'], true):'';
//        $arr[$row['goods_id']]['goods_img']        = isset($row['goods_img'])?"../".get_image_path($row['goods_id'], $row['goods_img']):'';
//        $arr[$row['goods_id']]['url']              = build_uri('goods', array('gid'=>$row['goods_id']), $row['goods_name']);
//    }
//
//    return $arr;
//}
///**
// * 获得指定省份的名字
// *
// * @access      public
// * @param       int     country    国家的编号
// * @return      array
// */
//function get_regions_name($orgin_id)
//{
//    $sql = 'SELECT  region_name FROM ' . $GLOBALS['ecs']->table('region') .
//    " WHERE region_id = '$orgin_id'";
//
//    return $GLOBALS['db']->getOne($sql);
//}
?>