<?php
require dirname(__FILE__).'/common.php';

//获取活动信息
if(isGet()){
	if(isAjax()){
		$work_id = isset($_GET['work_id'])?$_GET['work_id']:null;
		$url = isset($_GET['url'])?$_GET['url']:null;
		if($url==null){
			echo json_encode(array(
				'status'=>'fail',
				'message'=>'地址参数缺失',
				));
			exit;
		}
		$jssdk = $api->get_jssdk($url);
		$has_author_info = $db->has('author',['unionid'=>$_SESSION['unionid']]);
		if($work_id==null){
			$draft = $db->get('work','*',['AND'=>['unionid'=>$_SESSION['unionid'],'status'=>-2]]);
		}else{
			$draft = $db->get('work','*',['AND'=>['unionid'=>$_SESSION['unionid'],'id'=>$work_id]]);
		}
		
		echo json_encode(array(
			'status'=>'success',
			'message'=>'获取数据成功',
			'data'=>$draft,
			'author_info'=>$has_author_info,
			'jssdk'=>$jssdk,
			));
		exit;		

	}else{
		
		$main_html = file_get_contents('resource/app/workedit.html');
		require 'resource/app/layer.html';
		exit;
	}
}else if(isPost()){
	$name = isset($_POST['name'])?$_POST['name']:null;
	$nationality = isset($_POST['nationality'])?$_POST['nationality']:null;
	$email = isset($_POST['email'])?$_POST['email']:null;
	$mobile = isset($_POST['mobile'])?$_POST['mobile']:null;
	$has_author_info = $db->has('author',['unionid'=>$_SESSION['unionid']]);
	if(!$has_author_info){
		if($name==null ||$nationality==null ||$email==null ||$mobile==null){
			echo json_encode(array(
				'status'=>'fail',
				'message'=>'发布者信息参数缺失',
				));
			exit;
		}
		if(!preg_match("/^1[34578]\d{9}$/", $mobile)){
			echo json_encode(array(
			'status'=>'fail',
			'message'=>'手机格式错误',
			));
			exit;
		}
		if(!preg_match("/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i", $email)){
			echo json_encode(array(
			'status'=>'fail',
			'message'=>'邮箱格式错误',
			));
			exit;
		}
		
		$author_info = array(
			'unionid'=>$_SESSION['unionid'],
			'name'=>$name,
			'email'=>$email,
			'nationality'=>$nationality,
			'mobile'=>$mobile,
			'created_at'=>time(),
			);
		$author_info_result = $db->insert('author',$author_info);
		if(!$author_info_result){
			echo json_encode(array(
				'status'=>'fail',
				'message'=>'操作失败,发布者信息写入错误',
				));
			exit;
		}
	}

	$id = isset($_POST['id'])?$_POST['id']:null;
	$title = isset($_POST['title'])?$_POST['title']:null;
	$description = isset($_POST['description'])?$_POST['description']:null;
	$status = isset($_POST['status'])?$_POST['status']:null;
	$video_src = isset($_POST['video_src'])?$_POST['video_src']:null;
	$main_img_src = isset($_POST['main_img_src'])?$_POST['main_img_src']:null;
	$img = isset($_POST['img'])?$_POST['img']:null;

	if($video_src!=null){
		if(!preg_match('/.*\/id_(.{8,20}).html.*/',$video_src,$matches)){
			echo json_encode(array(
				'status'=>'fail',
				'message'=>'操作失败,视频地址不符合要求',
				));
			exit;
		}
		//$video_src = $matches[1];
	}

	$work_info = array(
		'unionid'=>$_SESSION['unionid'],
		'title'=>$title,
		'description'=>$description,
		'status'=>$status,
		'main_img_src'=>$main_img_src,
		'video_src'=>$video_src,
		'img'=>$img,
		'created_at'=>time(),
		);
	if($status==-2){
		//保存草稿
		$draft_id = $db->get('work','id',['AND'=>['unionid'=>$_SESSION['unionid'],'status'=>-2]]);
		if($draft_id){
			//更新
			$result = $db->update('work',$work_info,['id'=>$draft_id]);
		}else{
			//新增
			$result = $db->insert('work',$work_info);
		}

	}else if($status==-1){
		if($title==null ||$description==null ||$main_img_src==null ||$img==null){
			echo json_encode(array(
				'status'=>'fail',
				'message'=>'发布失败，作品信息不全',
				));
			exit;
		}
		//发布
		if($id==null){
			$result = $db->insert('work',$work_info);
		}else{
			$has_work = $db->has('work',['AND'=>['id'=>$id,'unionid'=>$_SESSION['unionid']]]);
			if(!$has_work){
				echo json_encode(array(
					'status'=>'fail',
					'message'=>'发布失败，非法的作品id',
					));
				exit;
			}
			$old_status = $db->get('work','status',['AND'=>['id'=>$id,'unionid'=>$_SESSION['unionid']]]);
			if($old_status==1){
				//发布后又修改
				try{
					$redis = new Redis();
					$result = $redis->connect(REDIS_HOST);
					if(!$result){
						exit('redis服务没有开启！');
					}
				}catch(Exception $e){
					exit('没有redis扩展！');
				}
				$vote_count = $redis->zScore('vote', $id);
				$work_info['vote_count'] = $vote_count;
				$delete_from_redis = $redis->zRem('vote',$id);
				if(!$delete_from_redis){
					echo json_encode(array(
						'status'=>'fail',
						'message'=>'redis服务操作失败',
						));
					exit;
				}
			}
			$result = $db->update('work',$work_info,['id'=>$id]);
		}

	}else{
		echo json_encode(array(
			'status'=>'fail',
			'message'=>'参数status非法',
			));
		exit;
	}
		
	if($result){
		echo json_encode(array(
			'status'=>'success',
			'message'=>'修改成功',
			));
		exit; 
	}else{
		echo json_encode(array(
			'status'=>'fail',
			'message'=>'修改失败',
			));
		exit;
	}	
}