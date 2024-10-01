<?php
session_start();
include '../database/connection.php';
$response = array("Error" => "", "msg" => "");
$username = $_POST['username'];
$password = $_POST['password'];
try {
    //check if valid user
    $params = [$username, $password];
    $result = Connection::_executeQuery("select * from admin_user where admin_username = ? and admin_password = ?", $params);

    $response['Error'] = false;
    $response['msg'] = $result['count'];
    echo json_encode($response);
    exit();
} catch (Exception $e) {
    $response['Error'] = true;
    $response['msg'] = $e->getMessage();
    echo json_encode($response);
    exit();
}
