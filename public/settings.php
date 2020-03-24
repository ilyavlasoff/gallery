<?php

session_start();
require_once ('../db/DBExecutor.php');
require_once('../internal/TemplateMaker.php');
Template::AddPathValue('settings', './static/settings.html');

$identify = ($_SESSION['usrAgent'] === $_SERVER['HTTP_USER_AGENT'] &&
    $_SESSION['remAddr'] === $_SERVER['REMOTE_ADDR'] &&
    $_SESSION['forwardedFor'] === $_SERVER['HTTP_X_FORWARDED_FOR']);

if (!$identify || ! (isset($_SESSION['logged']) && isset($_SESSION['username']))) {
    sendError("Resourse isn't available without registration");
}

$username = $_SESSION['username'];
$profileInfo = DBExecutor::GetUserInfo($username);
if (isset($_POST['changePicture'])) {
    ChangeProfilePicture();
}
elseif (isset($_POST['changePasswd'])) {
    ChangePasswd();
}
elseif (isset($_POST['changeNick'])) {
    ChangeNick();
}
elseif (isset($_POST['changeBio'])) {
    ChangeBio();
}
else {
    DisplayPage();
}

function DisplayPage(array $additional = null) {
    global $profileInfo;
    $args = [
        'NAME' => $profileInfo['name'],
        'SURNAME' => $profileInfo['surname'],
        'USERLOGIN' => $profileInfo['login'],
        'PROFILEPIC' => $profileInfo['profilepicpath'],
        'USERPAGE' => 'profile.php?id=' . $profileInfo['login'],
        'BIO' => $profileInfo['bio'],
        'NICK' => $profileInfo['nick']
    ];
    if ($additional) {
        $args = array_merge($args, $additional);
    }
    $page = new Template('settings', $args);
    echo $page;
    exit();
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

function ChangeNick() {
    if (!isset($_POST['nick'])) {
        displayPage(['ERROR' => "Nickname field wasn't filled correctly"]);
    }
    /*
     * Проверка на соответствие
     */
    try {
        DBExecutor::UpdateNick($_SESSION['username'], $_POST['nick']);

    }
    catch (Exception $ex) {
        displayPage(['ERROR' => "Can not change nickname. Try again later"]);
    }
    displayPage(['INFO' => "Nickname updated to ${_POST['nick']}"]);
}

function ChangeBio() {
    if (!isset($_POST['bio'])) {
        displayPage(['ERROR' => "Bio field wasn't filled correctly"]);
    }
    /*
     * Проверка на соответствие
     */
    try {
        DBExecutor::UpdateBio($_SESSION['username'], $_POST['bio']);
    }
    catch (Exception $ex) {
        displayPage(['ERROR' => "Can not change bio. Try again later"]);
    }
    displayPage(['INFO' => "Nickname updated to ${_POST['bio']}"]);
}