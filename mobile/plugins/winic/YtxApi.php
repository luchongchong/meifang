<?php
/*
 *  Copyright (c) 2014 The CCP project authors. All Rights Reserved.
 *
 *  Use of this source code is governed by a Beijing Speedtong Information Technology Co.,Ltd license
 *  that can be found in the LICENSE file in the root of the web site.
 *
 *   http://www.yuntongxun.com
 *
 *  An additional intellectual property rights grant can be found
 *  in the file PATENTS.  All contributing project authors may
 *  be found in the AUTHORS file in the root of the source tree.
 */

include_once("CCPRestSDK.php");

/* 云通讯接口 */

class YtxApi
{
    /* 主帐号 */
    protected static $accountSid = '8a48b55152a56fc20152e95b31f04ba2';

    /* 主帐号Token */
    protected static $accountToken = 'd8f0eb421ea844ab8ff9f596fd3e4bc2';

    /* 应用Id */
    protected static $appId = 'aaf98f8952f255490152f357e57202de';

    /* 请求地址，格式如下，不需要写https:// */
    protected static $serverIP = 'sandboxapp.cloopen.com';

    /* 请求端口 */
    protected static $serverPort = '8883';

    /* REST版本号 */
    protected static $softVersion = '2013-12-26';

    /**
     * 发送模板短信
     * @param to 手机号码集合,用英文逗号分开
     * @param datas 内容数据 格式为数组 例如：array('Marry','Alon')，如不需替换请填 null
     * @param $tempId 模板Id
     */
    public static function sendTemplateSMS($to, $datas, $tempId = 69006)
    {

        // 初始化REST SDK
        $rest = new REST(YtxApi::$serverIP, YtxApi::$serverPort, YtxApi::$softVersion);
        $rest->setAccount(YtxApi::$accountSid, YtxApi::$accountToken);
        $rest->setAppId(YtxApi::$appId);

        // 发送模板短信
        $result = $rest->sendTemplateSMS($to, $datas, $tempId);
        if ($result == NULL) {
            return "result error!";
        }
        if ($result->statusCode != 0) {
            return $result->statusCode .' '. $result->statusMsg;
        } else {
            // 获取返回信息
            $smsmessage = $result->TemplateSMS;
            return true;
        }
    }

    public static function sendSMS(){

    }

    /**
     * 获取随机字符串
     * @param int $length
     * @param string $chars
     * @return string
     */
    public static function random($length = 4, $chars = '0123456789') {
        $hash = '';
        $max = strlen($chars) - 1;
        for($i = 0; $i < $length; $i ++) {
            $hash .= $chars[mt_rand(0, $max)];
        }
        return $hash;
    }
}

?>
