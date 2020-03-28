<?php

namespace App\lib\websites;

use App\lib\entities;
use App\lib\validators\AuthorizeValudator;
use App\templates\TemplateBuilder;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;

class register {

    public static function render(): Response
    {

        $session = new Session();
        $session->start();
        $req = Request::createFromGlobals();

        if ($session->has('auth')) {
            $login = $session->get('auth')->login;
            $resp = new RedirectResponse("/profile/$login");
            return $resp;
        }

        if ($req->request->has('submit')) {

            $username = $req->request->get('username');
            $login = $req->request->get('login');
            $passwd = $req->request->get('passwd');
            $passwdRep = $req->request->get('passwdRep');
            $nick = $req->request->get('nick');
            $rulesAgree = $req->request->get('rulesAgree');

            $savedParams = [
                'header' => new TemplateBuilder('header.html', ['logged' => false]),
                'username' => $username,
                'login' => $login,
                'nick' => $nick,
                'password' => $passwd,
                'passwrep' => $passwdRep
            ];

            $validator = new AuthorizeValudator();
            $errors = $validator->validate($req->request->all());
            if (!empty($errors)) {
                $msg = implode('<br>', $errors);
                $savedParams['bottomLabel'] = $msg;
                $page = new TemplateBuilder('register.html', $savedParams);
                return new Response(strval($page));
            }
            list($name, $surname) = explode(' ', $username);

            try {
                $user = entities\User::createUser($login, $passwd, $name, $surname, $nick);
                $session->set('auth', $user);
                $resp = new RedirectResponse("/edit");
                return $resp;
            } catch (Exception $ex) {
                $savedParams['bottomLabel'] = $ex->getMessage();
                $errRegpage = new TemplateBuilder('register.html', $savedParams);
                return new Response(strval($errRegpage));
            }
        }
        else {
            $page = new TemplateBuilder('register.html', [
                'header' => new TemplateBuilder('header.html', ['logged' => false])
            ]);
            return new Response(strval($page));
        }
    }

}

/*
$_SESSION['logged'] = true;
$_SESSION['username'] = $login;
$_SESSION['usrAgent'] = $_SERVER['HTTP_USER_AGENT'];
$_SESSION['remAddr'] = $_SERVER['REMOTE_ADDR'];
$_SESSION['forwardedFor'] = $_SERVER['HTTP_X_FORWARDED_FOR'];*/