<?php
require 'api/api.php';
session_start();
$api = new api();
$code = isset($_GET['code'])?$_GET['code']:'';
$destination = isset($_GET['state'])?$_GET['state']:'main';
$work_id = isset($_GET['work_id'])?$_GET['work_id']:null;

if(!$code){
	Header("Location:index.php");
}else{
	if(is_weixin()){
		$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.APPID.'&secret='.APPSECRET.'&code='.$code.'&grant_type=authorization_code';
	}else{
		$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.WEB_APPID.'&secret='.WEB_APPSECRET.'&code='.$code.'&grant_type=authorization_code';
	}
	$json_raw = http_get($url);
	$json_data = json_decode($json_raw);
	if(isset($json_data->unionid)){
		$_SESSION['unionid'] = $json_data->unionid;
		$_SESSION['openid'] = $json_data->openid;
		if($work_id==null){
			Header("Location:index.php?destination=".$destination);
		}else{
			Header("Location:index.php?destination=".$destination."&work_id=".$work_id);
		}
		
		
	}else{
		exit('获取unionid失败');
	}
	
}
