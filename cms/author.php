<?php
require 'init.php';
//获取选手信息.
if (isGet()) {
    if (isAjax()) {//ajax请求数据
//        var_dump(api::get_all_author());
        /*$data = $db->get('author', ['mobile'], ['unionid' => 'faewrwa3']);
        if($_GET['mobile'] == $data['mobile']){
            var_dump("电话号码未改变");
        }
        var_dump($_GET['mobile'] == $data['mobile']);exit;*/
        $count = $db->count('author', ['id[>]' => 0]);//计算总数量
        $page = $_GET['page'];
        $res = $db->select('author', '*', ['id[>]' => 0, 'LIMIT' => [$page * PAGE_NOTE_AUTHOR, PAGE_NOTE_AUTHOR]]);//前端page=1时，相应的传0过来
//        var_dump($res[1]['id']);exit;
        /*$name = $_GET['name'];
        var_dump($name);*/
        /*$sql = "SELECT * FROM `author` LIMIT 10,5";
        $res = $db->query($sql)->fetchAll();*/
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
        $main_html = file_get_contents('../resource/cms/author.html');
        require '../resource/cms/layer.html';
        exit;
    }
} else if (isPost()) {

}