<?php

/**
 * 安装/设置工具类
 */
class Setup
{
    /**
     * 将错误页面文件复制到项目根目录
     *
     * @return array 返回操作结果 ['success' => bool, 'message' => string, 'files' => array]
     */
    public static function copyErrorPages(): array
    {
        $errorPageDir = __DIR__ . '/../error_page/';
        $rootDir = __DIR__ . '/../../';

        $errorPages = [
            '403.html',
            '404.html',
            '500.php',
            '502.html',
            '320.php',
        ];

        $copiedFiles = [];
        $failedFiles = [];

        foreach ($errorPages as $page) {
            $source = $errorPageDir . $page;
            $destination = $rootDir . $page;

            if (file_exists($source)) {
                if (copy($source, $destination)) {
                    $copiedFiles[] = $page;
                } else {
                    $failedFiles[] = $page;
                }
            } else {
                $failedFiles[] = $page . ' (源文件不存在)';
            }
        }

        if (empty($failedFiles)) {
            return [
                'success' => true,
                'message' => '所有错误页面已成功复制到根目录',
                'files' => $copiedFiles
            ];
        } else {
            return [
                'success' => false,
                'message' => '部分文件复制失败: ' . implode(', ', $failedFiles),
                'files' => $copiedFiles,
                'failed' => $failedFiles
            ];
        }
    }

    /**
     * 完整的安装流程
     *
     * @return array 返回安装结果
     */
    public static function install(): array
    {
        $results = [];

        // 1. 复制错误页面
        $results['error_pages'] = self::copyErrorPages();

        // 未来可以添加更多安装步骤
        // $results['config'] = self::generateConfig();
        // $results['permissions'] = self::setPermissions();

        return [
            'success' => true,
            'message' => '安装完成',
            'details' => $results
        ];
    }
}
