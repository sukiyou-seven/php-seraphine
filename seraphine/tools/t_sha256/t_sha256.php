<?php
class SHA256{
    public static function hash($data){
        return hash('sha256', $data);
    }
}