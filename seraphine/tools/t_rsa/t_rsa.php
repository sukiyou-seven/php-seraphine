<?php
class RSA
{
    private static $privateKey = null;
    private static $publicKey = null;

    /**
     * 初始化密钥
     *
     * @param string $privateKeyPath 私钥文件路径
     * @param string $publicKeyPath 公钥文件路径
     */
    public static function init($privateKeyPath = null, $publicKeyPath = null)
    {
        if ($privateKeyPath && file_exists($privateKeyPath)) {
            self::$privateKey = file_get_contents($privateKeyPath);
        }

        if ($publicKeyPath && file_exists($publicKeyPath)) {
            self::$publicKey = file_get_contents($publicKeyPath);
        }
    }

    /**
     * 设置私钥字符串
     *
     * @param string $privateKey 私钥内容
     */
    public static function setPrivateKey($privateKey)
    {
        self::$privateKey = $privateKey;
    }

    /**
     * 设置公钥字符串
     *
     * @param string $publicKey 公钥内容
     */
    public static function setPublicKey($publicKey)
    {
        self::$publicKey = $publicKey;
    }

    /**
     * 使用私钥生成签名
     *
     * @param string $data 待签名数据
     * @return string Base64编码的签名
     */
    public static function sign($data)
    {
        if (empty(self::$privateKey)) {
            throw new Exception('Private key not initialized');
        }

        $signature = '';
        $result = openssl_sign($data, $signature, self::$privateKey, OPENSSL_ALGO_SHA256);

        if (!$result) {
            throw new Exception('Failed to generate signature: ' . openssl_error_string());
        }

        return base64_encode($signature);
    }

    /**
     * 使用公钥验证签名
     *
     * @param string $data 原始数据
     * @param string $signature Base64编码的签名
     * @return bool 签名是否有效
     */
    public static function verify($data, $signature)
    {
        if (empty(self::$publicKey)) {
            throw new Exception('Public key not initialized');
        }

        $decodedSignature = base64_decode($signature);
        if ($decodedSignature === false) {
            return false;
        }

        $result = openssl_verify($data, $decodedSignature, self::$publicKey, OPENSSL_ALGO_SHA256);

        return $result === 1;
    }

    /**
     * 生成密钥对（用于测试或初始化）
     *
     * @param string $outputDir 密钥输出目录
     * @return array 包含私钥和公钥路径的数组
     */
    public static function generateKeyPair($outputDir = null)
    {
        $config = [
            "digest_alg" => "sha256",
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        ];

        $res = openssl_pkey_new($config);

        if (!$res) {
            throw new Exception('Failed to generate key pair: ' . openssl_error_string());
        }

        openssl_pkey_export($res, $privateKey);
        $publicKeyDetails = openssl_pkey_get_details($res);
        $publicKey = $publicKeyDetails['key'];

        openssl_free_key($res);

        if ($outputDir) {
            if (!is_dir($outputDir)) {
                mkdir($outputDir, 0755, true);
            }

            $privateKeyPath = rtrim($outputDir, '/') . '/private_key.pem';
            $publicKeyPath = rtrim($outputDir, '/') . '/public_key.pem';

            file_put_contents($privateKeyPath, $privateKey);
            file_put_contents($publicKeyPath, $publicKey);

            return [
                'private_key' => $privateKeyPath,
                'public_key' => $publicKeyPath
            ];
        }

        return [
            'private_key' => $privateKey,
            'public_key' => $publicKey
        ];
    }
}