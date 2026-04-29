<?php

require __DIR__ . "/../g/g.php";

//print_r($_SERVER['REQUEST_URI']);

// 清理路径，去掉前导斜杠
$uri = trim($_SERVER['REQUEST_URI'], '/');
$uri = strtok($uri, '?');  // 去掉查询参数
//print_r($uri);

# 调试模式 按道理 我不应该允许任何 GET 请求 但是开发阶段需要便捷测试
$app_config = ReadConfig::read_yml("app");


if ($uri == 'upload') {
    return require __DIR__ . "/../tools/t_upload/t_upload.php";
}


# 阻止 GET 请求
if (!$app_config['app']['debug']) {
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        # 将 get 请求 重定向到
        # 可用的已装修页面 320 403 404 500 502
        http_response_code(404);
        exit;
    }
}

# 以下须 开启 debug 模式

if ($uri == "openapi2.0") {
    # 返回vue打包后文件
    return require __DIR__ . "/../auto_api/dist/index.html";
}
if ($uri == "openapi1.0") {
    # 返回php文件
    return require __DIR__ . "/../auto_api/openapi.php";
}

if ($uri == "open_api") {
    require_once __DIR__ . "/../auto_api/auto_open_api.php";
    $Auto_api = new Auto_open_api();
    header('Content-Type: application/json; charset=utf-8');
    $res = $Auto_api->scanControllers();
    $base_url = $Auto_api->getBaseUrl();

    $send = [
        "base_url" => $base_url,
        "api_list" => $res,
        "times" => date('Y-m-d')
    ];

    print_r(json_encode($send, JSON_PRETTY_PRINT, JSON_UNESCAPED_UNICODE));
    return;
}

if ($uri == "help") {
    require_once __DIR__ . "/../config/read_config.php";
    $host_name = $_SERVER['HTTP_HOST'];
    $rc = new ReadConfig();
    $rc->write_yaml("app", "base", "http://" . $host_name);
    $rc->ymlToJson("app");
    return require __DIR__ . "/../help/help.php";
}

if ($uri == "rsa") {
    return require __DIR__ . "/../../.rsa/create_pem.php";
}


require_once __DIR__ . "/error_dispose.php";
$parts = explode('/', $uri);
$file_path = '/' . $parts[0];

if (count($parts) > 2) {
    $file_path = "";
    for ($i = 0; $i < count($parts) - 1; $i++) {
        $file_path .= '/' . $parts[$i];
    }
}


$file = __DIR__ . "/../../application/controllers" . $file_path . ".php";


if (file_exists($file)) {
    require_once $file;

    // 解析 URL 获取类名和方法
    // 例如: /user/info → User 类 + info() 方法
    $className = ucfirst($parts[count($parts) - 2]);  // User
    $method = $parts[count($parts) - 1] ?? 'index';   // info

    if (!$app_config['app']['debug']) {
        // 实例化并调用
        $controller = new $className();
        $res = $controller->$method();

        require_once __DIR__ . '/../../seraphine/sign_ctrl/sign_ctrl.php';
        if (!SignCtrl::verify()) {
            G::set("code", "USER_ERROR_A0341");
            G::set("response_data_g441g6aw8g4wg", "");
            require_once __DIR__ . '/../../seraphine/return_data/return_data.php';
            exit;
        }
    } else {
        $controller = new $className();
        $res = $controller->$method();
        G::set("response_data_g441g6aw8g4wg", $res);
    }

    # 格式化返回值
    require_once __DIR__ . "/../../seraphine/return_data/return_data.php";

} else {
    http_response_code(404);
    echo "Controller not found";
}

