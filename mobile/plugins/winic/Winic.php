<?php

/**
 * Created by PhpStorm.
 * User: hgwang
 * Date: 16/2/16
 * Time: 14:41
 */

/* 访问控制 */
defined('IN_ECTOUCH') or die('Deny Access');

class Winic
{
    /* 发送短信,吉信通 */
    static function sendSMS($strMobile, $content)
    {
        $url = "http://service.winic.org:8009/sys_port/gateway/?id=%s&pwd=%s&to=%s&content=%s&time=";
        $id = urlencode("test153");
        $pwd = urlencode("x891795");
        $to = urlencode($strMobile);
        $content = iconv("UTF-8", "GB2312", $content);
        $rurl = sprintf($url, $id, $pwd, $to, $content);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $rurl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        $result = curl_exec($ch);

        //返回结果
        if($result){
            curl_close($ch);
            return $result;
        }
        else {
            $error = curl_errno($ch);
            echo "curl出错，错误码:$error"."<br>";
            echo "<a href='http://curl.haxx.se/libcurl/c/libcurl-errors.html'>错误原因查询</a></br>";
            curl_close($ch);
            return false;
        }
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