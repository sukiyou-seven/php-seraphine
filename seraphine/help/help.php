<?php

$action = isset($_GET['action']) ? $_GET['action'] : '';


if ($action === 'get_db_config') {
    header('Content-Type: application/json');
    $dbConfigPath = __DIR__ . '/../../config/db.yml';
    $dbMysqlConfigPath = __DIR__ . '/../../config/db_mysql.yml';

    $result = ['success' => true];

    if (file_exists($dbConfigPath)) {
        $content = file_get_contents($dbConfigPath);
        $config = parseYamlSimple($content);
        $result['mongodb'] = isset($config['mongodb']) ? $config['mongodb'] : [];
    } else {
        $result['mongodb'] = [];
    }

    if (file_exists($dbMysqlConfigPath)) {
        $content = file_get_contents($dbMysqlConfigPath);
        $config = parseYamlSimple($content);
        $result['mysql'] = isset($config['mysql']) ? $config['mysql'] : [];
    } else {
        $result['mysql'] = [];
    }

    echo json_encode($result);
    exit;
}

if ($action === 'save_mongodb_config') {
    header('Content-Type: application/json');

    $input = file_get_contents('php://input');
    $config = json_decode($input, true);

    if (!$config) {
        echo json_encode([
                'success' => false,
                'message' => 'data error'
        ]);
        exit;
    }

    $dbConfigPath = __DIR__ . '/../../config/db.yml';

    try {
        $existingConfig = [];
        if (file_exists($dbConfigPath)) {
            $content = file_get_contents($dbConfigPath);
            $existingConfig = parseYamlSimple($content);
        }

        $yamlContent = generateMongoDbYaml($config, $existingConfig);

        if (file_put_contents($dbConfigPath, $yamlContent) !== false) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode([
                    'success' => false,
                    'message' => 'write error'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
        ]);
    }
    exit;
}

if ($action === 'save_mysql_config') {
    header('Content-Type: application/json');

    $input = file_get_contents('php://input');
    $config = json_decode($input, true);

    if (!$config) {
        echo json_encode([
                'success' => false,
                'message' => 'data error'
        ]);
        exit;
    }

    $dbMysqlConfigPath = __DIR__ . '/../../config/db_mysql.yml';

    try {
        $existingConfig = [];
        if (file_exists($dbMysqlConfigPath)) {
            $content = file_get_contents($dbMysqlConfigPath);
            $existingConfig = parseYamlSimple($content);
        }

        $yamlContent = generateMysqlYaml($config, $existingConfig);

        if (file_put_contents($dbMysqlConfigPath, $yamlContent) !== false) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode([
                    'success' => false,
                    'message' => 'write error'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
        ]);
    }
    exit;
}

function parseYamlSimple($content)
{
    $result = [];
    $lines = explode("\n", $content);
    $currentSection = null;

    foreach ($lines as $line) {
        $line = trim($line);

        if (empty($line) || strpos($line, '#') === 0) {
            continue;
        }

        if (preg_match('/^(\w+):$/', $line, $matches)) {
            $currentSection = $matches[1];
            $result[$currentSection] = [];
        } elseif (preg_match('/^\s+(\w+):\s*(.+)$/', $line, $matches) && $currentSection) {
            $result[$currentSection][$matches[1]] = $matches[2];
        }
    }

    return $result;
}

function generateMongoDbYaml($config, $existingConfig)
{
    $yaml = "mongodb:\n";
    $yaml .= "    host: " . (isset($config['host']) ? $config['host'] : 'localhost') . "\n";
    $yaml .= "    port: " . (isset($config['port']) ? $config['port'] : '27017') . "\n";
    $yaml .= "    database: " . (isset($config['database']) ? $config['database'] : 'test') . "\n";
    $yaml .= "    username: " . (isset($config['username']) ? $config['username'] : '') . "\n";
    $yaml .= "    password: " . (isset($config['password']) ? $config['password'] : '') . "\n";
    $yaml .= "    auth_source: " . (isset($config['auth_source']) ? $config['auth_source'] : 'admin') . "\n";

    return $yaml;
}

function generateMysqlYaml($config, $existingConfig)
{
    $yaml = "mysql:\n";
    $yaml .= "    host: " . (isset($config['host']) ? $config['host'] : 'localhost') . "\n";
    $yaml .= "    port: " . (isset($config['port']) ? $config['port'] : '3306') . "\n";
    $yaml .= "    database: " . (isset($config['database']) ? $config['database'] : 'test') . "\n";
    $yaml .= "    username: " . (isset($config['username']) ? $config['username'] : '') . "\n";
    $yaml .= "    password: " . (isset($config['password']) ? $config['password'] : '') . "\n";
    $yaml .= "    charset: " . (isset($config['charset']) ? $config['charset'] : 'utf8mb4') . "\n";
    $yaml .= "    time_zone: " . (isset($config['time_zone']) ? $config['time_zone'] : '+08:00') . "\n";

    return $yaml;
}


$baseUrl = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$baseUrl .= $_SERVER['HTTP_HOST'];
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seraphine 帮助 文档</title>
    <link rel="stylesheet" href="../../static/css/help.css">
    <script src="https://cdn.jsdelivr.net/npm/markdown-it/dist/markdown-it.min.js"></script>
</head>
<body>
<div class="sidebar">
    <h3>📑 目录导航</h3>
    <ul class="nav-list">
        <li><a href="#attention" class="active">⚠ 注意事项</a></li>
        <li><a href="#api-doc">📚 API 文档</a></li>
        <li><a href="#rsa-doc">🔑 RSA 密钥</a></li>
        <li><a href="#mongodb_setting">📦 MongoDB 设置</a></li>
        <li><a href="#mysql_setting">📦 Mysql 设置</a></li>
        <li><a href="#routing">📍 路由规则</a></li>
        <li><a href="#request">📝 请求数据</a></li>
        <li><a href="#response">📤 响应格式</a></li>
        <li><a href="#tools">🔧 常用工具</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="header">
        <h1>🚀 Seraphine 帮助</h1>
        <p>轻量级 PHP API 框架文档</p>
    </div>
    <div class="card" id="attention">
        <h2>⚠ 注意事项</h2>
        <div class="info-box warning">
            <strong>⚠️ 注意：</strong>该页面 在 <code>debug = false</code> 的状态下不可访问
        </div>

        <div class="info-box warning">
            <strong>⚠️ 注意：</strong>请在 <code>根目录/index.php</code> 文件中更改 <code>$sign_secret</code> 值
            可使用 <code>根目录/sign_create.php</code> 生成
        </div>
        <div class="info-box warning">
            <strong>⚠️ 注意：</strong> 请仔细阅读本页面
        </div>

        <div class="info-box warning">
            <strong>⚠️ 注意：</strong> 在 debug 模式下,支持以GET方式传递参数
            <code>?a=1&b=2&c=3</code><br/>
            但这只是暂时的测试功能，请勿用于生产环境
        </div>
    </div>
    <div class="card" id="api-doc">
        <h2>📚 API 接口文档</h2>
        <div class="route-example">
            <div><strong>访问地址：</strong><a href="<?php echo $baseUrl; ?>/openapi" target="_blank"
                                              style="color: #667eea; text-decoration: none;">
                    <?php echo $baseUrl; ?>/openapi</a></div>
            <div style="margin-top: 10px; color: #718096;">自动生成 · 实时同步 · 完整详细· <code>完成本页面后回来再看</code></div>
        </div>
    </div>

    <div class="card" id="rsa-doc">
        <h2>🔑 RSA </h2>
        <div class="route-example">
            <div><strong>访问地址：</strong><a href="<?php echo $baseUrl; ?>/rsa" target="_blank"
                                              style="color: #667eea; text-decoration: none;">
                    <?php echo $baseUrl; ?>/rsa</a></div>
            <div style="margin-top: 10px; color: #718096;">自动生成 · <code>请先去生成</code></div>
        </div>
    </div>


    <div class="card" id="mysql_setting">
        <h2>📦 MongoDB数据库设置</h2>

        <div style="margin-bottom: 30px;">
            <div class="route-example">
                <form id="mongodb-config-form" style="max-width: 600px;">
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; color: #667eea; font-weight: 600;">Host:</label>
                        <input type="text" id="mongodb-host" name="host" placeholder="例如: localhost"
                               style="width: 100%; padding: 10px; border: 2px solid #e2e8f0; border-radius: 6px; font-size: 1em;"/>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; color: #667eea; font-weight: 600;">Port:</label>
                        <input type="number" id="mongodb-port" name="port" placeholder="例如: 27017"
                               style="width: 100%; padding: 10px; border: 2px solid #e2e8f0; border-radius: 6px; font-size: 1em;"/>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; color: #667eea; font-weight: 600;">Database:</label>
                        <input type="text" id="mongodb-database" name="database" placeholder="例如: mydb"
                               style="width: 100%; padding: 10px; border: 2px solid #e2e8f0; border-radius: 6px; font-size: 1em;"/>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; color: #667eea; font-weight: 600;">Username:</label>
                        <input type="text" id="mongodb-username" name="username" placeholder="用户名（可选）"
                               style="width: 100%; padding: 10px; border: 2px solid #e2e8f0; border-radius: 6px; font-size: 1em;"/>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; color: #667eea; font-weight: 600;">Password:</label>
                        <input type="password" id="mongodb-password" name="password" placeholder="密码（可选）"
                               style="width: 100%; padding: 10px; border: 2px solid #e2e8f0; border-radius: 6px; font-size: 1em;"/>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; color: #667eea; font-weight: 600;">Auth
                            Source:</label>
                        <input type="text" id="mongodb-auth-source" name="auth_source" placeholder="例如: admin"
                               style="width: 100%; padding: 10px; border: 2px solid #e2e8f0; border-radius: 6px; font-size: 1em;"/>
                    </div>

                    <button type="submit"
                            style="padding: 12px 30px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 1em; font-weight: 600; transition: all 0.3s ease;">
                        💾 保存 MongoDB 配置
                    </button>

                    <div id="mongodb-save-result"
                         style="margin-top: 15px; padding: 10px; border-radius: 6px; display: none;"></div>
                </form>
            </div>
        </div>

    </div>
    <div class="card" id="mongodb_setting">
        <h2>📦 Mysql 数据库设置</h2>


        <div>
            <div class="route-example">
                <form id="mysql-config-form" style="max-width: 600px;">
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; color: #667eea; font-weight: 600;">Host:</label>
                        <input type="text" id="mysql-host" name="host" placeholder="例如: localhost"
                               style="width: 100%; padding: 10px; border: 2px solid #e2e8f0; border-radius: 6px; font-size: 1em;"/>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; color: #667eea; font-weight: 600;">Port:</label>
                        <input type="number" id="mysql-port" name="port" placeholder="例如: 3306"
                               style="width: 100%; padding: 10px; border: 2px solid #e2e8f0; border-radius: 6px; font-size: 1em;"/>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; color: #667eea; font-weight: 600;">Database:</label>
                        <input type="text" id="mysql-database" name="database" placeholder="例如: mydb"
                               style="width: 100%; padding: 10px; border: 2px solid #e2e8f0; border-radius: 6px; font-size: 1em;"/>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; color: #667eea; font-weight: 600;">Username:</label>
                        <input type="text" id="mysql-username" name="username" placeholder="用户名（可选）"
                               style="width: 100%; padding: 10px; border: 2px solid #e2e8f0; border-radius: 6px; font-size: 1em;"/>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; color: #667eea; font-weight: 600;">Password:</label>
                        <input type="password" id="mysql-password" name="password" placeholder="密码（可选）"
                               style="width: 100%; padding: 10px; border: 2px solid #e2e8f0; border-radius: 6px; font-size: 1em;"/>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; color: #667eea; font-weight: 600;">Charset:</label>
                        <input type="text" id="mysql-charset" name="charset" placeholder="例如: utf8mb4"
                               style="width: 100%; padding: 10px; border: 2px solid #e2e8f0; border-radius: 6px; font-size: 1em;"/>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; color: #667eea; font-weight: 600;">Time
                            Zone:</label>
                        <input type="text" id="mysql-time-zone" name="time_zone" placeholder="例如: +08:00"
                               style="width: 100%; padding: 10px; border: 2px solid #e2e8f0; border-radius: 6px; font-size: 1em;"/>
                    </div>

                    <button type="submit"
                            style="padding: 12px 30px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 1em; font-weight: 600; transition: all 0.3s ease;">
                        💾 保存 MySQL 配置
                    </button>

                    <div id="mysql-save-result"
                         style="margin-top: 15px; padding: 10px; border-radius: 6px; display: none;"></div>
                </form>
            </div>
        </div>
    </div>


    <div class="card" id="routing">
        <h2>📍 路由规则</h2>

        <div class="section">
            <h3>基本路由映射</h3>
            <div class="route-example">
                <div><strong>URL:</strong> <span class="url">example.com/user/login</span></div>
                <div><strong>URL:</strong> <span class="url">example.com/文件名/函数名</span></div>
                <div><strong>URL:</strong> <span class="url">example.com/文件夹名*/文件名/函数名</span></div>
            </div>

            <div class="file-tree">
                <span class="dir sst">application/</span>
                <span class="dir con sst">└── controllers/</span>
                <span class="file sst">└── user.php 路由从这里开始</span>
                <span class="classt sst">└── class User</span>
                <span class="method sst">└── function login()</span>
            </div>
        </div>

        <div class="section">
            <h3>路由示例</h3>
            <div class="route-example">
                <div><span class="url">/user/create_user</span> → User::create_user()</div>
                <div style="margin-top: 10px;"><span class="url">/product/list</span> → Product::list()</div>
                <div style="margin-top: 10px;"><span class="url">/order/detail</span> → Order::detail()</div>
            </div>
        </div>

        <div class="info-box">
            <strong>💡 提示：</strong>控制器文件位于 <code>application/controllers/</code> 目录，类名首字母大写，方法名小写。
        </div>
    </div>

    <div class="card" id="request">
        <h2>📝 请求数据</h2>

        <div class="section">
            <h3>Debug 模式</h3>
            <p>当 <code>config/app.yml</code> 中 <code>debug: true</code> 时：</p>
            <ul style="margin-left: 20px; margin-top: 10px; line-height: 2;">
                <li>✅ 支持 GET 参数传递 不建议生产环境使用</li>
                <li>✅ 支持 POST JSON Body</li>
            </ul>
        </div>

        <div class="section">
            <h3>生产模式</h3>
            <p>当 <code>debug: false</code> 时：</p>
            <ul style="margin-left: 20px; margin-top: 10px; line-height: 2;">
                <li>❌ 禁止 GET 请求</li>
                <li>✅ 仅支持 POST JSON Body</li>
            </ul>
        </div>
    </div>

    <div class="card" id="response">
        <h2>📤 响应格式</h2>

        <div class="section">
            <h3>标准响应结构</h3>
            <div class="file-tree">
                <pre class="json-font">{
<span class="method json-font ">"code"</span>:<span class="json-font">"0000"</span>,
<span class="method json-font ">"message"</span>:<span class="json-font">"success"</span>,
<span class="method json-font ">"data"</span>:<span class="json-font">[] / {}</span>
}</pre>
            </div>
        </div>

        <div class="info-box warning">
            <strong>⚠️ 注意：</strong>所有响应均为 JSON 格式，Content-Type 自动设置为 <code>application/json</code>
        </div>
    </div>

    <div class="card" id="tools">
        <h2>🔧 常用工具</h2>

        <div class="section">
            <h3>🗄️ MongoDB 数据库</h3>
            <div class="route-example" id="mk1">
<!--                require __DIR__ . '/../../seraphine/database/mongodb_client.php';<br><br>-->
<!--                $mongo = new MongoDB_Client();<br><br>-->
<!--                // 插入数据<br>-->
<!--                $result = $mongo->insertOne('users', ['name' => 'admin']);<br><br>-->
<!--                // 查询数据<br>-->
<!--                $user = $mongo->findOne('users', ['name' => 'admin']);<br><br>-->
<!--                // 更新数据<br>-->
<!--                $mongo->updateOne('users', ['name' => 'admin'], ['$set' => ['age' => 25]]);-->
            </div>
            <script>
                const md = window.markdownit(); // 初始化

                const markdown = `
\`\`\`php

require __DIR__ . '/../../seraphine/database/mongodb_client.php';

$mongo = new MongoDB_Client();

// 插入数据
$result = $mongo->insertOne('users', ['name' => 'admin']);

// 查询数据
$user = $mongo->findOne('users', ['name' => 'admin']);

// 更新数据
$mongo->updateOne('users', ['name' => 'admin'], ['$set' => ['age' => 25]]);
\`\`\`

`;

                document.getElementById('mk1').innerHTML = md.render(markdown);
            </script>
        </div>

        <div class="section">
            <h3>🗄️ MySQL 数据库</h3>
            <div class="route-example" id="tt2">
<!--                require __DIR__ . '/../../seraphine/database/mysql_client.php';<br><br>-->
<!--                $mysql = new MySQLClient();<br><br>-->
<!--                // 插入数据<br>-->
<!--                $result = $mysql->insertOne('users', ['name' => 'admin', 'email' => 'admin@example.com']);<br><br>-->
<!--                // 查询数据<br>-->
<!--                $user = $mysql->findOne('users', ['name' => 'admin']);<br><br>-->
<!--                // 更新数据<br>-->
<!--                $mysql->update('users', ['age' => 25], ['name' => 'admin']);<br><br>-->
<!--                // 事务操作<br>-->
<!--                $mysql->beginTransaction();<br>-->
<!--                // ... 执行多个操作 ...<br>-->
<!--                $mysql->commit();-->
            </div>

            <script>
                // const md = window.markdownit(); // 初始化

                const markdown2 = `
\`\`\`php

require __DIR__ . '/../../seraphine/database/mysql_client.php';

$mysql = new MySQLClient();

// 插入数据
$result = $mysql->insertOne(
        'users',
        ['name' => 'admin', 'email' => 'admin@example.com']
    );

// 查询数据
$user = $mysql->findOne('users', ['name' => 'admin']);

// 更新数据
$mysql->update('users', ['age' => 25], ['name' => 'admin']);

// 事务操作
$mysql->beginTransaction();

// ... 执行多个操作 ...
$mysql->commit();

\`\`\`

`;

                document.getElementById('tt2').innerHTML = md.render(markdown2);
            </script>
        </div>

        <div class="section">
            <h3>🆔 Snowflake 唯一ID</h3>
            <div class="route-example">
                require __DIR__ . '/../../seraphine/tools/snow_id/snow_id.php';<br><br>
                $sid = new UniquenessId("D_");<br>
                $id = $sid->getId(); // 获取字符串ID<br>
                // 或<br>
                echo (string)$sid; // 输出: D_571987083883843584
            </div>
        </div>

        <div class="section">
            <h3>📁 文件上传</h3>

            <div class="section">
                <h4 style="color: #667eea; margin: 15px 0 10px 0;">方式一：普通文件上传</h4>
                <div class="route-example">
                    require __DIR__ . '/../../seraphine/tools/t_upload/t_upload.php';<br><br>
                    $upload = new Upload();<br>
                    $result = $upload->upload($_FILES['file'], 'avatars');<br><br>
                    if ($result) {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;echo "上传成功: " . $result['path'];<br>
                    } else {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;echo "上传失败: " . $upload->getError();<br>
                    }
                </div>
            </div>

            <div class="section">
                <h4 style="color: #667eea; margin: 15px 0 10px 0;">方式二：Base64 图片上传</h4>
                <div class="route-example">
                    $upload = new Upload();<br>
                    $base64Image = $_POST['image_base64'];<br>
                    $result = $upload->uploadBase64($base64Image, '', 'images');
                </div>
            </div>

            <div class="section">
                <h4 style="color: #667eea; margin: 15px 0 10px 0;">自定义配置</h4>
                <div class="route-example">
                    $upload = new Upload();<br>
                    $upload->setAllowedTypes(['image/jpeg', 'image/png'])<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;->setMaxSize(5 * 1024 * 1024); // 5MB
                </div>
            </div>

            <div class="info-box">
                <strong>💡 返回值说明：</strong><br>
                <code>$result['filename']</code> - 文件名<br>
                <code>$result['path']</code> - 相对路径（含子目录）<br>
                <code>$result['full_path']</code> - 完整服务器路径<br>
                <code>$result['size']</code> - 文件大小（字节）<br>
                <code>$result['type']</code> - MIME 类型
            </div>

            <div class="info-box warning">
                <strong>⚠️ 默认限制：</strong>最大文件大小 10MB，允许类型：JPG、PNG、GIF、PDF
            </div>
        </div>

        <div class="section">
            <h3>⚙️ 配置读取</h3>
            <div class="route-example">
                $config = ReadConfig::read_yml("这里写文件名,不带后缀");<br>
                $debug = $config['app']['debug'];
            </div>
        </div>

        <div class="section">
            <h3>🔐 JWT Token</h3>
            <div class="route-example">
                use Firebase\JWT\JWT;<br><br>
                $secret = ReadConfig::read_yml("secret");<br>
                $key = $secret['secret']['jwt_key'];<br><br>
                $payload = [<br>
                &nbsp;&nbsp;&nbsp;&nbsp;"iss" => "seraphine-api",<br>
                &nbsp;&nbsp;&nbsp;&nbsp;"exp" => time() + 3600,<br>
                &nbsp;&nbsp;&nbsp;&nbsp;"data" => ["user_id" => 123]<br>
                ];<br><br>
                $jwt = JWT::encode($payload, $key, 'HS256');
            </div>
        </div>

        <div class="section">
            <h3>🌐 HTTP 请求</h3>
            <div class="route-example">
                require __DIR__ . '/../../seraphine/tools/action/actions.php';<br><br>
                // POST 请求<br>
                $headers = ['Authorization: Bearer token'];<br>
                $body = ['key' => 'value'];<br>
                $response = actions_post($url, $headers, $body);<br><br>
                // GET 请求<br>
                $response = actions_get($url, $headers, []);
            </div>
        </div>
    </div>


    <button class="back-to-top" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">↑</button>

    <script>
        const cards = document.querySelectorAll('.card');
        const navLinks = document.querySelectorAll('.nav-list a');

        window.addEventListener('scroll', () => {
            let current = '';

            cards.forEach(card => {
                const cardTop = card.offsetTop;
                const cardHeight = card.clientHeight;
                if (pageYOffset >= (cardTop - 200)) {
                    current = card.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href').slice(1) === current) {
                    link.classList.add('active');
                }
            });

            const backToTop = document.querySelector('.back-to-top');
            if (window.pageYOffset > 300) {
                backToTop.classList.add('show');
            } else {
                backToTop.classList.remove('show');
            }
        });

        navLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const targetId = link.getAttribute('href').slice(1);
                const targetCard = document.getElementById(targetId);
                targetCard.scrollIntoView({behavior: 'smooth'});
            });
        });


        document.addEventListener('DOMContentLoaded', function () {
            loadMongoDbConfig();
            loadMysqlConfig();

            const mongodbForm = document.getElementById('mongodb-config-form');
            if (mongodbForm) {
                mongodbForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    saveMongoDbConfig();
                });
            }

            const mysqlForm = document.getElementById('mysql-config-form');
            if (mysqlForm) {
                mysqlForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    saveMysqlConfig();
                });
            }
        });

        function loadMongoDbConfig() {
            fetch('?action=get_db_config')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.mongodb) {
                        document.getElementById('mongodb-host').value = data.mongodb.host || '';
                        document.getElementById('mongodb-port').value = data.mongodb.port || '';
                        document.getElementById('mongodb-database').value = data.mongodb.database || '';
                        document.getElementById('mongodb-username').value = data.mongodb.username || '';
                        document.getElementById('mongodb-password').value = data.mongodb.password || '';
                        document.getElementById('mongodb-auth-source').value = data.mongodb.auth_source || '';
                    }
                })
                .catch(error => console.error('加载 MongoDB 配置失败:', error));
        }

        function loadMysqlConfig() {
            fetch('?action=get_db_config')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.mysql) {
                        document.getElementById('mysql-host').value = data.mysql.host || '';
                        document.getElementById('mysql-port').value = data.mysql.port || '';
                        document.getElementById('mysql-database').value = data.mysql.database || '';
                        document.getElementById('mysql-username').value = data.mysql.username || '';
                        document.getElementById('mysql-password').value = data.mysql.password || '';
                        document.getElementById('mysql-charset').value = data.mysql.charset || '';
                        document.getElementById('mysql-time-zone').value = data.mysql.time_zone || '';
                    }
                })
                .catch(error => console.error('加载 MySQL 配置失败:', error));
        }

        function saveMongoDbConfig() {
            const config = {
                host: document.getElementById('mongodb-host').value,
                port: document.getElementById('mongodb-port').value,
                database: document.getElementById('mongodb-database').value,
                username: document.getElementById('mongodb-username').value,
                password: document.getElementById('mongodb-password').value,
                auth_source: document.getElementById('mongodb-auth-source').value
            };

            const resultDiv = document.getElementById('mongodb-save-result');

            fetch('?action=save_mongodb_config', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(config)
            })
                .then(response => response.json())
                .then(data => {
                    resultDiv.style.display = 'block';
                    if (data.success) {
                        resultDiv.style.background = '#d1fae5';
                        resultDiv.style.color = '#065f46';
                        resultDiv.textContent = '✅ MongoDB 配置保存成功！';
                    } else {
                        resultDiv.style.background = '#fee2e2';
                        resultDiv.style.color = '#991b1b';
                        resultDiv.textContent = '❌ 保存失败: ' + (data.message || '未知错误');
                    }

                    setTimeout(() => {
                        resultDiv.style.display = 'none';
                    }, 3000);
                })
                .catch(error => {
                    resultDiv.style.display = 'block';
                    resultDiv.style.background = '#fee2e2';
                    resultDiv.style.color = '#991b1b';
                    resultDiv.textContent = '❌ 网络错误: ' + error.message;

                    setTimeout(() => {
                        resultDiv.style.display = 'none';
                    }, 3000);
                });
        }

        function saveMysqlConfig() {
            const config = {
                host: document.getElementById('mysql-host').value,
                port: document.getElementById('mysql-port').value,
                database: document.getElementById('mysql-database').value,
                username: document.getElementById('mysql-username').value,
                password: document.getElementById('mysql-password').value,
                charset: document.getElementById('mysql-charset').value,
                time_zone: document.getElementById('mysql-time-zone').value
            };

            const resultDiv = document.getElementById('mysql-save-result');

            fetch('?action=save_mysql_config', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(config)
            })
                .then(response => response.json())
                .then(data => {
                    resultDiv.style.display = 'block';
                    if (data.success) {
                        resultDiv.style.background = '#d1fae5';
                        resultDiv.style.color = '#065f46';
                        resultDiv.textContent = '✅ MySQL 配置保存成功！';
                    } else {
                        resultDiv.style.background = '#fee2e2';
                        resultDiv.style.color = '#991b1b';
                        resultDiv.textContent = '❌ 保存失败: ' + (data.message || '未知错误');
                    }

                    setTimeout(() => {
                        resultDiv.style.display = 'none';
                    }, 3000);
                })
                .catch(error => {
                    resultDiv.style.display = 'block';
                    resultDiv.style.background = '#fee2e2';
                    resultDiv.style.color = '#991b1b';
                    resultDiv.textContent = '❌ 网络错误: ' + error.message;

                    setTimeout(() => {
                        resultDiv.style.display = 'none';
                    }, 3000);
                });
        }
    </script>
</body>
</html>
