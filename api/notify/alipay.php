<?php
/**
 * ECTouch E-Commerce Project
 * ============================================================================
 * Copyright (c) 2012-2014 http://ectouch.cn All rights reserved.
 * ----------------------------------------------------------------------------
 * 文件名称：notify_url.php
 * ----------------------------------------------------------------------------
 * 手机支付宝支付异步通知处理
 * ----------------------------------------------------------------------------
 * Licensed ( http://www.ectouch.cn/docs/license.txt )
 * ----------------------------------------------------------------------------
 */
define('IN_ECS', true);
define('CONTROLLER_NAME', 'Respond');

$params['type'] = 1;
$params['code'] = 'alipay';
$code = base64_encode(serialize($params));
$code = str_replace(array('+', '/', '='), array('-', '_', ''), $code);
$_GET['code'] = $code;
/* 加载核心文件 */
$ROOT_PATH = str_replace('\\', '/', dirname(__FILE__) . '/../../');
//require($ROOT_PATH . 'include/EcTouch.php');
require($ROOT_PATH.'index.php');