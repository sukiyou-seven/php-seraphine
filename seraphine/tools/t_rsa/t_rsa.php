<?php

require_once __DIR__ . '/../../g/g.php';

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
        if ($privateKeyPath) {
            if (!file_exists($privateKeyPath)) {
                throw new Exception("Private key file not found: " . $privateKeyPath);
            }

            $privateKeyContent = file_get_contents($privateKeyPath);
            if ($privateKeyContent === false) {
                throw new Exception("Failed to read private key file: " . $privateKeyPath);
            }

            self::$privateKey = $privateKeyContent;
        }

        if ($publicKeyPath) {
            if (!file_exists($publicKeyPath)) {
                throw new Exception("Public key file not found: " . $publicKeyPath);
            }

            $publicKeyContent = file_get_contents($publicKeyPath);
            if ($publicKeyContent === false) {
                throw new Exception("Failed to read public key file: " . $publicKeyPath);
            }

            self::$publicKey = $publicKeyContent;
        }

        error_log("RSA initialized - Private key: " . (self::$privateKey ? 'loaded' : 'not loaded') .
                  ", Public key: " . (self::$publicKey ? 'loaded' : 'not loaded'));
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
            G::set("response_data_seraphine", "Private key not initialized");
            return "Private key not initialized";
        }

        $signature = '';
        $result = openssl_sign($data, $signature, self::$privateKey, OPENSSL_ALGO_SHA256);

        if (!$result) {
            G::set("response_data_seraphine", 'Failed to generate signature: ' . openssl_error_string());
            return 'Failed to generate signature';
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
     * 使用公钥加密数据（前端用公钥加密，服务端用私钥解密）
     *
     * @param string $data 待加密数据
     * @return string Base64编码的加密数据
     */
    public static function publicEncrypt($data)
    {
        if (empty(self::$publicKey)) {
            throw new Exception('Public key not initialized');
        }

        $encrypted = '';
        $result = openssl_public_encrypt($data, $encrypted, self::$publicKey, OPENSSL_PKCS1_OAEP_PADDING);

        if (!$result) {
            throw new Exception('Failed to encrypt data: ' . openssl_error_string());
        }

        return base64_encode($encrypted);
    }

    /**
     * 使用私钥解密数据（服务端用私钥解密前端加密的数据）
     *
     * @param string $encryptedData Base64编码的加密数据
     * @return string 解密后的数据
     */
    public static function privateDecrypt($encryptedData)
    {
        if (empty(self::$privateKey)) {
            throw new Exception('Private key not initialized');
        }

        $decodedData = base64_decode($encryptedData);
        if ($decodedData === false) {
            throw new Exception('Invalid encrypted data format');
        }

        $decrypted = '';
        $result = openssl_private_decrypt($decodedData, $decrypted, self::$privateKey, OPENSSL_PKCS1_OAEP_PADDING);

        if (!$result) {
            throw new Exception('Failed to decrypt data: ' . openssl_error_string());
        }

        return $decrypted;
    }

    /**
     * 使用私钥加密数据（服务端签名响应）
     *
     * @param string $data 待加密数据
     * @return string Base64编码的加密数据
     */
    public static function privateEncrypt($data)
    {
        if (empty(self::$privateKey)) {
            throw new Exception('Private key not initialized');
        }

        $encrypted = '';
        $result = openssl_private_encrypt($data, $encrypted, self::$privateKey, OPENSSL_PKCS1_OAEP_PADDING);

        if (!$result) {
            throw new Exception('Failed to encrypt data: ' . openssl_error_string());
        }

        return base64_encode($encrypted);
    }

    /**
     * 使用公钥解密数据（前端验证服务端响应）
     *
     * @param string $encryptedData Base64编码的加密数据
     * @return string 解密后的数据
     */
    public static function publicDecrypt($encryptedData)
    {
        if (empty(self::$publicKey)) {
            throw new Exception('Public key not initialized');
        }

        $decodedData = base64_decode($encryptedData);
        if ($decodedData === false) {
            throw new Exception('Invalid encrypted data format');
        }

        $decrypted = '';
        $result = openssl_public_decrypt($decodedData, $decrypted, self::$publicKey, OPENSSL_PKCS1_OAEP_PADDING);

        if (!$result) {
            throw new Exception('Failed to decrypt data: ' . openssl_error_string());
        }

        return $decrypted;
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
            chmod($privateKeyPath, 0600);
            file_put_contents($publicKeyPath, $publicKey);
            chmod($publicKeyPath, 0644);

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
