<?php

require_once '../vendor/autoload.php';
define('root', realpath(__DIR__ . '/../'));

use App\templates\TemplateBuilder;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

set_exception_handler('displayError');

try {
    $cfgDir = '../config';
    $routesCfgFile = 'routes.yaml';
    $locator = new FileLocator(array($cfgDir));

    $rqContext = new RequestContext();
    $request = Request::createFromGlobals();
    $rqContext ->fromRequest($request);

    $router = new Router(
        new YamlFileLoader($locator),
        $routesCfgFile,
        array('cache_dir' => __DIR__.'/cache'),
        $rqContext
    );

    $params = $router->match($rqContext->getPathInfo());
    $controller = $params['controller'];
    list($controller, $function) = explode('::', $controller);
    $controller = new $controller($request);
    $response = $controller->$function($params);
    $response->send();
} catch (ResourceNotFoundException $ex) {
    displayError('Page does not exist');
} catch (Exception $ex) {
    displayError('Unexpected error');
}

function displayError($message): Response
{
    $errPage = new TemplateBuilder('placeholder.html', ['error' => $message]);
    $resp = new Response(strval($errPage));
    $resp->send();
}
