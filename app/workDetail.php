<?php
require dirname(__FILE__).'/common.php';
//获取活动信息
if(isGet()){
	if(isAjax()){
		try{
			$redis = new Redis();
			$result = $redis->connect(REDIS_HOST);
			if(!$result){
				exit('redis服务没有开启！');
			}
		}catch(Exception $e){
			exit('没有redis扩展！');
		}
		$work_id = isset($_GET['work_id'])?$_GET['work_id']:null;
		if($work_id==null){
			echo json_encode(array(
				'status'=>'fail',
				'message'=>'work_id参数缺失',
				));
			exit;
		}
		$work_info = $db->get('work',['title','description','main_img_src','img','video_src','status','unionid','created_at'],['id'=>$work_id]);
		if(!$work_info){
			echo json_encode(array(
				'status'=>'fail',
				'message'=>'查询失败',
				));
			exit;
		}
		if($work_info['status']!=1){
			if($work_info['unionid']!=$_SESSION['unionid']){
				echo json_encode(array(
					'status'=>'fail',
					'message'=>'作品不可见',
					));
				exit;
			}
			
		}
		if($work_info['unionid'] == $_SESSION['unionid']){
			$author_self = true;
		}else{
			$author_self = false;
		}
		if($work_info['video_src']){
			preg_match('/.*\/id_(.{8,20}).html.*/',$work_info['video_src'],$matches);
			$work_info['video_src'] = $matches[1];
		}		
		$work_info['rank'] = $redis->zRevRank('vote',$work_id)+1;
		$work_info['work_vote_count'] =  $redis->zScore('vote', $work_id);
		$work_info['author_name'] = $db->get('author','name',['unionid'=>$work_info['unionid']]);
		echo json_encode(array(
			'status'=>'success',
			'message'=>'查询成功',
			'data'=>$work_info,
			'author_self'=>$author_self,
			));
		exit;
	}else{
		$main_html = file_get_contents('resource/app/workdetail.html');
		require 'resource/app/layer.html';
		exit;
	}
}else if(isPost()){
	exit('错误的请求方式');
}