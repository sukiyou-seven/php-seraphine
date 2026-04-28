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



    // 写入 YAML 文件
//$array = ['name' => 'John', 'age' => 30];
//Yaml::dump($array, 2);  // 第二个参数是缩进级别
}

//$res = ReadConfig::read_yml("app");
//print_r($res);