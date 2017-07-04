<?php
require 'init.php';
//获取活动信息
if(isGet()){
	if(isAjax()){
		$has_introduce = $db->count('introduce',['id[>]'=>0]);
		if($has_introduce==0){
			echo json_encode(array(
				'status'=>'fail',
				'message'=>'暂无数据',
				));
			exit;
		}else{
			$introduce = $db->get('introduce','*',['id[>]'=>0]);
			$work_type = $db->select('work_type','*',['id[>]'=>0]);
			$introduce['work_type'] = $work_type;
			echo json_encode(array(
				'status'=>'success',
				'message'=>'获取数据成功',
				'data'=>$introduce,
				));
			exit;
		}
	}else{
		$main_html = file_get_contents('../resource/cms/introduce.html');
		require '../resource/cms/layer.html';
		exit;
	}
}else if(isPost()){
	$introduce = isset($_POST['introduce'])?$_POST['introduce']:'';
	$reward = isset($_POST['reward'])?$_POST['reward']:'';
	$work_judge = isset($_POST['work_judge'])?$_POST['work_judge']:'';
	$work_type = isset($_POST['work_type'])?$_POST['work_type']:'';
	if($introduce==''){
		echo json_encode(array(
			'status'=>'fail',
			'message'=>'introduce参数不能为空',
			));
		exit;
	}
	if(!$reward){
		echo json_encode(array(
			'status'=>'fail',
			'message'=>'reward参数不能为空',
			));
		exit;
	}
	if(!$work_judge){
		echo json_encode(array(
			'status'=>'fail',
			'message'=>'work_judge参数不能为空',
			));
		exit;
	}
	if(!is_array($work_type)){
		echo json_encode(array(
			'status'=>'fail',
			'message'=>'work_type参数必须是数组类型',
			));
		exit;
	}

	
	$flag = true;
	//编辑	
	$db->action(function($db) use ($introduce,$reward,$work_judge,$work_type,&$flag) {
	    try{
	    	$has_introduce = $db->count('introduce',['id[>]'=>0]);
	    	if($has_introduce){
	    		$db->update('introduce',[
					'introduce'=>$introduce,
					'reward'=>$reward,
					'work_judge'=>$work_judge,
					'created_at'=>time(),
					],['id[>]'=>0]);
	    	}else{
	    		$db->insert('introduce',[
					'introduce'=>$introduce,
					'reward'=>$reward,
					'work_judge'=>$work_judge,
					'created_at'=>time(),
					]);
	    	}	    	
			$db->query('truncate table work_type');
			foreach ($work_type as $value) {
				if(isset($value['type'])&&isset($value['description'])&&!empty($value['type'])&&!empty($value['description'])){
					$value['created_at'] = time();
					$db->insert('work_type',$value);
				}else{
					$flag = false;
	    			return false;
				}
				
			}
	    }catch(exception $e){
	    	$flag = false;
	    	return false;
	    }
	});
	if($flag){
		echo json_encode(array(
			'status'=>'success',
			'message'=>'修改数据成功',
			));
		exit;
	}else{
		echo json_encode(array(
			'status'=>'fail',
			'message'=>'修改数据失败.',
			));
		exit;
	}


}