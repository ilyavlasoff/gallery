<?php

namespace App\lib\entities;

use App\lib\db\DBExecutor;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\lib\entities\Post;


class User
{
    private $authorized;
    private $login;
    //private $hPasswd;
    private $name;
    private $surname;
    private $nickname;
    private $bio = "";
    private $profilePicPath = "";

    private function __construct(string $_login, string $_name, string $_surname, string $_nick, string $_bio, string $_profilePic)
    {
        $this->login = $_login;
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
        } else {
            throw new Exception("Field $name is not defined");
        }
    }

    public static function isUserExists(string $login): bool
    {
        return DBExecutor::CheckUserExists($login);
    }

    public static function createUser(
        string $_login,
        string $_passwd,
        string $_name,
        string $_surname,
        string $_nick,
        string $_profilePic = "",
        string $_bio = ""
    ): user {
        if (DBExecutor::CheckUserExists($_login)) {
            throw new Exception("User $_login is already exists");
        }
        if (DBExecutor::RegisterNewUser($_login, $_passwd, $_name, $_surname, $_nick) !== 1) {
            throw new Exception("Cannot register user $_login");
        }
        $uploadRootDir = root . "/uploads/". $_login;
        if (!is_dir($uploadRootDir))
            mkdir($uploadRootDir);
        if ($_bio) {
            DBExecutor::UpdateBio($_login, $_bio);
        }
        $userDir = root . '/uploads/' . $_login;
        $realpath = $userDir . '/' . $_profilePic;
        if (!$exist = is_file($realpath) || !is_readable($realpath)) {
            $defaultPath = root . '/public/media/userplaceholder.png';
            $_profilePic =  md5(uniqid(rand(), true)) . '.png';
            $exist = copy($defaultPath, $userDir . '/' .$_profilePic);
        }
        if ($exist) {
            DBExecutor::UpdateUserpic($_login, $_profilePic);
        }
        $user = new User($_login, $_name, $_surname, $_nick, $_bio, $_profilePic);
        $user->authorized = true;
        return $user;
    }

    public static function getUserFromDB(string $_login): user
    {
        $userdata = DBExecutor::GetUserInfo($_login);
        $user = new User(
            $userdata['login'],
            $userdata['name'],
            $userdata['surname'],
            $userdata['nick'],
            $userdata['bio'] ?? "",
            $userdata['profilepicpath'] ?? ""
        );
        $user->authorized = false;
        return $user;
    }

    public static function loginUserFromDB(string $_login, string $_passwd): user
    {
        if (!DBExecutor::CheckUserRegistred($_login, $_passwd)) {
            throw new Exception("User $_login doesn't exists or password incorrect");
        } else {
            $userdata = DBExecutor::GetUserInfo($_login);
            $user = new User(
                $userdata['login'],
                $userdata['name'],
                $userdata['surname'],
                $userdata['nick'],
                $userdata['bio'] ?? "",
                $userdata['profilepicpath'] ?? ""
            );
            $user->authorized = true;
            return $user;
        }
    }

    public static function findUsers(string $pattern, int $count): array
    {
        $data = DBExecutor::findUsers($pattern, $count);
        $users = [];
        foreach ($data as $user) {
            $users[] = new User($user['login'], $user['name'], $user['surname'], $user['nick'], strval($user['bio']), strval($user['profilepicpath']));
        }
        return $users;
    }

    public function addPost(UploadedFile $file, string $comment): Post
    {
        $filename = md5(uniqid(rand(), true)) . '.' . $file->getClientOriginalExtension();
        $filedir = root . '/uploads/' . $this->login;
        return Post::CreateNewPost($file, $filename, $filedir, $comment, $this->login);
    }

    public function updatePassword(string $old, string $passwd): bool
    {
        if (!DBExecutor::CheckUserRegistred($this->login, $old)) {
            throw new Exception('Old password is not valid');
        }
        return DBExecutor::ChangePassword($this->login, $passwd);
    }

    public function checkSubscription(User $other): bool
    {
        if (!self::isUserExists($this->login) || !self::isUserExists($other->login)) {
            throw new Exception("User doen't exists");
        }
        return DBExecutor::CheckSubscription($this->login, $other->login);
    }

    public function getPostsCount(): int
    {
        return DBExecutor::GetPostsQuan($this->login);
    }

    public function subscribeQuanInfo(): array
    {
        return DBExecutor::GetSubscriptions($this->login);
    }

    public function changeNickname($value): bool
    {
        if (DBExecutor::UpdateNick($this->login, $value) === 1) {
            $this->nickname = $value;
            return true;
        }
    }

    public function changeBio($value): bool
    {
        if (DBExecutor::UpdateBio($this->login, $value) === 1) {
            $this->bio = $value;
            return true;
        }
    }

    public function updateProfilePic(UploadedFile $file): bool
    {
        $filename = md5(uniqid(rand(), true)) . '.' . $file->getClientOriginalExtension();
        $filedir = root . '/uploads/' . $this->login;
        $errno = $file->getError();
        if ($errno !== UPLOAD_ERR_OK) {
            throw new Exception("Error: ". $file->getErrorMessage());
        }
        if (!is_dir($filedir)) {
            throw new Exception("Error. User's storage not exists");
        }
        $file->move($filedir, $filename);
        $res = DBExecutor::UpdateUserpic($this->login, $filename);
        if ($res) {
            $this->profilePicPath = $filename;
        }
        return $res;
    }

    public function subscribeTo(User $other): bool
    {
        if (!self::checkSubscription($other)) {
            DBExecutor::Subscribe($this->login, $other->login, false);
        }
        return self::checkSubscription($other);
    }

    public function cancelSubscribe(User $other): bool
    {
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

    public function getSubscriptionsPosts(int $quan, int $offset)
    {
        $data = DBExecutor::GetPostsofSubscriptions($this->login, $quan, $offset);
        $posts = [];
        foreach ($data as $post) {
            $posts[] = new \App\lib\entities\Post($post['phid'], $post['path'], $post['description'], $post['ownerlogin'], $post['addtime']);
        }
        return $posts;
    }
}
