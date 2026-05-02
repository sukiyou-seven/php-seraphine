<?php
require_once __DIR__ . "/../config/read_config.php";


// Fatal Error 处理
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_COMPILE_ERROR, E_CORE_ERROR, E_PARSE])) {
        session_start();
        $_SESSION['error'] = $error;
        # POST 取消重定向
        $app_config = ReadConfig::read_yml("app");
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!$app_config['app']['debug']) {
                http_response_code(500);
                exit();
            }else{
                print_r($error);
                exit();
            }
        }
        else{
            http_response_code(500);
        }

    }
});