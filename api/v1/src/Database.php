<?php
namespace Backend;

class Database {
    private $pdo;

    public function __construct($host, $username, $password, $dbname) {
        $this->pdo = new \PDO("mysql:host=".$host.";dbname=".$dbname, $username, $password);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function prepare($query) {
        return $this->pdo->prepare($query);
    }

    public function getPDO() {
        return $this->pdo;
    }
}