<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        min-height: 100vh;
        padding: 40px 20px;
        color: #e0e0e0;
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
        background: #1f2937;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        padding: 25px;
        max-height: calc(100vh - 80px);
        overflow-y: auto;
        z-index: 100;
        border: 1px solid #374151;
    }

    .sidebar h3 {
        color: #60a5fa;
        font-size: 1.2em;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #60a5fa;
    }

    .nav-list {
        list-style: none;
    }

    .nav-list li {
        margin-bottom: 8px;
    }

    .nav-controller {
        margin-bottom: 10px;
    }

    .nav-controller-title {
        display: block;
        padding: 10px 15px;
        color: #d1d5db;
        text-decoration: none;
        border-radius: 6px;
        transition: all 0.3s ease;
        font-size: 0.95em;
        cursor: pointer;
        font-weight: 500;
        user-select: none;
    }

    .nav-controller-title:hover {
        background: #374151;
        color: #60a5fa;
    }

    .nav-controller-title::before {
        content: "▶";
        display: inline-block;
        margin-right: 8px;
        transition: transform 0.3s ease;
        font-size: 0.8em;
    }

    .nav-controller-title.expanded::before {
        transform: rotate(90deg);
    }

    .nav-methods {
        list-style: none;
        margin-left: 20px;
        margin-top: 5px;
        display: none;
    }

    .nav-methods.show {
        display: block;
    }

    .nav-methods li a {
        display: block;
        padding: 8px 15px;
        color: #9ca3af;
        text-decoration: none;
        border-radius: 6px;
        transition: all 0.3s ease;
        font-size: 0.9em;
    }

    .nav-methods li a:hover {
        background: #374151;
        color: #60a5fa;
        transform: translateX(5px);
    }

    .nav-methods li a.active {
        background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
        color: white;
    }

    .main-content {
        flex: 1;
        margin-left: 280px;
    }

    .header {
        text-align: center;
        color: #e0e0e0;
        margin-bottom: 40px;
    }

    .header h1 {
        font-size: 2.5em;
        margin-bottom: 10px;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    }

    .header p {
        font-size: 1.1em;
        opacity: 0.9;
    }

    .base-url-section {
        background: #1f2937;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        padding: 20px 30px;
        margin-bottom: 30px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 15px;
        border: 1px solid #374151;
    }

    .base-url-label {
        color: #60a5fa;
        font-weight: 600;
        font-size: 1.1em;
    }

    .base-url-value {
        flex: 1;
        background: #111827;
        padding: 12px 20px;
        border-radius: 8px;
        font-family: "Courier New", monospace;
        color: #e5e7eb;
        font-size: 1em;
        border: 2px solid #374151;
        min-width: 300px;
    }

    .base-url-copy-btn {
        padding: 12px 24px;
        background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.95em;
        font-weight: 500;
        transition: all 0.3s ease;
        white-space: nowrap;
    }

    .base-url-copy-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
    }

    .card {
        background: #1f2937;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        padding: 30px;
        margin-bottom: 30px;
        scroll-margin-top: 20px;
        border: 1px solid #374151;
    }

    .card h2 {
        color: #60a5fa;
        font-size: 1.8em;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 3px solid #60a5fa;
    }

    .class-desc {
        color: #9ca3af;
        margin-bottom: 20px;
        line-height: 1.6;
    }

    .method {
        background: #111827;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        border-left: 4px solid #60a5fa;
    }

    .method:last-child {
        margin-bottom: 0;
    }

    .method-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 15px;
        flex-wrap: wrap;
        gap: 10px;
    }

    .method-name {
        font-size: 1.3em;
        color: #e5e7eb;
        font-weight: 600;
    }

    .route-badge {
        background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
        color: white;
        padding: 6px 15px;
        border-radius: 20px;
        font-family: "Courier New", monospace;
        font-size: 0.9em;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .route-badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
    }

    .method-desc {
        color: #d1d5db;
        margin-bottom: 15px;
        line-height: 1.6;
    }

    .params-section {
        margin-top: 15px;
    }

    .params-title {
        color: #60a5fa;
        font-weight: 600;
        margin-bottom: 10px;
        font-size: 0.95em;
    }

    .param-item {
        background: #1f2937;
        padding: 10px 15px;
        border-radius: 6px;
        margin-bottom: 8px;
        font-family: "Courier New", monospace;
        font-size: 0.9em;
        border: 1px solid #374151;
    }

    .param-type {
        color: #60a5fa;
        font-weight: 600;
    }

    .param-name {
        color: #f87171;
    }

    .param-desc {
        color: #9ca3af;
        margin-left: 10px;
    }

    .return-section {
        margin-top: 15px;
        padding: 10px 15px;
        background: #1f2937;
        border-radius: 6px;
        border-left: 3px solid #fb923c;
        border: 1px solid #374151;
    }

    .return-label {
        color: #fb923c;
        font-weight: 600;
        margin-right: 10px;
    }

    .example-section {
        margin-top: 15px;
    }

    .example-title {
        color: #60a5fa;
        font-weight: 600;
        margin-bottom: 10px;
        font-size: 0.95em;
    }

    .example-box {
        background: #0f172a;
        color: #e2e8f0;
        padding: 15px;
        border-radius: 6px;
        font-family: "Courier New", monospace;
        font-size: 0.9em;
        line-height: 1.6;
        overflow-x: auto;
        white-space: pre-wrap;
        word-wrap: break-word;
        border: 1px solid #334155;
    }

    .copy-btn {
        margin-top: 10px;
        padding: 8px 16px;
        background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.9em;
        transition: all 0.3s ease;
    }

    .copy-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
    }

    .no-methods {
        text-align: center;
        padding: 40px;
        color: #6b7280;
    }

    .stats {
        background: #1f2937;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        padding: 20px;
        margin-bottom: 30px;
        display: flex;
        justify-content: space-around;
        flex-wrap: wrap;
        gap: 20px;
        border: 1px solid #374151;
    }

    .stat-item {
        text-align: center;
    }

    .stat-number {
        font-size: 2em;
        font-weight: bold;
        color: #60a5fa;
    }

    .stat-label {
        color: #9ca3af;
        font-size: 0.9em;
        margin-top: 5px;
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

        .base-url-section {
            flex-direction: column;
            align-items: stretch;
        }

        .base-url-value {
            min-width: auto;
        }
    }

    .back-to-top {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
        color: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        display: none;
        align-items: center;
        justify-content: center;
        font-size: 1.5em;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5);
        transition: all 0.3s ease;
        z-index: 999;
    }

    .back-to-top:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.6);
    }

    .back-to-top.show {
        display: flex;
    }

    html {
        scroll-padding-top: calc(50vh - 100px);
        scroll-behavior: smooth;
    }

    :target {
        padding-top: 50px;
        margin-top: -50px;
    }
</style>


<?php

class AutoApiDoc
{
    private $controllerDir;
    private $apiList = [];

    public function __construct()
    {
        $this->controllerDir = __DIR__ . '/../../application/controllers/';
    }

    public function generate()
    {
        $this->scanControllers();
        return $this->buildHtml();
    }

    private function scanControllers()
    {
        $files = glob($this->controllerDir . '*.php');

        $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($this->controllerDir)
        );

        foreach ($iterator as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $filepath = $file->getPathname();
            $filename = $file->getBasename('.php');
            $content = file_get_contents($filepath);

            $className = ucfirst($filename);

            if (!preg_match('/class\s+' . $className . '\s*\{/i', $content)) {
                continue;
            }

            $relativePath = str_replace($this->controllerDir, '', $filepath);
            $pathParts = explode(DIRECTORY_SEPARATOR, dirname($relativePath));

            $routePrefix = '';
            if (!empty($pathParts[0]) && $pathParts[0] !== '.') {
                $routePrefix = implode('/', $pathParts) . '/';
            }

            $methods = $this->extractMethods($content, $className, $routePrefix);

            $this->apiList[] = [
                    'name' => $className,
                    'file' => $filename . '.php',
                    'methods' => $methods,
                    'description' => $this->extractClassDescription($content),
                    'route_prefix' => $routePrefix
            ];
        }
    }

    private function extractMethods($content, $className, $routePrefix = '')
    {
        $methods = [];

        preg_match_all('/\/\*\*(.*?)\*\/\s*public\s+(?:static\s+)?function\s+(\w+)\s*\(([^)]*)\)/s', $content, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $docComment = $match[1];
            $methodName = $match[2];
            $params = $match[3];

            if (strpos($methodName, '__') === 0) {
                continue;
            }

            $methodInfo = $this->parseDocComment($docComment);
            $paramList = $this->parseParams($params);

            $methods[] = [
                    'name' => $methodName,
                    'params' => $paramList,
                    'description' => $methodInfo['description'],
                    'params_desc' => $methodInfo['params'],
                    'return' => $methodInfo['return'],
                    'example' => $methodInfo['example'],
                    'route' => '/' . $routePrefix . strtolower($className) . '/' . $methodName
            ];
        }

        if (empty($methods)) {
            preg_match_all('/public\s+(?:static\s+)?function\s+(\w+)\s*\(([^)]*)\)/', $content, $simpleMatches, PREG_SET_ORDER);

            foreach ($simpleMatches as $match) {
                $methodName = $match[1];
                $params = $match[2];

                if (strpos($methodName, '__') === 0) {
                    continue;
                }

                $methods[] = [
                        'name' => $methodName,
                        'params' => $this->parseParams($params),
                        'description' => '',
                        'params_desc' => [],
                        'return' => '',
                        'example' => '',
                        'route' => '/' . $routePrefix . strtolower($className) . '/' . $methodName
                ];
            }
        }

        return $methods;
    }

    private function parseDocComment($comment)
    {
        $info = [
            'description' => '',
            'params' => [],
            'return' => '',
            'example' => ''
        ];

        $lines = explode("\n", $comment);
        $description = [];
        $exampleLines = [];
        $inExample = false;

        foreach ($lines as $line) {
            $line = trim($line, " \t/*");

            if (preg_match('/@param\s+(\S+)\s+(\$\w+)\s*(.*)/', $line, $matches)) {
                $info['params'][] = [
                    'type' => $matches[1],
                    'name' => $matches[2],
                    'desc' => $matches[3]
                ];
                $inExample = false;
            } elseif (preg_match('/@return\s+(.*)/', $line, $matches)) {
                $info['return'] = $matches[1];
                $inExample = false;
            } elseif (preg_match('/@example\s*/i', $line)) {
                $inExample = true;
                $exampleContent = trim(preg_replace('/@example\s*/i', '', $line));
                if (!empty($exampleContent)) {
                    $exampleLines[] = $exampleContent;
                }
            } elseif ($inExample && !empty($line)) {
                $exampleLines[] = $line;
            } elseif (!empty($line) && !$inExample) {
                $description[] = $line;
            }
        }

        $info['description'] = implode(' ', $description);
        $info['example'] = implode("\n", $exampleLines);

        return $info;
    }

    private function parseParams($params)
    {
        if (empty(trim($params))) {
            return [];
        }

        $paramList = explode(',', $params);
        $result = [];

        foreach ($paramList as $param) {
            $param = trim($param);
            if (!empty($param)) {
                $result[] = $param;
            }
        }

        return $result;
    }

    private function extractClassDescription($content)
    {
        if (preg_match('/\/\*\*(.*?)\*\/\s*class/s', $content, $matches)) {
            $comment = $matches[1];
            $lines = explode("\n", $comment);
            $desc = [];

            foreach ($lines as $line) {
                $line = trim($line, " \t/*");
                if (!preg_match('/^@/', $line) && !empty($line)) {
                    $desc[] = $line;
                }
            }

            return implode(' ', $desc);
        }

        return '';
    }

    private function buildHtml()
    {
        $baseUrl = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $baseUrl .= $_SERVER['HTTP_HOST'];

        $html = '<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seraphine API 接口文档</title>
    
</head>
<body>
<div class="sidebar">
    <h3>📑 API 导航</h3>
    <ul class="nav-list">';

        foreach ($this->apiList as $api) {
            $html .= '<li class="nav-controller">';
            $html .= '<span class="nav-controller-title">' . $api['name'] . '</span>';
            $html .= '<ul class="nav-methods">';

            foreach ($api['methods'] as $method) {
                $methodId = strtolower($api['name']) . '-' . $method['name'];
                $html .= '<li><a href="#method-' . $methodId . '">' . $method['name'] . '()</a></li>';
            }

            $html .= '</ul></li>';
        }

        $html .= '    </ul>
</div>

<div class="main-content">
    <div class="header">
        <h1>📚 Seraphine API 接口文档</h1>
        <p>自动生成 · 实时同步 · 完整详细</p>
    </div>

    <div class="base-url-section">
        <div class="base-url-label">🌐 Base URL:</div>
        <div class="base-url-value" id="base-url">' . htmlspecialchars($baseUrl) . '</div>
        <button class="base-url-copy-btn" onclick="copyBaseUrl()">📋 复制 Base URL</button>
    </div>

    <div class="stats">
        <div class="stat-item">
            <div class="stat-number">' . count($this->apiList) . '</div>
            <div class="stat-label">控制器</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">' . array_sum(array_map(function($api) { return count($api['methods']); }, $this->apiList)) . '</div>
            <div class="stat-label">API 接口</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">' . date('Y-m-d') . '</div>
            <div class="stat-label">更新时间</div>
        </div>
    </div>';

        foreach ($this->apiList as $api) {
            $html .= '    <div class="card" id="' . strtolower($api['name']) . '">
        <h2>🔧 ' . $api['name'] . '</h2>';

            if (!empty($api['description'])) {
                $html .= '        <p class="class-desc">' . htmlspecialchars($api['description']) . '</p>';
            }

            if (empty($api['methods'])) {
                $html .= '        <div class="no-methods">暂无公开方法</div>';
            } else {
                foreach ($api['methods'] as $method) {
                    $methodId = strtolower($api['name']) . '-' . $method['name'];
                    $html .= '        <div class="method" id="method-' . $methodId . '">
            <div class="method-header">
                <div class="method-name">' . $method['name'] . '()</div>
                <div class="route-badge" id="route-' . md5($method['route']) . '"
                 onclick="copyExample(this, \'route-' . md5($method['route']) . '\')" >' . $method['route'] . '</div>
            </div>';

                    if (!empty($method['description'])) {
                        $html .= '            <p class="method-desc">' . htmlspecialchars($method['description']) . '</p>';
                    }

                    if (!empty($method['params'])) {
                        $html .= '            <div class="params-section">
                <div class="params-title">📥 参数列表：</div>';

                        foreach ($method['params'] as $index => $param) {
                            $paramDesc = isset($method['params_desc'][$index]) ? $method['params_desc'][$index]['desc'] : '';
                            $paramType = isset($method['params_desc'][$index]) ? $method['params_desc'][$index]['type'] : 'mixed';

                            $html .= '                <div class="param-item">
                    <span class="param-type">' . htmlspecialchars($paramType) . '</span> 
                    <span class="param-name">' . htmlspecialchars($param) . '</span>';

                            if (!empty($paramDesc)) {
                                $html .= ' <span class="param-desc">- ' . htmlspecialchars($paramDesc) . '</span>';
                            }

                            $html .= '                </div>';
                        }

                        $html .= '            </div>';
                    }

                    if (!empty($method['example'])) {
                        $html .= '            <div class="example-section">
                <div class="params-title">📝 请求示例：</div>
                <div class="example-box" id="example-' . md5($method['route']) . '">' . htmlspecialchars($method['example']) . '</div>
                <button class="copy-btn" onclick="copyExample(this, \'example-' . md5($method['route']) . '\')">📋 复制示例</button>
            </div>';
                    }

                    if (!empty($method['return'])) {
                        $html .= '            <div class="return-section">
                <span class="return-label">📤 返回：</span>' . htmlspecialchars($method['return']) . '
            </div>';
                    }

                    $html .= '        </div>';
                }
            }

            $html .= '    </div>';
        }

        $html .= '</div>

<button class="back-to-top" onclick="window.scrollTo({top: 0, behavior: \'smooth\'})">↑</button>

<script>
const cards = document.querySelectorAll(\'.card\');
const navLinks = document.querySelectorAll(\'.nav-methods a\');
const controllerTitles = document.querySelectorAll(\'.nav-controller-title\');

window.addEventListener(\'scroll\', () => {
    let current = \'\';

    cards.forEach(card => {
        const cardTop = card.offsetTop;
        if (pageYOffset >= (cardTop - 200)) {
            current = card.getAttribute(\'id\');
        }
    });

    navLinks.forEach(link => {
        link.classList.remove(\'active\');
        if (link.getAttribute(\'href\').slice(1) === current) {
            link.classList.add(\'active\');
        }
    });

    const backToTop = document.querySelector(\'.back-to-top\');
    if (window.pageYOffset > 300) {
        backToTop.classList.add(\'show\');
    } else {
        backToTop.classList.remove(\'show\');
    }
});

controllerTitles.forEach(title => {
    title.addEventListener(\'click\', (e) => {
        e.preventDefault();
        const methodsList = title.nextElementSibling;
        if (methodsList && methodsList.classList.contains(\'nav-methods\')) {
            methodsList.classList.toggle(\'show\');
            title.classList.toggle(\'expanded\');
        }
    });
});

navLinks.forEach(link => {
    link.addEventListener(\'click\', (e) => {
        e.preventDefault();
        const targetId = link.getAttribute(\'href\').slice(1);
        const targetCard = document.getElementById(targetId);
        if (targetCard) {
            targetCard.scrollIntoView({ behavior: \'smooth\' });
        }
    });
});

function copyExample(btn, elementId) {
    const exampleBox = document.getElementById(elementId);
    const text = exampleBox.textContent;

    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(() => {
            const originalText = btn.textContent;
            btn.textContent = \'✅ 已复制\';
            setTimeout(() => {
                btn.textContent = originalText;
            }, 2000);
        }).catch(err => {
            console.error(\'复制失败:\', err);
            fallbackCopy(btn, text);
        });
    } else {
        fallbackCopy(btn, text);
    }
}

function fallbackCopy(btn, text) {
    const textarea = document.createElement(\'textarea\');
    textarea.value = text;
    textarea.style.position = \'fixed\';
    textarea.style.opacity = \'0\';
    document.body.appendChild(textarea);
    textarea.select();

    try {
        document.execCommand(\'copy\');
        const originalText = btn.textContent;
        btn.textContent = \'✅ 已复制\';
        setTimeout(() => {
            btn.textContent = originalText;
        }, 2000);
    } catch (err) {
        console.error(\'复制失败:\', err);
        alert(\'复制失败，请手动复制\');
    }

    document.body.removeChild(textarea);
}

function copyBaseUrl() {
    const baseUrlElement = document.getElementById(\'base-url\');
    const text = baseUrlElement.textContent;
    
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(() => {
            const btn = event.target;
            const originalText = btn.textContent;
            btn.textContent = \'✅ 已复制\';
            setTimeout(() => {
                btn.textContent = originalText;
            }, 2000);
        }).catch(err => {
            console.error(\'复制失败:\', err);
            fallbackCopyBtn(event.target, text);
        });
    } else {
        fallbackCopyBtn(event.target, text);
    }
}

function fallbackCopyBtn(btn, text) {
    const textarea = document.createElement(\'textarea\');
    textarea.value = text;
    textarea.style.position = \'fixed\';
    textarea.style.opacity = \'0\';
    document.body.appendChild(textarea);
    textarea.select();
    
    try {
        document.execCommand(\'copy\');
        const originalText = btn.textContent;
        btn.textContent = \'✅ 已复制\';
        setTimeout(() => {
            btn.textContent = originalText;
        }, 2000);
    } catch (err) {
        console.error(\'复制失败:\', err);
        alert(\'复制失败，请手动复制\');
    }
    
    document.body.removeChild(textarea);
}
</script>

</body>
</html>';

        return $html;
    }
}


?>
