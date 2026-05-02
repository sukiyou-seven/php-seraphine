<?php
/**
 * Token 管理类
 * 基于 JWT (JSON Web Token) 实现
 */

require_once __DIR__ . '/../g/g.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;


class Token
{
    private static $secretKey = null;
    private static $algorithm = 'HS256';
    private static $expireTime = 3600; // 默认1小时
    private static $refreshExpireTime = 604800; // 刷新token默认7天
    private static $issuer = "seraphine";
    private static $audience = 'seraphine';

    /**
     * 初始化 Token 配置
     *
     * @param string $secretKey 密钥
     * @param string $algorithm 加密算法 HS256, HS384, HS512, RS256, RS384, RS512
     * @param int $expireTime 过期时间（秒）
     */
    public static function init($secretKey = null, $algorithm = 'HS256', $expireTime = 3600)
    {
        if ($secretKey) {
            self::$secretKey = $secretKey;
        } elseif (!self::$secretKey) {
            // 如果没有设置密钥，使用 sign_secret
            self::$secretKey = G::get("sign_secret") ?: 'default_secret_key_change_in_production';
        }

        self::$algorithm = $algorithm;
        self::$expireTime = $expireTime;
    }

    /**
     * 生成 Access Token
     *
     * @param array $payload 自定义数据（如 user_id, username 等）
     * @param int $expireTime 过期时间（秒），null 则使用默认值
     * @return string JWT Token
     */
    public static function generateAccessToken($payload = [], $expireTime = null)
    {
        if (!self::$secretKey) {
            self::init();
        }

        $currentTime = time();
        $exp = $expireTime ?: self::$expireTime;

        $tokenPayload = [
            'iss' => self::$issuer,
            'aud' => self::$audience,
            'iat' => $currentTime,
            'nbf' => $currentTime,
            'exp' => $currentTime + $exp,
            'type' => 'access'
        ];

        // 合并自定义数据
        if (!empty($payload)) {
            $tokenPayload = array_merge($tokenPayload, $payload);
        }

        return JWT::encode($tokenPayload, self::$secretKey, self::$algorithm);
    }

    /**
     * 生成 Refresh Token
     *
     * @param array $payload 自定义数据
     * @param int $expireTime 过期时间（秒）
     * @return string JWT Token
     */
    public static function generateRefreshToken($payload = [], $expireTime = null)
    {
        if (!self::$secretKey) {
            self::init();
        }

        $currentTime = time();
        $exp = $expireTime ?: self::$refreshExpireTime;

        $tokenPayload = [
            'iss' => self::$issuer,
            'aud' => self::$audience,
            'iat' => $currentTime,
            'nbf' => $currentTime,
            'exp' => $currentTime + $exp,
            'type' => 'refresh'
        ];

        if (!empty($payload)) {
            $tokenPayload = array_merge($tokenPayload, $payload);
        }

        return JWT::encode($tokenPayload, self::$secretKey, self::$algorithm);
    }

    /**
     * 生成 Token 对（Access + Refresh）
     *
     * @param array $payload 自定义数据
     * @return array ['access_token' => ..., 'refresh_token' => ...]
     */
    public static function generateTokenPair($payload = [])
    {
        return [
            'access_token' => self::generateAccessToken($payload),
            'refresh_token' => self::generateRefreshToken($payload),
            'expires_in' => self::$expireTime,
            'token_type' => 'Bearer'
        ];
    }

    /**
     * 验证并解析 Token
     *
     * @param string $token JWT Token
     * @param bool $verifyType 是否验证 token 类型
     * @param string $expectedType 期望的 token 类型（access/refresh）
     * @return array|false 解析后的数据，失败返回 false
     */
    public static function verify($token, $verifyType = false, $expectedType = 'access')
    {
        if (!self::$secretKey) {
            self::init();
        }

        try {
            $decoded = JWT::decode($token, new Key(self::$secretKey, self::$algorithm));

            // 验证 token 类型
            if ($verifyType && isset($decoded->type) && $decoded->type !== $expectedType) {
                G::set("response_data_seraphine", "invalid_token_type");
                return false;
            }

            return (array)$decoded;

        } catch (ExpiredException $e) {
            G::set("response_data_seraphine", "token_expired");
            return false;
        } catch (SignatureInvalidException $e) {
            G::set("response_data_seraphine", "invalid_signature");
            return false;
        } catch (Exception $e) {
            G::set("response_data_seraphine", "token_invalid: " . $e->getMessage());
            return false;
        }
    }

    /**
     * 验证 Access Token
     *
     * @param string $token JWT Token
     * @return array|false
     */
    public static function verifyAccessToken($token)
    {
        return self::verify($token, true, 'access');
    }

    /**
     * 验证 Refresh Token
     *
     * @param string $token JWT Token
     * @return array|false
     */
    public static function verifyRefreshToken($token)
    {
        return self::verify($token, true, 'refresh');
    }

    /**
     * 刷新 Token
     * 使用 Refresh Token 生成新的 Access Token
     *
     * @param string $refreshToken
     * @return array|false ['access_token' => ..., 'refresh_token' => ...] 或 false
     */
    public static function refreshToken($refreshToken)
    {
        $payload = self::verifyRefreshToken($refreshToken);

        if (!$payload) {
            return false;
        }

        // 移除 JWT 标准字段，保留自定义数据
        $customPayload = [];
        $excludeFields = ['iss', 'aud', 'iat', 'nbf', 'exp', 'type'];

        foreach ($payload as $key => $value) {
            if (!in_array($key, $excludeFields)) {
                $customPayload[$key] = $value;
            }
        }

        // 生成新的 token 对
        return self::generateTokenPair($customPayload);
    }

    /**
     * 从请求头获取 Token
     *
     * @return string|null
     */
    public static function getTokenFromHeader()
    {
        // 方式1: Authorization: Bearer <token>
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
            if (preg_match('/Bearer\s+(.+)$/i', $authHeader, $matches)) {
                return $matches[1];
            }
        }

        // 方式2: X-Access-Token
        if (isset($_SERVER['HTTP_X_ACCESS_TOKEN'])) {
            return $_SERVER['HTTP_X_ACCESS_TOKEN'];
        }

        return null;
    }

    /**
     * 验证请求中的 Token（从 Header 自动获取）
     *
     * @return array|false
     */
    public static function verifyRequestToken()
    {
        $token = self::getTokenFromHeader();

        if (!$token) {
            G::set("code", "USER_ERROR_A0404");
            return false;
        }

        return self::verifyAccessToken($token);
    }

    /**
     * 获取 Token 剩余有效期（秒）
     *
     * @param string $token
     * @return int|false 剩余秒数，失败返回 false
     */
    public static function getTimeRemaining($token)
    {
        $payload = self::verify($token);

        if (!$payload || !isset($payload['exp'])) {
            return false;
        }

        $remaining = $payload['exp'] - time();
        return max(0, $remaining);
    }

    /**
     * 检查 Token 是否即将过期
     *
     * @param string $token
     * @param int $threshold 阈值（秒），默认300秒（5分钟）
     * @return bool
     */
    public static function isTokenExpiringSoon($token, $threshold = 300)
    {
        $remaining = self::getTimeRemaining($token);

        if ($remaining === false) {
            return true;
        }

        return $remaining <= $threshold;
    }

    /**
     * 使 Token 失效（黑名单机制）
     * 需要配合缓存或数据库实现
     *
     * @param string $token
     * @param int $ttl 黑名单存活时间（秒），应 >= token 剩余有效期
     */
    public static function blacklistToken($token, $ttl = null)
    {
        if ($ttl === null) {
            $remaining = self::getTimeRemaining($token);
            $ttl = $remaining ?: 3600;
        }

        $blacklistKey = 'token_blacklist_' . md5($token);

        // 这里可以使用 Redis、Memcached 或其他缓存系统
        // 示例：Redis::setex($blacklistKey, $ttl, '1');

        // 临时使用全局变量存储（不推荐生产环境）
        $blacklist = G::get('token_blacklist') ?: [];
        $blacklist[$blacklistKey] = time() + $ttl;
        G::set('token_blacklist', $blacklist);
    }

    /**
     * 检查 Token 是否在黑名单中
     *
     * @param string $token
     * @return bool
     */
    public static function isTokenBlacklisted($token)
    {
        $blacklistKey = 'token_blacklist_' . md5($token);
        $blacklist = G::get('token_blacklist') ?: [];

        if (isset($blacklist[$blacklistKey])) {
            // 检查是否过期
            if ($blacklist[$blacklistKey] > time()) {
                return true;
            }
            // 清理过期的黑名单记录
            unset($blacklist[$blacklistKey]);
            G::set('token_blacklist', $blacklist);
        }

        return false;
    }

    /**
     * 设置配置项
     */
    public static function setConfig($key, $value)
    {
        switch ($key) {
            case 'secret_key':
                self::$secretKey = $value;
                break;
            case 'algorithm':
                self::$algorithm = $value;
                break;
            case 'expire_time':
                self::$expireTime = $value;
                break;
            case 'refresh_expire_time':
                self::$refreshExpireTime = $value;
                break;
            case 'issuer':
                self::$issuer = $value;
                break;
            case 'audience':
                self::$audience = $value;
                break;
        }
    }

    /**
     * 获取配置项
     */
    public static function getConfig($key)
    {
        switch ($key) {
            case 'secret_key':
                return self::$secretKey;
            case 'algorithm':
                return self::$algorithm;
            case 'expire_time':
                return self::$expireTime;
            case 'refresh_expire_time':
                return self::$refreshExpireTime;
            case 'issuer':
                return self::$issuer;
            case 'audience':
                return self::$audience;
            default:
                return null;
        }
    }
}
