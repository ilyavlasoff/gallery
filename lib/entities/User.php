<?php

namespace App\lib\entities;

use App\lib\db\DBExecutor;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\lib\entities\Post;


class User {

    private $authorized;
    private $login;
    //private $hPasswd;
    private $name;
    private $surname;
    private $nickname;
    private $bio = "";
    private $profilePicPath = "";

    private function __construct (string $_login, /*string $_passwd, */string $_name, string $_surname,
                                 string $_nick, string $_bio, string $_profilePic) {
        $this->login = $_login;
        //$this->hPasswd = $_passwd;
        $this->name = $_name;
        $this->surname = $_surname;
        $this->nickname = $_nick;
        $this->bio = $_bio;
        $this->profilePicPath = $_profilePic;
    }

    public function __get($name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        }
        else {
            throw new Exception("Field $name is not defined");
        }
    }

    public static function isUserExists(string $login): bool {
        return DBExecutor::CheckUserExists($login);
    }

    public static function createUser(string $_login, string $_passwd, string $_name, string $_surname,
                                      string $_nick, string $_bio = "", string $_profilePic = ""): user {
        if (DBExecutor::CheckUserExists($_login)) {
            throw new Exception("User $_login is already exists");
        }
        $user = new User($_login, $_name, $_surname, $_nick, $_bio, $_profilePic);
        $user->authorized = true;
        if (DBExecutor::RegisterNewUser($_login, $_passwd, $_name, $_surname, $_nick) !== 1) {
            throw new Exception("Cannot register user $_login");
        }
        $uploadRootDir = "../uploads/". $_login;
        if (!is_dir($uploadRootDir))
            mkdir($uploadRootDir);
        if ($_bio) {
            DBExecutor::UpdateBio($_login, $_bio);
        }
        if ($_profilePic) {
            /*
             * ДОБАВИТЬ СОХРАНЕНИЕ ПУТИ К АВАТАРУ
             */
        }
        return $user;
    }

    public static function getUserFromDB(string $_login): user {
        $userdata = DBExecutor::GetUserInfo($_login);
        $user = new User($userdata['login'], $userdata['name'], $userdata['surname'], $userdata['nick'],
            $userdata['bio'] ?? "", $userdata['profilePicPath'] ?? "");
        $user->authorized = false;
        return $user;
    }

    public static function loginUserFromDB(string $_login, string $_passwd): user {
        if (!DBExecutor::CheckUserRegistred($_login, $_passwd)) {
            throw new Exception("User $_login doesn't exists or password incorrect");
        }
        else {
            $userdata = DBExecutor::GetUserInfo($_login);
            $user = new User($userdata['login'], $userdata['name'], $userdata['surname'], $userdata['nick'],
                $userdata['bio'] ?? "", $userdata['profilepicpath'] ?? "");
            $user->authorized = true;
            return $user;
        }
    }

    public function checkCorrectServerParams(): bool {

    }

    public function addPost(UploadedFile $file, string $comment): Post {
        $filename = md5(uniqid(rand(), true)) . '.' . $file->getClientOriginalExtension();
        $filedir = root . '/uploads/' . $this->login;
        return Post::CreateNewPost($file, $filename, $filedir, $comment, $this->login);
    }

    public function checkSubscription(User $other): bool {
        if (!self::isUserExists($this->login) || !self::isUserExists($other->login)) {
            throw new Exception("User doen't exists");
        }
        return DBExecutor::CheckSubscription($this->login, $other->login);
    }

    public function getPostsCount(): int {
        return DBExecutor::GetPostsQuan($this->login);
    }

    public function subscribeQuanInfo(): array {
        return DBExecutor::GetSubscriptions($this->login);
    }

    public function changeNickname($value): bool {
        if (DBExecutor::UpdateNick($this->login, $value) === 1) {
            $this->nickname = $value;
            return true;
        }
    }

    public function changeBio($value): bool {
        if (DBExecutor::UpdateBio($this->login, $value) === 1) {
            $this->bio = $value;
            return true;
        }
    }

    public function subscribeTo(User $other): bool {
        if (!self::checkSubscription($other)) {
            DBExecutor::Subscribe($this->login, $other->login, false);
        }
        return self::checkSubscription($other);
    }

    public function cancelSubscribe(User $other): bool {
        if (self::checkSubscription($other)) {
            DBExecutor::Subscribe($this->login, $other->login, true);
        }
        return self::checkSubscription($other);
    }

    public function getPosts(int $quan, int $offset): array
    {
        $data = DBExecutor::GetPosts($this->login, $quan, $offset);
        $posts = [];
        foreach ($data as $post) {
            $posts[] = new \App\lib\entities\Post($post['phid'], $post['path'], $post['description'], $this->login, $post['addtime']);
        }
        return $posts;
    }
}