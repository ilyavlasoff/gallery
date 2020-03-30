<?php

namespace App\lib\websites;

use App\lib\db\DBExecutor;
use App\lib\entities;
use App\lib\validators\AuthorizeValudator;
use App\templates\TemplateBuilder;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;


class Profile {

    public static function render($param): Response {

        $session = new Session();
        $session->start();

        // если вход не выполнен, переадресация на главную страницу
        if (!$session->has('auth')) {
            $template = new TemplateBuilder('placeholder.html', [
                'header' => new TemplateBuilder('header.html', ['logged' => false]),
                'error' => 'Resource is unavailable without registration'
            ]);
            return new Response(strval($template));
        }
        $reqId = $param['id'];
        //проверка существования пользователя
        $owner = $session->get('auth');
        if (!entities\User::isUserExists($reqId)) {
            $template = new TemplateBuilder('placeholder.html', [
                'header' => new TemplateBuilder('header.html', ['logged' => true, 'login' => $owner->login]),
                'error' => "Page $reqId not exists"
            ]);
            return new Response(strval($template));
        }

        try {
            if ($session->get('auth')->login === $reqId) {
                $user = $session->get('auth');
                $own = true;
                $subscripted = false;
            }
            else {
                $user = entities\User::getUserFromDB($reqId);
                $own = false;
                $subscripted = $session->get('auth')->checkSubscription($user);
            }
            $postsCount = $user->getPostsCount();
            list($incomingSubs, $outcomingSubs) = $user->subscribeQuanInfo();
        }
        catch (\Exception $ex) {
            return new Response("Can not load page:" . $ex->getMessage());
        }

        $page = new TemplateBuilder('userpage.html', [
            'header' => new TemplateBuilder('header.html', [
                'logged' => true,
                'login' => $owner->login
            ]),
            'own' => $own,
            'subscripted' => $subscripted,
            'nick' => $user->nickname,
            'pageId' => $user->login,
            'profilePic' => $user->profilePicPath,
            'posts' => $postsCount,
            'name' => $user->name,
            'surname' => $user->surname,
            'bio' => $user->bio,
            'session' => $session->getId(),
            'subscribers' => $incomingSubs,
            'subscriptions' => $outcomingSubs]);
        return new Response(strval($page));
    }
}