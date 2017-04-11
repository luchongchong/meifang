<?php

/**
 *  壁画页面
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}
$category_id = isset($_REQUEST['id'])  ? intval($_REQUEST['id']) : 0;
/*------------------------------------------------------ */
//-- 判断是否存在缓存，如果存在则调用缓存，反之读取相应内容
/*------------------------------------------------------ */
/* 缓存编号 */
$cache_id = sprintf('%X', crc32($_SESSION['user_rank'] . '-' . $_CFG['lang']));
if (!$smarty->is_cached('curtain.dwt', $cache_id)){

    //每个风格各显示3个商品，1.田园风格,2.欧式风格3,现代风格,4中式风格
    //田园风格
    $smarty->assign('countryside_goods',       get_goods_list(43,0,3));

    $smarty->assign('european_goods',       get_goods_list(43,0,3));

    $smarty->assign('modern_goods',       get_goods_list(43,0,3));

    $smarty->assign('china_goods',       get_goods_list(43,0,3));

    $smarty->display('curtain.dwt', $cache_id);
}

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
/**
 *  author:royallu
 *  funciton:取出该分类下的商品，参数,cat_id,type,show_num
 *  return arr
 * */
function get_goods_list($cat_id,$type,$show_num)
{
    $where=" is_delete=0";
    if($type==1){
        $where .= " and is_best =1 ";
    }elseif($type==2){
        $where .= " and is_new =1 ";
    }elseif($type==3){
        $where .= " and is_hot =1 ";
    }elseif($type==4){
        $where .= " and is_promote =1 ";
    }
    $cat_list=get_cat_list($cat_id);
    $sql="SELECT * FROM ". $GLOBALS['ecs']->table('goods'). " WHERE cat_id in (".$cat_list .")" ." And " .$where."  limit $show_num";

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
        $goods[$idx]['shop_price']   = $row['shop_price'];

        $goods[$idx]['thumb']        = "../".get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $goods[$idx]['goods_img']    = "../".get_image_path($row['goods_id'], $row['goods_img']);
        $goods[$idx]['url']          = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);
    }

    return $goods;

}




?>