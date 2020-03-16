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
        $expr = self::GetPdo()->prepare('SELECT hpasswd FROM usr WHERE login = :login');
        $expr->execute(['login' => $user]);
        $passwd_hash = $expr->fetch(PDO::FETCH_ASSOC)['hpasswd'];
        return  password_verify($passwd, $passwd_hash);
    }

    public static function RegisterNewUser(string $login, string $passwd, string $name, string $surname): int {
        if (self::CheckUserRegistred($login, $passwd) > 0)
        {
            throw new Exception("User $login is already exists");
        }
        $passwd_hash = password_hash($passwd,  PASSWORD_BCRYPT);
        $expr = self::GetPdo()->prepare('INSERT INTO usr (login, hpasswd, name, surname) VALUES (:login, :hpasswd, :name, :surname)');
        $expr->execute(['login' => $login, 'hpasswd' => $passwd_hash, 'name' => $name, 'surname' => $surname]);
        return $expr->rowCount();
    }

    public static function ChangePassword(string $login, string $oldPasswd, string $newPasswd): int {
        if (self::CheckUserRegistred($login, $oldPasswd) === 0)
        {
            throw new Exception("User $login is not exists");
        }
        $expr=self::GetPdo()->prepare('UPDATE usr SET hpasswd=:newPasswd WHERE hpasswd=:oldPasswd');
        $expr->execute(['newPasswd' => $newPasswd, 'oldPasswd' => $oldPasswd]);
        return $expr->rowCount();
    }

    /*
    public static function ChangeLogin($oldLogin, $newLogin) : int {

        $expr=self::GetPdo()->prepare('UPDATE usr SET hpasswd=:newPasswd WHERE hpasswd=:oldPasswd');
        $expr->execute(['newPasswd' => $newPasswd, 'oldPasswd' => $oldPasswd]);
        return $expr->rowCount();
    }*/


}
?>