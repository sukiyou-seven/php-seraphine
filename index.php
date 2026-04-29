<?php
# 签名密钥
$sign_secret = "b76d016f63b88aa82eadca0954027b72c6d10126d9f4e495704f5377073f986c";

// 加载配置
$rsaConfig = require __DIR__ . '/.rsa/keys/rsa_config.php';

// 初始化 RSA
require_once __DIR__ . '/seraphine/tools/t_rsa/t_rsa.php';
RSA::init(
    $rsaConfig['rsa_private_key_path'],
    $rsaConfig['rsa_public_key_path']
);

# 跨域头设置
require __DIR__."/seraphine/header_config/header_config.php";

# 读取配置文件
require __DIR__."/seraphine/config/read_config.php";

# OPTIONS 请求
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit;
}

# 安装 反馈 页面
require __DIR__ . '/seraphine/setup/setup.php';
$result = Setup::copyErrorPages();

# 路由
require __DIR__. "/seraphine/route/route.php";


require_once __DIR__ . '/seraphine/g/g.php';
G::set("sign_secret", $sign_secret);
# ..........没了 就这样就行了

