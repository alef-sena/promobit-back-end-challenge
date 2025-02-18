<?php

namespace App\Config;

class DatabaseConfig
{
    private $config;

    public function __construct()
    {
        $this->config = require __DIR__ . '/../../config/database.php';
    }

    public function getHost()
    {
        return $this->config['host'];
    }

    public function getDbName()
    {
        return $this->config['dbname'];
    }

    public function getUsername()
    {
        return $this->config['username'];
    }

    public function getPassword()
    {
        return $this->config['password'];
    }
}