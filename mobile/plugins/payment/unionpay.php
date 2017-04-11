<?php

/**
 * ECTouch Open Source Project
 * ============================================================================
 * Copyright (c) 2012-2014 http://ectouch.cn All rights reserved.
 * ----------------------------------------------------------------------------
 * 文件名称：unionpay.php
 * ----------------------------------------------------------------------------
 * 功能描述：银联WAP支付插件
 * ----------------------------------------------------------------------------
 * Licensed ( http://www.ectouch.cn/docs/license.txt )
 * ----------------------------------------------------------------------------
 */
/* 访问控制 */
defined('IN_ECTOUCH') or die('Deny Access');

defined('SDK_SIGN_CERT_PATH') or define('SDK_SIGN_CERT_PATH', ROOT_PATH . 'data/certificate/pay/PM_700000000000001_acp.pfx');
defined('SDK_SIGN_CERT_PWD') or define('SDK_SIGN_CERT_PWD', '000000');
defined('SDK_VERIFY_CERT_DIR') or define('SDK_VERIFY_CERT_DIR', ROOT_PATH . 'data/certificate/pay/');
defined('SDK_FRONT_TRANS_URL') or define('SDK_FRONT_TRANS_URL', 'https://101.231.204.80:5000/gateway/api/frontTransReq.do');
defined('SDK_BACK_TRANS_URL') or define('SDK_BACK_TRANS_URL', 'https://101.231.204.80:5000/gateway/api/backTransReq.do');
defined('SDK_SINGLE_QUERY_URL') or define('SDK_SINGLE_QUERY_URL', 'https://101.231.204.80:5000/gateway/api/queryTrans.do');

/**
 * 余额支付插件类
 */
class unionpay {
    /**
     * 生成支付代码
     *
     * @param array $order
     *            订单信息
     * @param array $payment
     *            支付方式信息
     */
    function get_code($order, $payment) {
        if (!defined('EC_CHARSET')) {
            $charset = 'UTF-8';
        } else {
            $charset = EC_CHARSET;
        }
		if(!$order['add_time']){
            $order['add_time'] = gmtime();
        }
        if(!isset($order['order_sn']{10})){ 
          $order['order_sn'] = get_order_sn();
        }
        $parameter = array(
            'version' => '5.0.0', // 接口版本号
            'encoding' => $charset,
            'certId' => $this->getSignCertId(), //证书ID
            'txnType' => '01', //交易类型	
            'txnSubType' => '01', //交易子类
            'bizType' => '000000', //业务类型
            'frontUrl' => return_url(basename(__FILE__, '.php'), array('type' => 1)), //前台通知地址
            'backUrl' => return_url(basename(__FILE__, '.php'), array('type' => 0)), //后台通知地址	
            'signMethod' => '01', //签名方法
            'channelType' => '08', //渠道类型
            'accessType' => '0', //接入类型
            'merId' => $payment['mer_id'], //商户代码
            'orderId' => $order['order_sn'] . '0' . $order['log_id'], // 请求号，唯一
            'txnTime' => date('YmdHis', $order['add_time']), //订单发送时间
            'txnAmt' => $order['order_amount'] * 100, //交易金额 单位分
            'currencyCode' => '156', //交易币种
            'defaultPayType' => '0001', //默认支付方式	 
			'reqReserved' =>' 透传信息', //请求方保留域，透传字段，查询、通知、对账文件中均会原样出现 
        );
        $this->sign($parameter);
        $front_uri = 'https://101.231.204.80:5000/gateway/api/frontTransReq.do';
        // 构造 自动提交的表单
        $html_form = $this->create_html($parameter, $front_uri);

        return $html_form;
    }

    /**
     * 查询交易
     */
    public function verify_query($order, $payment) {
        if (!defined('EC_CHARSET')) {
            $charset = 'UTF-8';
        } else {
            $charset = EC_CHARSET;
        }
        $params = array(
            'version' => '5.0.0', //版本号
            'encoding' => $charset, //编码方式
            'certId' => $this->getSignCertId(), //证书ID	
            'signMethod' => '01', //签名方法
            'txnType' => '00', //交易类型	
            'txnSubType' => '00', //交易子类
            'bizType' => '000000', //业务类型
            'accessType' => '0', //接入类型
            'channelType' => '07', //渠道类型
            'orderId' => $order['order_sn'] . '0' . $order['log_id'], //请修改被查询的交易的订单号
            'merId' => $payment['mer_id'], //商户代码，请修改为自己的商户号
            'txnTime' => date('YmdHis', $order['add_time']), //请修改被查询的交易的订单发送时间
        );
        $this->sign($params);
        $result = $this->sendHttpRequest($params, SDK_SINGLE_QUERY_URL);
        //返回结果展示
        $result_arr = $this->coverStringToArray($result);
        if ($this->verify($result_arr)) {
            if ($result_arr['respCode'] == '00') {
                print_r($result_arr );
                echo "ee";
                //改变订单支付方式
                $log_id = substr($result_arr['orderId'], 14);
                model('Payment')->order_paid($log_id, 2);
                return true;
            }
        }
        //echo $this->verify($result_arr) ? '验签成功' : '验签失败';
    }

    /**
     * 银联同步响应操作
     * 
     * @return boolean
     */
    public function callback($data) {
        if (isset($_POST ['signature'])) {
            if ($this->verify($_POST)) {
                if ($_POST['respCode'] == '00') {
                    // 获取支付订单号log_id
                    $log_id = substr($_POST['orderId'], 14);
                    model('Payment')->order_paid($log_id, 2);
                    return true;
                }
            } else {
                return false;
            }
        } else {
            echo '签名为空';
        }
    }

    /**
     * 银联异步通知
     * 
     * @return string
     */
    public function notify($data) {
        if (!empty($_POST)) {
            if (isset($_POST ['signature'])) {
                if ($this->verify($_POST)) {
                    if ($_POST['respCode'] == '00') {
                        // 获取支付订单号log_id
                        $log_id = substr($_POST['orderId'], 14);
                        model('Payment')->order_paid($log_id, 2);
                        return true;
                    }
                } else {
                    return false;
                }
            } else {
                echo '签名为空';
            }
        } else {
            exit("fail");
        }
    }

    /**
     * 签名证书ID
     *
     * @return unknown
     */
    function getSignCertId() {
        // 签名证书路径

        return $this->getCertId(SDK_SIGN_CERT_PATH);
    }

    /**
     * 取证书ID(.pfx)
     *
     * @return unknown
     */
    function getCertId($cert_path) {
        $pkcs12certdata = file_get_contents($cert_path);
        openssl_pkcs12_read($pkcs12certdata, $certs, SDK_SIGN_CERT_PWD);
        $x509data = $certs ['cert'];
        openssl_x509_read($x509data);
        $certdata = openssl_x509_parse($x509data);
        $cert_id = $certdata ['serialNumber'];
        return $cert_id;
    }

    /**
     * 签名
     *
     * @param String $params_str
     */
    function sign(&$params) {
        //global $log;
        //$log->LogInfo('=====签名报文开始======');
        if (isset($params['transTempUrl'])) {
            unset($params['transTempUrl']);
        }
        // 转换成key=val&串
        $params_str = $this->coverParamsToString($params);
        //$log->LogInfo("签名key=val&...串 >" . $params_str);

        $params_sha1x16 = sha1($params_str, FALSE);
        //$log->LogInfo("摘要sha1x16 >" . $params_sha1x16);
        // 签名证书路径
        $cert_path = SDK_SIGN_CERT_PATH;
        $private_key = $this->getPrivateKey($cert_path);
        // 签名
        $sign_falg = openssl_sign($params_sha1x16, $signature, $private_key, OPENSSL_ALGO_SHA1);
        if ($sign_falg) {
            $signature_base64 = base64_encode($signature);
            // $log->LogInfo("签名串为 >" . $signature_base64);
            $params ['signature'] = $signature_base64;
        } else {
            echo "签名失败";
            exit;
            //$log->LogInfo(">>>>>签名失败<<<<<<<");
        }
        //$log->LogInfo('=====签名报文结束======');
    }

    /**
     * 返回(签名)证书私钥 -
     *
     * @return unknown
     */
    function getPrivateKey($cert_path) {
        $pkcs12 = file_get_contents($cert_path);
        openssl_pkcs12_read($pkcs12, $certs, SDK_SIGN_CERT_PWD);
        return $certs ['pkey'];
    }

    /**
     * 数组 排序后转化为字体串
     *
     * @param array $params        	
     * @return string
     */
    function coverParamsToString($params) {
        $sign_str = '';
        // 排序
        ksort($params);
        foreach ($params as $key => $val) {
            if ($key == 'signature') {
                continue;
            }
            $sign_str .= sprintf("%s=%s&", $key, $val);
            // $sign_str .= $key . '=' . $val . '&';
        }
        return substr($sign_str, 0, strlen($sign_str) - 1);
    }

    /**
     * 构造自动提交表单
     *
     * @param unknown_type $params        	
     * @param unknown_type $action        	
     * @return string
     */
    function create_html($params, $action) {
        $encodeType = isset($params ['encoding']) ? $params ['encoding'] : 'UTF-8';
        $html = <<<eot
    <form id="pay_form" name="pay_form" action="{$action}" method="post" target="_blank">
	
eot;
        foreach ($params as $key => $value) {
            $html .= "    <input type=\"hidden\" name=\"{$key}\" id=\"{$key}\"  value=\"{$value}\" />\n";
        }
        $html .= <<<eot
    <input type="submit" type="hidden" value="去付款" class="btn btn-info ect-btn-info ect-colorf ect-bg c-btn3">
    </form>
eot;
        return $html;
    }

    /**
     * 验签
     *
     * @param String $params_str        	
     * @param String $signature_str        	
     */
    function verify($params) {
        global $log;
        // 公钥
        $public_key = $this->getPulbicKeyByCertId($params ['certId']);
        // echo $public_key . '<br/>';
        // 签名串
        $signature_str = $params ['signature'];
        unset($params ['signature']);
        $params_str = $this->coverParamsToString($params);
        // $log->LogInfo('报文去[signature] key=val&串>' . $params_str);
        $signature = base64_decode($signature_str);
        //echo date('Y-m-d', time());
        $params_sha1x16 = sha1($params_str, FALSE);
        //$log->LogInfo('摘要shax16>' . $params_sha1x16);
        $isSuccess = openssl_verify($params_sha1x16, $signature, $public_key, OPENSSL_ALGO_SHA1);
        //$log->LogInfo($isSuccess ? '验签成功' : '验签失败' );
        return $isSuccess;
    }

    /**
     * 根据证书ID 加载 证书
     *
     * @param unknown_type $certId        	
     * @return string NULL
     */
    function getPulbicKeyByCertId($certId) {
        global $log;
        //$log->LogInfo ( '报文返回的证书ID>' . $certId );
        // 证书目录
        $cert_dir = SDK_VERIFY_CERT_DIR;
        //$log->LogInfo ( '验证签名证书目录 :>' . $cert_dir );
        $handle = opendir($cert_dir);
        if ($handle) {
            while ($file = readdir($handle)) {
                clearstatcache();
                $filePath = $cert_dir . '/' . $file;
                if (is_file($filePath)) {
                    if (pathinfo($file, PATHINFO_EXTENSION) == 'cer') {
                        if ($this->getCertIdByCerPath($filePath) == $certId) {
                            closedir($handle);
                            //$log->LogInfo ( '加载验签证书成功' );
                            return $this->getPublicKey($filePath);
                        }
                    }
                }
            }
            //$log->LogInfo ( '没有找到证书ID为[' . $certId . ']的证书' );
        } else {
            //$log->LogInfo ( '证书目录 ' . $cert_dir . '不正确' );
        }
        closedir($handle);
        return null;
    }

    /**
     * 取证书ID(.cer)
     *
     * @param unknown_type $cert_path        	
     */
    function getCertIdByCerPath($cert_path) {
        $x509data = file_get_contents($cert_path);
        openssl_x509_read($x509data);
        $certdata = openssl_x509_parse($x509data);
        $cert_id = $certdata ['serialNumber'];
        return $cert_id;
    }

    /**
     * 取证书公钥 -验签
     *
     * @return string
     */
    function getPublicKey($cert_path) {
        return file_get_contents($cert_path);
    }

    /**
     * 后台交易 HttpClient通信
     * @param unknown_type $params
     * @param unknown_type $url
     * @return mixed
     */
    function sendHttpRequest($params, $url) {
        $opts = $this->getRequestParamString($params);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //不验证证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //不验证HOST
        curl_setopt($ch, CURLOPT_SSLVERSION, 3);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-type:application/x-www-form-urlencoded;charset=UTF-8'
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $opts);

        /**
         * 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。
         */
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // 运行cURL，请求网页
        $html = curl_exec($ch);
        // close cURL resource, and free up system resources
        curl_close($ch);
        return $html;
    }

    /**
     * 字符串转换为 数组
     *
     * @param unknown_type $str        	
     * @return multitype:unknown
     */
    function coverStringToArray($str) {
        $result = array();

        if (!empty($str)) {
            $temp = preg_split('/&/', $str);
            if (!empty($temp)) {
                foreach ($temp as $key => $val) {
                    $arr = preg_split('/=/', $val, 2);
                    if (!empty($arr)) {
                        $k = $arr ['0'];
                        $v = $arr ['1'];
                        $result [$k] = $v;
                    }
                }
            }
        }
        return $result;
    }

    /**
     * 组装报文
     *
     * @param unknown_type $params        	
     * @return string
     */
    function getRequestParamString($params) {
        $params_str = '';
        foreach ($params as $key => $value) {
            $params_str .= ($key . '=' . (!isset($value) ? '' : urlencode($value)) . '&');
        }
        return substr($params_str, 0, strlen($params_str) - 1);
    }

}

?>