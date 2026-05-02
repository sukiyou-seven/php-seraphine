<?php
require_once __DIR__ . '/../g/g.php';
require_once __DIR__ . '/../error_code/error_code.php';

class ResponseSignCtrl
{
    /**
     * 为响应数据生成签名（服务端使用私钥签名）
     *
     * @param mixed $responseData 响应数据
     * @return array 包含数据和签名的数组
     */
    public static function signResponse($responseData)
    {
        $timestamp = time();
        $nonceStr = bin2hex(random_bytes(8));

        // 将响应数据转换为字符串
        $dataString = is_array($responseData) ? json_encode($responseData, JSON_UNESCAPED_UNICODE) : strval($responseData);

        // 构建签名字符串
        $signString = $dataString . '&timestamp=' . $timestamp . '&nonceStr=' . $nonceStr;

        // 使用私钥生成签名
        $signature = RSA::sign($signString);

        return [
            'data' => $responseData,
            'signature' => $signature,
            'timestamp' => $timestamp,
            'nonceStr' => $nonceStr
        ];
    }

    /**
     * 验证请求签名（使用公钥验证客户端签名）
     *
     * @return bool 签名是否有效
     */
    public static function verifyRequest()
    {
        $sign = self::getSignFromHeader();

        if (empty($sign)) {
            G::set("response_data_seraphine", "missing_sign");
            return false;
        }

        $timestamp = self::getTimestampFromHeader();
        if (empty($timestamp)) {
            G::set("response_data_seraphine", "missing_timestamp");
            return false;
        }

        if (!self::checkTimestamp($timestamp)) {
            G::set("response_data_seraphine", "timestamp_expired");
            return false;
        }

        $requestData = self::getRequestData();
        $nonceStr = self::getNonceStrFromHeader();

        // 构建待验证的签名字符串
        $dataString = is_array($requestData) ? json_encode($requestData, JSON_UNESCAPED_UNICODE) : strval($requestData);
        $signString = $dataString . '&timestamp=' . $timestamp . '&nonceStr=' . $nonceStr;

        // 使用公钥验证签名
        $isValid = RSA::verify($signString, $sign);

        if (!$isValid) {
            G::set("response_data_seraphine", "invalid_sign");
            return false;
        }

        return true;
    }

    private static function getSignFromHeader()
    {
        return isset($_SERVER['HTTP_X_SIGNATURE']) ? $_SERVER['HTTP_X_SIGNATURE'] : '';
    }

    private static function getTimestampFromHeader()
    {
        return isset($_SERVER['HTTP_X_TIMESTAMP']) ? $_SERVER['HTTP_X_TIMESTAMP'] : '';
    }

    private static function getNonceStrFromHeader()
    {
        return isset($_SERVER['HTTP_X_NONCESTR']) ? $_SERVER['HTTP_X_NONCESTR'] : '';
    }

    private static function getRequestData()
    {
        $data = file_get_contents("php://input");
        $decoded = json_decode($data, true);
        return $decoded === null ? $data : $decoded;
    }

    private static function checkTimestamp($timestamp, $tolerance = 300)
    {
        if (!is_numeric($timestamp)) {
            return false;
        }

        $currentTime = time();
        $timeDiff = abs($currentTime - intval($timestamp));
        return $timeDiff <= $tolerance;
    }
}
