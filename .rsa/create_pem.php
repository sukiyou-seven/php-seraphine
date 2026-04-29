<?php

/**
 * RSA 密钥对生成工具
 *
 * 使用方法：
 * php create_pem.php [输出目录] [密钥位数]
 *
 * 示例：
 * php create_pem.php                    # 使用默认配置
 * php create_pem.php ./keys             # 指定输出目录
 * php create_pem.php ./keys 4096        # 指定密钥位数
 */

class PemGenerator
{
    /**
     * 生成 RSA 密钥对
     *
     * @param string $outputDir 输出目录
     * @param int $keyBits 密钥位数（2048 或 4096）
     * @return array 密钥信息
     */
    public static function generate($outputDir = null, $keyBits = 2048)
    {
        if ($outputDir === null) {
            $outputDir = __DIR__ . '/keys';
        }

        if (!in_array($keyBits, [1024, 2048, 4096])) {
            throw new Exception('密钥位数必须是 1024、2048 或 4096');
        }

        $config = [
            "digest_alg" => "sha256",
            "private_key_bits" => $keyBits,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        ];

        echo "正在生成 {$keyBits} 位 RSA 密钥对...\n";

        $res = openssl_pkey_new($config);

        if (!$res) {
            throw new Exception('生成密钥对失败: ' . openssl_error_string());
        }

        openssl_pkey_export($res, $privateKey);
        $publicKeyDetails = openssl_pkey_get_details($res);
        $publicKey = $publicKeyDetails['key'];

        openssl_free_key($res);

        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
            echo "创建目录: {$outputDir}\n";
        }

        $privateKeyPath = rtrim($outputDir, DIRECTORY_SEPARATOR) . '/private_key.pem';
        $publicKeyPath = rtrim($outputDir, DIRECTORY_SEPARATOR) . '/public_key.pem';

        file_put_contents($privateKeyPath, $privateKey);
        chmod($privateKeyPath, 0600);
        echo "私钥已保存: {$privateKeyPath}\n";

        file_put_contents($publicKeyPath, $publicKey);
        chmod($publicKeyPath, 0644);
        echo "公钥已保存: {$publicKeyPath}\n";

        echo "\n密钥对生成成功！\n";
        echo "========================================\n";
        echo "私钥文件: {$privateKeyPath}\n";
        echo "公钥文件: {$publicKeyPath}\n";
        echo "密钥位数: {$keyBits}\n";
        echo "加密算法: SHA256\n";
        echo "========================================\n";
        echo "\n安全提示:\n";
        echo "- 请妥善保管私钥文件，不要将其提交到版本控制系统\n";
        echo "- 建议在 .gitignore 中添加: .rsa/keys/\n";
        echo "- 公钥可以分发给需要验证签名的客户端或服务端\n";

        return [
            'private_key' => $privateKeyPath,
            'public_key' => $publicKeyPath,
            'private_key_content' => $privateKey,
            'public_key_content' => $publicKey,
            'bits' => $keyBits
        ];
    }

    /**
     * 生成 PHP 格式的密钥配置代码
     *
     * @param string $outputDir 输出目录
     * @param int $keyBits 密钥位数
     */
    public static function generateWithConfig($outputDir = null, $keyBits = 2048)
    {
        $result = self::generate($outputDir, $keyBits);

        $configContent = <<<'PHP'
<?php
/**
 * RSA 密钥配置
 * 此文件由 create_pem.php 自动生成
 */

return [
    'rsa_private_key_path' => __DIR__ . '/keys/private_key.pem',
    'rsa_public_key_path' => __DIR__ . '/keys/public_key.pem',
    'rsa_key_bits' => KEY_BITS_PLACEHOLDER,
];
PHP;

        $configContent = str_replace('KEY_BITS_PLACEHOLDER', $keyBits, $configContent);
        $configPath = rtrim($outputDir ?: __DIR__ . '/keys', DIRECTORY_SEPARATOR) . '/rsa_config.php';
        file_put_contents($configPath, $configContent);
        chmod($configPath, 0644);

        echo "\n配置文件已保存: {$configPath}\n";

        return $result;
    }
}

// 命令行执行
if (php_sapi_name() === 'cli') {
    try {
        $outputDir = $argv[1] ?? null;
        $keyBits = isset($argv[2]) ? intval($argv[2]) : 2048;

        echo "RSA 密钥对生成工具\n";
        echo "========================================\n\n";

        PemGenerator::generateWithConfig($outputDir, $keyBits);
    } catch (Exception $e) {
        echo "错误: " . $e->getMessage() . "\n";
        exit(1);
    }
} else {
    // Web 访问时的简单界面
    ?>
    <!DOCTYPE html>
    <html lang="zh-CN">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>RSA 密钥对生成工具</title>
        <style>
            body {
                font-family: 'Microsoft YaHei', Arial, sans-serif;
                max-width: 800px;
                margin: 50px auto;
                padding: 20px;
                background-color: #f5f5f5;
            }
            .container {
                background: white;
                padding: 30px;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }
            h1 {
                color: #333;
                border-bottom: 2px solid #4CAF50;
                padding-bottom: 10px;
            }
            .form-group {
                margin-bottom: 20px;
            }
            label {
                display: block;
                margin-bottom: 5px;
                font-weight: bold;
                color: #555;
            }
            input, select {
                width: 100%;
                padding: 10px;
                border: 1px solid #ddd;
                border-radius: 4px;
                box-sizing: border-box;
            }
            button {
                background-color: #4CAF50;
                color: white;
                padding: 12px 30px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                font-size: 16px;
            }
            button:hover {
                background-color: #45a049;
            }
            .result {
                margin-top: 20px;
                padding: 15px;
                background-color: #f9f9f9;
                border-left: 4px solid #4CAF50;
                border-radius: 4px;
            }
            .error {
                border-left-color: #f44336;
                background-color: #ffebee;
            }
            .info {
                background-color: #e3f2fd;
                border-left-color: #2196F3;
                padding: 15px;
                margin: 20px 0;
                border-radius: 4px;
            }
            code {
                background-color: #f5f5f5;
                padding: 2px 6px;
                border-radius: 3px;
                font-family: 'Courier New', monospace;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>🔐 RSA 密钥对生成工具</h1>

            <div class="info">
                <strong>使用说明：</strong><br>
                - 生成用于数字签名的 RSA 密钥对<br>
                - 私钥用于生成签名，必须妥善保管<br>
                - 公钥用于验证签名，可以公开分发
            </div>

            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                try {
                    $outputDir = $_POST['output_dir'] ?: (__DIR__ . '/keys');
                    $keyBits = intval($_POST['key_bits']);

                    echo '<div class="result">';
                    PemGenerator::generateWithConfig($outputDir, $keyBits);
                    echo '</div>';

                } catch (Exception $e) {
                    echo '<div class="result error">';
                    echo '<strong>错误:</strong> ' . htmlspecialchars($e->getMessage());
                    echo '</div>';
                }
            }
            ?>

            <form method="POST">
                <div class="form-group">
                    <label for="output_dir">输出目录：</label>
                    <input type="text" id="output_dir" name="output_dir"
                           placeholder="<?php echo htmlspecialchars(__DIR__ . '/keys'); ?>"
                           value="">
                    <small style="color: #999;">留空则使用默认目录: .rsa/keys/</small>
                </div>

                <div class="form-group">
                    <label for="key_bits">密钥位数：</label>
                    <select id="key_bits" name="key_bits">
                        <option value="2048" selected>2048 位（推荐，平衡安全性和性能）</option>
                        <option value="4096">4096 位（更高安全性，性能稍慢）</option>
                    </select>
                </div>

                <button type="submit">生成密钥对</button>
            </form>

            <div class="info" style="margin-top: 30px;">
                <strong>⚠️ 安全提示：</strong><br>
                1. 生成的私钥文件权限已设置为 <code>0600</code>（仅所有者可读写）<br>
                2. 请将私钥目录添加到 <code>.gitignore</code>：<br>
                <code style="display: block; margin-top: 10px;">.rsa/keys/</code><br>
                3. 建议定期轮换密钥对以提高安全性
            </div>
        </div>
    </body>
    </html>
    <?php
}
