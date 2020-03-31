<?php

namespace App\lib\webscripts;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Response;

class GetNewsLine
{

    public static function call(): Response
    {
        $session = new Session();
        $session->start();
        $req = Request::createFromGlobals();

        if (!$session->has('auth')) {
            return new Response(json_encode(['error' => 'Authorizaion required']), Response::HTTP_UNAUTHORIZED);
        }

        $quan = $req->get('quan');
        $offset = $req->get('offset');

        try {
            $user = $session->get('auth');
            if ($user->subscribeQuanInfo()[1] === 0) {
                $msg = ['loaded' => 0, 'message' => "<p>No subscriptions yet</p>"];
            } else {
                $posts = $user->getSubscriptionsPosts($quan, $offset);
                $content = "";
                foreach ($posts as $element) {
                    $content .= "<div style=\"background-image: url(/image/".$element->owner . "/" . $element->pathOrig .
                        "/sm)\" class='ph' id='" . $element->id . "'></div>";
                }
                $msg = ['loaded' => count($posts), 'message' => $content];
            }
            return new Response(json_encode($msg), Response::HTTP_OK, ['Content-Type' => 'application/json']);
        } catch (Exception $ex) {
            return new Response(json_encode(['error' => 'Can not get posts' . $ex->getMessage()]), Response::HTTP_INTERNAL_SERVER_ERROR, ['Content-Type' => 'application/json']);
        }
    }
}

