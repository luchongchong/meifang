<?php

/**
 * ECSHOP 首页文件
 * ============================================================================
 * * 版权所有 2008-2015 秦皇岛商之翼网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.68ecshop.com;
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: derek $
 * $Id: index.php 17217 2011-01-19 06:29:08Z derek $
*/

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');

if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}
if (isset($_REQUEST['is_c']))
{
    $is_c = intval($_REQUEST['is_c']);
}
if($is_c == 1){

    header("Location:../index.php?is_c=1"); 
}
/*------------------------------------------------------ */
//-- Shopex系统地址转换
/*------------------------------------------------------ */
if (!empty($_GET['gOo']))
{
    if (!empty($_GET['gcat']))
    {
        /* 商品分类。*/
        $Loaction = 'category.php?id=' . $_GET['gcat'];
    }
    elseif (!empty($_GET['acat']))
    {
        /* 文章分类。*/
        $Loaction = 'article_cat.php?id=' . $_GET['acat'];
    }
    elseif (!empty($_GET['goodsid']))
    {
        /* 商品详情。*/
        $Loaction = 'goods.php?id=' . $_GET['goodsid'];
    }
    elseif (!empty($_GET['articleid']))
    {
        /* 文章详情。*/
        $Loaction = 'article.php?id=' . $_GET['articleid'];
    }

    if (!empty($Loaction))
    {
        ecs_header("Location: $Loaction\n");

        exit;
    }
}

//判断是否有ajax请求
$act = !empty($_GET['act']) ? $_GET['act'] : '';
if ($act == 'cat_rec')
{
    $rec_array = array(1 => 'best', 2 => 'new', 3 => 'hot');
    $rec_type = !empty($_REQUEST['rec_type']) ? intval($_REQUEST['rec_type']) : '1';
    $cat_id = !empty($_REQUEST['cid']) ? intval($_REQUEST['cid']) : '0';
    include_once('includes/cls_json.php');
    $json = new JSON;
    $result   = array('error' => 0, 'content' => '', 'type' => $rec_type, 'cat_id' => $cat_id);

    $children = get_children($cat_id);
    $smarty->assign($rec_array[$rec_type] . '_goods',      get_category_recommend_goods($rec_array[$rec_type], $children));    // 推荐商品
    $smarty->assign('cat_rec_sign', 1);
    $result['content'] = $smarty->fetch('library/recommend_' . $rec_array[$rec_type] . '.lbi');
    die($json->encode($result));
}
//首页分页需要用到的东西
/* 初始化分页信息 */

$page = isset($_REQUEST['page'])   && intval($_REQUEST['page'])  > 0 ? intval($_REQUEST['page'])  : 1;
$size =$size = isset($_CFG['page_size'])  && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) : 10;;
$filter_attr_str = trim(urldecode($filter_attr_str));
$filter_attr_str = preg_match('/^[\d\.]+$/',$filter_attr_str) ? $filter_attr_str : '';
$filter_attr = empty($filter_attr_str) ? '' : explode('.', $filter_attr_str);
//扩展字段


$style_id =isset($_REQUEST['extension_code'])?$_REQUEST['extension_code']:0;
/* 排序、显示方式以及类型 */
$default_display_type = $_CFG['show_order_type'] == '0' ? 'list' : ($_CFG['show_order_type'] == '1' ? 'grid' : 'album');
$default_sort_order_method = $_CFG['sort_order_method'] == '0' ? 'DESC' : 'ASC';
$default_sort_order_type   = $_CFG['sort_order_type'] == '0' ? 'goods_id' : ($_CFG['sort_order_type'] == '1' ? 'shop_price' : 'last_update');

$sort  = (isset($_REQUEST['sort'])  && in_array(trim(strtolower($_REQUEST['sort']))   , array('goods_id', 'shop_price', 'last_update', 'click_count', 'sales_count'))) ? trim($_REQUEST['sort'])  : $default_sort_order_type; // 增加按人气、按销量排序 by wang
$order = (isset($_REQUEST['order']) && in_array(trim(strtoupper($_REQUEST['order'])), array('ASC', 'DESC'))) ? trim($_REQUEST['order']) : $default_sort_order_method;
$display  = (isset($_REQUEST['display']) && in_array(trim(strtolower($_REQUEST['display'])), array('list', 'grid', 'album'))) ? trim($_REQUEST['display'])  : (isset($_COOKIE['ECS']['display']) ? $_COOKIE['ECS']['display'] : $default_display_type);
$display  = in_array($display, array('list', 'grid', 'album')) ? $display : 'album'; // by wang
setcookie('ECS[display]', $display, gmtime() + 86400 * 7);
/*------------------------------------------------------ */
//-- 判断是否存在缓存，如果存在则调用缓存，反之读取相应内容
/*------------------------------------------------------ */
/* 缓存编号 */
/* 页面的缓存ID */
$cache_id = sprintf('%X', crc32($cat_id . '-' . $display . '-' . $sort  .'-' . $order  .'-' . $page . '-' . $size . '-' . $_SESSION['user_rank'] . '-' .
    $_CFG['lang'] .'-'. $brand. '-' . $filter_attr_str));
    assign_template();
    $position = assign_ur_here();
    $smarty->assign('page_title',      $position['title']);    // 页面标题
    $smarty->assign('ur_here',         $position['ur_here']);  // 当前位置
    //获取当前的路径
    
//     $url='http://localhost';
//     $smarty->assign('ecs_url',$url.$_SERVER['REQUEST_URI']);
   
    /* meta information */
    $smarty->assign('keywords',        htmlspecialchars($_CFG['shop_keywords']));
    $smarty->assign('description',     htmlspecialchars($_CFG['shop_desc']));
    $smarty->assign('flash_theme',     $_CFG['flash_theme']);  // Flash轮播图片模板

    $smarty->assign('feed_url',        ($_CFG['rewrite'] == 1) ? 'feed.xml' : 'feed.php'); // RSS URL

    $smarty->assign('categories',      get_categories_tree()); // 分类树
    $smarty->assign('helps',           get_shop_help());       // 网店帮助
    $smarty->assign('top_goods',       get_top10());           // 销售排行
    
   // $smarty->assign('best_goods',      get_recommend_goods('best'));    // 推荐商品
    //最新推荐
    $smarty->assign('new_goods_nums',get_goods_nums('new'));
    $smarty->assign('new_goods',       get_recommend_goods('new'));     // 新品推荐
    //明星热卖
    $smarty->assign('hot_goods_nums',get_goods_nums('hot'));
    $smarty->assign('hot_goods',       get_recommend_goods('hot'));     // 热买商品

//墙纸系列

$smarty->assign('qiangzhi_goods',       get_goods_list(45,1));
//窗帘系列

$smarty->assign('chuanlian_goods',       get_goods_list(43,1));
//软包系列
$smarty->assign('ruanbao_goods',       get_goods_list(48,1));
//猜你喜欢
$smarty->assign('like_goods',       get_goods_list(45,3));

    //var_dump(get_recommend_goods('sample'));die();
    //获取样本
    //$smarty->assign('sample_goods_nums',get_goods_nums('sample'));
    //$smarty->assign('sample_goods',    get_recommend_goods('sample'));
    //获取场所分类
    $cate_name_place = CATEGROY_NAME_PLACE;
    $smarty->assign('categories_list_place', get_categories_name($cate_name_place));
    
   /*  $categories_list_place = get_categories_name($cate_name_place);
    $cate_name_style = CATEGROY_NAME_STYLE;
    $goods_type_child = get_categories_name($cate_name_style);
    foreach ($categories_list_place as $k=>&$value){
    	if($value['name'] == '儿童房'){
    		if(count($goods_type_child) >7){
    			$new_goods = $goods_type_child[8];
    			$goods_type_child[8]=  $categories_list_place[$k];
    			
    		}else{
    			$goods_type_child[] = $categories_list_place[$k];
    		}
    		
    	}
    } */
    
    //获取风格分类
    $cate_name_style = CATEGROY_NAME_STYLE;
    $goods_type_child = get_categories_name($cate_name_style);
    foreach ($goods_type_child as $key =>&$item){
    	if($item['name'] == '简约' || $item['name'] == '英式'){
    		unset($goods_type_child[$key]);
    	}
    }
   
    $smarty->assign('goods_type_child',      $goods_type_child);
  
    //找到儿童房的id
    $Kids_room=get_categories_name($cate_name_place);
    foreach ($Kids_room as $value){
        if($value['name'] == CATEGROY_NAME_PLACE){
            $cat_id=$value['id'];
        }
    }

    $cat_id = empty($cat_id)?0:$cat_id;
    if($style_id>0){
        $cat_id='';
    }
    $count = get_cagtegory_goods_count($cat_id, $brand, $price_min, $price_max, $ext,$style_id);
    $max_page = ($count> 0) ? ceil($count / $size) : 1;
    if ($page > $max_page)
    {
        $page = $max_page;
    }
    /**
     * 异步显示信息
     */
    if ($_GET['act'] == 'asynclist') {
        $asyn_last = intval($_POST['last']) + 1;
        $size = $_POST['amount'];
        $page = ($asyn_last > 0) ? ceil($asyn_last / $size) : 1;
    }
    $goodslist =get_goods_info_by_cat($cat_id, $size, $page, $sort, $order,$style_id);
    if ($_GET['act'] == 'asynclist') {
    	$sayList = array();
    	   
    	if (is_array($goodslist)) {
    		foreach ($goodslist as $vo) {
                $sayList[] = array(
    				'pro-inner' => '
		                       <li> <a href="'.$vo['url'].'" > <img src="'.$config['site_url'].$vo['goods_thumb'].'" alt="'.$vo['goods_name'].'">
		                      <div class="info">
                              <p>'.$vo['goods_name'].'</p>
                              </div>
                              </a></li>'
    				);
    
    		}
    	}
    	echo json_encode($sayList);
    	exit;
    }
    /*
     * 异步显示商品列表 by wang end
    */
    
    if($display == 'grid')
    {
    	if(count($goodslist) % 2 != 0)
    	{
    		$goodslist[] = array();
    	}
    }
    $smarty->assign('index',         $cat_id);
    //echo  $cat_id, $count, $size, $sort, $order, $page, '', $brand, $price_min, $price_max, $display, $filter_attr_str;die();
    assign_pager('index',            $cat_id, $count, $size, $sort, $order, $page, '', $brand, $price_min, $price_max, $display, $filter_attr_str); // 分页
    if($display == 'grid')
    {
        if(count($goodslist) % 2 != 0)
        {
            $goodslist[] = array();
        }
    }
    //传递分类
    $smarty->assign('extension_code',$style_id);
    //$smarty->assign('goods_list',       $goodslist);
    //获取分页
//     $pager=index_paging($cat_id);
//     //$pager['start'].','.$pager['size']
//     //显示所属儿童房分类的商品
//     $smarty->assign('goods_list', get_cat_id_goods_list($cat_id,$pager['start'].','.$pager['size']));
    
    $smarty->assign('promotion_goods', get_promote_goods()); // 特价商品
    $smarty->assign('brand_list',      get_brands());
    $smarty->assign('promotion_info',  get_promotion_info()); // 增加一个动态显示所有促销信息的标签栏
    
   
    
    
    $smarty->assign('invoice_list',    index_get_invoice_query());  // 发货查询
    $smarty->assign('new_articles',    index_get_new_articles());   // 最新文章
    $smarty->assign('group_buy_goods', index_get_group_buy());      // 团购商品
    $smarty->assign('auction_list',    index_get_auction());        // 拍卖活动
    $smarty->assign('shop_notice',     $_CFG['shop_notice']);       // 商店公告
	//yyy添加start
	$smarty->assign('wap_index_ad',get_wap_advlist('wap首页幻灯广告', 5));  //wap首页幻灯广告位
	$smarty->assign('wap_index_icon',get_wap_advlist('wap端首页8个图标', 8));  //wap首页幻灯广告位
	//var_dump(get_wap_advlist('wap首页幻灯广告', 5));exit();
    $smarty->assign('wap_tree1', get_wap_parent_id_tree(1));     //wap首页分类广告下子分类
    $smarty->assign('wap_tree2', get_wap_parent_id_tree(20));
    $smarty->assign('wap_tree3', get_wap_parent_id_tree(19));
    
    $smarty->assign('menu_list',get_menu());
    
	//yyy添加end
	
    /* 首页主广告设置 */
    $smarty->assign('index_ad',     $_CFG['index_ad']);
    if ($_CFG['index_ad'] == 'cus')
    {
        $sql = 'SELECT ad_type, content, url FROM ' . $ecs->table("    ") . ' WHERE ad_status = 1';
        $ad = $db->getRow($sql, true);

        
        $smarty->assign('ad', $ad);
    }
   

    /* links */
    $links = index_get_links();
    $smarty->assign('img_links',       $links['img']);
    $smarty->assign('txt_links',       $links['txt']);
    $smarty->assign('data_dir',        DATA_DIR);       // 数据目录

	
	/*jdy add 0816 添加首页幻灯插件*/	
    $smarty->assign("flash",get_flash_xml());
    $smarty->assign('flash_count',count(get_flash_xml()));


    /* 首页推荐分类 */
    $cat_recommend_res = $db->getAll("SELECT c.cat_id, c.cat_name, cr.recommend_type FROM " . $ecs->table("cat_recommend") . " AS cr INNER JOIN " . $ecs->table("category") . " AS c ON cr.cat_id=c.cat_id");
    if (!empty($cat_recommend_res))
    {
        $cat_rec_array = array();
        foreach($cat_recommend_res as $cat_recommend_data)
        {
            $cat_rec[$cat_recommend_data['recommend_type']][] = array('cat_id' => $cat_recommend_data['cat_id'], 'cat_name' => $cat_recommend_data['cat_name']);
        }
        $smarty->assign('cat_rec', $cat_rec);
    }
    $pager=assign_pager('index',            $cat_id, $count, $size, $sort, $order, $page, '', $brand, $price_min, $price_max, $display, $filter_attr_str); // 分页
    $smarty->assign('page',$pager);
    /* 页面中的动态内容 */

    assign_dynamic('index');


    $smarty->display('index_new.dwt');
//$smarty->display('index.dwt');

/*------------------------------------------------------ */
//-- PRIVATE FUNCTIONS
/*------------------------------------------------------ */
function get_cat_list($cat_id)
{
    $return_cat_id=$cat_id;
    $sql="SELECT cat_id FROM ". $GLOBALS['ecs']->table('category'). " WHERE parent_id in (".$cat_id .")";
    $id=$GLOBALS['db']->getAll($sql);
    if($id){
        $a=array();
        foreach($id as $key =>$val){
            $a[]=$val['cat_id'];
        }
        $id=implode(",", $a);

        $return_cat_id=get_cat_list($id);
    }else{

        $return_cat_id=$cat_id;

    }
    return $return_cat_id;
}
function get_goods_list($cat_id,$type)
{
    $where=" 1=1";
    if($type=1){
        $where = " is_best =1 ";
    }elseif($type=2){
        $where = " is_new =1 ";
    }elseif($type=3){
        $where = " is_hot =1 ";
    }elseif($type=4){
        $where = " is_promote =1 ";
    }

    $cat_list=get_cat_list($cat_id);
    $sql="SELECT * FROM ". $GLOBALS['ecs']->table('goods'). " WHERE cat_id in (".$cat_list .")" ." And " .$where."  and is_delete=0 limit 4";
    $result=$GLOBALS['db']->getAll($sql);
    foreach ($result AS $idx => $row)
    {
        if ($row['promote_price'] > 0)
        {
            $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
            $goods[$idx]['promote_price'] = $promote_price > 0 ? price_format($promote_price) : '';
        }
        else
        {
            $goods[$idx]['promote_price'] = '';
        }

        $goods[$idx]['id']           = $row['goods_id'];
        $goods[$idx]['name']         = $row['goods_name'];
        $goods[$idx]['brief']        = $row['goods_brief'];
        $goods[$idx]['material_id']     = $row['material_id'];
        $goods[$idx]['brand_name']   = isset($goods_data['brand'][$row['goods_id']]) ? $goods_data['brand'][$row['goods_id']] : '';
        $goods[$idx]['goods_style_name']   = add_style($row['goods_name'],$row['goods_name_style']);

        $goods[$idx]['short_name']   = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
                                               sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
        $goods[$idx]['short_style_name']   = add_style($goods[$idx]['short_name'],$row['goods_name_style']);
        $goods[$idx]['market_price'] = price_format($row['market_price']);
        $goods[$idx]['shop_price']   = price_format($row['shop_price']);

        $goods[$idx]['thumb']        = "../".get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $goods[$idx]['goods_img']    = "../".get_image_path($row['goods_id'], $row['goods_img']);
        $goods[$idx]['url']          = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);
    }

    return $goods;

}
/**
 * 调用发货单查询
 *
 * @access  private
 * @return  array
 */
function index_get_invoice_query()
{
    $sql = 'SELECT o.order_sn, o.invoice_no, s.shipping_code FROM ' . $GLOBALS['ecs']->table('order_info') . ' AS o' .
            ' LEFT JOIN ' . $GLOBALS['ecs']->table('shipping') . ' AS s ON s.shipping_id = o.shipping_id' .
            " WHERE invoice_no > '' AND shipping_status = " . SS_SHIPPED .
            ' ORDER BY shipping_time DESC LIMIT 10';
    $all = $GLOBALS['db']->getAll($sql);

    foreach ($all AS $key => $row)
    {
        $plugin = ROOT_PATH . 'includes/modules/shipping/' . $row['shipping_code'] . '.php';

        if (file_exists($plugin))
        {
            include_once($plugin);

            $shipping = new $row['shipping_code'];
            $all[$key]['invoice_no'] = $shipping->query((string)$row['invoice_no']);
        }
    }

    clearstatcache();

    return $all;
}

/**
 * 获得最新的文章列表。
 *
 * @access  private
 * @return  array
 */
function index_get_new_articles()
{
    $sql = 'SELECT a.article_id, a.title, ac.cat_name, a.add_time, a.file_url, a.open_type, ac.cat_id, ac.cat_name ' .
            ' FROM ' . $GLOBALS['ecs']->table('article') . ' AS a, ' .
                $GLOBALS['ecs']->table('article_cat') . ' AS ac' .
            ' WHERE a.is_open = 1 AND a.cat_id = ac.cat_id AND ac.cat_type = 1' .
            ' ORDER BY a.article_type DESC, a.add_time DESC LIMIT ' . $GLOBALS['_CFG']['article_number'];
    $res = $GLOBALS['db']->getAll($sql);

    $arr = array();
    foreach ($res AS $idx => $row)
    {
        $arr[$idx]['id']          = $row['article_id'];
        $arr[$idx]['title']       = $row['title'];
        $arr[$idx]['short_title'] = $GLOBALS['_CFG']['article_title_length'] > 0 ?
                                        sub_str($row['title'], $GLOBALS['_CFG']['article_title_length']) : $row['title'];
        $arr[$idx]['cat_name']    = $row['cat_name'];
        $arr[$idx]['add_time']    = local_date($GLOBALS['_CFG']['date_format'], $row['add_time']);
        $arr[$idx]['url']         = $row['open_type'] != 1 ?
                                        build_uri('article', array('aid' => $row['article_id']), $row['title']) : trim($row['file_url']);
        $arr[$idx]['cat_url']     = build_uri('article_cat', array('acid' => $row['cat_id']), $row['cat_name']);
    }

    return $arr;
}

/**
 * 获得最新的团购活动
 *
 * @access  private
 * @return  array
 */
function index_get_group_buy()
{
    $time = gmtime();
    $limit = get_library_number('group_buy', 'index');
	
    $group_buy_list = array();
    if ($limit > 0)
    {
        $sql = 'SELECT gb.*,g.*,gb.act_id AS group_buy_id, gb.goods_id, gb.ext_info, gb.goods_name, g.goods_thumb, g.goods_img ' .
                'FROM ' . $GLOBALS['ecs']->table('goods_activity') . ' AS gb, ' .
                    $GLOBALS['ecs']->table('goods') . ' AS g ' .
                "WHERE gb.act_type = '" . GAT_GROUP_BUY . "' " .
                "AND g.goods_id = gb.goods_id " .
                "AND gb.start_time <= '" . $time . "' " .
                "AND gb.end_time >= '" . $time . "' " .
                "AND g.is_delete = 0 " .
                "ORDER BY gb.act_id DESC " .
                "LIMIT $limit" ;
				
        $res = $GLOBALS['db']->query($sql);

        while ($row = $GLOBALS['db']->fetchRow($res))
        {
            /* 如果缩略图为空，使用默认图片 */
            $row['goods_img'] = get_image_path($row['goods_id'], $row['goods_img']);
            $row['thumb'] = get_image_path($row['goods_id'], $row['goods_thumb'], true);

            /* 根据价格阶梯，计算最低价 */
            $ext_info = unserialize($row['ext_info']);
            $price_ladder = $ext_info['price_ladder'];
            if (!is_array($price_ladder) || empty($price_ladder))
            {
                $row['last_price'] = price_format(0);
            }
            else
            {
                foreach ($price_ladder AS $amount_price)
                {
                    $price_ladder[$amount_price['amount']] = $amount_price['price'];
                }
            }
            ksort($price_ladder);
            $row['last_price'] = price_format(end($price_ladder));
            $row['url'] = build_uri('group_buy', array('gbid' => $row['group_buy_id']));
            $row['short_name']   = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
                                           sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
            $row['short_style_name']   = add_style($row['short_name'],'');
			
			$stat = group_buy_stat($row['act_id'], $row['deposit']);
			$row['valid_goods'] = $stat['valid_goods'];
            $group_buy_list[] = $row;
        }
    }

    return $group_buy_list;
}

/**
 * 取得拍卖活动列表
 * @return  array
 */
function index_get_auction()
{
    $now = gmtime();
    $limit = get_library_number('auction', 'index');
    $sql = "SELECT a.act_id, a.goods_id, a.goods_name, a.ext_info, g.goods_thumb ".
            "FROM " . $GLOBALS['ecs']->table('goods_activity') . " AS a," .
                      $GLOBALS['ecs']->table('goods') . " AS g" .
            " WHERE a.goods_id = g.goods_id" .
            " AND a.act_type = '" . GAT_AUCTION . "'" .
            " AND a.is_finished = 0" .
            " AND a.start_time <= '$now'" .
            " AND a.end_time >= '$now'" .
            " AND g.is_delete = 0" .
            " ORDER BY a.start_time DESC" .
            " LIMIT $limit";
    $res = $GLOBALS['db']->query($sql);

    $list = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $ext_info = unserialize($row['ext_info']);
        $arr = array_merge($row, $ext_info);
        $arr['formated_start_price'] = price_format($arr['start_price']);
        $arr['formated_end_price'] = price_format($arr['end_price']);
        $arr['thumb'] = get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $arr['url'] = build_uri('auction', array('auid' => $arr['act_id']));
        $arr['short_name']   = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
                                           sub_str($arr['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $arr['goods_name'];
        $arr['short_style_name']   = add_style($arr['short_name'],'');
        $list[] = $arr;
    }

    return $list;
}

/**
 * 获得所有的友情链接
 *
 * @access  private
 * @return  array
 */
function index_get_links()
{
    $sql = 'SELECT link_logo, link_name, link_url FROM ' . $GLOBALS['ecs']->table('friend_link') . ' ORDER BY show_order';
    $res = $GLOBALS['db']->getAll($sql);

    $links['img'] = $links['txt'] = array();

    foreach ($res AS $row)
    {
        if (!empty($row['link_logo']))
        {
            $links['img'][] = array('name' => $row['link_name'],
                                    'url'  => $row['link_url'],
                                    'logo' => $row['link_logo']);
        }
        else
        {
            $links['txt'][] = array('name' => $row['link_name'],
                                    'url'  => $row['link_url']);
        }
    }

    return $links;
}



function get_flash_xml()
{
    $flashdb = array();
    if (file_exists(ROOT_PATH . DATA_DIR . '/flash_data.xml'))
    {

        // 兼容v2.7.0及以前版本
        if (!preg_match_all('/item_url="([^"]+)"\slink="([^"]+)"\stext="([^"]*)"\ssort="([^"]*)"/', file_get_contents(ROOT_PATH . DATA_DIR . '/flash_data.xml'), $t, PREG_SET_ORDER))
        {
            preg_match_all('/item_url="([^"]+)"\slink="([^"]+)"\stext="([^"]*)"/', file_get_contents(ROOT_PATH . DATA_DIR . '/flash_data.xml'), $t, PREG_SET_ORDER);
        }

        if (!empty($t))
        {
            foreach ($t as $key => $val)
            {
                $val[4] = isset($val[4]) ? $val[4] : 0;
                $flashdb[] = array('src'=>$val[1],'url'=>$val[2],'text'=>$val[3],'sort'=>$val[4]);
				
				//print_r($flashdb);
            }
        }
    }
    return $flashdb;
}
//yyy添加start
function get_wap_advlist( $position, $num )
{
		$arr = array( );
		$sql = "select ap.ad_width,ap.ad_height,ad.ad_id,ad.ad_name,ad.ad_code,ad.ad_link,ad.ad_id from ".$GLOBALS['ecs']->table( "ecsmart_ad_position" )." as ap left join ".$GLOBALS['ecs']->table( "ecsmart_ad" )." as ad on ad.position_id = ap.position_id where ap.position_name='".$position.( "' and UNIX_TIMESTAMP()>ad.start_time and UNIX_TIMESTAMP()<ad.end_time and ad.enabled=1 limit ".$num );
		$res = $GLOBALS['db']->getAll( $sql );
		foreach ( $res as $idx => $row )
		{
				$arr[$row['ad_id']]['name'] = $row['ad_name'];
				$arr[$row['ad_id']]['url'] = "affiche.php?ad_id=".$row['ad_id']."&uri=".$row['ad_link'];
				$arr[$row['ad_id']]['image'] = "data/afficheimg/".$row['ad_code'];
				$arr[$row['ad_id']]['content'] = "<a href='".$arr[$row['ad_id']]['url']."' target='_blank'><img src='data/afficheimg/".$row['ad_code']."' width='".$row['ad_width']."' height='".$row['ad_height']."' /></a>";
				$arr[$row['ad_id']]['ad_code'] = $row['ad_code'];
		}
		return $arr;
}
//yyy添加end
//yyy手机start
function get_is_computer(){
$is_computer=$_REQUEST['is_computer'];
return $is_computer;
}
//yyy手机end

function get_menu()
{
	$sql = "select * from ".$GLOBALS['ecs']->table('ecsmart_menu')." order by sort";
	$list = $GLOBALS['db']->getAll($sql);
	$arr = array();
	foreach($list as $key => $rows)
	{
		$arr[$key]['id'] = $rows['id'];
		$arr[$key]['menu_name'] = $rows['menu_name'];
		$arr[$key]['menu_img'] = $rows['menu_img'];
		$arr[$key]['menu_url'] = $rows['menu_url']; 
	} 
	return $arr;
}
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
 * 获得分类下的商品
 *
 * @access  public
 * @param   string  $children
 * @return  array
 */
function category_get_goods($children, $ext, $size, $page, $sort, $order)
{


	$display = $GLOBALS['display'];
	$where = "g.is_on_sale = 1 AND g.is_alone_sale = 1 AND ".
			"g.is_delete = 0 AND ($children OR " . get_extension_goods($children) . ')';
	/* 获得商品列表 */
	$sql = 'SELECT g.goods_id, g.goods_name, g.goods_name_style, g.market_price, g.is_new, g.is_best, g.is_hot, g.shop_price AS org_price, ' .
			"IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, g.promote_price, g.goods_type, " .
			'g.promote_start_date, g.promote_end_date, g.goods_brief, g.goods_thumb , g.goods_img ' .
			'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
			'LEFT JOIN ' . $GLOBALS['ecs']->table('member_price') . ' AS mp ' .
			"ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' " .
			" $ext ORDER BY $sort $order";
	$res = $GLOBALS['db']->selectLimit($sql, $size, ($page - 1) * $size);

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
			$arr[$row['goods_id']]['watermark_img'] =  $watermark_img;
		}

		$arr[$row['goods_id']]['goods_id']         = $row['goods_id'];
		if($display == 'grid')
		{
			$arr[$row['goods_id']]['goods_name']       = $GLOBALS['_CFG']['goods_name_length'] > 0 ? sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
		}
		else
		{
			$arr[$row['goods_id']]['goods_name']       = $row['goods_name'];
		}
		$arr[$row['goods_id']]['name']             = $row['goods_name'];
		$arr[$row['goods_id']]['goods_brief']      = $row['goods_brief'];
		$arr[$row['goods_id']]['goods_style_name'] = add_style($row['goods_name'],$row['goods_name_style']);
		$arr[$row['goods_id']]['market_price']     = price_format($row['market_price']);
		$arr[$row['goods_id']]['shop_price']       = price_format($row['shop_price']);
		$arr[$row['goods_id']]['type']             = $row['goods_type'];
		$arr[$row['goods_id']]['promote_price']    = ($promote_price > 0) ? price_format($promote_price) : '';
		$arr[$row['goods_id']]['goods_thumb']      =isset($row['goods_thumb'])?"../".get_image_path($row['goods_id'], $row['goods_thumb'], true):'';
		$arr[$row['goods_id']]['goods_img']        = isset($row['goods_img'])?"../".get_image_path($row['goods_id'], $row['goods_img']):'';
		$arr[$row['goods_id']]['url']              = build_uri('goods', array('gid'=>$row['goods_id']), $row['goods_name']);
	}

	return $arr;
}
/*
 *get_goods_info 获得指定分类下面的商品
 * @access  public
 * @param   string  $children
 * @return  array
 */
function get_goods_info_by_cat($cat_id, $size, $page, $sort, $order, $extension_code)
{
    $where = " WHERE g.is_delete = 0 ";
    if(!empty($cat_id)){
        $where.="AND g.cat_id=$cat_id";
    }
    if(!empty($extension_code)){
        $where.=" AND g.style_id=$extension_code";        
    }
    $display = $GLOBALS['display'];
    
    /* 获得商品列表 */
    $sql = 'SELECT g.goods_id, g.goods_name, g.goods_name_style, g.market_price, g.is_new, g.is_best, g.is_hot, g.shop_price AS org_price, g.shop_price AS shop_price, g.promote_price, g.goods_type, 
            g.promote_start_date, g.promote_end_date, g.goods_brief, g.goods_thumb , g.goods_img ' .
            'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g '." $where AND g.is_on_sale=1 ORDER BY $sort $order";
    //ykq_update_AND g.is_on_sale=1xian显示上架商品
    $res = $GLOBALS['db']->selectLimit($sql, $size, ($page - 1) * $size);
    //echo $sql;
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
            $arr[$row['goods_id']]['watermark_img'] =  $watermark_img;
        }
    
        $arr[$row['goods_id']]['goods_id']         = $row['goods_id'];
        if($display == 'grid')
        {
            $arr[$row['goods_id']]['goods_name']       = $GLOBALS['_CFG']['goods_name_length'] > 0 ? sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
        }
        else
        {
            $arr[$row['goods_id']]['goods_name']       = $row['goods_name'];
        }
        $arr[$row['goods_id']]['name']             = $row['goods_name'];
        $arr[$row['goods_id']]['goods_brief']      = $row['goods_brief'];
        $arr[$row['goods_id']]['goods_style_name'] = add_style($row['goods_name'],$row['goods_name_style']);
        $arr[$row['goods_id']]['market_price']     = price_format($row['market_price']);
        $arr[$row['goods_id']]['shop_price']       = price_format($row['shop_price']);
        $arr[$row['goods_id']]['type']             = $row['goods_type'];
        $arr[$row['goods_id']]['promote_price']    = ($promote_price > 0) ? price_format($promote_price) : '';
        $arr[$row['goods_id']]['goods_thumb']      =isset($row['goods_thumb'])?"../".get_image_path($row['goods_id'], $row['goods_thumb'], true):'';
        $arr[$row['goods_id']]['goods_img']        = isset($row['goods_img'])?"../".get_image_path($row['goods_id'], $row['goods_img']):'';
        $arr[$row['goods_id']]['url']              = build_uri('goods', array('gid'=>$row['goods_id']), $row['goods_name']);
    }
    
    return $arr;
}
/**
 * 获得分类下的商品总数
 *
 * @access  public
 * @param   string     $cat_id
 * @return  integer
 */
function get_cagtegory_goods_count($cat_id, $brand = 0, $min = 0, $max = 0, $ext='',$extension_code)
{
    $where = " g.is_delete = 0 ";
    if(!empty($cat_id)){
        $where.="AND g.cat_id=$cat_id";
    }
    if(!empty($extension_code)){
        $where.=" AND g.style_id=$extension_code";        
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

    /* 返回商品总数 */
    return $GLOBALS['db']->getOne('SELECT COUNT(*) FROM ' . $GLOBALS['ecs']->table('goods') . " AS g WHERE $where $ext");
}
/**
 * 获得某个分类下的商品总数
 */
function  get_goods_nums($type){
    $type='is_'.$type;
    return $GLOBALS['db']->getOne('SELECT count(*) FROM '.$GLOBALS['ecs']->table('goods')." WHERE 
is_delete = 0 AND is_on_sale = 1 AND is_alone_sale = 1 AND $type=1");
    
}
?>