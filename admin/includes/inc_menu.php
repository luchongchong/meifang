<?php

/**
 * ECSHOP 管理中心菜单数组
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: inc_menu.php 17217 2011-01-19 06:29:08Z liubo $
*/

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

$modules['02_cat_and_goods']['01_goods_list']       = 'goods.php?act=list';         // 商品列表
$modules['02_cat_and_goods']['02_goods_add']        = 'goods.php?act=add';          // 添加商品
$modules['02_cat_and_goods']['03_category_list']    = 'category.php?act=list';
$modules['02_cat_and_goods']['05_comment_manage']   = 'comment_manage.php?act=list';
$modules['02_cat_and_goods']['06_goods_brand_list'] = 'brand.php?act=list';
$modules['02_cat_and_goods']['08_goods_type']       = 'goods_type.php?act=manage';
$modules['02_cat_and_goods']['11_goods_trash']      = 'goods.php?act=trash';        // 商品回收站
$modules['02_cat_and_goods']['12_batch_pic']        = 'picture_batch.php';
$modules['02_cat_and_goods']['13_batch_add']        = 'goods_batch.php?act=add';    // 商品批量上传
$modules['02_cat_and_goods']['14_goods_export']     = 'goods_export.php?act=goods_export';
$modules['02_cat_and_goods']['15_batch_edit']       = 'goods_batch.php?act=select'; // 商品批量修改
//$modules['02_cat_and_goods']['16_goods_script']     = 'gen_goods_script.php?act=setup';
$modules['02_cat_and_goods']['17_tag_manage']       = 'tag_manage.php?act=list';
//$modules['02_cat_and_goods']['50_virtual_card_list']   = 'goods.php?act=list&extension_code=virtual_card';
//$modules['02_cat_and_goods']['51_virtual_card_add']    = 'goods.php?act=add&extension_code=virtual_card';
//$modules['02_cat_and_goods']['52_virtual_card_change'] = 'virtual_card.php?act=change';
//$modules['02_cat_and_goods']['goods_auto']             = 'goods_auto.php?act=list';

//erp系统
$modules['044_erp']['01_purchase_order']          = 'purchase_order.php?act=list';//采购订单
$modules['044_erp']['02_purchase_order_examine']          = 'purchase_order_examine.php?act=list';//采购订单申请
$modules['044_erp']['03_order_payment']          = 'purchase_order_payment.php?act=list';//付款画面
$modules['044_erp']['04_order_shipping_handler']          = 'purchase_shipping_handler.php?act=list';//发货处理

$modules['03_promotion']['02_snatch_list']          = 'snatch.php?act=list';
$modules['03_promotion']['04_bonustype_list']       = 'bonus.php?act=list';
$modules['03_promotion']['06_pack_list']            = 'pack.php?act=list';
$modules['03_promotion']['07_card_list']            = 'card.php?act=list';
$modules['03_promotion']['08_group_buy']            = 'group_buy.php?act=list';
$modules['03_promotion']['09_topic']                = 'topic.php?act=list';
$modules['03_promotion']['10_auction']              = 'auction.php?act=list';
$modules['03_promotion']['12_favourable']           = 'favourable.php?act=list';
$modules['03_promotion']['13_wholesale']            = 'wholesale.php?act=list';
$modules['03_promotion']['14_package_list']         = 'package.php?act=list';
//$modules['03_promotion']['ebao_commend']            = 'ebao_commend.php?act=list';
$modules['03_promotion']['15_exchange_goods']       = 'exchange_goods.php?act=list';


$modules['04_order']['02_order_list']               = 'order.php?act=list';
$modules['04_order']['11_order_find']               = 'order_status.php?act=list';//物流状态查询
//$modules['04_order']['03_order_query']              = 'order.php?act=order_query';
 //$modules['04_order']['04_merge_order']              = 'order.php?act=merge';
 //$modules['04_order']['05_edit_order_print']         = 'order.php?act=templates';
//$modules['04_order']['06_undispose_booking']        = 'goods_booking.php?act=list_all';
//$modules['04_order']['07_repay_application']        = 'repay.php?act=list_all';
//$modules['04_order']['add_order']                = 'order.php?act=add';
$modules['04_order']['08_add_order']                = 'order_add.php?act=add';
 //$modules['04_order']['09_delivery_order']           = 'order.php?act=delivery_list';
 //$modules['04_order']['10_back_order']               = 'order.php?act=back_list';

//$modules['04_order']['sample_manage_list'] = "sample.php?act=list";
$modules['04_order']['sample_manage_list'] = "order_management.php?act=list";//订单管理
//$modules['04_order']['sample_manage_add'] = "sample.php?act=add";

//$modules['04_order']['order_management'] = "order_management.php?act=list";//订单管理


$modules['05_banner']['ad_position']                = 'ad_position.php?act=list';
$modules['05_banner']['ad_list']                    = 'ads.php?act=list';

$modules['06_stats']['flow_stats']                  = 'flow_stats.php?act=view';
$modules['06_stats']['searchengine_stats']          = 'searchengine_stats.php?act=view';
$modules['06_stats']['z_clicks_stats']              = 'adsense.php?act=list';
$modules['06_stats']['report_guest']                = 'guest_stats.php?act=list';
$modules['06_stats']['report_order']                = 'order_stats.php?act=list';
$modules['06_stats']['report_sell']                 = 'sale_general.php?act=list';
$modules['06_stats']['sale_list']                   = 'sale_list.php?act=list';
$modules['06_stats']['collect_detail']              = 'collect_detail.php?act=list';
$modules['06_stats']['sell_stats']                  = 'sale_order.php?act=goods_num';
$modules['06_stats']['report_users']                = 'users_order.php?act=order_num';
$modules['06_stats']['visit_buy_per']               = 'visit_sold.php?act=list';

$modules['07_content']['03_article_list']           = 'article.php?act=list';
$modules['07_content']['02_articlecat_list']        = 'articlecat.php?act=list';
$modules['07_content']['vote_list']                 = 'vote.php?act=list';
$modules['07_content']['article_auto']              = 'article_auto.php?act=list';
//$modules['07_content']['shop_help']                 = 'shophelp.php?act=list_cat';
//$modules['07_content']['shop_info']                 = 'shopinfo.php?act=list';

/**
 *  author:royallu
 *  funciton:添加粉丝数据排名菜单
 * */
$modules['08_members']['03_users_list']             = 'users.php?act=list';
$modules['08_members']['04_users_add']              = 'users.php?act=add';
$modules['08_members']['09_user_account']           = 'user_account.php?act=list';
$modules['08_members']['11_user_fan_data_ranking']  = 'user_fans_data_rank.php?act=list';

$modules['10_priv_admin']['admin_logs']             = 'admin_logs.php?act=list';
$modules['10_priv_admin']['admin_list']             = 'privilege.php?act=list';
$modules['10_priv_admin']['admin_role']             = 'role.php?act=list';
$modules['10_priv_admin']['agency_list']            = 'agency.php?act=list';
$modules['10_priv_admin']['suppliers_list']         = 'suppliers.php?act=list'; // 供货商

$modules['11_system']['01_shop_config']             = 'shop_config.php?act=list_edit';
$modules['11_system']['shop_authorized']             = 'license.php?act=list_edit';
$modules['11_system']['02_payment_list']            = 'payment.php?act=list';
$modules['11_system']['03_shipping_list']           = 'shipping.php?act=list';
$modules['11_system']['04_mail_settings']           = 'shop_config.php?act=mail_settings';
$modules['11_system']['05_area_list']               = 'area_manage.php?act=list';
//$modules['11_system']['06_plugins']                 = 'plugins.php?act=list';
$modules['11_system']['07_cron_schcron']            = 'cron.php?act=list';
$modules['11_system']['08_friendlink_list']         = 'friend_link.php?act=list';
$modules['11_system']['sitemap']                    = 'sitemap.php';
$modules['11_system']['check_file_priv']            = 'check_file_priv.php?act=check';
$modules['11_system']['captcha_manage']             = 'captcha_manage.php?act=main';
$modules['11_system']['ucenter_setup']              = 'integrate.php?act=setup&code=ucenter';
$modules['11_system']['flashplay']                  = 'flashplay.php?act=list';
$modules['11_system']['navigator']                  = 'navigator.php?act=list';
$modules['11_system']['file_check']                 = 'filecheck.php';
//$modules['11_system']['fckfile_manage']             = 'fckfile_manage.php?act=list';
$modules['11_system']['021_reg_fields']             = 'reg_fields.php?act=list';


// $modules['12_template']['02_template_select']       = 'template.php?act=list';
// $modules['12_template']['03_template_setup']        = 'template.php?act=setup';
// $modules['12_template']['04_template_library']      = 'template.php?act=library';
// $modules['12_template']['05_edit_languages']        = 'edit_languages.php?act=list';
// $modules['12_template']['06_template_backup']       = 'template.php?act=backup_setting';
// $modules['12_template']['mail_template_manage']     = 'mail_template.php?act=list';


$modules['13_backup']['02_db_manage']               = 'database.php?act=backup';
$modules['13_backup']['03_db_optimize']             = 'database.php?act=optimize';
$modules['13_backup']['04_sql_query']               = 'sql.php?act=main';
//update by lifei  2016/7/19   add  a  user_factory_backup 
//$modules['13_backup']['05_db_reg_fields']           = 'user_c.php?act=register';
$modules['13_backup']['convert']                    = 'convert.php?act=main';

// $modules['14_school']['01_school']       = 'http://www.68ecshop.com/study/index.htm';
// $modules['14_school']['02_school_a']     = 'http://www.68ecshop.com/article_cat-26.html';
// $modules['14_school']['03_school_b']      = 'http://www.68ecshop.com/article_cat-31.html';
// $modules['14_school']['04_school_c']      = 'http://bbs.68ecshop.com/forum.php';

//$modules['15_sms']['02_sms_my_info']                = 'sms.php?act=display_my_info';
$modules['15_sms']['03_sms_send']                   = 'sms.php?act=display_send_ui';
$modules['15_sms']['04_sms_sign']                   = 'sms.php?act=sms_sign';
//$modules['15_sms']['04_sms_charge']                 = 'sms.php?act=display_charge_ui';
//$modules['15_sms']['05_sms_send_history']           = 'sms.php?act=display_send_history_ui';
//$modules['15_sms']['06_sms_charge_history']         = 'sms.php?act=display_charge_history_ui';
//门店添加
$modules['21_mendian_manage']['01_mendian_list']              = 'store.php?act=list';
$modules['21_mendian_manage']['02_mendian_add']              = 'store.php?act=add';
// $modules['21_mendian_manage']['03_mendian_clerk']              = 'store.php?act=clerkadd';
//施工队添加
//$modules['22_shigongdui_manage']['01_shigongdui_list']='construction.php?act=list';
// $modules['22_shigongdui_manage']['01_shigongdui_add']='construction.php?act=add';

// $modules['16_rec']['affiliate']                     = 'affiliate.php?act=list';
// $modules['16_rec']['affiliate_ck']                  = 'affiliate_ck.php?act=list';

$modules['17_email_manage']['email_list']           = 'email_list.php?act=list';
$modules['17_email_manage']['magazine_list']        = 'magazine_list.php?act=list';
$modules['17_email_manage']['attention_list']       = 'attention_list.php?act=list';
$modules['17_email_manage']['view_sendlist']        = 'view_sendlist.php?act=list';

//$modules['21_mendian_manage']['03_mendian_yuyue']              = 'store_bespeak.php?act=list';
//分利管理
$modules['23_affiliate']['01_affiliate_list']       = 'affiliate.php?act=list';//分利策略
$modules['23_affiliate']['02_affiliate_details']       = 'affiliate.php?act=details';//分利明细

//工厂发货管理
$modules['factory_delivery']['factory_delivery_list']       = 'user_p.php?act=list';//工厂发货

//主题管理
$modules['24_theme']['01_theme_list']       = 'theme.php?act=list';//主题列表
$modules['24_theme']['02_theme_add']        = 'theme.php?act=add';//添加主题

//样本订单管理
//$modules['sample_manage']['sample_manage_list'] = "sample.php?act=list";
//$modules['sample_manage']['sample_manage_add'] = "sample.php?act=add";

//费用管理
$modules['cost_management']['ecs_subject'] = "ecs_subject.php?act=manage";
$modules['cost_management']['ecs_person'] = "ecs_person.php?act=manage";
$modules['cost_management']['ecs_fee'] = "ecs_fee.php?act=list";


//银行账目
$modules['bank_management']['bank_account'] = "bank_account.php?act=list";
$modules['bank_management']['bank_cost_name'] = "bank_cost_name.php?act=manage";
$modules['bank_management']['bank_department'] = "bank_department.php?act=manage";
$modules['bank_management']['bank_name'] = "bank_name.php?act=manage";

$modules['goods_warehousing']['goods_warehousings'] = "ecs_goods_storage.php?act=list";
//工厂发货管理
$modules['goods_warehousing']['sample_delivery_list']       = 'sample_p.php?act=list';//样本发货
?>
