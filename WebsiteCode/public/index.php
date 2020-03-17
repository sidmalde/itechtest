<?php

use itechTest\App\Controllers\HomeController;
use itechTest\App\Controllers\TwitterController;
use itechTest\Components\Routing\Exception\RouteNotFoundException;
use itechTest\Components\Routing\Router;

/**
 * Composer
 */
require dirname(__DIR__) . '/vendor/autoload.php';

// add the bootstrap
require __DIR__ . '/bootstrap.php';


// Initiate Error Handler
\itechTest\App\Services\ErrorHandlerService::initiateErrorHandler();

/**
 * Routing
 */
$router = new Router($app);


/*
 * Add Routes
 */
$router->get('/', new HomeController($app), 'getIndex');
$router->get('/iframe', new TwitterController($app), 'getIframe');
$router->get('/api/v1', new TwitterController($app), 'getApiTweets');

try {
    // intentionally enable CORS
    header('Access-Control-Allow-Origin: *');
    $router->execute();
} catch (RouteNotFoundException|Exception $exception) {


    // return a response code of 404 (Not Found)
    http_response_code(404);

    // show the 404 error page
    (new HomeController($app))->getErrorPage();
}
