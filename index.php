<?php
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

# ..........没了 就这样就行了

