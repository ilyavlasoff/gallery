<?php

    namespace App\lib\websites;

    use App\lib\entities;
    use App\lib\validators\LoginValidator;
    use App\templates\TemplateBuilder;
    use Symfony\Component\Config\Definition\Exception\Exception;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Session\Session;
    use Symfony\Component\HttpFoundation\RedirectResponse;


class login {

    public static function render(): Response {
        $session = new Session();
        $session->start();
        $req = Request::createFromGlobals();

        if ($session->has('auth')) {
            $login = $session->get('auth')->login;
            $resp = new RedirectResponse("/profile/$login");
            return $resp;
        }

        if ($req->request->has('submit')) {

            $login = htmlspecialchars(strip_tags(trim($req->request->get('login'))));
            $passwd = htmlspecialchars(strip_tags(trim($req->request->get('passwd'))));

            $savedParams = [
                'header' => new TemplateBuilder('header.html', ['logged' => false]),
                'login' => $login, 'password' => $passwd
            ];

            $validator = new LoginValidator();
            $errors = $validator->validate($req->request->all());
            if (!empty($errors)) {
                $msg = implode('<br>', $errors);
                $savedParams['bottomLabel'] = $msg;
                $page = new TemplateBuilder('login.html', $savedParams);
                return new Response('Submit = (' . $req->request->get('submit') . ')<br>' . strval($page));
            }

            try {
                $user = entities\User::loginUserFromDB($login, $passwd);
                $session->set('auth', $user);
                $resp = new RedirectResponse("/profile/$login");
                return $resp;
            }
            catch (\Exception $ex) {
                $savedParams['bottomLabel'] = "A problem has occured:".$ex->getMessage();
                $page = new TemplateBuilder('login.html', $savedParams);
                return new Response(strval($page));
            }
        }
        else {
            $page = new TemplateBuilder('login.html', [
                'header' => new TemplateBuilder('header.html', ['logged' => false])
            ]);
            return new Response(strval($page));
        }
    }
}

