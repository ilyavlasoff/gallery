<?php

session_start();

if (!isset($_SESSION['logged']) || !isset($_SESSION['username'])) {
  require_once ('TemplateMaker.php');
  Template::AddPathValue('index', 'static/startpage.html');
  $mainPage = new Template('index');
  echo $mainPage;
}
else
{
  $identify = ($_SESSION['usrAgent'] === $_SERVER['HTTP_USER_AGENT'] &&
  $_SESSION['remAddr'] === $_SERVER['REMOTE_ADDR'] &&
  $_SESSION['forwardedFor'] === $_SERVER['HTTP_X_FORWARDED_FOR']);

  if ($identify) {
      header("Location: profile.php?id=${_SESSION['username']}");
  }
  else {
      header('Location: login.php');
  }
}

