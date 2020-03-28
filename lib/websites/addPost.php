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

class AddPost {

    public static function render(): Response {
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

        if($req->request->has('submit')) {
            $photo = $req->files->get('fileinp');
            $comment = htmlspecialchars(strip_tags(trim($req->get('photoDescription'))));
            $validator = new PostValidator();
            $errors = $validator->validate([
                'comment' => $comment,
                'photo' => $photo
            ]);
            if (!empty($errors)) {
                $params['error'] = implode('<br>', $errors);
            }
            else {
                try {
                    $user->addPost($photo, $comment);
                    return new RedirectResponse('/profile/' . $user->login);
                }
                catch (\Exception $ex) {
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

/*
 * Обработать пользовательский ввод!
 */
/*
if (isset($_POST['submit'])) {



    $uploadRootDir = './uploads/';
    $fileMaxSize = '100000000000';
    $fileAllowMimeTypes = ['image/jpg', 'image/jpeg', 'image/png'];
    $ownerLogin = $_SESSION['username'];
    $uploadDir = $uploadRootDir . $ownerLogin . '/';
    $description = $_POST['photoDescription'] ?? "";
    $file = $_FILES['fileinp'] ?? displayPage(['ERROR' => 'File was not attached']);
    if ($file['error'] !== 0) {
        displayPage(['ERROR' => 'File was not attached']);
    }
    $uploadFilePath = $uploadDir . basename($file['tmp_name']);

    if ($file['size'] >= $fileMaxSize) {
        displayPage(['ERROR' => "File is too big. File size: ${file['size']}, max file size $fileMaxSize"]);
    }
    if (array_key_exists($file['type'], $fileAllowMimeTypes)) {
        displayPage(['ERROR' => "You can not upload files with extension ${file['type']}"]);
    }
    if (!move_uploaded_file($file['tmp_name'], $uploadFilePath)) {
        displayPage(['ERROR' => "Can not upload file"]);
    }

    try {
        $inserted = DBExecutor::AddPhoto($ownerLogin, $uploadFilePath, $description);
        if ($inserted !== 1) {
            throw new Exception('Can not insert file');
        }
    }
    catch(Exception $ex) {
        displayPage(['ERROR'=>'Error']);
    }
    header("Location: profile.php?id=${_SESSION['username']}");
}
else {
    displayPage();
}

function displayPage(array $additional = null) {
    $info = DBExecutor::GetUserInfo($_SESSION['username']);
    $params = [
        'USERPAGE' => "./profile.php?id=${info['login']}",
        'NAME' => $info['name'],
        'SURNAME' => $info['surname'],
        'USERLOGIN' => $info['login'],
        'PROFILEPIC' => $info['profilepicpath'],
    ];
    if ($additional) {
        $params = array_merge($params, $additional);
    }
    $page = new Template('addpost', $params);
    echo $page;
    exit();
}
*/