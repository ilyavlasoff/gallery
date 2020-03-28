<?php

namespace App\lib\webscripts;

use App\lib\entities\Post;
use App\lib\entities\User;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Response;

class GetFullPost
{

    public static function call(): Response
    {
        $session = new Session();
        $session->start();
        $req = Request::createFromGlobals();
        $phId = $req->get('id');

        if (!$session->has('auth')) {
            return new Response(json_encode(['error' => 'Authorizaion required']), Response::HTTP_UNAUTHORIZED);
        }

        try {
            $post = Post::getPostFromDb($phId);
            $owner = $post->owner;
            if (!User::isUserExists($owner)) {
                throw new Exception("User $owner doesn't exists");
            }
            $yourMark = $session->get('auth')->getMarkOnPost($post);
            $user = User::getUserFromDB($owner);
            $stat = $post->getPostStat();
            $body = [
                'path' => '/image/' . $user->login . '/' . $post->pathOrig . '/orig',
                'profilePicPath' => $user->profilePicPath,
                'ownerLink' => '/profile/' . $user->login,
                'ownerName' => $user->name . ' ' . $user->surname,
                'date' => $post->time->format('d-m-Y H:i'),
                'marksCount' => $stat['countmarks'],
                'marksAvg' => $stat['avgmarks'],
                'yourMark' => $yourMark
            ];
        }
        catch (Exception $ex) {
            $body = ['message' => "Error occured: " . $ex->getMessage()];
        }

        return new Response(json_encode($body), Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }
}