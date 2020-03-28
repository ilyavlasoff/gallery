<?php

namespace App\lib\websites;

use App\lib\entities;
use App\templates\TemplateBuilder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;


class startpage
{
    public static function render(): Response {
        $session = new Session();
        $session->start();

        if ($user = $session->get('auth'))
        {
            if ($user instanceof entities\user && $user->checkCorrectServerParams())
            {
                $login = $user->login;
                $resp = new RedirectResponse("/profile/$login");
                return $resp;
            }

            else {
                $resp = new RedirectResponse("/login");
                return $resp;
            }
        }
        else
        {
            $template = new TemplateBuilder('startpage.html', [
                'header' => new TemplateBuilder('header.html', ['logged' => false])
            ]);
            return new Response($template);
        }
    }
}

