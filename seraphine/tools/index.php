<?php


function user_ip()
{
    return $_SERVER['REMOTE_ADDR'];
}

function rec_post()
{
    $data = (file_get_contents("php://input"));
    $data = json_decode($data);
    return $data;
}

function send_json($data, $error_code = '00000', $msg = '', $num = 0, $status = 200)
{
    $status = array(
        'status' => $status,
        'data' => $data,
        'num' => $num,
        'code' => $error_code,
        'message' => $msg
    );
    $send = json_encode($status);
    return $send;
}

function send_msg($data = array(), $error_code = \error_code\SUCCESS, $num = 0)
{
    if ($error_code['code'] == "00000") {
        $error_code['data'] = $data;
        $error_code['num'] = $num;
    } else {
        $error_code['data'] = $data;
    }
    $error_code = json_encode($error_code, JSON_UNESCAPED_UNICODE);
    return $error_code;
}

