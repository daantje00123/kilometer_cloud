<?php
namespace Backend\Controllers;

use Backend\Models\JwtModel;
use Backend\Models\UserModel;
use Interop\Container\ContainerInterface;
use Zend\Config\Config;

class JwtController {
    private $ci;
    private $config;
    private $jwtModel;
    private $userModel;

    public function __construct(ContainerInterface $ci) {
        $this->ci = $ci;
        $this->config = new Config(require(__DIR__.'/../../api/v1/config/config.php'));
        $this->userModel = new UserModel($this->config);
        $this->jwtModel = new JwtModel($this->config, $this->userModel);
    }

    public function login($req, $res) {
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
            $user = $this->userModel->validateLogin($username, $password);
        } catch (\Exception $e) {
            return $res->withJson(array(
                'success' => false,
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ), 500);
        }

        $jwt = $this->jwtModel->getJwt(array(
            'id_user' => $user->id_user,
            'username' => $user->username
        ));

        return $res->withJson(array(
            'success' => true,
            'message' => 'User is successfully authenticated',
            'jwt' => $jwt,
            'id_user' => $user->id_user
        ));
    }

    public function ping($req, $res) {
        try {
            $new_jwt = $this->jwtModel->regenerateJwt($req->getAttribute('jwt'));
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
    }
}