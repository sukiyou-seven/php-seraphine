<?php

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../error_code/error_code.php';

$output = G::get("response_data_g441g6aw8g4wg", array());

$code = G::get("code", "success");

$constant_name = 'error_code\\' . strtoupper($code);
$error_code = defined($constant_name) ? constant($constant_name) : \error_code\SUCCESS;

$send_data = array(
    'code' => $error_code['code'],
    'message' => $error_code['message'],
    'data' => $output
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
    $_SESSION['api_response_json'] = json_encode($send_data, JSON_PRETTY_PRINT, JSON_UNESCAPED_UNICODE);
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

