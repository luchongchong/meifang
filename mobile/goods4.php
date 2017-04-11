<?php
session_start();
define('IN_ECS', true);
function getInfo($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    return curl_exec($ch);
}

function object_array($array)
{
    if(is_object($array))
    {
        $array = (array)$array;
    }
    if(is_array($array))
    {
        foreach($array as $key=>$value)
        {
            $array[$key] = object_array($value);
        }
    }
    return $array;
}
if(@$_GET['code']){

    $ret_info = $db -> getRow("SELECT * FROM " . $GLOBALS['ecs']->table('weixin_config') . " WHERE `id` = 1");
    var_dump($ret_info);die();
    //鑾峰彇openid
    $code = $_GET['code'];
    $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$appid."&secret=".$appsecret."&code=".$code."&grant_type=authorization_code";
    $re = getInfo($url);
    $re = json_decode($re);

    //$_SESSION['test_session'] = $re;

    $re = object_array($re);
    $wechaId = $re['openid'];

    if(!$wechaId){
        //鑾峰彇openid
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$ret_info['appid']."&secret=".$ret_info['appsecret']."&code=".$code."&grant_type=authorization_code";
        $re = getInfo($url);
        $re = json_decode($re);
        	
        $re = object_array($re);
        $wechaId = $re['openid'];
    }

    //鑾峰緱鐢ㄦ埛鐨刬d
    $sql_weixin='SELECT * FROM'.$ecs->table('weixin_user').'WHERE fake_id="'.$wechaId.'" and fake_id!=""';
    	
    $weixin_data=$db->getRow($sql_weixin);

    if(!$weixin_data){
        $_SESSION['user_id'] = 0;
        ecs_header("Location:user.php\n");
        exit();
    }
    //鐧诲綍鐢�
    //$sql_weixin = "SELECT * FROM ".$ecs->table('weixin_user')." WHERE wxid='$wechaId'";
    //$data_weixin = $db->getRow($sql_weixin);
    $sql_user = "SELECT * FROM ".$ecs->table('users')." WHERE user_id = '".$weixin_data['ecuid']."'";
    $data_user = $db->getRow($sql_user);
    if(!$data_user){
        $_SESSION['user_id'] = 0;
        ecs_header("Location:user.php\n");
        exit();
    }

    $_SESSION['openid_self'] = $wechaId;
    $_SESSION['user_id'] = $data_user['user_id'];
    $_SESSION['user_name'] = $data_user['user_name'];
    $_SESSION['user_rank'] = $data_user['user_rank'];
    update_user_info();
    $state = $_GET['state'];

    
}
if(@!$_SESSION['user_id']){
    //$url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx2326ecf73474cfaa&redirect_uri=http://www.mfmb58.com/mobile/goods.php&response_type=code&scope=snsapi_base&state=".$_GET['id']."#wechat_redirect";
    $url = "http://www.mfmb58.com/mobile/goods.php?id=".$_GET['id'];
    echo "<script>";
    echo "location.href='$url'";
    echo "</script>";
}
?> 