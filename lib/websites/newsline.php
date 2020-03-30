<?php

namespace App\lib\websites;

use App\templates\TemplateBuilder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class Newsline
{
    public static function render(): Response
    {
        $session = new Session();
        $session->start();

        if (!$session->has('auth')) {
            $template = new TemplateBuilder('placeholder.html', ['error' => 'Resource is unavailable without registration']);
            return new Response(strval($template));
        }

        $user = $session->get('auth');
        $template = new TemplateBuilder('newsline.html', [
            'header' => new TemplateBuilder('header.html', [
                'logged' => true,
                'login' => $user->login
            ]),
            'login' => $user->login,
            'profilePic' => $user->profilePicPath,
            'name' => $user->name,
            'surname' => $user->surname,
            'userpage' => '/profile/' . $user->login,
        ]);
        return new Response(strval($template));
    }
}