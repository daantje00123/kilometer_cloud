<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once(__DIR__.'/../../vendor/autoload.php');

$config = new \Zend\Config\Config(require __DIR__.'/config/config.php');

$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];
$c = new \Slim\Container($configuration);
$app = new \Slim\App($c);

$pdo = new PDO('mysql:host='.$config->database->get('host', 'localhost').';dbname='.$config->database->get('database'),
    $config->database->get('username'),
    $config->database->get('password')
);

$user_model = new \Backend\Models\UserModel($config);
$jwt_model = new \Backend\Models\JwtModel($config, $user_model);

// Generate JWT when the username and password combination is valid
$app->post('/auth/login', '\Backend\Controllers\JwtController:login');

// This group is only accessible when a valid JWT is send in the headers
$app->group('/protected', function() {
    // Get the route history
    $this->get('/routes', '\Backend\Controllers\RouteController:routeHistory');

    // Get a single route
    $this->get('/route', '\Backend\Controllers\RouteController:getSingleRoute');

    // Save a new route
    $this->post('/route', '\Backend\Controllers\RouteController:saveRoute');

    // Edit route
    $this->put('/route', '\Backend\Controllers\RouteController:editRoute');

    // Delete route
    $this->delete('/route', '\Backend\Controllers\RouteController:deleteRoute');

    // Used to generate a new JWT with a valid JWT
    $this->get('/ping', '\Backend\Controllers\JwtController:ping');
})->add(new \Backend\Middleware\JwtMiddleware());

$app->run();