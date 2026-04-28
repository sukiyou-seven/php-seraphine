<?php

require __DIR__."/../g/g.php";

//print_r($_SERVER['REQUEST_URI']);

// 清理路径，去掉前导斜杠
$uri = trim($_SERVER['REQUEST_URI'], '/');
$uri = strtok($uri, '?');  // 去掉查询参数
//print_r($uri);

# 调试模式 按道理 我不应该允许任何 GET 请求 但是开发阶段需要便捷测试
$app_config = ReadConfig::read_yml("app");

if($uri == "help"){
    if ($app_config['app']['debug']){
        return require __DIR__ . "/../help/help.php";
    }

}
if($uri == 'upload'){
    return require __DIR__ . "/../tools/t_upload/t_upload.php";
}
if($uri == "openapi"){
    return require __DIR__ . "/../auto_api/openapi.php";
}

if (!$app_config['app']['debug']){
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        http_response_code(404);
        exit;
    }
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


    // 实例化并调用
    $controller = new $className();
    $res = $controller->$method();

    G::set("response_data_g441g6aw8g4wg", $res);

    # 格式化返回值
    require __DIR__ . "/../../seraphine/return_data/return_data.php";

} else {
    http_response_code(404);
    echo "Controller not found";
}

