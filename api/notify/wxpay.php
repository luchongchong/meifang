<?php

/**
 * ECTouch E-Commerce Project
 * ============================================================================
 * Copyright (c) 2014-2015 http://ectouch.cn All rights reserved.
 * ----------------------------------------------------------------------------
 * 文件名称：wxpay.php
 * ----------------------------------------------------------------------------
 * 功能描述：微信支付异步通知文件
 * ----------------------------------------------------------------------------
 * Licensed ( http://www.ectouch.cn/license.txt )
 * ----------------------------------------------------------------------------
 */

define('IN_ECTOUCH', true);
define('CONTROLLER_NAME', 'Respond');

$params['type'] = 1;
$params['code'] = 'wxpay';
$params['postStr'] = $GLOBALS["HTTP_RAW_POST_DATA"];
$code = base64_encode(serialize($params));
$code = str_replace(array('+', '/', '='), array('-', '_', ''), $code);
$_GET['code'] = $code;
/* 加载核心文件 */
$ROOT_PATH = str_replace('\\', '/', dirname(__FILE__) . '/../../');
require($ROOT_PATH . 'include/EcTouch.php');
