<?php
require dirname(__FILE__).'/common.php';
if(isGet()){
	exit('请求方式错误');
}else if(isPost()){
	if(is_weixin()){
		$serveID = isset($_POST['serveID'])?$_POST['serveID']:'';
		if(!$serveID){
			echo json_encode(array(
		 		'status'=>'fail',
		 		'message'=>'图片serveID不能为空！',
		 		));
		 	exit;
		}
		$access_token = $api->get_access_token();
		$url =  "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token={$access_token}&media_id={$serveID}";
		
		$img_info = downloadWeixinFile($url);

		if(!checkDownload($img_info)){
			echo json_encode(array(
		 		'status'=>'fail',
		 		'message'=>'图片下载失败！',
		 		));
		 	exit;
		}

		$img_name = time().'_weixin.jpg';

		$result = saveWeixinFile('public/uploads',$img_name,$img_info['body']);
		if(!$result){
			echo json_encode(array(
		 		'status'=>'fail',
		 		'message'=>'图片上传失败！',
		 		));
		 	exit;
		}else{
			echo json_encode(array(
		 		'status'=>'success',
		 		'message'=>'图片上传成功！',
		 		'url'=>'public/uploads/'.$img_name,
		 		));
		 	exit;
		}
	}else{
		if($upload->hasFile('img')){
			$upload->set('path','../public/uploads');
			if($upload->upload('img')){
				$img_url = 'public/uploads/'.$upload->getFileName();
				echo json_encode(array(
					'status'=>'success',
					'url'=>$img_url,
					'message'=>'图片上传成功'
					));
				exit;
			}else{
				echo json_encode(array(
					'status'=>'fail',
					'message'=>'图片上传失败'
					));
				exit;
			}
		}else{
			echo json_encode(array(
				'status'=>'fail',
				'message'=>'没有上传的图片'
				));
			exit;
		}
	}
	
}