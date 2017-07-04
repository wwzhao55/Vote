<?php
require 'init.php';
//获取活动信息
if (isGet()) {
    if (isAjax()) {
        $data = $db->get('introduce', ['work_order_type']);
//        var_dump($data['work_order_type']);exit;
        $work_order_type = isset($_GET['work_order_type']) ? $_GET['work_order_type'] : 0;

        if($work_order_type == 4){
            echo json_encode(array(
                'status' => 'success',
                'message' => $data['work_order_type'],
            ));
            exit;
        }

        if($data['work_order_type'] == $work_order_type){
            echo json_encode(array(
                'status' => 'success',
                'message' => '查询方式未改动',
            ));
            exit;
        }

        $update = $db->update('introduce', ['work_order_type' => $work_order_type]);
        if ($update) {
            echo json_encode(array(
                'status' => 'success',
                'message' => '查询方式修改成功',
            ));
            exit;
        } else {
            echo json_encode(array(
                'status' => 'fail',
                'message' => '查询方式修改失败',
            ));
            exit;
        }
    } else {
        $main_html = file_get_contents('../resource/cms/workorder.html');
        require '../resource/cms/layer.html';
        exit;
    }
} else if (isPost()) {

}