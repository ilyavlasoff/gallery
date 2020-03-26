<?php

namespace App\lib\controller;

use App\lib\websites\startpage;
use App\lib\websites\login;

use Symfony\Component\HttpFoundation\Response;

class Controller {

    public static function startpageCall(): Response {
        return startpage::render();
    }

    public static function loginCall(): Response {
        return login::render();
    }


}