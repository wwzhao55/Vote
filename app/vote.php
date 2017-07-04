<?php
require dirname(__FILE__).'/common.php';
//获取活动信息
if(isGet()){
	exit('错误的请求方式');
}else if(isPost()){
	try{
		$redis = new Redis();
		$result = $redis->connect(REDIS_HOST);
		if(!$result){
			exit('redis服务没有开启！');
		}
	}catch(Exception $e){
		exit('没有redis扩展！');
	}
	$work_id = isset($_POST['work_id'])?$_POST['work_id']:null;
	if($work_id==null){
		echo json_encode(array(
			'status'=>'fail',
			'message'=>'作品id缺失',
			));
		exit;
	}
	$has_work = $redis->zRank('vote',$work_id);
	if($has_work===false){
		echo json_encode(array(
			'status'=>'fail',
			'message'=>'不存在的作品',
			));
		exit;
	}
	$unionid = $_SESSION['unionid'];
	$count = $redis->lSize('unionid:vote:'.$_SESSION['unionid']);
	$work_id_lists = $redis->lRange('unionid:vote:'.$_SESSION['unionid'],0,$count);
	if(in_array($work_id, $work_id_lists)){
		echo json_encode(array(
			'status'=>'fail',
			'message'=>'不能重复投票！',
			));
		exit;
	}else{
		$result1 = $redis->lPush('unionid:vote:'.$_SESSION['unionid'],$work_id);
		$result2 = $redis->zIncrBy('vote', 1 , $work_id);		
		$result3 = $redis->sAdd('vote_temp',$work_id.'_dataguiding_'.$_SESSION['unionid']);
	}
	if($result1>0 && $result2>0 && $result3>0){
		echo json_encode(array(
			'status'=>'success',
			'message'=>'修改成功',
			));
		$vote_temp_count = $redis->sCard('vote_temp');
		if($vote_temp_count==100){
			$redis->multi(Redis::PIPELINE);				
			$redis->sMembers('vote_temp');
			$redis->del('vote_temp');
			$result = $redis->exec();
			$vote_temp_lists =$result[0];
			$insert_lists = array();
			foreach ($vote_temp_lists as $list) {
				$arg_array = explode('_dataguiding_', $list);
				$insert_list = array();
				$insert_list['work_id'] = $arg_array[0];
				$insert_list['unionid'] = $arg_array[1];
				$insert_list['created_at'] = time();
				array_push($insert_lists, $insert_list);
			}
			$db->insert('vote',$insert_lists);
							
		}		
		exit; 
	}else{
		if($result1>0){
			$redis->lRem('unionid:vote:'.$_SESSION['unionid'],0,$work_id);
		}
		if($result2>0){
			$redis->zIncrBy('vote',-1,$work_id);
		}
		if($result3>0){
			$redis->sRem('vote_temp',$work_id.'_dataguiding_'.$_SESSION['unionid']);
		}
		echo json_encode(array(
			'status'=>'fail',
			'message'=>'修改失败',
			'data'=>$result1.'_'.$result2.'_'.$result3,
			));
		exit;
	}	
}