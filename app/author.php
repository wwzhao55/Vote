<?php
require dirname(__FILE__).'/common.php';
//获取选手信息
if(isGet()){
	if(isAjax()){
        $res = $db->get('author', '*', ['unionid' => $_SESSION['unionid']]);
        $count = $db->count('work', ['AND' => ['unionid' => $_SESSION['unionid'], 'status' => -2]]);
        $res['count'] = $count;
        if ($res) {
            echo json_encode(array(
                'status' => 'success',
                'message' => '查询成功',
                'author' => $res
            ));
            exit;
        } else {
            echo json_encode(array(
                'status' => 'fail',
                'message' => '查询失败',
            ));
            exit;
        }
	}else{
		$main_html = file_get_contents('resource/app/author.html');
		require 'resource/app/layer.html';
		exit;
	}
}else if(isPost()){
	exit('错误的请求');
}