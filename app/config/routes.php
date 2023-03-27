<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$routes = new RouteCollection();

$routes->add('home', (new Route(
    '/',
    [
        '_controller' => 'SimplexWeb\Controller\HomeController::index'
    ]))->setMethods([Request::METHOD_GET])
);

return $routes;

