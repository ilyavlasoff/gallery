<?php

namespace App\lib\websites;

use App\lib\entities;
use App\lib\validators\PasswordValidator;
use App\lib\validators\PostValidator;
use App\templates\TemplateBuilder;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class Settings {

    public static function render(): Response {
        $session = new Session();
        $session->start();
        $req = Request::createFromGlobals();

        if (!$session->has('auth')) {
            $template = new TemplateBuilder('placeholder.html', ['error' => 'Resource is unavailable without registration']);
            return new Response(strval($template));
        }

        $user = $session->get('auth');
        $args = [];

        if ($req->request->has('changePicture')) {
            $file = $req->files->get('photo');
            $validator = new PostValidator();
            $errors = $validator->validate(['photo' => $file, 'comment' => '']);
            if (empty($errors)) {
                try {
                    $user->updateProfilePic($file);
                    $args['info'] = "Photo updated";
                }
                catch (Exception $ex) {
                    $args['err'] = "Error occured: " . $ex->getMessage();
                }
            }
            else {
                $args['err'] = implode('<br>', $errors);
            }
        }
        elseif ($req->request->has('changePasswd')) {
            $old = htmlspecialchars(strip_tags(trim($req->request->get('oldPasswd'))));
            $new = htmlspecialchars(strip_tags(trim($req->request->get('newPasswd'))));
            $duplicate = htmlspecialchars(strip_tags(trim($req->request->get('newPasswdRep'))));
            $validator = new PasswordValidator();
            $errors = $validator->validate(['passwd' => $new, 'duplicate' => $duplicate, 'old' => $old]);
            if (empty($errors)) {
                try {
                    $user->updatePassword($old, $new);
                    $args['info'] = "Password updated";
                }
                catch (Exception $ex) {
                    $args['err'] = "Error: " . $ex->getMessage();
                }
            }
            else {
                $args['err'] = implode('<br>', $errors);
            }
        }
        elseif ($req->request->has('changeNick')) {
            $nick = htmlspecialchars(strip_tags(trim($req->request->get('nick'))));
            try {
                $user->changeNickname($nick);
                $args['info'] = "Nick was updated";
            }
            catch (Exception $ex) {
                $args['err'] = "Can not change nick:" . $ex->getMessage();
            }
        }
        elseif ($req->request->has('changeBio')) {
            $bio = htmlspecialchars(strip_tags(trim($req->request->get('bio'))));
            try {
                $user->changeBio($bio);
                $args['info'] = "Bio was updated";
            }
            catch (Exception $ex) {
                $args['err'] = "Can not change bio:" . $ex->getMessage();
            }
        }

        $args = array_merge($args, [
            'header' => new TemplateBuilder('header.html', [
                'logged' => true,
                'login' => $user->login
            ]),
            'name' => $user->name,
            'surname' => $user->surname,
            'login' => $user->login,
            'profilePic' => $user->profilePicPath,
            'userpage' => 'profile.php?id=' . $user->login,
            'bio' => $user->bio,
            'nick' => $user->nickname
        ]);

        $template = new TemplateBuilder('settings.html', $args);
        return new Response(strval($template));
    }
}

