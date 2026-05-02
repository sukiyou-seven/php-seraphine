<?php
require __DIR__ . "/../../vendor/autoload.php";
require_once __DIR__ . "/../../seraphine/g/g.php";
require_once __DIR__ . "/../../seraphine/receive_data/receive_data.php";

require_once __DIR__ . "/../../seraphine/database/mongodb_client.php";
require_once __DIR__ . "/../../seraphine/base_class/BaseClass.php";
require_once __DIR__ . "/../../seraphine/tools/t_sha256/t_sha256.php";

use BaseClass\BaseClass;
use rec\ReceiveData;

class User extends BaseClass
{

    private $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = new MongoDB_Client();
    }

    /**
     * 创建新用户
     * @param string $username 用户名
     * @param string $nickname 邮箱
     * @param string $password 邮箱
     * @param string $team_code 邮箱
     * @return string 是否成功
     * @example
     * {
     *   "username": "zhangsan",
     *   "email": "zhangsan@example.com"
     * }
     */
    public function create_user()
    {


        $collection = $this->db->getCollection("client_user");


        // 根据实际需求设置唯一字段
        // 示例1: username 唯一
        $collection->createIndex(
            ['username' => 1],
            ['unique' => true, 'name' => 'idx_username_unique']
        );
        $this->rec['password'] = SHA256::hash($this->rec['password']);

        $res = $this->db->insertOne("client_user",
            $this->rec
        );
        if (!$res['success']) {
            G::set("code", "USER_ERROR_A0111");
            return "error";
        }
        return "success";
    }

    /**
     * 用户登录
     * @param string $username 用户名
     * @param string $password 密码
     * @return array 用户信息
     * @example
     *  {
     *    "username": "zhangsan",
     *    "email": "zhangsan@example.com"
     *  }
     */
    public function login()
    {
        $user_data = $this->db->findOne("client_user", [
            "username" => $this->rec['username']
        ]);
        $password = $this->rec['password'];
        $password_enpty = SHA256::hash($password);
        if ($user_data['password'] == $password_enpty) {
            $token = Token::generateAccessToken(["username" => $this->rec['username']]);
            G::set("token", $token);
            return $user_data;
        } else {
            G::set("code", "USER_ERROR_A0120");
            return "error";
        }
    }
}
