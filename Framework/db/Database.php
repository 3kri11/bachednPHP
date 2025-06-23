<?php

namespace Framework\db;

use PDO;
use Throwable;

class Database {
    private PDO $db;
    public function __construct(array $config) {
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8;port={$config['port']};";

        try {
            $this->db = new PDO($dsn, $config['username'], $config['password']);
            return $this->db;
        } catch (Throwable $e) {
            return $e->getMessage();
        }
    }

    public function query(string $sql,array $params = [],array $controls = ['count' => 'all']) {
        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            $paramType = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($key, $value,$paramType);
        }

        $stmt->execute();

        return $controls['count'] === 'single' ? $stmt->fetch(PDO::FETCH_ASSOC) : $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}