<?php

namespace App\lib\controller;

use App\lib\websites\startpage;
use App\lib\websites\login;
use App\lib\websites\register;
use App\lib\websites\profile;
use App\lib\websites\settings;
use App\lib\websites\addPost;
use App\lib\webscripts\logout;
use App\lib\webscripts\imageloader;
use App\lib\webscripts\subscribe;
use App\lib\webscripts\getphotos;

use Symfony\Component\HttpFoundation\Response;

class Controller {

    public static function startpageCall(): Response {
        return startpage::render();
    }

    public static function loginCall(): Response {
        return login::render();
    }

    public static function registerCall(): Response {
        return register::render();
    }

    public static function profileCall($id): Response {
        return profile::render($id);
    }

    public static function editCall(): Response {
        return settings::render();
    }

    public static function newpostCall(): Response {
        return addPost::render();
    }

    public static function logoutCall(): Response {
        return logout::call();
    }

    public static function loadimageCall($param): Response {
        return imageloader::getImage($param['username'], $param['filename'], $param['size']);
    }

    public static function subscribeCall(): Response {
        return subscribe::call();
    }

    public static function getphotosCall(): Response {
        return getphotos::call();
    }
}