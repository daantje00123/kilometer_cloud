<?php
use Firebase\JWT\JWT;

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

$user_model = new \Backend\Models\User_model($config);
$jwt_model = new \Backend\Models\Jwt_model($config);
$route_model = new \Backend\Models\Route_model($config);

// Generate JWT when the username and password combination is valid
$app->post('/auth/login', function($req, $res) use ($user_model, $jwt_model) {
    $body = $req->getParsedBody();

    $username = (!isset($body['username']) ? null : $body['username']);
    $password = (!isset($body['password']) ? null : $body['password']);

    if (
        empty($username) ||
        empty($password)
    ) {
        return $res->withJson(array(
            'success' => false,
            'message' => 'Username or password is not set or empty'
        ), 400);
    }

    try {
        $user = $user_model->validateLogin($username, $password);
    } catch (\Backend\Exceptions\UserException $e) {
        return $res->withJson(array(
            'success' => false,
            'message' => $e->getMessage(),
            'code' => $e->getCode()
        ), 500);
    }

    $jwt = $jwt_model->getJwt(array(
        'id_user' => $user->id_user,
        'username' => $user->username
    ));

    return $res->withJson(array(
        'success' => true,
        'message' => 'User is successfully authenticated',
        'jwt' => $jwt,
        'id_user' => $user->id_user
    ));
});

// This group is only accessible when a valid JWT is send in the headers
$app->group('/protected', function() use ($jwt_model, $route_model) {
    // Save a new route
    $this->post('/route', function($req, $res) use ($route_model) {
        $body = $req->getParsedBody();
        
        $id_user = (!isset($body['id_user']) ? null : $body['id_user']);
        $start_date = (!isset($body['start_date']) ? null : $body['start_date']);
        $route = (!isset($body['route']) ? null : $body['route']);
        $kms = (!isset($body['kms']) ? null : $body['kms']);

        try {
            $route_model->saveRoute($id_user, $start_date, $route, $kms);
        } catch (Exception $e) {
            return $res->withJson(array(
                'success' => false,
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ), 500);
        }

        return $res->withJson(array(
            'success' => true,
            'message' => "Route saved"
        ));
    });

    // Get the route history
    $this->get('/routes', function($req, $res) use ($route_model) {
        $id_user = $req->getAttribute('jwt')->data->id_user;
        $page_number = (isset($_GET['page']) ? $_GET['page'] : 1);

        try {
            $routes = $route_model->getRoutesByUserId($id_user, $page_number);
            $kms = $route_model->getTotalKmsByUserId($id_user);
            $price = $route_model->getTotalPriceByUserId($id_user);
            $count = $route_model->getCountByUserId($id_user);
        } catch (\Backend\Exceptions\RouteException $e) {
            return $res->withJson(array(
                'success' => false,
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ), 500);
        }

        return $res->withJson(array(
            'success' => true,
            'message' => 'Routes found',
            'routes' => $routes,
            'totals' => array(
                'kms' => $kms,
                'price' => $price,
                'count' => $count
            )
        ));
    });

    // Get a single route
    $this->get('/route', function($req, $res) use ($route_model) {
        $id_route = (isset($req->getQueryParams()['id_route']) ? $req->getQueryParams()['id_route'] : 0);
        $id_user = $req->getAttribute('jwt')->data->id_user;

        try {
            $route = $route_model->getRouteById($id_route, $id_user);
        } catch (\Backend\Exceptions\RouteException $e) {
            return $res->withJson(array(
                'success' => false,
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ));
        }

        return $res->withJson(array(
            'success' => true,
            'message' => 'Route found',
            'route' => $route
        ));
    });

    // Edit route
    $this->put('/route', function($req, $res) use ($route_model) {
        $body = $req->getParsedBody();

        $id_user = $req->getAttribute('jwt')->data->id_user;
        $id_route = (isset($body['id_route']) ? $body['id_route'] : null);
        $description = (isset($body['description']) ? $body['description'] : null);
        $paid = (isset($body['paid']) ? $body['paid'] : null);
        
        try {
            $route_model->editRoute($id_route, $id_user, $description, $paid);
            $route = $route_model->getRouteById($id_route, $id_user);
        } catch (\Backend\Exceptions\RouteException $e) {
            return $res->withJson(array(
                'success' => false,
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ), 500);
        }

        return $res->withJson(array(
            'success' => true,
            'message' => 'Route changed',
            'route' => $route
        ));
    });

    // Delete route
    $this->delete('/route', function($req, $res) use ($route_model) {
        $id_route = (isset($req->getQueryParams()['id_route']) ? $req->getQueryParams()['id_route'] : 0);
        $id_user = $req->getAttribute('jwt')->data->id_user;

        try {
            $route_model->deleteRoute($id_route, $id_user);
        } catch (\Backend\Exceptions\RouteException $e) {
            return $res->withJson(array(
                'success' => false,
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ), 500);
        }

        return $res->withJson(array(
            'success' => true,
            'message' => 'Route deleted'
        ));
    });

    // Used to generate a new JWT with a valid JWT
    $this->get('/ping', function($req, $res) use ($jwt_model) {
        try {
            $new_jwt = $jwt_model->regenerateJwt($req->getAttribute('jwt'));
        } catch(Exception $e) {
            return $res->withJson(array(
                'success' => false,
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ), 500);
        }

        return $res->withJson(array(
            'success' => true,
            'message' => 'pong!',
            'jwt' => $new_jwt
        ));
    });
})->add(function($req, $res, $next) use ($config) {
    if (!$req->hasHeader('HTTP_AUTHORIZATION') || empty($req->getHeader('HTTP_AUTHORIZATION')[0])) {
        return $res->withJson(array(
            'success' => false,
            'message' => 'No JWT found'
        ), 403);
    }

    $jwt = $req->getHeader("HTTP_AUTHORIZATION")[0];
    $jwt = str_replace('Bearer ', "", $jwt);

    try {
        $decoded = JWT::decode($jwt, $config->jwt->get('key'), array($config->jwt->get('algorithm')));
    } catch(Exception $e) {
        return $res->withJson(array(
            'success' => false,
            'message' => $e->getMessage()
        ), 500);
    }

    if (!isset($decoded)) {
        return $res->withJson(array(
            'success' => false,
            'message' => 'Internal Server Error'
        ), 500);
    }

    $req = $req->withAttribute('jwt', $decoded);

    $res = $next($req, $res);

    return $res;
});

$app->run();