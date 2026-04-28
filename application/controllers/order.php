<?php
require __DIR__."/../../vendor/autoload.php";
require_once __DIR__."/../../seraphine/g/g.php";
require_once __DIR__."/../../seraphine/receive_data/receive_data.php";

require_once __DIR__."/../../seraphine/database/mongodb_client.php";

use rec\ReceiveData;
//use mongoself\MongoDB_Connect;

class Order
{

    /**
     * 创建新用户
     * @param string $username 用户名
     * @param string $email 邮箱
     * @return string 是否成功
     * @example
     * {
     *   "username": "zhangsan",
     *   "email": "zhangsan@example.com"
     * }
     */
    public static function mongo_test()
    {
        $mg = new MongoDB_Client();
        $mg->insertOne("test1",
        array("username" => "zhangsan", "email" => "zhangsan@example.com")
        );
        return "success";
    }

    /**
     * 用户登录
     * @param string $username 用户名
     * @param string $password 密码
     * @return array 用户信息
     */
    public static function test500()
    {
//        $a = 0/0;
        $rec = ReceiveData::rec();
//        print_r($rec);

        G::set("code","USER_ERROR_0001");

        return array("username" => "admin", "password" => "<PASSWORD>",
            "e"=>"dd");
    }
}


//$a = new User();
//$res = $a->login();
//
//print_r($res);