<?php
session_start();
require_once('../internal/TemplateMaker.php');
require_once('../internal/ConfigReader.php');
require_once ('../db/DBExecutor.php');
Template::AddPathValue('addpost','./static/addpost.html');

Template::AddPathValue('placeholder', 'static/placeholder.html');
if (!isset($_SESSION['logged']) || !isset($_SESSION['username'])) {
    $loginRedirect = new Template('placeholder', ['ERRNO' => 'Resource is unavailble without registration']);
    echo $loginRedirect;
    exit();
}

/*
 * Обработать пользовательский ввод!
 */

if (isset($_POST['submit'])) {

    /*
     * Сделать чтение из конфигов параметров максимального размера файла и корневой директории
     */

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
