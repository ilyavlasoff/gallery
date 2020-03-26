<?php

    namespace App\lib\websites;

    use App\lib\entities;
    use App\templates\TemplateBuilder;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Session\Session;
    use Symfony\Component\HttpFoundation\RedirectResponse;


class login {

    public static function render(): Response {
        $session = new Session();
        $session->start();
        $req = Request::createFromGlobals();

        if ($req->request->has('submit')) {

            $login = $req->request->get('login');
            $passwd = $req->request->get('passwd');

            if ($verLogin = verify($login, 'email') && $verPasswd = verify($passwd, 'password')) {


                if (DBExecutor::CheckUserRegistred($login, $passwd)) {
                    $_SESSION['logged'] = true;
                    $_SESSION['username'] = $login;
                    $_SESSION['usrAgent'] = $_SERVER['HTTP_USER_AGENT'];
                    $_SESSION['remAddr'] = $_SERVER['REMOTE_ADDR'];
                    $_SESSION['forwardedFor'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
                    header("Location: profile.php?id=$login");
                }
                else {
                    $errLoginpage = new Template('login', ['LOGIN'=> $login, 'BOTTOMLABEL' => "Account $login does'nt exists or password incorrect"]);
                    echo $errLoginpage;
                }
            }
            else {
                $errLoginpage = new Template('login', ['LOGIN'=> $login, 'PASSWORD' => $passwd, 'BOTTOMLABEL' => 'Your data in incorrect']);
                echo $errLoginpage;
            }

        }
        else {
            $page = new TemplateBuilder('login.html', ['bottomLabel' => 'Hello!']);
            return new Response(strval($page));
        }
    }
}

