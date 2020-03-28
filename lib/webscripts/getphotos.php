<?php

namespace App\lib\webscripts;

use http\Client\Curl\User;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Response;

class GetPhotos {

    public static function call(): Response {
        $session = new Session();
        $session->start();
        $req = Request::createFromGlobals();

        if (!$session->has('auth')) {
            return new Response(json_encode(['error' => 'Authorizaion required']), Response::HTTP_UNAUTHORIZED);
        }

        $page = $req->get('pageId');
        $quan = $req->get('quan');
        $offset = $req->get('offset');

        try {
            $user = \App\lib\entities\User::getUserFromDB($page);
            if($user->getPostsCount() === 0) {
                $msg = ['loaded' => 0, 'message' => "<p>Profile is empty</p>"];
            }
            else {
                $posts = $user->getPosts($quan, $offset);
                $content = "";
                foreach ($posts as $element) {
                    $content .= "<div style=\"background-image: url(/image/".$page . "/" . $element->pathOrig . "/300*)\" class='ph'></div>";
                }
                $msg = ['loaded' => count($posts), 'message' => $content];
            }
            return new Response(json_encode($msg), Response::HTTP_OK,
                ['Content-Type' => 'application/json']);
        }
        catch (Exception $ex) {
            return new Response(json_encode(['error' => 'Can not get posts' . $ex->getMessage()]), Response::HTTP_INTERNAL_SERVER_ERROR,
                ['Content-Type' => 'application/json']);
        }
    }
}

