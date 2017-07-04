<?php
require 'init.php';
if(isGet()){
	if(isAjax()){
		$vote_count_total = $db->count('vote',['id[>]'=>0]);
		$work_count_total = $db->count('work',['id[>]'=>0]);
		$author_count_total = $db->count('author',['id[>]'=>0]);
		$comment_count_total = $db->count('comment',['id[>]'=>0]);

		$work_count_array = get_day_count('work',$db);
		$vote_count_array = get_day_count('vote',$db);
		$author_count_array = get_day_count('author',$db);
		$comment_count_array = get_day_count('comment',$db);

		$audit_count = $db->count('work',['status'=>-1]);

		echo json_encode(array(
			'status'=>'success',
			'message'=>'获取成功',
			'data'=>array(
				'vote_count'=>$vote_count_total,
				'work_count'=>$work_count_total,
				'author_count'=>$author_count_total,
				'comment_count'=>$comment_count_total,
				'work_audit_count'=>$audit_count,
				'vote_count_array'=>$vote_count_array,
				'work_count_array'=>$work_count_array,
				'author_count_array'=>$author_count_array,
				'comment_count_array'=>$comment_count_array,
				),
			));

	}else{
		$main_html = file_get_contents('../resource/cms/datacenter.html');
		require '../resource/cms/layer.html';
		exit;
	}
}else if(isPost()){
	echo json_decode(array(
		'status'=>'fail',
		'message'=>'请求方式错误'
		));
	exit;
}