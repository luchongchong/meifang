<?php

/**
 * MEIFANG 邮局汇款插件
 * ============================================================================ 
 * 版权所有 2005-2014 上海优辉商务，并保留所有权利。
 * 网站地址: http://www.j345.net
 * ----------------------------------------------------------------------------
 * 优辉网络,共创你我
 * ============================================================================
 * $Author: liubo $
 * $Id: post.php 17217 2011-01-19 06:29:08Z liubo $
 */

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

$payment_lang = ROOT_PATH . 'languages/' .$GLOBALS['_CFG']['lang']. '/payment/post.php';

if (file_exists($payment_lang))
{
    global $_LANG;

    include_once($payment_lang);
}

/* 模块的基本信息 */
if (isset($set_modules) && $set_modules == TRUE)
{
    $i = isset($modules) ? count($modules) : 0;

    /* 代码 */
    $modules[$i]['code']    = basename(__FILE__, '.php');

    /* 描述对应的语言项 */
    $modules[$i]['desc']    = 'post_desc';

    /* 是否支持货到付款 */
    $modules[$i]['is_cod']  = '0';

    /* 是否支持在线支付 */
    $modules[$i]['is_online']  = '0';

    /* 作者 */
    $modules[$i]['author']  = 'MEIFANG TEAM';

    /* 网址 */
    $modules[$i]['website'] = 'http://www.ecshop.com';

    /* 版本号 */
    $modules[$i]['version'] = '1.0.0';

    /* 配置信息 */
    $modules[$i]['config']  = array();

    return;
}

/**
 * 类
 */
class post
{
    /**
     * 构造函数
     *
     * @access  public
     * @param
     *
     * @return void
     */


    function __construct()
    {
        $this->post();
    }

    /**
     * 提交函数
     */
    function get_code()
    {
        return '';
    }

    /**
     * 处理函数
     */
    function response()
    {
        return;
    }
}

?>