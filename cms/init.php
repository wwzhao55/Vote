<?php
ini_set('date.timezone','Asia/Shanghai');
session_start();
require '../api/api.php';
$api = new api();

$db = $api->get_db();

if($db==null){
	exit('数据库错误');
}

$upload = $api->get_upload();

$has_introduce = $db->has('introduce',['id[>]'=>0]);
$has_share_config = $db->has('share_config',['id[>]'=>0]);
if(!$has_introduce){
	$db->insert('introduce',array(
		'introduce'=>'活动介绍',
		'reward'=>'活动奖励',
		'work_judge'=>'作品评判标准',
		'work_order_type'=>0,
		'created_at'=>time(),
		));
}
if(!$has_share_config){
	$db->insert('share_config',[
		'title'=>'标题',
		'description'=>'描述',
		'img'=>'url',
		'access_token'=>'access_token',
		'expire_time'=>0,
		'ticket'=>'ticket',
		'ticket_expire_time'=>0,
		'created_at'=>time(),
		]);
}


/*if(!isset($_SESSION['uid'])){
	if(isAjax()){
		echo json_encode(array(
			'status'=>'fail',
			'message'=>'您还未登陆',
			));
		exit; 
	}else{
		Header("Location: login.php");
	}
}*/


