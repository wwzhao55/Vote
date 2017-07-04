<?php
require dirname(__FILE__).'/common.php';
//获取活动信息
if(isGet()){
	if(isAjax()){
		$url = isset($_GET['url'])?$_GET['url']:null;
		if($url==null){
			echo json_encode(array(
				'status'=>'fail',
				'message'=>'地址参数缺失',
				));
			exit;
		}
		$jssdk = $api->get_jssdk($url);
		$share_config = $db->get('share_config',['title','description','img'],['id[>]'=>0]);
		echo json_encode(array(
			'status'=>'success',
			'message'=>'获取数据成功',
			'jssdk'=>$jssdk,
			'share_config'=>$share_config,
			));
		exit;		
	}else{
		$main_html = file_get_contents('resource/app/index.html');
		require 'resource/app/layer.html';
		exit;
	}
}else if(isPost()){
	exit('错误的请求方式');
}