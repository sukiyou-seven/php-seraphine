<?php

$action = isset($_GET['action']) ? $_GET['action'] : '';


if ($action === 'get_db_config') {
    header('Content-Type: application/json');
    $dbConfigPath = __DIR__ . '/../../config/db.yml';

    if (file_exists($dbConfigPath)) {
        $content = file_get_contents($dbConfigPath);
        $config = parseYamlSimple($content);

        echo json_encode([
                'success' => true,
                'config' => isset($config['mongodb']) ? $config['mongodb'] : []
        ]);
    } else {
        echo json_encode([
                'success' => false,
                'message' => '配置文件不存在'
        ]);
    }
    exit;
}

if ($action === 'save_db_config') {
//    echo "<script>alert($action) </script>";
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
        $yamlContent = generateYaml($config);

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

function parseYamlSimple($content) {
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

function generateYaml($config) {
    $yaml = "mongodb:\n";
    $yaml .= "    host: " . (isset($config['host']) ? $config['host'] : 'localhost') . "\n";
    $yaml .= "    port: " . (isset($config['port']) ? $config['port'] : '27017') . "\n";
    $yaml .= "    database: " . (isset($config['database']) ? $config['database'] : 'test') . "\n";
    $yaml .= "    username: " . (isset($config['username']) ? $config['username'] : '') . "\n";
    $yaml .= "    password: " . (isset($config['password']) ? $config['password'] : '') . "\n";
    $yaml .= "    auth_source: " . (isset($config['auth_source']) ? $config['auth_source'] : 'admin') . "\n";

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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            gap: 30px;
        }

        .sidebar {
            position: fixed;
            left: 20px;
            top: 40px;
            width: 250px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            padding: 25px;
            max-height: calc(100vh - 80px);
            overflow-y: auto;
            z-index: 100;
        }

        .sidebar h3 {
            color: #667eea;
            font-size: 1.2em;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }

        .nav-list {
            list-style: none;
        }

        .nav-list li {
            margin-bottom: 8px;
        }

        .nav-list a {
            display: block;
            padding: 10px 15px;
            color: #4a5568;
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.3s ease;
            font-size: 0.95em;
        }

        .nav-list a:hover {
            background: #f7fafc;
            color: #667eea;
            transform: translateX(5px);
        }

        .nav-list a.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 500;
        }

        .main-content {
            flex: 1;
            margin-left: 280px;
        }

        .header {
            text-align: center;
            color: white;
            margin-bottom: 40px;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .header p {
            font-size: 1.1em;
            opacity: 0.9;
        }

        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            padding: 30px;
            margin-bottom: 30px;
            transition: transform 0.3s ease;
            scroll-margin-top: 20px;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card h2 {
            color: #667eea;
            font-size: 1.8em;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #667eea;
        }

        .section {
            margin-bottom: 25px;
        }

        .section h3 {
            color: #333;
            font-size: 1.3em;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }

        .section h3::before {
            content: "▸";
            margin-right: 10px;
            color: #667eea;
            font-size: 1.2em;
        }

        .route-example {
            background: #f7f9fc;
            border-left: 4px solid #667eea;
            padding: 15px 20px;
            margin: 15px 0;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
        }

        .route-example .url {
            color: #e74c3c;
            font-weight: bold;
            font-size: 1.1em;
        }

        .file-tree {
            background: #2d3748;
            color: #e2e8f0;
            padding: 20px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            line-height: 1.8;
            overflow-x: auto;
        }

        .file-tree .dir {
            color: #63b3ed;
        }

        .file-tree .file {
            color: #fbbf24;
        }

        .file-tree .class {
            color: #9ae6b4;
        }

        .file-tree .method {
            color: #f687b3;
        }

        .info-box {
            background: #ebf8ff;
            border: 1px solid #90cdf4;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
        }

        .info-box.warning {
            background: #fffaf0;
            border-color: #fbd38d;
        }

        .info-box strong {
            color: #2b6cb0;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            background: #667eea;
            color: white;
            border-radius: 20px;
            font-size: 0.85em;
            margin-left: 10px;
        }

        code {
            background: #edf2f7;
            padding: 2px 8px;
            border-radius: 4px;
            color: #e53e3e;
            font-family: 'Courier New', monospace;
        }

        @media (max-width: 1024px) {
            .sidebar {
                display: none;
            }

            .main-content {
                margin-left: 0;
            }

            .container {
                display: block;
            }
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 2em;
            }

            .card {
                padding: 20px;
            }
        }

        .sst {
            display: block;
        }

        .con {
            margin-left: 20px;
        }

        .file {
            margin-left: 40px;
        }

        .classt {
            margin-left: 60px;
        }

        .method {
            margin-left: 80px;
        }

        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 1.5em;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
            z-index: 999;
        }

        .back-to-top:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
        }

        .back-to-top.show {
            display: flex;
        }
    </style>
</head>
<body>
<div class="sidebar">
    <h3>📑 目录导航</h3>
    <ul class="nav-list">
        <li><a href="#api-doc" class="active">📚 API 文档</a></li>
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

    <div class="card" id="api-doc">
        <h2>📚 API 接口文档</h2>
        <div class="route-example">
            <div><strong>访问地址：</strong><a href="<?php echo $baseUrl; ?>/openapi" target="_blank"
                                              style="color: #667eea; text-decoration: none;"><?php echo $baseUrl; ?>
                    /openapi</a></div>
            <div style="margin-top: 10px; color: #718096;">自动生成 · 实时同步 · 完整详细</div>
        </div>
    </div>

    <div class="card" id="setting">
        <h2>⚙ MongoDB 数据库设置</h2>
        <div class="route-example">
            <form id="db-config-form" style="max-width: 600px;">
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; color: #667eea; font-weight: 600;">Host:</label>
                    <input type="text" id="db-host" name="host" placeholder="例如: localhost"
                           style="width: 100%; padding: 10px; border: 2px solid #e2e8f0; border-radius: 6px; font-size: 1em;" />
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; color: #667eea; font-weight: 600;">Port:</label>
                    <input type="number" id="db-port" name="port" placeholder="例如: 27017"
                           style="width: 100%; padding: 10px; border: 2px solid #e2e8f0; border-radius: 6px; font-size: 1em;" />
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; color: #667eea; font-weight: 600;">Database:</label>
                    <input type="text" id="db-database" name="database" placeholder="例如: mydb"
                           style="width: 100%; padding: 10px; border: 2px solid #e2e8f0; border-radius: 6px; font-size: 1em;" />
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; color: #667eea; font-weight: 600;">Username:</label>
                    <input type="text" id="db-username" name="username" placeholder="用户名（可选）"
                           style="width: 100%; padding: 10px; border: 2px solid #e2e8f0; border-radius: 6px; font-size: 1em;" />
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; color: #667eea; font-weight: 600;">Password:</label>
                    <input type="password" id="db-password" name="password" placeholder="密码（可选）"
                           style="width: 100%; padding: 10px; border: 2px solid #e2e8f0; border-radius: 6px; font-size: 1em;" />
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; color: #667eea; font-weight: 600;">Auth Source:</label>
                    <input type="text" id="db-auth-source" name="auth_source" placeholder="例如: admin"
                           style="width: 100%; padding: 10px; border: 2px solid #e2e8f0; border-radius: 6px; font-size: 1em;" />
                </div>

                <button type="submit" style="padding: 12px 30px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 1em; font-weight: 600; transition: all 0.3s ease;">
                    💾 保存配置
                </button>

                <div id="save-result" style="margin-top: 15px; padding: 10px; border-radius: 6px; display: none;"></div>
            </form>
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
                {
                "<span class="method">code</span>": "00000",
                "<span class="method">message</span>": "success",
                "<span class="method">data</span>": {}
                }
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
            <div class="route-example">
                require __DIR__ . '/../../seraphine/database/mongodb_client.php';<br><br>
                $mongo = new MongoDB_Client();<br><br>
                // 插入数据<br>
                $result = $mongo->insertOne('users', ['name' => 'admin']);<br><br>
                // 查询数据<br>
                $user = $mongo->findOne('users', ['name' => 'admin']);<br><br>
                // 更新数据<br>
                $mongo->updateOne('users', ['name' => 'admin'], ['$set' => ['age' => 25]]);
            </div>
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


        document.addEventListener('DOMContentLoaded', function() {
            loadDbConfig();

            const dbForm = document.getElementById('db-config-form');
            if (dbForm) {
                dbForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    saveDbConfig();
                });
            }
        });

        function loadDbConfig() {
            fetch('?action=get_db_config')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.config) {
                        document.getElementById('db-host').value = data.config.host || '';
                        document.getElementById('db-port').value = data.config.port || '';
                        document.getElementById('db-database').value = data.config.database || '';
                        document.getElementById('db-username').value = data.config.username || '';
                        document.getElementById('db-password').value = data.config.password || '';
                        document.getElementById('db-auth-source').value = data.config.auth_source || '';
                    }
                })
                .catch(error => console.error('加载配置失败:', error));
        }

        function saveDbConfig() {
            const config = {
                host: document.getElementById('db-host').value,
                port: document.getElementById('db-port').value,
                database: document.getElementById('db-database').value,
                username: document.getElementById('db-username').value,
                password: document.getElementById('db-password').value,
                auth_source: document.getElementById('db-auth-source').value
            };

            const resultDiv = document.getElementById('save-result');

            fetch('?action=save_db_config', {
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
                        resultDiv.textContent = '✅ 配置保存成功！';
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
