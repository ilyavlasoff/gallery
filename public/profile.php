<?php
session_start();
$identify = ($_SESSION['usrAgent'] === $_SERVER['HTTP_USER_AGENT'] &&
    $_SESSION['remAddr'] === $_SERVER['REMOTE_ADDR'] &&
    $_SESSION['forwardedFor'] === $_SERVER['HTTP_X_FORWARDED_FOR']);

if (!$identify || ! (isset($_SESSION['logged']) && isset($_SESSION['username']))) {
    require_once ('TemplateMaker.php');
    Template::AddPathValue('login', 'static/login.html');
    $loginRedirect = new Template('login', ['TOPLABEL' => 'You must be logged in to view user accounts']);
    echo $loginRedirect;
    exit();
}

require_once ('../db/DBExecutor.php');
try {
    $profileData = DBExecutor::GetUserInfo($_SESSION['username']);
}
catch (Exception $ex) {
    echo "Sorry, user {$_SESSION['username']} doesn't exists";
    exit();
}

require_once ('TemplateMaker.php');
Template::AddPathValue('page', 'static/userpage.html');
$page = new Template('page', ['NICK' => $profileData['nick'], 'PAGEID' => $profileData['login'],
    'PROFILEPIC' => $profileData['profilepicpath'], 'PROFILEACTION' => 'Edit profile',
    'NAME' => $profileData['name'], 'SURNAME' => $profileData['surname'], 'BIO' => $profileData['bio'], 'SESSION' => session_id()]);
echo $page;