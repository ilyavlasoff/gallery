<?php

namespace App\lib\websites;

use App\lib\entities\User;
use App\templates\TemplateBuilder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class Finder
{
    const COUNTTOFIND = 20;

    public static function render(): Response
    {
        $session = new Session();
        $session->start();
        $req = Request::createFromGlobals();


        if (!$session->has('auth')) {
            $template = new TemplateBuilder('placeholder.html', ['error' => 'Resource is unavailable without registration']);
            return new Response(strval($template));
        }

        $owner = $session->get('auth');
        $criteria = htmlspecialchars(strip_tags(trim($req->get('criteria'))));

        $param = [
            'header' => new TemplateBuilder('header.html', [
                'logged' => true,
                'login' => $owner->login
            ]),
            'login' => $owner->login,
            'profilePic' => $owner->profilePicPath,
            'name' => $owner->name,
            'surname' => $owner->surname,
            'userpage' => '/profile/' . $owner->login,
            'criteria' => $criteria
        ];

        if ($req->request->has('submit')) {
            try {
                $users = User::findUsers($criteria, self::COUNTTOFIND);
                if (count($users)) {
                    foreach ($users as $user) {
                        list($inSubs, $outSubs) = $user->subscribeQuanInfo();
                        $isSub = $owner->checkSubscription($user);
                        $itemTemplate = new TemplateBuilder('foundItem.html', [
                            'name' => $user->name,
                            'surname' => $user->surname,
                            'profilePic' => '/image/' . $user->login . '/' . $user->profilePicPath . '/sm',
                            'profileLink' => '/profile/' . $user->login,
                            'login' => $user->login,
                            'postsCount' => $user->getPostsCount(),
                            'inSubsCount' => $inSubs,
                            'outSubsCount' => $outSubs,
                            'display' => $user->login !== $owner->login
                        ]);
                        $param['result'] .= strval($itemTemplate);
                    }
                } else {
                    $param['error'] = 'No matches found';
                }
            } catch (\Exception $ex) {
                $param['error'] = 'Can not load results';
            }
        }
        $template = new TemplateBuilder('finder.html', $param);
        return new Response(strval($template));
    }
}