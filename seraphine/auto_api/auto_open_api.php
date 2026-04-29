<?php

class Auto_open_api
{
    private $controllerDir;

    private $apiList = [];

    public function __construct()
    {
        $this->controllerDir = __DIR__ . '/../../application/controllers/';
    }


    public function scanControllers()
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

        return $this->apiList;
    }

    public function extractMethods($content, $className, $routePrefix = '')
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

    public function parseDocComment($comment)
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

    public function parseParams($params)
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

    public function extractClassDescription($content)
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

    public function getBaseUrl()
    {
        $baseUrl = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $baseUrl .= $_SERVER['HTTP_HOST'];
        return $baseUrl;
    }
}