<?php
    session_start();
    require_once ('TemplateMaker.php');
    Template::AddPathValue('login', 'static/login.html');

    if (isset($_POST['submit'])) {
        require_once ('../internal/verifier.php');
        $login = filter($_POST['login'], 'email');
        $passwd = filter($_POST['passwd'], 'passwd');

        if ($verLogin = verify($login, 'email') && $verPasswd = verify($passwd, 'password')) {

            require_once ('../db/DBExecutor.php');
            if (DBExecutor::CheckUserRegistred($login, $passwd)) {
                $_SESSION['logged'] = true;
                $_SESSION['username'] = $login;
                $_SESSION['usrAgent'] = $_SERVER['HTTP_USER_AGENT'];
                $_SESSION['remAddr'] = $_SERVER['REMOTE_ADDR'];
                $_SESSION['forwardedFor'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
                header("Location: profile.php?id=$login");
            }
            else {
                $errLoginpage = new Template('login', ['LOGIN'=> $login, 'BOTTOMLABEL' => "Account $login does'nt exists or password incorrect"]);
                echo strval($errLoginpage);
            }
        }
        else {
            $errLoginpage = new Template('login', ['LOGIN'=> $login, 'PASSWORD' => $passwd, 'BOTTOMLABEL' => 'Your data in incorrect']);
            echo strval($errLoginpage);
        }

    }
    else {
        $errLoginpage = new Template('login');
        echo strval($errLoginpage);
    }

