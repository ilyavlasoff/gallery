<?php
session_start();

require_once ('TemplateMaker.php');
Template::AddPathValue('placeholder', 'static/placeholder.html');
Template::AddPathValue('page', 'static/userpage.html');

$identify = ($_SESSION['usrAgent'] === $_SERVER['HTTP_USER_AGENT'] &&
    $_SESSION['remAddr'] === $_SERVER['REMOTE_ADDR'] &&
    $_SESSION['forwardedFor'] === $_SERVER['HTTP_X_FORWARDED_FOR']);

if (!$identify || ! (isset($_SESSION['logged']) && isset($_SESSION['username']))) {
    sendError("Resourse isn't available without registration");
}

require_once ('../db/DBExecutor.php');
$reqId = $_GET['id'];
try {
    $profileData = DBExecutor::GetUserInfo($reqId);
    $postsCount = DBExecutor::GetPostsQuan($reqId);
    list($incomingSubs, $outcomingSubs) = DBExecutor::GetSubscriptions($reqId);
}
catch (Exception $ex) {
    sendError("User doesn't exists");
}

if ($reqId === $_SESSION['username']) {
    $profileAction = 'Edit profile';
    //Template::AddPathValue('editprof', 'static/editprofile.html');
    //$profileActionHandler = new Template('editprof');
    $profileActionHandler = "";
    $addPostButton = '<button class="btn btn-secondary btn-sm" onclick="addPost()">Add post</button>';
}

$page = new Template('page', [
    'NICK' => $profileData['nick'],
    'PAGEID' => $profileData['login'],
    'PROFILEPIC' => $profileData['profilepicpath'],
    'PROFILEACTION' => $profileAction,
    'POSTS' => $postsCount,
    'NAME' => $profileData['name'],
    'SURNAME' => $profileData['surname'],
    'BIO' => $profileData['bio'],
    'SESSION' => session_id(),
    'SUBSCRIBERS' => $incomingSubs,
    'SUBSCRIPTIONS' => $outcomingSubs,
    'ADDPOSTBUTTON' => $addPostButton,
    'PROFILEACTIONFUNC' => $profileActionHandler]);
echo $page;

function sendError($text) {
    $loginRedirect = new Template('placeholder', ['ERRNO' => $text]);
    echo $loginRedirect;
    exit();
}