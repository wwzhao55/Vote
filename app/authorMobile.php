<?php
require dirname(__FILE__) . '/common.php';
//修改手机号码
if (isGet()) {
    if (isAjax()) {

    } else {
        $main_html = file_get_contents('resource/app/authormobile.html');
        require 'resource/app/layer.html';
        exit;
    }
} else if (isPost()) {
    $mobile = isset($_POST['mobile']) ? $_POST['mobile'] : null;
    $name = isset($_POST['name']) ? $_POST['name'] : null;
    $email = isset($_POST['email']) ? $_POST['email'] : null;
    $nationality = isset($_POST['nationality']) ? $_POST['nationality'] : null;

    $update_info = array();

    if ($mobile!=null) {
        if (!preg_match('/^1[34578]\d{9}$/', $mobile)) {
            echo json_encode(array(
                'status' => 'fail',
                'message' => '手机号输入格式不正确',
            ));
            exit;
        }
        $update_info['mobile'] = $mobile; 
    }
    if($name!=null){
        $update_info['name'] = $name;
    }
    if($email!=null){
        if(!preg_match("/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i", $email)){
            echo json_encode(array(
            'status'=>'fail',
            'message'=>'邮箱格式错误',
            ));
            exit;
        }
        $update_info['email'] = $email;
    }
    if($nationality!=null){
        $update_info['nationality'] = $nationality;
    }
    if(count($update_info)==0){
        echo json_encode(array(
        'status'=>'fail',
        'message'=>'没有更新的信息',
        ));
        exit;
    }

    $result = $db->update('author', $update_info, ['unionid' => $_SESSION['unionid']]);
    if ($result) {
        echo json_encode(array(
            'status' => 'success',
            'message' => '修改成功'
        ));
        exit;
    } else {
        echo json_encode(array(
            'status' => 'fail',
            'message' => '修改失败'
        ));
        exit;
    }
}