<?php
namespace HttpStack\Credential;

class MySQLCredentials {
    const HOST = "localhost";
    const DB = "cmcintosh";
    const DSN = "mysql:host=".self::HOST.";dbname=".self::DB.";charset=utf8mb4";
    const USER = "http_user";
    const PASS = "bf6912";

    public static function get() {
        return [
            'host' => self::HOST,
            'db' => self::DB,
            'dsn' => self::DSN,
            'user' => self::USER,
            'pass' => self::PASS,
        ];
    }
}