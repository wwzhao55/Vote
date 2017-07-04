<?php
require dirname(__FILE__).'/common.php';
//获取活动信息
if(isGet()){
	if(isAjax()){
		exit('错误的请求方式');
	}else{
		$main_html = file_get_contents('resource/app/qrcode.html');
		require 'resource/app/layer.html';
		exit;
	}
}else if(isPost()){
	exit('错误的请求方式');
}