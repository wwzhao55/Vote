<?php
require 'init.php';
//获取活动信息
if(isGet()){
	if(isAjax()){
		$has_share_config = $db->count('share_config',['id[>]'=>0]);
		if($has_share_config==0){
			echo json_encode(array(
				'status'=>'fail',
				'message'=>'暂无数据',
				));
			exit;
		}else{
			$share_config = $db->get('share_config','*',['id[>]'=>0]);
			echo json_encode(array(
				'status'=>'success',
				'message'=>'获取数据成功',
				'data'=>$share_config,
				));
			exit;
		} 
	}else{
		$main_html = file_get_contents('../resource/cms/shareconfig.html');
		require '../resource/cms/layer.html';
		exit;
	}
}else if(isPost()){
	$title = isset($_POST['title'])?$_POST['title']:'';
	$description = isset($_POST['description'])?$_POST['description']:'';

	$data = [];
	if($title){
		$data['title'] = $title;
	}
	if($description){
		$data['description'] = $description;
	}
	if($upload->hasFile('img')){
		$upload->set('path','../public/uploads');
		if($upload->upload('img')){
			$img_url = 'public/uploads/'.$upload->getFileName();
			$data['img'] = $img_url;
		}else{
			echo json_encode(array(
				'status'=>'fail',
				'message'=>'图片上传失败'
				));
			exit;
		}
	}
	$data['created_at'] = time();

	$has_share_config = $db->count('share_config',['id[>]'=>0]);
	if($has_share_config){
		//编辑
		$result = $db->update('share_config',$data,['id[>]'=>0]);
	}else{
		//新增
		if(isset($data['title'])&&isset($data['description'])&&isset($data['img'])){
			$result = $db->insert('share_config',$data);
		}else{
			echo json_encode(array(
				'status'=>'fail',
				'message'=>'参数缺失'
				));
			exit;
		}
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