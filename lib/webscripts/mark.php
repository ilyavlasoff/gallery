<?php

namespace App\lib\webscripts;

use App\lib\entities\Post;
use App\lib\entities\User;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class Mark
{
    public static function call(): Response
    {
        $session = new Session();
        $session->start();
        $req = Request::createFromGlobals();

        if (!$session->has('auth')) {
            return new Response(json_encode(['error' =>"Authorization required"]), Response::HTTP_UNAUTHORIZED);
        }

        $picId = $req->get('id');
        $value = $req->get('value');

        try {
            $post = Post::getPostFromDb($picId);
            $post->setMarkByUser($session->get('auth'), $value);
            return new Response(json_encode(['message' => $value]), Response::HTTP_OK, ['Content-Type' => 'application/json']);
        } catch (\Exception $ex) {
            return new Response(json_encode(['error' => 'Can not set mark']), Response::HTTP_BAD_REQUEST, ['Content-Type' => 'application/json']);
        }
    }
}