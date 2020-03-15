<?php
  session_start();
  require_once ('TemplateMaker.php');
  Template::AddPathValue('login', 'static/login.html');
  if (isset($_POST['submit'])) {
    $login = $_POST['login'];
    $passwd = $_POST['passwd'];

    require_once ('../internal/verifier.php');
    if (verify($login, 'email') && verify($passwd, 'password')) {
      require_once ('../db/DBExecutor.php');
      if (DBExecutor::CheckUserRegistred($login, $passwd)) {
        $_SESSION['logged'] = true;
      }
      else {
        $errLoginpage = new Template('login', ['LOGIN'=> $login, 'BOTTOMLABEL' => "
      Account $login does'nt exists or password incorrect"]);
        echo strval($errLoginpage);
      }
    }
    else {
      $errLoginpage = new Template('login', ['LOGIN'=> $login, 'PASSWORD' => $passwd, 'BOTTOMLABEL' => '
      Your data in incorrect']);
      echo strval($errLoginpage);
    }

  }
  else {
    $errLoginpage = new Template('login');
    echo strval($errLoginpage);
  }

?>
