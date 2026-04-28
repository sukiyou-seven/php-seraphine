<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

class TToken
{
    private string $secret;
    private string $iss;

    public function __construct()
    {
        $config = ReadConfig::read_yml('secret');
        $this->secret = $config['secret'];
        $this->iss = $config['iss'];
    }

    /**
     * 创建 Access Token 和 Refresh Token
     */
    public function create(array $data, int $expireTime = 604800): array
    {
        $now = time();
        $exp = $now + $expireTime;

        // Access Token Payload
        $accessPayload = [
            'data' => $data,
            'exp' => $exp,
            'iss' => $this->iss,
            'iat' => $now,  // 签发时间
        ];

        // Refresh Token Payload（无过期时间）
        $refreshPayload = [
            'data' => $data,
            'iss' => $this->iss,
            'iat' => $now,
        ];

        $accessToken = JWT::encode($accessPayload, $this->secret, 'HS256');
        $refreshToken = $this->refreshToken($data);

        return [$accessToken, $refreshToken];
    }

    /**
     * 创建 Refresh Token
     */
    public function refreshToken(array $data): string
    {
        $now = time();

        $payload = [
            'data' => $data,
            'iss' => $this->iss,
            'iat' => $now,
        ];

        return JWT::encode($payload, $this->secret, 'HS256');
    }

    /**
     * 解码验证 Token
     */
    public function decode(string $token): object
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));
            return $decoded;
        } catch (ExpiredException $e) {
            throw new Exception('EXPIRED');
        } catch (SignatureInvalidException $e) {
            throw new Exception('用户认证失败, 请重新登录');
        } catch (Exception $e) {
            throw new Exception('Token 错误: ' . $e->getMessage());
        }
    }
}