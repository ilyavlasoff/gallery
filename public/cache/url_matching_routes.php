<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/foo' => [[['_route' => 'foo_route', 'controller' => 'FooController::indexAction'], null, null, null, false, false, null]],
        '/' => [[['_route' => 'root_route', 'controller' => 'App\\lib\\controller\\Controller::startpageCall'], null, null, null, false, false, null]],
        '/login' => [[['_route' => 'login_route', 'controller' => 'App\\lib\\controller\\Controller::loginCall'], null, null, null, false, false, null]],
        '/register' => [[['_route' => 'reg_route', 'controller' => 'App\\lib\\controller\\Controller::registerCall'], null, null, null, false, false, null]],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/foo/([0-9]+)(*:20)'
            .')/?$}sD',
    ],
    [ // $dynamicRoutes
        20 => [
            [['_route' => 'foo_placeholder_route', 'controller' => 'FooController::loadAction'], ['id'], null, null, false, true, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
