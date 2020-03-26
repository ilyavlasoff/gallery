<?php

require_once '../vendor/autoload.php';


use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;


try {
    $cfgDir = '../config';
    $routesCfgFile = 'routes.yaml';
    $locator = new FileLocator(array($cfgDir));

    $rqContext = new RequestContext();
    $request = Request::createFromGlobals();
    $rqContext ->fromRequest($request);

    $router = new Router (
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

}
catch (ResourceNotFoundException $ex) {
    echo $ex->getMessage();
}