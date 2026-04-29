<?php

class SignKeyGenerator
{
    /**
     * 生成签名密钥
     *
     * @param int $length 密钥长度（字节），默认32字节（256位）
     * @return string 十六进制格式的密钥
     */
    public static function generate($length = 32)
    {
        if ($length < 16) {
            $length = 16;
        }

        $randomBytes = random_bytes($length);
        return bin2hex($randomBytes);
    }

    /**
     * 生成带时间戳的签名密钥
     *
     * @param string $secret 基础密钥
     * @param string $data 需要签名的数据
     * @return string SHA256签名
     */
    public static function createSignature($secret, $data)
    {
        return hash_hmac('sha256', $data, $secret);
    }

    /**
     * 验证签名
     *
     * @param string $secret 基础密钥
     * @param string $data 原始数据
     * @param string $signature 待验证的签名
     * @return bool 签名是否有效
     */
    public static function verifySignature($secret, $data, $signature)
    {
        $expectedSignature = self::createSignature($secret, $data);
        return hash_equals($expectedSignature, $signature);
    }
}

$res = SignKeyGenerator::generate();
print_r($res);