<?php

namespace App\lib\db;

require_once "../internal/ConfigReader.php";

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
            $confReader = new ConfigReader($pathToConfig);
            $host = $confReader->getValue("host");
            $port = $confReader->getValue("port");
            $db = $confReader->getValue("db");
            $username = $confReader->getValue("username");
            $password = $confReader->getValue("passwd");
        }
        catch (Exception $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
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
