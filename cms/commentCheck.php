<?php
require 'init.php';
//评论审核，通过或删除
if (isGet()) {
    if (isAjax()) {
        $count = $db->count('comment', ['AND' => ['id[>]' => 0, 'status' => 0]]);//计算总数量
        $page = isset($_GET['page']) ? $_GET['page'] : 0;
        $res = $db->select('comment', '*', ['status' => 0, 'LIMIT' => [$page * PAGE_NOTE_COMMENT, PAGE_NOTE_COMMENT]]);//前端page=1时，相应的传0过来
        foreach ($res as &$comment) {
     
            $comment['work_title'] = $db->get('work', 'title', ['id' => $comment['work_id']]);
        }
//        var_dump($count);exit;
        /*$name = $_GET['name'];
        var_dump($name);*/
        /*$sql = "SELECT * FROM `author` LIMIT 10,5";
        $res = $db->query($sql)->fetchAll();*/
        if ($res) {
            echo json_encode(array(
                'status' => 'success',
                'message' => '查询成功',
                'current_page' => $page + 1,
                'last_page' => ceil($count / PAGE_NOTE_COMMENT),
                'per_page' => PAGE_NOTE_COMMENT,
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
        $main_html = file_get_contents('../resource/cms/commentcheck.html');
        require '../resource/cms/layer.html';
        exit;
    }
} else if (isPost()) {
    $id = isset($_POST['id']) ? $_POST['id'] : '';
//    var_dump($id);exit;
    $type = isset($_POST['type']) ? $_POST['type'] : '';
    if ($type) {//type=1，评论审核通过；type=0，删除该评论
        $res = $db->update('comment', ['status' => 1], ['id' => $id]);
        if ($res) {
            echo json_encode(array(
                'status' => 'success',
                'message' => '评论审核成功',
            ));
            exit;
        } else {
            echo json_encode(array(
                'status' => 'fail',
                'message' => '评论审核失败',
            ));
            exit;
        }
    } else {
        $res = $db->delete('comment', ['id' => $id]);
        if ($res) {
            echo json_encode(array(
                'status' => 'success',
                'message' => '评论删除成功',
            ));
            exit;
        } else {
            echo json_encode(array(
                'status' => 'fail',
                'message' => '评论删除失败',
            ));
            exit;
        }
    }
}