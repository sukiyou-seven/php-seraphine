<?php

require __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../error_code/error_code.php';


$code = G::get("code", "success");

$constant_name = 'error_code\\' . strtoupper($code);
$error_code = defined($constant_name) ? constant($constant_name) : \error_code\SUCCESS;

$output = G::get("response_data_seraphine");

try {
    foreach ($output as $key => $value) {
        if ($key == "_id") {
            $output['id'] = (string)$value;
            unset($output['_id']);
        }
        if ($key == "password") {
            $value = "密码保护协议";
        }
    }
} catch (Exception $e) {

}

try {
    $output['id'] = (string)$output['_id'];
    unset($output['_id']);
    $output['password'] = "密码保护协议";
} catch (Exception $e) {
}

try {
    $token = G::get("token", false);
} catch (Exception $e) {
}

$send_data = array(
    'code' => $error_code['code'],
    'message' => $error_code['message'],
    'data' => $output,
    'X-Token' => $token
);

$nums = G::get("nums", false);
if ($nums) {
    $send_data['nums'] = $nums;
}


if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // 存储到 Session
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['api_response_json'] = $send_data;
    http_response_code(320);
    exit;
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 设置 JSON 响应头
    try {
        header('Content-Type: application/json; charset=utf-8');
    } catch (Exception  $e) {

    }
    print_r(
        json_encode($send_data)
    );
    exit;
}

