<?php
use Firebase\JWT\JWT;

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once(__DIR__.'/../../vendor/autoload.php');

$config = new \Zend\Config\Config(require __DIR__.'/config/config.php');
$app = new \Slim\App();
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