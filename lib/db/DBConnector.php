<?php

namespace App\lib\db;

use App\lib\ConfReaders;
use Symfony\Component\Config\Definition\Exception\Exception;
use PDO;

class DBConnector
{
    private $pdo;

    public function __construct(string $filename, string $filepath = "")
    {
        try {
            $reader = new ConfReaders\YamlConfigReader($filename, $filepath);
            $host = $reader->get("host");
            $port = $reader->get("port");
            $db = $reader->get("db");
            $username = $reader->get("username");
            $password = $reader->get("passwd");
        } catch (Exception $ex) {
            echo "Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
        $dsn="pgsql:host={$host};port={$port};dbname={$db};user={$username};password={$password}";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $this->pdo = new PDO($dsn, $username, $password, $options);
    }
    private function query(string $query, array $params): \PDOStatement
    {
        $expr = $this->pdo->prepare($query);
        $expr->execute($params);
        return $expr;
    }
    public function multirows(string $query, array $params)
    {
        $statement = $this->query($query, $params);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    public function row(string $query, array $params)
    {
        $statement = $this->query($query, $params);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }
    public function scalar(string $query, array $params)
    {
        $statement = $this->query($query, $params);
        return $statement->fetchColumn();
    }
    public function nonQuery(string $query, array $params)
    {
        $statement = $this->query($query, $params);
        return $statement->rowCount();
    }
}
