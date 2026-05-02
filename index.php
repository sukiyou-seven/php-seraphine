<?php

set_error_handler(function($errno, $errstr) {
    throw new ErrorException($errstr, $errno);
});

require_once __DIR__ . '/seraphine/g/g.php';


// 加载配置
$rsaConfig = require __DIR__ . '/.rsa/keys/rsa_config.php';

// 初始化 RSA
require_once __DIR__ . '/seraphine/tools/t_rsa/t_rsa.php';

try {
    RSA::init(
        $rsaConfig["rsa_private_key_path"],
        $rsaConfig["rsa_public_key_path"]
    );
} catch (Exception $e) {

}

// 初始化 Token
require_once __DIR__ . '/seraphine/config/read_config.php';
$sign_secret = ReadConfig::read_yml("token");
require_once __DIR__ . '/seraphine/tokens/token.php';
Token::init($sign_secret['token']['secret'], 'HS256', 7200); // 2小时过期


# 跨域头设置
require_once __DIR__ . "/seraphine/header_config/header_config.php";

# 读取配置文件
require_once __DIR__ . "/seraphine/config/read_config.php";

# OPTIONS 请求
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit;
}

# 安装 反馈 页面
require_once __DIR__ . '/seraphine/setup/setup.php';
$result = Setup::copyErrorPages();

# 路由
require_once __DIR__ . "/seraphine/route/route.php";


# ..........没了 就这样就行了

