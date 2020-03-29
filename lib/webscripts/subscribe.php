<?php

namespace App\lib\webscripts;

use App\lib\entities\User;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class Subscribe {

    public static function call(): Response {
        $session = new Session();
        $session->start();
        $req = Request::createFromGlobals();

        if (!$session->has('auth')) {
            return new Response(json_encode(['error' =>"Authorization required"]), Response::HTTP_UNAUTHORIZED);
        }

        $subTo = $req->get('to');
        $operation = $req->get('oper');
        $own = $session->get('auth');

        try {
            $person = User::getUserFromDB($subTo);
            if($operation === 'check') {
                $status = $own->checkSubscription($person);
            }
            elseif ($operation === 'add') {
                $status = $own->subscribeTo($person);
            }
            elseif ($operation === 'cancel') {
                $status = $own->cancelSubscribe($person);
            }
            else {
                return new Response(json_encode(['error' => 'Wrong operation code']), Response::HTTP_BAD_REQUEST,
                    ['Content-Type' => 'application/json']);
            }
            if ($status) {
                $data = json_encode(['subscr' => 1]);
            }
            else {
                $data = json_encode(['subscr' => 0]);
            }
            return new Response($data, Response::HTTP_OK, ['Content-Type' => 'application/json']);
        }
        catch (Exception $ex) {
            return new Response(json_encode(['error' => $ex->getMessage()]), Response::HTTP_INTERNAL_SERVER_ERROR,
                ['Content-Type' => 'application/json']);
        }
    }
}