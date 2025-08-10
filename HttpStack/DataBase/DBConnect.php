<?php
namespace HttpStack\DataBase;
use HttpStack\Traits\DBModel;
use PDO;
use PDOException;

class DBConnect extends PDO
{
    protected PDO $dbConnect;

    public function __construct(
        string $dsn = 'mysql:host=localhost;dbname=cmcintosh;charset=utf8mb4',
        string $user = 'http_user',
        string $pass = 'bf6912'
    ) { 
        try {
            parent::__construct($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
    
        } catch (PDOException $e) {
            throw new \RuntimeException('DB Connection failed: ' . $e->getMessage());
        }
    }
}