<?php
require dirname(__FILE__).'/common.php';
//获取活动信息 -2表草稿，-1表发布未审核，0已审核（未通过），1已审核（通过）
if(isGet()){
	if(isAjax()){
        $res = $db->select('work', ['id', 'title', 'status'], ['unionid' => $_SESSION['unionid']]);//作品编号10000+id
        if ($res) {
            echo json_encode(array(
                'status' => 'success',
                'message' => '作品信息获取成功',
                'author' => $res
            ));
            exit;
        } else {
            echo json_encode(array(
                'status' => 'fail',
                'message' => '作品信息获取失败',
            ));
            exit;
        }
	}else{
		$main_html = file_get_contents('resource/app/authorwork.html');
		require 'resource/app/layer.html';
		exit;
	}
}else if(isPost()){//删除该作品相关信息
    $id = isset($_POST['id']) ? $_POST['id'] : '';
    $del_work = $db->delete('work', ['id' => $id]);
    $del_work_img = $db->delete('work_img', ['work_id' => $id]);
    $del_comment = $db->delete('comment', ['work_id' => $id]);
    $del_vote = $db->delete('vote', ['work_id' => $id]);
    if ($del_work) {
        echo json_encode(array(
            'status' => 'success',
            'message' => '作品删除成功',
        ));
        exit;
    } else {
        echo json_encode(array(
            'status' => 'fail',
            'message' => '作品删除失败',
        ));
        exit;
    }
}