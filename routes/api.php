<?php

use Laravel\Lumen\Routing\Router;


/** @var Router $router */
$router->get("/sites", 'SiteController@index');
$router->patch("/sites/{id:\d+}/crawl", 'SiteController@crawl');