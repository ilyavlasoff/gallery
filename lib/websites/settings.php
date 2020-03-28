<?php

namespace App\lib\websites;

use App\lib\entities;
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
        $args = [
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
        ];

        if ($req->request->has('changePicture')) {
            return new Response('No ok');
        }
        elseif ($req->request->has('changePasswd')) {
            return new Response('No ok');
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

        $template = new TemplateBuilder('settings.html', $args);
        return new Response(strval($template));
    }
}

function ChangeProfilePicture() {
    $uploadRootDir = './uploads/';
    $fileMaxSize = '100000000000';
    $fileAllowMimeTypes = ['image/jpg', 'image/jpeg', 'image/png'];
    $file = $_FILES['photo'];
    if ($file['error'] !== 0) {
        displayPage(['ERROR' => 'File was not attached']);
    }
    if ($file['size'] >= $fileMaxSize) {
        displayPage(['ERROR' => "File is too big. File size: ${file['size']}, max file size $fileMaxSize"]);
    }
    if (array_key_exists($file['type'], $fileAllowMimeTypes)) {
        displayPage(['ERROR' => "You can not upload files with extension ${file['type']}"]);
    }
    $uploadPath = $uploadRootDir . $_SESSION['username' . 'logo'];;
    if(file_exists($uploadPath)) {
        unlink($uploadPath);
    }
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        displayPage(['ERROR' => "Can not upload file"]);
    }
    displayPage(['INFO' => 'Profile picture changed successfully']);
}

function ChangePasswd() {
    if(!isset($_POST['oldPasswd']) || !isset($_POST['newPasswd']) || !isset($_POST['newPasswdRepeat'])) {
        displayPage(['ERROR' => "Password fields weren't filled correctly"]);
    }
    /*
     * ДОБАВИТЬ ПРОВЕРКУ ПАРОЛЯ НА СООТВЕТВИЕ
     */
    if ($_POST['newPasswd'] !== $_POST['newPasswdRepeat']) {
        displayPage(['ERROR' => "Repeat new password correctly"]);
    }
    try {
        $count = DBExecutor::ChangePassword($_SESSION['username'], $_POST['oldPasswd'], $_POST['newPasswd']);
        if ($count != 1) throw new Exception();
    }
    catch (Exception $ex) {
        displayPage(['ERROR' => "Can not change password. Try again later"]);
    }
    displayPage(['INFO' => 'Password was updated']);
}
