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
		$keyword = isset($_GET['keyword'])?$_GET['keyword']:null;
		if($keyword != null){
			$page = isset($_GET['page'])?$_GET['page']:1;
			$from = ($page-1)*PAGE_NOTE_WORK;
			$work_lists = $db->select('work',[
				'id','title','main_img_src','unionid'
				],[
					'AND'=>[
						'status'=>1,
						'OR'=>[
							'id'=>$keyword+10000,
							'title[~]'=>$keyword,
						]
					],
					"LIMIT"=>[$from,PAGE_NOTE_WORK]
					
				]);
			if($work_lists){
				foreach ($work_lists as $key => &$value) {
					$value['author_name'] = $db->get('author','name',['unionid'=>$value['unionid']]);
					$value['work_vote_count'] = $redis->zScore('vote',$value['id']);
					$value['rank'] = $redis->zRevRank('vote',$value['id'])+1;
				}
				echo json_encode(array(
					'status'=>'success',
					'message'=>'获取数据成功',
					'data'=>$work_lists,
					));
				exit;
			}else{
				echo json_encode(array(
					'status'=>'fail',
					'message'=>'获取数据失败',
					));
				exit;
			}
		}
		$work_order_type = $db->get('introduce','work_order_type',['id[>]'=>0]);
		$page = isset($_GET['page'])?$_GET['page']:1;
		$from = ($page-1)*PAGE_NOTE_WORK;
		switch ($work_order_type) {
			case 2:
				//按评论数排序
				$work_lists = $db->select('work',[
					'id','comment_count','title','main_img_src','unionid'
					],[
					"AND"=>['status'=>1],
					"ORDER" => ["comment_count"=> "DESC"],
					"LIMIT"=>[$from,PAGE_NOTE_WORK]
					]);
				if($work_lists){
					foreach ($work_lists as $key => &$value) {
						$value['author_name'] = $db->get('author','name',['unionid'=>$value['unionid']]);
						$value['work_vote_count'] = $redis->zScore('vote',$value['id']);
						$value['rank'] = $redis->zRevRank('vote',$value['id'])+1;
					}
				}
				break;
			case 1:
				//按作品发布时间排序
				$work_lists = $db->select('work',[
					'id','created_at','title','main_img_src','unionid'
					],[
					"AND"=>['status'=>1],
					"ORDER" => ["created_at"=> "DESC"],
					"LIMIT"=>[$from,PAGE_NOTE_WORK]
					]);
				if($work_lists){
					foreach ($work_lists as $key => &$value) {
						$value['author_name'] = $db->get('author','name',['unionid'=>$value['unionid']]);
						$value['work_vote_count'] = $redis->zScore('vote',$value['id']);
						$value['rank'] = $redis->zRevRank('vote',$value['id'])+1;
					}
				}
				# code...
				break;
			case 3:
				//按评论发布时间排序
				$work_lists = $db->select('work',[
					'id','last_comment_time','main_img_src','title','unionid'
					],[
					"AND"=>['status'=>1],
					"ORDER" => ["last_comment_time"=> "DESC"],
					"LIMIT"=>[$from,PAGE_NOTE_WORK]
					]);
				if($work_lists){
					foreach ($work_lists as $key => &$value) {
						$value['author_name'] = $db->get('author','name',['unionid'=>$value['unionid']]);
						$value['work_vote_count'] = $redis->zScore('vote',$value['id']);
						$value['rank'] = $redis->zRevRank('vote',$value['id'])+1;
					}
				}
				break;
			default:
				$work_lists = array();
				$work_id_lists = $redis->zRevrange('vote',$from,$from+PAGE_NOTE_WORK,true);
				if($work_id_lists){
					foreach ($work_id_lists as $key => &$value) {						
						$work_info = $db->get('work',['id','title','main_img_src','unionid'],['id'=>$key]);
						$work_info['author_name'] = $db->get('author','name',['unionid'=>$work_info['unionid']]);
						$work_info['work_vote_count'] = $value;
						$work_info['rank'] = $redis->zRevRank('vote',$key)+1;
						array_push($work_lists, $work_info);
					}
				}
				break;
		}
		if(count($work_lists)==0){
			echo json_encode(array(
				'status'=>'fail',
				'message'=>'暂时没有数据',
				));
		}else{
			echo json_encode(array(
				'status'=>'success',
				'message'=>'获取数据成功',
				'data'=>$work_lists,
				));
		}
	}else{
		$main_html = file_get_contents('resource/app/worklist.html');
		require 'resource/app/layer.html';
		exit;
	}
}else if(isPost()){
	exit('错误的请求方式');
}