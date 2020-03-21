<?php
session_start();

require_once ('TemplateMaker.php');
require_once ('../db/DBExecutor.php');
Template::AddPathValue('placeholder', 'static/placeholder.html');
Template::AddPathValue('page', 'static/userpage.html');

$identify = ($_SESSION['usrAgent'] === $_SERVER['HTTP_USER_AGENT'] &&
    $_SESSION['remAddr'] === $_SERVER['REMOTE_ADDR'] &&
    $_SESSION['forwardedFor'] === $_SERVER['HTTP_X_FORWARDED_FOR']);

if (!$identify || ! (isset($_SESSION['logged']) && isset($_SESSION['username']))) {
    sendError("Resourse isn't available without registration");
}

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
    $profileActionHandler = "";
    $addPostButton = '<button class="btn btn-secondary btn-sm" onclick="addPost()">Add post</button>';
    $profileActionOnclick = 'changeSettings()';
}
else {
    $profileAction = 'Subscribe';
    Template::AddPathValue('subscr', 'static/subscription.js');
    $addPostButton = '';
    $isSubscripted = DBExecutor::CheckSubscription($_SESSION['username'], $reqId);
    $profileActionHandler = new Template('subscr', ['SUBSCRIPTED' => $isSubscripted]);
    $profileActionOnclick = 'subscr()';
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
    'PROFILEACTIONFUNC' => strval($profileActionHandler),
    'PROFILEACTIONCALL' => $profileActionOnclick]);
echo $page;

function sendError($text) {
    $loginRedirect = new Template('placeholder', ['ERRNO' => $text]);
    echo $loginRedirect;
    exit();
}