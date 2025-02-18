<?php

namespace App\Database;

use PDO;
use App\Config\DatabaseConfig;

class Connection
{
    private static $instance = null;
    private $conn;

    private function __construct(DatabaseConfig $dbConfig)
    {
        try {
            $this->conn = new PDO(
                "mysql:host={$dbConfig->getHost()};dbname={$dbConfig->getDbName()}",
                $dbConfig->getUsername(),
                $dbConfig->getPassword()
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            throw new \Exception("Connection error: " . $e->getMessage());
        }
    }

    public static function getInstance(DatabaseConfig $dbConfig = null)
    {
        if (self::$instance === null) {
            if ($dbConfig === null) {
                $dbConfig = new DatabaseConfig();
            }
            self::$instance = new Connection($dbConfig);
        }
        return self::$instance->conn;
    }
}