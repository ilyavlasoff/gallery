<?php

namespace App\lib\webscripts;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;

class Logout
{
    public static function call(): RedirectResponse
    {
        $session = new Session();
        $session->start();
        if ($session->has('auth')) {
            $session->remove('auth');
        }
        $session->invalidate();
        $resp = new RedirectResponse('/');
        return $resp;
    }
}