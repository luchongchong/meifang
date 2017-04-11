<?php

/**
 * ECSHOP 支付宝插件
 * ============================================================================
 * $Author: royallu $
 * $Id: alipay.php 17217 2016-11-9 11:16:08Z royallu $
 */

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}
$payment_lang = ROOT_PATH . 'languages/' .$GLOBALS['_CFG']['lang']. '/payment/alipay.php';

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
    $modules[$i]['desc']    = 'alipay_desc';

    /* 是否支持货到付款 */
    $modules[$i]['is_cod']  = '0';

    /* 是否支持在线支付 */
    $modules[$i]['is_online']  = '1';

    /* 作者 */
    $modules[$i]['author']  = 'Lu chongchong';

    /* 网址 */
    $modules[$i]['website'] = 'http://www.alipay.com';

    /* 版本号 */
    $modules[$i]['version'] = '3.0.0';

    /* 配置信息 */
    $modules[$i]['config']  = array(
        array('name' => 'alipay_account',           'type' => 'text',   'value' => ''),
        array('name' => 'alipay_key',               'type' => 'text',   'value' => ''),
        array('name' => 'alipay_partner',           'type' => 'text',   'value' => ''),
//        array('name' => 'alipay_real_method',       'type' => 'select', 'value' => '0'),
//        array('name' => 'alipay_virtual_method',    'type' => 'select', 'value' => '0'),
//        array('name' => 'is_instant',               'type' => 'select', 'value' => '0')
      //  array('name' => 'alipay_pay_method',        'type' => 'select', 'value' => '')
    );

    return;
}
/**
 *  author:royallu
 *  funciton:支付支付类
 *  time:20161109
 * */
/**
 * 支付插件类
 */
class alipay
{
    static public $way = 0;
//    function __construct()
//    {
//        $this->alipay();
//    }
    /**
     * 手动设置访问方式
     * @param type $way
     */
    static public function setWay($way) {
        self::$way = intval($way);
    }
    /**
     * 生成支付代码
     *
     * @param array $order
     *            订单信息
     * @param array $payment
     *            支付方式信息
     */
    function get_code($order, $payment)
    {

        if (! defined('EC_CHARSET')) {
            $charset = 'utf-8';
        } else {
            $charset = EC_CHARSET;
        }

        $gateway = 'http://wappaygw.alipay.com/service/rest.htm?';
        // 请求业务数据
        $req_data = '<direct_trade_create_req>' . '<subject>' . $order['order_sn'] . '</subject>' . '<out_trade_no>' . $order['order_sn'] . 'O' . $order['log_id'] . '</out_trade_no>' . '<total_fee>' . $order['order_amount'] . '</total_fee>' . '<seller_account_name>' . $payment['alipay_account'] . '</seller_account_name>' . '<call_back_url>' . return_url(basename(__FILE__, '.php'), array('type'=>0)) . '</call_back_url>' . '<notify_url>' . $_SERVER ['HTTP_HOST'].'/api/notify/alipay.php' . '</notify_url>' . '<out_user>' . $order['consignee'] . '</out_user>' . '<merchant_url>' . $_SERVER ['HTTP_HOST'] . '</merchant_url>' . '<pay_expire>3600</pay_expire>' . '</direct_trade_create_req>';
        $parameter = array(
            'service' => 'alipay.wap.trade.create.direct', // 接口名称
            'format' => 'xml', // 请求参数格式
            'v' => '2.0', // 接口版本号
            'partner' => $payment['alipay_partner'], // 合作者身份ID
            'req_id' => $order['order_sn'] . $order['log_id'], // 请求号，唯一
            'sec_id' => 'MD5', // 签名方式
            'req_data' => $req_data, // 请求业务数据
            "_input_charset" => $charset
        );

        ksort($parameter);
        reset($parameter);
        $param = '';
        $sign = '';

        foreach ($parameter as $key => $val) {
            $param .= "$key=" . urlencode($val) . "&";
            $sign .= "$key=$val&";
        }

        $param = substr($param, 0, - 1);
        $sign = substr($sign, 0, - 1) . $payment['alipay_key'];

        // 请求授权接口
        //加载这个类
        $result =$this->doPost($gateway, $param . '&sign=' . md5($sign));

        $result = urldecode($result); // URL转码
        $result_array = explode('&', $result); // 根据 & 符号拆分
        // 重构数组
        $new_result_array = $temp_item = array();
        if (is_array($result_array)) {
            foreach ($result_array as $vo) {
                $temp_item = explode('=', $vo, 2); // 根据 & 符号拆分
                $new_result_array[$temp_item[0]] = $temp_item[1];
            }
        }
        $xml = simplexml_load_string($new_result_array['res_data']);
        $request_token = (array) $xml->request_token;
        // 请求交易接口
        $parameter = array(
            'service' => 'alipay.wap.auth.authAndExecute', // 接口名称
            'format' => 'xml', // 请求参数格式
            'v' => $new_result_array['v'], // 接口版本号
            'partner' => $new_result_array['partner'], // 合作者身份ID
            'sec_id' => $new_result_array['sec_id'],
            'req_data' => '<auth_and_execute_req><request_token>' . $request_token[0] . '</request_token></auth_and_execute_req>',
            'request_token' => $request_token[0],
            '_input_charset' => $charset
        );

        ksort($parameter);
        reset($parameter);
        $param = '';
        $sign = '';

        foreach ($parameter as $key => $val) {
            $param .= "$key=" . urlencode($val) . "&";
            $sign .= "$key=$val&";
        }

        $param = substr($param, 0, - 1);
        $sign = substr($sign, 0, - 1) . $payment['alipay_key'];

        /* 生成支付按钮 */
        $button = '<script type="text/javascript" src="'.'http://'.$_SERVER ['HTTP_HOST'].'/mobile/data/common/js/ap.js"></script><div class="option_zjb"><input type="button" class="btn buy radius5" onclick="javascript:_AP.pay(\'' . $gateway . $param . '&sign=' . md5($sign) . '\')" value="立即使用支付宝支付" class="c-btn3" /></div>';

        return $button;
    }
//    function get_payment($code) {
//        $sql = 'SELECT * FROM ' . $this->pre . "payment WHERE pay_code = '$code' AND enabled = '1'";
//        $payment = $this->row($sql);
//
//        if ($payment) {
//            $config_list = unserialize($payment['pay_config']);
//
//            foreach ($config_list AS $config) {
//                $payment[$config['name']] = $config['value'];
//            }
//        }
//        return $payment;
//    }
    /**
     * 手机支付宝同步响应操作
     *
     * @return boolean
     */
//    public function callback($data)
//    {
//        if (! empty($_GET)) {
//            $out_trade_no = explode('O', $_GET['out_trade_no']);
//            $log_id = $out_trade_no[1];
//           // $payment = model('Payment')->get_payment($data['code']);
//            $payment=get_payment($data['code']);
//            /* 检查数字签名是否正确 */
//            ksort($_GET);
//            reset($_GET);
//
//            $sign = '';
//            foreach ($_GET as $key => $val) {
//                if ($key != 'sign' && $key != 'sign_type' && $key != 'code') {
//                    $sign .= "$key=$val&";
//                }
//            }
//            $sign = substr($sign, 0, - 1) . $payment['alipay_key'];
//            if (md5($sign) != $_GET['sign']) {
//                return false;
//            }
//
//            if ($_GET['result'] == 'success') {
//                /* 改变订单状态 */
//                order_paid($log_id, 2);
////                model('Payment')->order_paid($log_id, 2);
//                return true;
//            } else {
//                return false;
//            }
//        }else{
//            return false;
//        }
//    }
    public function respond()
    {
        if (! empty($_GET)) {
            $out_trade_no = explode('O', $_GET['out_trade_no']);
            $log_id = $out_trade_no[1];
            // $payment = model('Payment')->get_payment($data['code']);
//            $payment=get_payment($data['code']);
            $payment  = get_payment($_GET['code']);
            /* 检查数字签名是否正确 */
            ksort($_GET);
            reset($_GET);

            $sign = '';
            foreach ($_GET as $key => $val) {
                if ($key != 'sign' && $key != 'sign_type' && $key != 'code') {
                    $sign .= "$key=$val&";
                }
            }
            $sign = substr($sign, 0, - 1) . $payment['alipay_key'];
            if (md5($sign) != $_GET['sign']) {
                return false;
            }

            if ($_GET['result'] == 'success') {
                /* 改变订单状态 */
                order_paid($log_id, 2);
                /*ecs_due 将数据插入到进货单列表*/
                stock_insert($log_id);
//                model('Payment')->order_paid($log_id, 2);
                return true;
            } else {
                return false;
            }
        }else{
            return false;
        }
    }
    /**
     * 手机支付宝异步通知
     *
     * @return string
     */
    public function notify($data)
    {
        if (! empty($_POST)) {
          //  $payment = model('Payment')->get_payment($data['code']);
            $payment=get_payment($data['code']);
            // 支付宝系统通知待签名数据构造规则比较特殊，为固定顺序。
            $parameter['service'] = $_POST['service'];
            $parameter['v'] = $_POST['v'];
            $parameter['sec_id'] = $_POST['sec_id'];
            $parameter['notify_data'] = $_POST['notify_data'];
            // 生成签名字符串
            $sign = '';
            foreach ($parameter as $key => $val) {
                $sign .= "$key=$val&";
            }
            $sign = substr($sign, 0, - 1) . $payment['alipay_key'];
            // 验证签名
            if (md5($sign) != $_POST['sign']) {
                exit("fail");
            }
            // 解析notify_data
            $data = (array) simplexml_load_string($parameter['notify_data']);
            // 交易状态
            $trade_status = $data['trade_status'];
            // 获取支付订单号log_id
            $out_trade_no = explode('O', $data['out_trade_no']);
            $log_id = $out_trade_no[1]; // 订单号log_id
            if ($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
                /* 改变订单状态 */
             //   model('Payment')->order_paid($log_id, 2);
                /* 改变订单状态 */
                order_paid($log_id, 2);
                exit("success");
            } else {
                exit("fail");
            }
        } else {
            exit("fail");
        }
    }

     public function doPost($url, $post_data = array(), $timeout = 5, $header = "", $data_type = "") {
        if (empty($url) || empty($post_data) || empty($timeout))
            return false;
        if (!preg_match('/^(http|https)/is', $url))
            $url = "http://" . $url;
        $code = self::getSupport();
        switch ($code) {
            case 1:return self::curlPost($url, $post_data, $timeout, $header, $data_type);
                break;
            case 2:return self::socketPost($url, $post_data, $timeout, $header, $data_type);
                break;
            case 3:return self::phpPost($url, $post_data, $timeout, $header, $data_type);
                break;
            default:return false;
        }
    }
    static public function getSupport() {
        //如果指定访问方式，则按指定的方式去访问
        if (isset(self::$way) && in_array(self::$way, array(1, 2, 3)))
            return self::$way;

        //自动获取最佳访问方式
        if (function_exists('curl_init')) {//curl方式
            return 1;
        } else if (function_exists('fsockopen')) {//socket
            return 2;
        } else if (function_exists('file_get_contents')) {//php系统函数file_get_contents
            return 3;
        } else {
            return 0;
        }
    }
    static public function curlPost($url, $post_data = array(), $timeout = 5, $header = "", $data_type = "") {
        $header = empty($header) ? '' : $header;
        //支持json数据数据提交
        if($data_type == 'json'){
            $post_string = json_encode($post_data);
        }
        else if(is_array($post_data)){
            $post_string = http_build_query($post_data, '', '&');
        }else {
            $post_string = $post_data;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    // https请求 不验证证书和hosts
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array($header)); //模拟的header头
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    static public function socketPost($url, $post_data = array(), $timeout = 5, $header = "", $data_type = "") {
        $header = empty($header) ? self::defaultHeader() : $header;
        //支持json数据数据提交
        if($data_type == 'json'){
            $post_string = json_encode($post_data);
        }
        else if(is_array($post_data)){
            $post_string = http_build_query($post_data, '', '&');
        }else {
            $post_string = $post_data;
        }

        $url2 = parse_url($url);
        $url2["path"] = ($url2["path"] == "" ? "/" : $url2["path"]);
        $url2["port"] = ($url2["port"] == "" ? 80 : $url2["port"]);
        $host_ip = @gethostbyname($url2["host"]);
        $fsock_timeout = $timeout; //超时时间
        if (($fsock = fsockopen($host_ip, $url2['port'], $errno, $errstr, $fsock_timeout)) < 0) {
            return false;
        }
        $request = $url2["path"] . ($url2["query"] ? "?" . $url2["query"] : "");
        $in = "POST " . $request . " HTTP/1.0\r\n";
        $in .= "Host: " . $url2["host"] . "\r\n";
        $in .= $header;
        $in .= "Content-type: application/x-www-form-urlencoded\r\n";
        $in .= "Content-Length: " . strlen($post_string) . "\r\n";
        $in .= "Connection: Close\r\n\r\n";
        $in .= $post_string . "\r\n\r\n";
        unset($post_string);
        if (!@fwrite($fsock, $in, strlen($in))) {
            @fclose($fsock);
            return false;
        }
        return self::GetHttpContent($fsock);
    }
    static public function phpPost($url, $post_data = array(), $timeout = 5, $header = "", $data_type = "") {
        $header = empty($header) ? self::defaultHeader() : $header;
        //支持json数据数据提交
        if($data_type == 'json'){
            $post_string = json_encode($post_data);
        }
        else if(is_array($post_data)){
            $post_string = http_build_query($post_data, '', '&');
        }else {
            $post_string = $post_data;
        }
        $header.="Content-length: " . strlen($post_string);
        $opts = array(
            'http' => array(
                'protocol_version' => '1.0', //http协议版本(若不指定php5.2系默认为http1.0)
                'method' => "POST", //获取方式
                'timeout' => $timeout, //超时时间
                'header' => $header,
                'content' => $post_string)
        );
        $context = stream_context_create($opts);
        return @file_get_contents($url, false, $context);
    }

}

/**
 * 类
 */
//class alipay
//{
//
//    /**
//     * 构造函数
//     *
//     * @access  public
//     * @param
//     *
//     * @return void
//     */
//    function alipay()
//    {
//    }
//
//    function __construct()
//    {
//        $this->alipay();
//    }
//
//    /**
//     * 生成支付代码
//     * @param   array   $order      订单信息
//     * @param   array   $payment    支付方式信息
//     */
//    function get_code($order, $payment)
//    {
//        if (!defined('EC_CHARSET'))
//        {
//            $charset = 'utf-8';
//        }
//        else
//        {
//            $charset = EC_CHARSET;
//        }
////        if (empty($payment['is_instant']))
////        {
////            /* 未开通即时到帐 */
////            $service = 'trade_create_by_buyer';
////        }
////        else
////        {
////            if (!empty($order['order_id']))
////            {
////                /* 检查订单是否全部为虚拟商品 */
////                $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('order_goods').
////                        " WHERE is_real=1 AND order_id='$order[order_id]'";
////
////                if ($GLOBALS['db']->getOne($sql) > 0)
////                {
////                    /* 订单中存在实体商品 */
////                    $service =  (!empty($payment['alipay_real_method']) && $payment['alipay_real_method'] == 1) ?
////                        'create_direct_pay_by_user' : 'trade_create_by_buyer';
////                }
////                else
////                {
////                    /* 订单中全部为虚拟商品 */
////                    $service = (!empty($payment['alipay_virtual_method']) && $payment['alipay_virtual_method'] == 1) ?
////                        'create_direct_pay_by_user' : 'create_digital_goods_trade_p';
////                }
////            }
////            else
////            {
////                /* 非订单方式，按照虚拟商品处理 */
////                $service = (!empty($payment['alipay_virtual_method']) && $payment['alipay_virtual_method'] == 1) ?
////                    'create_direct_pay_by_user' : 'create_digital_goods_trade_p';
////            }
////        }
//
//        $real_method = $payment['alipay_pay_method'];
//
//        switch ($real_method){
//            case '0':
//                $service = 'trade_create_by_buyer';
//                break;
//            case '1':
//                $service = 'create_partner_trade_by_buyer';
//                break;
//            case '2':
//                $service = 'create_direct_pay_by_user';
//                break;
//        }
//
//        $extend_param = 'isv^sh22';
//
//        $parameter = array(
//            'extend_param'      => $extend_param,
//            'service'           => $service,
//            'partner'           => $payment['alipay_partner'],
//            //'partner'           => ALIPAY_ID,
//            '_input_charset'    => $charset,
//            'notify_url'        => return_url(basename(__FILE__, '.php')),
//            'return_url'        => return_url(basename(__FILE__, '.php')),
//            /* 业务参数 */
//            'subject'           => $order['order_sn'],
//            'out_trade_no'      => $order['order_sn'] . $order['log_id'],
//            'price'             => $order['order_amount'],
//            'quantity'          => 1,
//            'payment_type'      => 1,
//            /* 物流参数 */
//            'logistics_type'    => 'EXPRESS',
//            'logistics_fee'     => 0,
//            'logistics_payment' => 'BUYER_PAY_AFTER_RECEIVE',
//            /* 买卖双方信息 */
//            'seller_email'      => $payment['alipay_account']
//        );
//
//        ksort($parameter);
//        reset($parameter);
//
//        $param = '';
//        $sign  = '';
//
//        foreach ($parameter AS $key => $val)
//        {
//            $param .= "$key=" .urlencode($val). "&";
//            $sign  .= "$key=$val&";
//        }
//
//        $param = substr($param, 0, -1);
//        $sign  = substr($sign, 0, -1). $payment['alipay_key'];
//        //$sign  = substr($sign, 0, -1). ALIPAY_AUTH;
//
//        $button = '<div style="text-align:center"><input type="button" onclick="window.open(\'https://www.alipay.com/cooperate/gateway.do?'.$param. '&sign='.md5($sign).'&sign_type=MD5\')" value="' .$GLOBALS['_LANG']['pay_button']. '" /></div>';
//
//        return $button;
//    }
//
//    /**
//     * 响应操作
//     */
//    function respond()
//    {
//        if (!empty($_POST))
//        {
//            foreach($_POST as $key => $data)
//            {
//                $_GET[$key] = $data;
//            }
//        }
//        $payment  = get_payment($_GET['code']);
//        $seller_email = rawurldecode($_GET['seller_email']);
//        $order_sn = str_replace($_GET['subject'], '', $_GET['out_trade_no']);
//        $order_sn = trim(addslashes($order_sn)) ;
//
//        /* 检查支付的金额是否相符 */
//        if (!check_money($order_sn, $_GET['total_fee']))
//        {
//            return false;
//        }
//
//        /* 检查数字签名是否正确 */
//        ksort($_GET);
//        reset($_GET);
//
//        $sign = '';
//        foreach ($_GET AS $key=>$val)
//        {
//            if ($key != 'sign' && $key != 'sign_type' && $key != 'code')
//            {
//                $sign .= "$key=$val&";
//            }
//        }
//
//        $sign = substr($sign, 0, -1) . $payment['alipay_key'];
//        //$sign = substr($sign, 0, -1) . ALIPAY_AUTH;
//        if (md5($sign) != $_GET['sign'])
//        {
//            return false;
//        }
//
//        if ($_GET['trade_status'] == 'WAIT_SELLER_SEND_GOODS')
//        {
//            /* 改变订单状态 */
//            order_paid($order_sn, 2);
//
//            return true;
//        }
//        elseif ($_GET['trade_status'] == 'TRADE_FINISHED')
//        {
//            /* 改变订单状态 */
//            order_paid($order_sn);
//
//            return true;
//        }
//        elseif ($_GET['trade_status'] == 'TRADE_SUCCESS')
//        {
//            /* 改变订单状态 */
//            order_paid($order_sn, 2);
//
//            return true;
//        }
//        else
//        {
//            return false;
//        }
//    }
//}

?>