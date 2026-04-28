<?php


namespace rec {

//    require __DIR__ . "/../../seraphine/config/read_config.php";
    use ReadConfig;

    class ReceiveData
    {
        public static function rec()
        {
            $app_config = ReadConfig::read_yml("app");

            if ($app_config['app']['debug']) {
                $getData = $_GET;
                if (!empty($getData)) {
                    return $getData;
                }
            }

            $data = file_get_contents("php://input");
            return json_decode($data, true);
        }
    }
}

