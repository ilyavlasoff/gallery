<?php

namespace App\lib\db;

use App\lib\ConfReaders;
use Symfony\Component\Config\Definition\Exception\Exception;
use PDO;

class DBConnector {
    /*
    private static PDO $pdo;

    public function __get(): PDO
    {
        if (is_null(self::$pdo)) {
            //self::$pdo = $this->CreateDboInstance();
        }

    }*/
    public static function CreateDboInstance(string $pathToConfig = ""): PDO
    {
        try {
            $reader = new ConfReaders\YamlConfigReader('dbconf.yaml');
            $host = $reader->get("host");
            $port = $reader->get("port");
            $db = $reader->get("db");
            $username = $reader->get("username");
            $password = $reader->get("passwd");
        }
        catch (Exception $ex) {
            print "Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
        $dsn="pgsql:host={$host};port={$port};dbname={$db};user={$username};password={$password}";
        $opt = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $pdo = new PDO($dsn, $username, $password, $opt);
        return $pdo;
    }
}
