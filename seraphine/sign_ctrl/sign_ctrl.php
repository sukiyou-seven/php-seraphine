<?php
require_once __DIR__ . '/../g/g.php';
require_once __DIR__ . '/../error_code/error_code.php';
require_once __DIR__ . '/../tools/t_sha256/t_sha256.php';

$sign_secret = G::get("sign_secret");

class SignCtrl
{
    private static $sign_secret;

    public static function init($secret)
    {
        self::$sign_secret = $secret;
    }

    /**
     * 验证客户端签名
     *
     * @return bool 签名是否有效
     */
    public static function verify()
    {
        if (empty(self::$sign_secret)) {
            return false;
        }

        $sign = self::getSignFromHeader();

        if (empty($sign)) {
            self::setError('missing_sign');
            return false;
        }

        $timestamp = self::getTimestampFromHeader();

        if (empty($timestamp)) {
            self::setError('missing_timestamp');
            return false;
        }

        if (!self::checkTimestamp($timestamp)) {
            self::setError('timestamp_expired');
            return false;
        }

        $requestData = self::getRequestData();
        $expectedSign = self::generateSign($requestData, $timestamp);

        if (!hash_equals($expectedSign, $sign)) {
            self::setError('invalid_sign');
            return false;
        }

        return true;
    }

    /**
     * 从请求头获取签名
     */
    private static function getSignFromHeader()
    {
        if (isset($_SERVER['HTTP_SIGN'])) {
            return $_SERVER['HTTP_SIGN'];
        }
        return '';
    }

    /**
     * 从请求头获取时间戳
     */
    private static function getTimestampFromHeader()
    {
        if (isset($_SERVER['HTTP_TIMESTAMP'])) {
            return $_SERVER['HTTP_TIMESTAMP'];
        }
        return '';
    }

    /**
     * 获取请求数据
     */
    private static function getRequestData()
    {
        $data = file_get_contents("php://input");
        $decoded = json_decode($data, true);

        if ($decoded === null) {
            return $data;
        }

        return $decoded;
    }

    /**
     * 生成签名
     *
     * @param mixed $data 请求数据
     * @param int $timestamp 时间戳
     * @return string 签名字符串
     */
    private static function generateSign($data, $timestamp)
    {
        if (is_array($data)) {
            ksort($data);
            $queryString = http_build_query($data);
        } else {
            $queryString = $data;
        }

        $signString = $queryString . '&timestamp=' . $timestamp . '&secret=' . self::$sign_secret;

        return RSA::sign($signString);
    }

    /**
     * 检查时间戳是否有效（防止重放攻击）
     *
     * @param int $timestamp 客户端时间戳
     * @param int $tolerance 允许的误差范围（秒），默认300秒
     * @return bool 是否有效
     */
    private static function checkTimestamp($timestamp, $tolerance = 300)
    {
        if (!is_numeric($timestamp)) {
            return false;
        }

        $currentTime = time();
        $timeDiff = abs($currentTime - intval($timestamp));

        return $timeDiff <= $tolerance;
    }

    /**
     * 设置错误码
     */
    private static function setError($errorCode)
    {
        G::set("code", $errorCode);
    }

    /**
     * 创建签名（用于服务端生成签名示例）
     *
     * @param mixed $data 请求数据
     * @param int $timestamp 时间戳
     * @return string 签名字符串
     */
    public static function createSign($data, $timestamp = null)
    {
        if ($timestamp === null) {
            $timestamp = time();
        }

        return self::generateSign($data, $timestamp);
    }
}

SignCtrl::init($sign_secret);
