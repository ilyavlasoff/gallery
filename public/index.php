<?php

session_start();

if (!isset($_SESSION['logged'])) {
  $_SESSION['usrAgent'] = $_SERVER['HTTP_USER_AGENT'];
  $_SESSION['remAddr'] = $_SERVER['REMOTE_ADDR'];
  $_SESSION['forwardedFor'] = $_SERVER['HTTP_X_FORWARDED_FOR'];

  require_once ('TemplateMaker.php');
  Template::AddPathValue('index', 'static/startpage.html');
  $mainPage = new Template('index');
  echo $mainPage;
}
else {
  $identify = ($_SESSION['usrAgent'] === $_SERVER['HTTP_USER_AGENT'] &&
  $_SESSION['remAddr'] === $_SERVER['REMOTE_ADDR'] &&
  $_SESSION['forwardedFor'] === $_SERVER['HTTP_X_FORWARDED_FOR']);

  if ($identify) {
    echo "Redirecting to your page...";
  }
  else {
      header('Location: login.php');
  }
}

