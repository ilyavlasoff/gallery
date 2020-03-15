<?php
require_once "DBConnector.php";

class DBExecutor {

    private static function GetPdo(): PDO {
        static $pdo = null;
        if (is_null($pdo)) {
            $pdo = DBConnector::CreateDboInstance();
        }
        return $pdo;
    }

    public static function CheckUserRegistred(string $user, string $passwd): bool {
        $expr = self::GetPdo()->prepare('SELECT count(*) FROM user WHERE login = :login and passwd = :passwd');
        $expr->execute(['login' => $user, 'passwd' => $passwd]);
        return  $expr->fetchColumn(PDO::FETCH_LAZY) > 0;
    }


}
?>