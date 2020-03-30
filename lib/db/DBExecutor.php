<?php

namespace App\lib\db;

use App\lib\db\DBConnector;
use PDO;
use Symfony\Component\Config\Definition\Exception\Exception;

class DBExecutor {
    private static function getConn(): DBConnector
    {
        static $connection;
        if (is_null($connection)) {
            $connection = new DBConnector('dbconf.yaml');
        }
        return $connection;
    }

    public static function CheckUserRegistred(string $user, string $passwd): bool
    {
        $resp = self::getConn()->row('SELECT hpasswd FROM usr WHERE login = :login', ['login' => $user]);
        return  password_verify($passwd, $resp['hpasswd']);
    }

    public static function FindUsers(string $pattern, $count) {
        return self::getConn()->multirows('SELECT login, name, surname, bio, profilepicpath, nick FROM usr 
            WHERE login LIKE :pattern OR nick LIKE :pattern LIMIT :count', ['pattern' => "%$pattern%", 'count' => $count]);
    }

    public static function CheckUserExists(string $username): bool {
        return self::getConn()->scalar('SELECT count(*) FROM usr WHERE login=:login', ['login' => $username]);
    }

    public static function RegisterNewUser(string $login, string $passwd, string $name, string $surname, string $nick): int
    {
        if (self::CheckUserRegistred($login, $passwd) > 0)
        {
            throw new Exception("User $login is already exists");
        }
        $passwd_hash = password_hash($passwd,  PASSWORD_BCRYPT);
        return self::getConn()->nonQuery('INSERT INTO usr (login, hpasswd, name, surname, nick) VALUES (:login, :hpasswd, :name, :surname, :nick)',
            ['login' => $login, 'hpasswd' => $passwd_hash, 'name' => $name, 'surname' => $surname, 'nick' => $nick]);
    }

    public static function ChangePassword(string $login, string $newPasswd): bool
    {
        $newPasswdHash = password_hash($newPasswd,  PASSWORD_BCRYPT);
        return self::getConn()->nonQuery('UPDATE usr SET hpasswd=:newPasswd WHERE login=:login', ['newPasswd' => $newPasswdHash, 'login' => $login]);
    }

    public static function UpdateUserpic(string $user, string $path): bool {
        return self::getConn()->nonQuery('UPDATE usr SET profilePicPath=:path WHERE login=:login', ['path' => $path, 'login' => $user]);
    }

    public static function UpdateNick(string $user, string $nick) : int {
        if (!self::CheckUserExists($user)) {
            throw new Exception("User $user doesn't exists");
        }
        return self::getConn()->nonQuery('UPDATE usr SET nick=:nick WHERE login=:login', ['nick' => $nick, 'login' => $user]);
    }

    public static function UpdateBio(string $user, string $bio) : int{
        if (!self::CheckUserExists($user)) {
            throw new Exception("User $user doesn't exists");
        }
        return self::getConn()->nonQuery('UPDATE usr SET bio=:bio WHERE login=:login', ['bio' => $bio, 'login' => $user]);
    }

    public static function GetUserInfo(string $login): array {
        return self::getConn()->row('SELECT login, name, surname, bio, profilepicpath, nick FROM usr WHERE login=:login', ['login' => $login]);
    }

    public static function GetPosts(string $user, int $quan, int $offset): array
    {
        if (!self::CheckUserExists($user)) {
            throw new Exception("User $user doesn't exists");
        }
        return self::getConn()->multirows('SELECT phid, path, description, addtime FROM photo WHERE ownerLogin=:owner ORDER BY addtime DESC LIMIT :quan OFFSET :offset',
            ['owner' => $user, 'quan' => $quan, 'offset' => $offset]);
    }

    public static function GetPostsQuan(string $user) : int
    {
        if (!self::CheckUserExists($user)) {
            throw new Exception("User $user doesn't exists");
        }
        return self::getConn()->scalar('SELECT count(*) FROM photo WHERE ownerLogin=:login', ['login' => $user]);
    }

    public static function GetSubscriptions(string $user): array {
        if (!self::CheckUserExists($user)) {
            throw new Exception("User $user doesn't exists");
        }
        $incoming = self::getConn()->scalar('SELECT count(*) FROM subs WHERE login=:user', ['user' => $user]);
        $outcoming = self::getConn()->scalar('SELECT count(*) FROM subs WHERE subLogin=:user', ['user' => $user]);
        return [intval($incoming), intval($outcoming)];
    }

    public static function Subscribe(string $from,  string $to, bool $deny): int {
        if (!self::CheckUserExists($from) || !self::CheckUserExists($to)) {
            throw new Exception("User doesn't exists");
        }
        $issub = self::getConn()->scalar('SELECT count(*) FROM subs WHERE login=:to and subLogin=:from', ['to' => $to, 'from' => $from]);
        if ($deny && $issub === 1) {
            return self::getConn()->nonQuery('DELETE FROM subs WHERE login=:to and subLogin=:from', ['to' => $to, 'from' => $from]);
        }
        elseif (!$deny && $issub === 0) {
            return self::getConn()->nonQuery('INSERT INTO subs VALUES (:to, :from)', ['to' => $to, 'from' => $from]);
        }
    }

    public static function AddPhoto(string $login, string $path, string $description, \DateTime $dt): int {
        if (!self::CheckUserExists($login)) {
            throw new Exception("User doesn't exists");
        }
        $timestr = $dt->format('Y.m.d h:i:s');
        return self::getConn()->nonQuery('INSERT INTO photo VALUES (default, :path, :owner, :descr, :addedtime)',
            ['path' => $path, 'owner' => $login, 'descr' => $description, 'addedtime' => $timestr]);
    }

    public static function CheckSubscription(string $from, string $to): int {
        if (!self::CheckUserExists($from) || !self::CheckUserExists($to)) {
            throw new Exception("User doesn't exists");
        }
        return self::getConn()->scalar('SELECT count(*) FROM subs WHERE login=:to AND subLogin=:from', ['to' => $to, 'from' => $from]);
    }

    public static function CheckPostExsists(string $phId): bool {
        return self::getConn()->scalar('SELECT COUNT(*)FROM photo WHERE phId=:phId', ['phId' => $phId]);
    }

    public static function GetPhotoDataById(string $phId): array {
        return self::getConn()->row('SELECT path, ownerLogin, description, addTime FROM photo WHERE phId=:phId', ['phId' => $phId]);
    }

    public static function GetMarksStat(string $phId): array {
        return self::getConn()->row('SELECT AVG(value) as avgmarks, COUNT(value) as countmarks from mark WHERE phId=:phId',
            ['phId' => $phId]);
    }

    public static  function GetMarksByUser(string $phId, string $login): int {
        return self::getConn()->scalar('SELECT value FROM mark WHERE userId=:user AND phId=:photo',
            ['user' => $login, 'photo' => intval($phId)]);
    }

    public static function SetMark(string $postId, string $userId, int $value): int {
        return self::getConn()->nonQuery('INSERT INTO mark VALUES (:userId, :phId, :value)',
            ['userId' => $userId, 'phId' => $postId, 'value' => $value]);
    }

    public static function ChangeMark(string $postId, string $userId, int $value): int {
        return self::getConn()->nonQuery('UPDATE mark SET value=:value WHERE userId=:userId and phId=:phId', ['userId' => $userId, 'phId' => $postId, 'value' => $value]);
    }

    public static function GetPostsofSubscriptions(string $userId, int $count, $offset): array {
        return self::getConn()->multirows('SELECT phid, path, description, addtime, ownerlogin FROM photo WHERE ownerlogin IN (
            SELECT login FROM subs WHERE sublogin = :userId) ORDER BY addtime DESC LIMIT :quan OFFSET :offset',
            ['userId' => $userId, 'quan' => $count, 'offset' => $offset]);
    }
}
