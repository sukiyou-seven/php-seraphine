<?php

// Fatal Error 处理
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_COMPILE_ERROR, E_CORE_ERROR, E_PARSE])) {
        session_start();
        $_SESSION['error'] = $error;
        http_response_code(500);
    }
});