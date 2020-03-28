<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/' => [[['_route' => 'root_route', 'controller' => 'App\\lib\\controller\\Controller::startpageCall'], null, null, null, false, false, null]],
        '/login' => [[['_route' => 'login_route', 'controller' => 'App\\lib\\controller\\Controller::loginCall'], null, null, null, false, false, null]],
        '/register' => [[['_route' => 'reg_route', 'controller' => 'App\\lib\\controller\\Controller::registerCall'], null, null, null, false, false, null]],
        '/edit' => [[['_route' => 'edit_route', 'controller' => 'App\\lib\\controller\\Controller::editCall'], null, null, null, false, false, null]],
        '/newpost' => [[['_route' => 'newpost_route', 'controller' => 'App\\lib\\controller\\Controller::newpostCall'], null, null, null, false, false, null]],
        '/logout' => [[['_route' => 'logout_route', 'controller' => 'App\\lib\\controller\\Controller::logoutCall'], null, null, null, false, false, null]],
        '/subscribe' => [[['_route' => 'subscribe_route', 'controller' => 'App\\lib\\controller\\Controller::subscribeCall'], null, null, null, false, false, null]],
        '/getphotos' => [[['_route' => 'getphotos_route', 'controller' => 'App\\lib\\controller\\Controller::getphotosCall'], null, null, null, false, false, null]],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/profile/(.+@.+\\..+)(*:27)'
                .'|/image/([^/]++)/([^/]++)/([^/]++)(*:67)'
            .')/?$}sD',
    ],
    [ // $dynamicRoutes
        27 => [[['_route' => 'page_route', 'controller' => 'App\\lib\\controller\\Controller::profileCall'], ['id'], null, null, false, true, null]],
        67 => [
            [['_route' => 'loadimage_route', 'controller' => 'App\\lib\\controller\\Controller::loadimageCall'], ['username', 'filename', 'size'], null, null, false, true, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
