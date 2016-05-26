<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once(__DIR__.'/vendor/autoload.php');

$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];
$c = new \Slim\Container($configuration);
$app = new \Slim\App($c);

// Generate JWT when the username and password combination is valid
$app->post('/auth/login', '\Backend\Controllers\JwtController:login');

// Create a new user account
$app->post('/auth/register', '\Backend\Controllers\UserController:register');

// Activate a new user account
$app->get('/auth/activate', '\Backend\Controllers\UserController:activate');

// Get iOS token
$app->post('/auth/swift/login', '\Backend\Controllers\UserController:login');

// This group is only accessible when a valid JWT is send in the Authorization header
$app->group('/protected', function() {
    // Get the route history
    $this->get('/routes', '\Backend\Controllers\RouteController:routeHistory');

    // Get a single route
    $this->get('/route', '\Backend\Controllers\RouteController:getSingleRoute');

    // Save a new route
    $this->post('/route', '\Backend\Controllers\RouteController:saveRoute');

    // Save a part of the route
    $this->post('/route-part', '\Backend\Controllers\RouteController:saveRoutePart');

    // Edit route
    $this->put('/route', '\Backend\Controllers\RouteController:editRoute');

    // Delete route
    $this->delete('/route', '\Backend\Controllers\RouteController:deleteRoute');

    // Generate a new JWT with a valid JWT
    $this->get('/ping', '\Backend\Controllers\JwtController:ping');

    // Batch change paid status
    $this->put('/batch/routes/paid-status', '\Backend\Controllers\BatchController:changePaidStatus');

    // Batch delete routes
    $this->delete('/batch/routes/delete', '\Backend\Controllers\BatchController:deleteRoutes');
})->add(new \Backend\Middleware\JwtMiddleware());

// This group is only accessible when a valid user token is send
$app->group('/swift', function() {
    // Save a new route
    $this->post('/route', '\Backend\Controllers\RouteController:swiftSave');

    $this->post('/check', '\Backend\Controllers\UserController:swiftCheck');
})->add(new \Backend\Middleware\SwiftMiddleware());

$app->run();