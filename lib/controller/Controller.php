<?php

namespace App\lib\controller;

use App\lib\websites\startpage;
use App\lib\websites\login;
use App\lib\websites\register;
use App\lib\websites\profile;
use App\lib\websites\settings;
use App\lib\websites\addPost;
use App\lib\websites\newsline;
use App\lib\websites\finder;
use App\lib\webscripts\logout;
use App\lib\webscripts\imageloader;
use App\lib\webscripts\subscribe;
use App\lib\webscripts\getphotos;
use App\lib\webscripts\getFullPost;
use App\lib\webscripts\mark;
use App\lib\webscripts\getNewsLine;

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
    public static function getFullPostCall(): Response {
        return getFullPost::call();
    }
    public static function markCall(): Response {
        return mark::call();
    }
    public static function newslineCall(): Response {
        return newsline::render();
    }

    public static function getNewslineCall(): Response {
        return getNewsLine::call();
    }

    public static function findCall(): Response {
        return finder::render();
    }
}