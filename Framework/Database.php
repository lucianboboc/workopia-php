<?php

namespace Framework;

use PDO;

class Database
{
    public $conn;

    /**
     * Constructor for Database class
     * @param array $config
     * @throws Exception
     */
    public function __construct(array $config)
    {
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        try {
            $this->conn = new PDO($dsn, $config['username'], $config['password'], $options);
        } catch (PDOException $exception){
            throw new Exception("Database connection failed: {$exception->getMessage()}");
        }
    }

    /**
     * Query the Database
     * @param string $query
     * @return PDOStatement
     * @throws PDOException
     */
    public function query($query, $params = null)
    {
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $exception) {
            throw new Exception("Query failed to execute: {$exception->getMessage()}");
        }
    }
}