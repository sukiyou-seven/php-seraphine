<?php

require __DIR__.'/../../vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;

class ReadConfig
{
    public static function read_yml($file)
    {
        $data = Yaml::parseFile(__DIR__ . "/../../config/" . $file.".yml");
        return $data;
    }


    /**
     * 将 YAML 配置文件转换为 JSON 文件
     *
     * @param string $file YAML 文件名（不含扩展名）
     * @param string $outputPath JSON 输出路径（可选，默认为 config 目录下同名 .json）
     * @param bool $prettyPrint 是否格式化输出
     * @return array 转换结果
     */
    public static function ymlToJson($file, $outputPath = null, $prettyPrint = true)
    {
        $yamlPath = __DIR__ . "/../../config/" . $file . ".yml";

        if (!file_exists($yamlPath)) {
            return [
                'success' => false,
                'message' => 'YAML 文件不存在: ' . $yamlPath
            ];
        }

        try {
            $config = self::read_yml($file);

            if ($outputPath === null) {
                $outputPath = __DIR__ . "/../../config/" . $file . ".json";
            }

            $jsonContent = $prettyPrint
                ? json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                : json_encode($config, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            if (file_put_contents($outputPath, $jsonContent) === false) {
                return [
                    'success' => false,
                    'message' => '写入 JSON 文件失败: ' . $outputPath
                ];
            }

            return [
                'success' => true,
                'message' => '转换成功',
                'source' => $yamlPath,
                'target' => $outputPath,
                'size' => filesize($outputPath)
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => '转换失败: ' . $e->getMessage()
            ];
        }
    }


    /**
     * 修改 YAML 配置文件中的某个值
     *
     * @param string $file YAML 文件名（不含扩展名）
     * @param string|array $key 配置键名，支持点号分隔的嵌套键，如 'app.debug' 或数组 ['app', 'debug']
     * @param mixed $value 新的值
     * @return array 操作结果
     */
    public static function write_yaml($file, $key, $value = null)
    {
        $yamlPath = __DIR__ . "/../../config/" . $file . ".yml";

        if (!file_exists($yamlPath)) {
            return [
                'success' => false,
                'message' => 'YAML 文件不存在: ' . $yamlPath
            ];
        }

        try {
            // 读取现有配置
            $config = self::read_yml($file);

            // 解析键路径
            if (is_string($key)) {
                $keys = explode('.', $key);
            } else {
                $keys = $key;
            }

            // 设置新值
            self::setNestedValue($config, $keys, $value);

            // 生成 YAML 内容
            $yamlContent = Yaml::dump($config, 4, 2, Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE | Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK);

            // 写入文件
            if (file_put_contents($yamlPath, $yamlContent) === false) {
                return [
                    'success' => false,
                    'message' => '写入文件失败: ' . $yamlPath
                ];
            }

            return [
                'success' => true,
                'message' => '配置更新成功',
                'file' => $yamlPath,
                'key' => is_array($key) ? implode('.', $key) : $key,
                'value' => $value
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => '更新失败: ' . $e->getMessage()
            ];
        }
    }

    /**
     * 设置嵌套数组的值
     *
     * @param array &$array 引用数组
     * @param array $keys 键路径数组
     * @param mixed $value 要设置的值
     */
    private static function setNestedValue(&$array, $keys, $value)
    {
        $current = &$array;

        foreach ($keys as $index => $key) {
            if ($index === count($keys) - 1) {
                // 最后一个键，设置值
                $current[$key] = $value;
            } else {
                // 中间键，确保存在且为数组
                if (!isset($current[$key]) || !is_array($current[$key])) {
                    $current[$key] = [];
                }
                $current = &$current[$key];
            }
        }
    }

    /**
     * 批量更新多个配置项
     *
     * @param string $file YAML 文件名
     * @param array $updates 更新数据，格式: ['key.path' => value, ...]
     * @return array 操作结果
     */
    public static function batchWriteYaml($file, $updates)
    {
        $yamlPath = __DIR__ . "/../../config/" . $file . ".yml";

        if (!file_exists($yamlPath)) {
            return [
                'success' => false,
                'message' => 'YAML 文件不存在: ' . $yamlPath
            ];
        }

        try {
            $config = self::read_yml($file);

            foreach ($updates as $key => $value) {
                $keys = is_string($key) ? explode('.', $key) : $key;
                self::setNestedValue($config, $keys, $value);
            }

            $yamlContent = Yaml::dump($config, 4, 2, Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE | Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK);

            if (file_put_contents($yamlPath, $yamlContent) === false) {
                return [
                    'success' => false,
                    'message' => '写入文件失败'
                ];
            }

            return [
                'success' => true,
                'message' => '批量更新成功',
                'updated_keys' => count($updates)
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => '更新失败: ' . $e->getMessage()
            ];
        }
    }


}

//$res = ReadConfig::read_yml("app");
//print_r($res);