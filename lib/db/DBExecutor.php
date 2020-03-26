<?php

namespace App\lib\db;

use App\db\DBConnector as DBConnector;

class DBExecutor {

    private static function GetPdo(): PDO
    {
        static $pdo = null;
        if (is_null($pdo)) {
            $pdo = DBConnector::CreateDboInstance();
        }
        return $pdo;
    }

    public static function CheckUserRegistred(string $user, string $passwd): bool
    {
        $expr = self::GetPdo()->prepare('SELECT hpasswd FROM usr WHERE login = :login');
        $expr->execute(['login' => $user]);
        $passwd_hash = $expr->fetch(PDO::FETCH_ASSOC)['hpasswd'];
        return  password_verify($passwd, $passwd_hash);
    }

    public static function CheckUserExists(string $username): bool {
        $expr = self::GetPdo()->prepare('SELECT count(*) FROM usr WHERE login=:login');
        $expr->execute(['login' => $username]);
        return $expr->fetchColumn() != false;
    }

    public static function RegisterNewUser(string $login, string $passwd, string $name, string $surname, string $nick): int
    {
        if (self::CheckUserRegistred($login, $passwd) > 0)
        {
            throw new Exception("User $login is already exists");
        }
        $passwd_hash = password_hash($passwd,  PASSWORD_BCRYPT);
        $expr = self::GetPdo()->prepare('INSERT INTO usr (login, hpasswd, name, surname, nick) VALUES (:login, :hpasswd, :name, :surname, :nick)');
        $expr->execute(['login' => $login, 'hpasswd' => $passwd_hash, 'name' => $name, 'surname' => $surname, 'nick' => $nick]);
        return $expr->rowCount();
    }

    public static function ChangePassword(string $login, string $oldPasswd, string $newPasswd): int
    {
        if (self::CheckUserRegistred($login, $oldPasswd) === 0)
        {
            throw new Exception("User $login is not exists");
        }
        $newPasswdHash = password_hash($newPasswd,  PASSWORD_BCRYPT);
        $expr=self::GetPdo()->prepare('UPDATE usr SET hpasswd=:newPasswd WHERE login=:login');
        $expr->execute(['newPasswd' => $newPasswdHash, 'login' => $login]);
        return $expr->rowCount();
    }

    public static function UpdateNick(string $user, string $nick) : int{
        if (!self::CheckUserExists($user)) {
            throw new Exception("User $user doesn't exists");
        }
        $expr = self::GetPdo()->prepare('UPDATE usr SET nick=:nick WHERE login=:login');
        $expr->execute(['nick' => $nick, 'login' => $user]);
        return $expr->rowCount();
    }

    public static function UpdateBio(string $user, string $bio) : int{
        if (!self::CheckUserExists($user)) {
            throw new Exception("User $user doesn't exists");
        }
        $expr = self::GetPdo()->prepare('UPDATE usr SET bio=:bio WHERE login=:login');
        $expr->execute(['bio' => $bio, 'login' => $user]);
        return $expr->rowCount();
    }

    public static function GetUserInfo(string $login): array {
        $expr=self::GetPdo()->prepare('SELECT login, name, surname, bio, profilepicpath, nick FROM usr WHERE login=:login');
        $expr->execute(['login' => $login]);
        $result = $expr->fetchAll(PDO::FETCH_ASSOC);
        if (count($result) === 0) {
            throw new Exception("User doesn't exists");
        }
        else return $result[0];
    }

    public static function GetPosts(string $user, int $quan, int $offset, string $mode): array
    {
        if (!self::CheckUserExists($user)) {
            throw new Exception("User $user doesn't exists");
        }
        $expr = self::GetPdo()->prepare('SELECT phid, path, description, addtime FROM photo WHERE ownerLogin=:owner ORDER BY addtime DESC LIMIT :quan OFFSET :offset');
        $expr->execute(['owner' => $user, 'quan' => $quan, 'offset' => $offset]);
        return $expr->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function GetPostsQuan(string $user) : int
    {
        if (!self::CheckUserExists($user)) {
            throw new Exception("User $user doesn't exists");
        }
        $expr = self::GetPdo()->prepare('SELECT count(*) FROM photo WHERE ownerLogin=:login');
        $expr->execute(['login' => $user]);
        return $expr->fetchColumn();
    }

    public static function GetSubscriptions(string $user): array {
        if (!self::CheckUserExists($user)) {
            throw new Exception("User $user doesn't exists");
        }
        $exprIncoming = self::GetPdo()->prepare('SELECT count(*) FROM subs WHERE login=:user');
        $exprIncoming->execute(['user' => $user]);
        $exprOutcoming = self::GetPdo()->prepare('SELECT count(*) FROM subs WHERE subLogin=:user');
        $exprOutcoming->execute(['user' => $user]);
        return [intval($exprIncoming->fetchColumn()), intval($exprOutcoming->fetchColumn())];
    }

    public static function Subscribe(string $from,  string $to, bool $deny): int {
        if (!self::CheckUserExists($from) || !self::CheckUserExists($to)) {
            throw new Exception("User doesn't exists");
        }
        $checkSubs = self::GetPdo()->prepare('SELECT count(*) FROM subs WHERE login=:to and subLogin=:from');
        $checkSubs->execute(['to' => $to, 'from' => $from]);
        if ($deny && $checkSubs->fetchColumn() === 1) {
            $expr = self::GetPdo()->prepare('DELETE FROM subs WHERE login=:to and subLogin=:from');
            $expr->execute(['to' => $to, 'from' => $from]);
            return $expr->rowCount();
        }
        elseif (!$deny && $checkSubs->fetchColumn() === 0) {
            $expr = self::GetPdo()->prepare('INSERT INTO subs VALUES (:to, :from)');
            $expr->execute(['to' => $to, 'from' => $from]);
            return $expr->rowCount();
        }
    }

    public static function AddPhoto(string $login, string $path, string $description): int {
        if (!self::CheckUserExists($login)) {
            throw new Exception("User doesn't exists");
        }
        $timestr = date('Y.m.d h:i:s');
        $expr = self::GetPdo()->prepare('INSERT INTO photo VALUES (default, :path, :owner, :descr, :addedtime)');
        $expr->execute(['path' => $path, 'owner' => $login, 'descr' => $description, 'addedtime' => $timestr]);
        return $expr->rowCount();
    }

    public static function CheckSubscription(string $from, string $to): int {
        if (!self::CheckUserExists($from) || !self::CheckUserExists($to)) {
            throw new Exception("User doesn't exists");
        }
        $expr = self::GetPdo()->prepare('SELECT count(*) FROM subs WHERE login=:to AND subLogin=:from');
        $expr->execute(['to' => $to, 'from' => $from]);
        return $expr->fetchColumn();
    }

    public static function GetPhotoFullData(string $phId): array {
        $response = [];
        $expr = self::GetPdo()->prepare('SELECT phId, path, ownerLogin, description, addTime FROM photo WHERE phId=:phId');
        $expr->execute(['phId' => $phId]);
        $res = $expr->fetch(PDO::FETCH_ASSOC);
        if (!$res) {
            throw new Exception('Photo doesnt exists');
        }
        else {
            array_merge($response, $res);
        }
        $marksExpr = self::GetPdo()->prepare('SELECT AVG(value) as avgmarks from mark WHERE phId=:phId');
        $marksExpr->execute(['phId' => $phId]);
        array_merge($response, $marksExpr->fetchColumn());
        $commentsExpr = self::GetPdo()->prepare('SELECT userId, text, date FROM comment where phId=:phId');
        $commentsExpr->execute(['phId' => $phId]);
        array_merge($response, $marksExpr->fetch(PDO::FETCH_ASSOC));
        $ownerExpr = self::GetPdo()->prepare('SELECT name, surname, nick FROM usr WHERE login=:ownerLogin');
        $ownerExpr->execute(['ownerLogin' => $res['ownerLogin']]);
        $res = $ownerExpr->fetch();
        if ($res) {
            array_merge($response, $res);
        }
        return $response;
    }
}
