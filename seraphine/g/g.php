<?php
class G
{
    private static array $data = [];
    private static $storage = [];

    public static function set($key, $value)
    {
        self::$storage[$key] = $value;
    }

    public static function get($key, $default = null)
    {
        return self::$storage[$key] ?? $default;
    }

    public static function clear()
    {
        self::$storage = [];
    }
}