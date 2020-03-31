<?php

namespace App\lib\websites;

use App\lib\entities;
use App\lib\validators\LoginValidator;
use App\lib\validators\PostValidator;
use App\templates\TemplateBuilder;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AddPost
{

    public static function render(): Response
    {
        $session = new Session();
        $session->start();
        $req = Request::createFromGlobals();

        if (!$session->has('auth')) {
            $template = new TemplateBuilder('placeholder.html', ['error' => 'Resource is unavailable without registration']);
            return new Response(strval($template));
        }

        $user = $session->get('auth');
        $params = [
            'userpage' => "./profile/" . $user->login,
            'name' => $user->name,
            'surname' => $user->surname,
            'userlogin' => $user->login,
            'profilePic' => $user->profilePicPath,
        ];

        if ($req->request->has('submit')) {
            $photo = $req->files->get('fileinp');
            $comment = htmlspecialchars(strip_tags(trim($req->get('photoDescription'))));
            $validator = new PostValidator();
            $errors = $validator->validate([
                'comment' => $comment,
                'photo' => $photo
            ]);
            if (!empty($errors)) {
                $params['error'] = implode('<br>', $errors);
            } else {
                try {
                    $user->addPost($photo, $comment);
                    return new RedirectResponse('/profile/' . $user->login);
                } catch (\Exception $ex) {
                    $params['error'] = 'Error' . $ex->getMessage();
                }
            }
        }
        $params['header'] = new TemplateBuilder('header.html', [
            'logged' => true,
            'login' => $user->login
        ]);
        $template = new TemplateBuilder('addpost.html', $params);
        return new Response(strval($template));
    }
}

