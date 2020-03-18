<?php
session_start();
require_once ('TemplateMaker.php');
Template::AddPathValue('reg', 'static/register.html');

if (isset($_POST['submit'])) {

    require_once ('../internal/verifier.php');
    $username = filter($_POST['username'], 'str');
    $login = filter($_POST['login'], 'email');
    $passwd = filter($_POST['passwd'], 'passwd');
    $passwdRep = filter($_POST['passwdRep'], 'passwd');
    $rulesAgree = $_POST['rulesAgree'];


    $correct = (verify($username, 'name') && verify($login, 'email') && verify($passwd, 'password')
    && ($passwd === $passwdRep) && $rulesAgree);

    if (!$correct) {
        $message = '';
        if (!$rulesAgree)
            $message .= 'You must accept our rules to use service';
        elseif ($passwd !== $passwdRep)
            $message .= 'Password and the duplicate password must match';
        elseif (!verify($username, 'name')) {
            $message .= 'You should add real name and surname';
        }
        $errRegpage = new Template('reg', ['BOTTOMLABEL' => "$message"]);
        echo strval($errRegpage);
        exit();
    }

    require_once ('../db/DBExecutor.php');

    list($name, $surname) = explode(' ', $username);

    try {
        if (DBExecutor::RegisterNewUser($login, $passwd, $name, $surname) > 0) {
            $_SESSION['logged'] = true;
            $_SESSION['username'] = $login;
            header('Location: profile.php');
        }
    }
    catch (Exception $ex) {
        $errRegpage = new Template('reg', ['BOTTOMLABEL' => "Cannot register user $username"]);
        echo strval($errRegpage);
        die();
    }
}
else {
    $regPage = new Template('reg');
    echo strval($regPage);
}
