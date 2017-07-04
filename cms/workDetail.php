<?php
require 'init.php';
//作品审核列表，作品详情
if (isGet()) {
    if (isAjax()) {
        /*$sql = "SELECT `work`.`id`, `work`.`title`, `work`.`main_img_src`, `work_img`.`img_src`, `work_img`.`order` FROM `work` LEFT JOIN `work_img` ON `work`.`id` = `work_img`.`work_id` WHERE `work`.`id` = ". $_GET['id'];
        $res = $db->query($sql)->fetchAll();*/
        $res = $db->select('work', ['title', 'main_img_src', 'description', 'img'], ['id' => $_GET['id']]);
//        var_dump(explode(";", $res[0]['img']));exit;
//        $res1 = $db->select('work_img', ['img_src', 'order'], ['work_id' => $_GET['id']]);
        $res['img'] = explode(";", $res[0]['img']);
//    $res['img'] = $res1;
//    var_dump($res1);exit;
        if ($res) {
            echo json_encode(array(
                'status' => 'success',
                'message' => '查询成功',
                'work' => $res
            ));
            exit;
        } else {
            echo json_encode(array(
                'status' => 'fail',
                'message' => '查询失败',
            ));
            exit;
        }
    } else {
        $main_html = file_get_contents('../resource/cms/workdetail.html');
        require '../resource/cms/layer.html';
        exit;
    }
} else if (isPost()) {//删除该作品相关信息
    try{
        $redis = new Redis();
        $result = $redis->connect(REDIS_HOST);
        if(!$result){
            exit('redis服务没有开启！');
        }
    }catch(Exception $e){
        exit('没有redis扩展！');
    }
    $id = isset($_POST['id']) ? $_POST['id'] : '';
    $del_work = $db->delete('work', ['id' => $id]);
    $del_work_img = $db->delete('work_img', ['work_id' => $id]);
    $del_comment = $db->delete('comment', ['work_id' => $id]);
    $del_vote = $db->delete('vote', ['work_id' => $id]);
    $delete_from_redis = $redis->Zrem('vote',$id);
    if(!$delete_from_redis){
        echo json_encode(array(
            'status'=>'fail',
            'message'=>'redis服务操作失败',
            ));
        exit;
    }
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