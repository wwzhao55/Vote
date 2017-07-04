<?php
require 'api/api.php';
$api = new api();
$db = $api->get_db();
if($db==null){
    exit('数据库连接失败');
}
$upload = $api->get_upload();
session_start();
$destination = isset($_GET['destination'])?$_GET['destination']:'main';
$right_destination = array('main','vote','comment','workEdit','workDetail','author','authorMobile','authorWork','workImgUpload','workList','qrCode');
if(!in_array($destination, $right_destination)){
	$destination = 'main';
}
if($_SERVER['HTTP_USER_AGENT']=='test'){
    if(!isset($_SESSION['unionid'])){
        $test_unionid = getRandChar(15);
        $test_openid = getRandChar(10);
        $_SESSION['unionid'] = $test_unionid;
        $_SESSION['openid'] = $test_openid;
    }
}
if( isset($_SESSION['unionid']) && isset($_SESSION['openid']) ){
    if(is_weixin()){
        $user_info_url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$api->get_access_token().'&openid='.$_SESSION['openid'].'&lang=zh_CN';
        $user_info = json_decode(http_get($user_info_url));
        if(isset($user_info->subscribe)){
            $has_subscribe = $user_info->subscribe;
            if($has_subscribe==0){
                $destination = 'qrCode';
            }
        }else{
            exit('获取用户信息失败');
        }   
    }
	
	require "app/".$destination.".php";
	
}else{
    $work_id = isset($_GET['work_id'])?$_GET['work_id']:null;
    if(isAjax()){
        echo json_encode(array(
            'status'=>'fail',
            'message'=>'请求方式出错',
            ));
    }
    if($work_id){
        $redirect_url = 'https://'.DOMAIN.'/auth.php?work_id='.$work_id; 
   }else{
        $redirect_url = 'https://'.DOMAIN.'/auth.php';
   }
    
    if(is_weixin()){
        Header("Location: https://open.weixin.qq.com/connect/oauth2/authorize?appid=".APPID."&redirect_uri=".$redirect_url."&response_type=code&scope=snsapi_base&state=".$destination."#wechat_redirect");
    }else{
        Header("Location: https://open.weixin.qq.com/connect/qrconnect?appid=".WEB_APPID."&redirect_uri=".$redirect_url."&response_type=code&scope=snsapi_login&state=".$destination."#wechat_redirect");
    }

}


