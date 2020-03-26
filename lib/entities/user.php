<?php

namespace App\lib\entities;

use App\lib\db\DBExecutor;

class user {

    private $authorized;
    public $login;
    private $hPasswd;
    private $name;
    private $surname;
    private $nickname;
    private $bio = null;
    private $profilePicPath = null;

    public function __construct (string $_login, string $_passwd, string $_name, string $_surname,
        string $_nick = "", string $_bio = "") {

    }

    public static function isUserExists(string $login): bool {
        return DBExecutor::CheckUserExists($login);
    }

    public static function getUserFromDB(string $login): user {

    }

    public function checkCorrectServerParams(): bool {

    }


}