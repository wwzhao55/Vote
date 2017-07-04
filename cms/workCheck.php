<?php
require 'init.php';
//作品审核列表，-1表未审核，1表已审核（通过）
if (isGet()) {
    if (isAjax()) {
    $status = isset($_GET['status']) ? $_GET['status'] : -1;
    $page = isset($_GET['page']) ? $_GET['page'] : 0;
    if ($status == -1) {
        $sql = "SELECT COUNT(*) AS 'total' FROM `work` LEFT JOIN `author` ON `work`.`unionid` = `author`.`unionid` WHERE `work`.`status` = -1";
        $sql1 = "SELECT `work`.`id`, `work`.`title`, `work`.`main_img_src`, `author`.`name` FROM `work` LEFT JOIN `author` ON `work`.`unionid` = `author`.`unionid` WHERE `work`.`status` = -1" . " LIMIT " . $page * PAGE_NOTE_AUTHOR . "," . PAGE_NOTE_AUTHOR;
    } else {
        $sql = "SELECT COUNT(*) AS 'total' FROM `work` LEFT JOIN `author` ON `work`.`unionid` = `author`.`unionid` WHERE `work`.`status` > -1";
        $sql1 = "SELECT `work`.`id`, `work`.`title`, `work`.`main_img_src`, `author`.`name` FROM `work` LEFT JOIN `author` ON `work`.`unionid` = `author`.`unionid` WHERE `work`.`status` > -1" . " LIMIT " . $page * PAGE_NOTE_AUTHOR . "," . PAGE_NOTE_AUTHOR;
    }

    $count = $db->query($sql)->fetchAll();//计算总数量
    $res = $db->query($sql1)->fetchAll();//前端page=1时，相应的传0过来
//    var_dump(ceil(12 / 10));exit;
    if ($res) {
        echo json_encode(array(
            'status' => 'success',
            'message' => '查询成功',
            'current_page' => $page + 1,
            'last_page' => ceil($count[0][0] / PAGE_NOTE_AUTHOR),
            'per_page' => PAGE_NOTE_AUTHOR,
            'total' => $count,
            'comment' => $res
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
        $main_html = file_get_contents('../resource/cms/workcheck.html');
        require '../resource/cms/layer.html';
        exit;
    }
} else if (isPost()) {
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
    $type = isset($_POST['type']) ? $_POST['type'] : '';
    $refuse_reason = isset($_POST['refuse_reason']) ? $_POST['refuse_reason'] : '';
    if ($type) {//type=1，作品审核通过；type=0，删除该作品
        $res = $db->update('work', ['status' => 1], ['id' => $id]);
        if ($res) {
            $old_vote_count = $db->get('work','vote_count',['id'=>$id]);
            $redis->zIncrBy('vote',$old_vote_count,$id);
            echo json_encode(array(
                'status' => 'success',
                'message' => '作品审核通过',
            ));
            exit;
        } else {
            echo json_encode(array(
                'status' => 'fail',
                'message' => '作品审核失败',
            ));
            exit;
        }
    } else {
        $res = $db->update('work', ['status' => -2, 'refuse_reason' => $refuse_reason], ['id' => $id]);
        if ($res) {
            echo json_encode(array(
                'status' => 'success',
                'message' => '作品审核不通过',
            ));
            exit;
        } else {
            echo json_encode(array(
                'status' => 'fail',
                'message' => '作品审核失败',
            ));
            exit;
        }
    }
}
