<?php
require dirname(__FILE__) . '/common.php';
//获取评论信息
if (isGet()) {
    if (isAjax()) {
        $id = isset($_GET['id']) ? $_GET['id'] : '';

        if (!$id) {
            echo json_encode(array(
                'status' => 'fail',
                'message' => '作品id不能为空',
            ));
            exit;
        }

        $count = $db->count('comment', ['AND' => ['work_id' => $id, 'status' => 1]]);//计算总数量
        $page = isset($_GET['page']) ? $_GET['page'] : 0;
        $res = $db->select('comment', '*', ['AND' => ['work_id' => $id, 'status' => 1], 'LIMIT' => [$page * PAGE_NOTE_AUTHOR, PAGE_NOTE_AUTHOR]]);//前端page=1时，相应的传0过来

        if ($res) {
            echo json_encode(array(
                'status' => 'success',
                'message' => '查询成功',
                'current_page' => $page + 1,
                'last_page' => ceil($count / PAGE_NOTE_AUTHOR),
                'per_page' => PAGE_NOTE_AUTHOR,
                'total' => $count,
                'author' => $res
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
        exit('错误的请求方式');
    }
} else if (isPost()) {
    $content = isset($_POST['content']) ? $_POST['content'] : null;
    $id = isset($_POST['id']) ? $_POST['id'] : '';
    if (!$content) {
        echo json_encode(array(
            'status' => 'fail',
            'message' => '评论内容不能为空',
        ));
        exit;
    }

    if (!$id) {
        echo json_encode(array(
            'status' => 'fail',
            'message' => '作品id不能为空',
        ));
        exit;
    }

    $data = $db->get('work', ['status'], ['id' => $id]);
    if ($data['status'] != 1) {
        echo json_encode(array(
            'status' => fail,
            'message' => '无法对作品进行评论',
        ));
        exit;
    }

    $res = $db->insert('comment', ['work_id' => $id, 'unionid' => $_SESSION['unionid'], 'content' => $content, 'created_at' => time()]);
    $update = $db->update('work', ['comment_count[+]' => 1, 'last_comment_time' => time()], ['id' => $id]);
    if ($res) {
        echo json_encode(array(
            'status' => 'success',
            'message' => '作品评论成功',
        ));
        exit;
    } else {
        echo json_encode(array(
            'status' => 'fail',
            'message' => '作品评论失败',
        ));
        exit;
    }
}