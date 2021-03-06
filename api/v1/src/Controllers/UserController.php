<?php
namespace Backend\Controllers;

use Interop\Container\ContainerInterface;
use Backend\Models\UserModel;

/**
 * Class UserController
 * @package Backend\Controllers
 */
class UserController extends Controller {
    private $model;

    /**
     * UserController constructor.
     * @param ContainerInterface $ci
     */
    public function __construct(ContainerInterface $ci) {
        parent::__construct($ci);

        $this->model = new UserModel($this->config, $ci);
    }

    /**
     * Login user in for iOS app
     *
     * @param       \Psr\Http\Message\ServerRequestInterface        $req        The client request
     * @param       \Psr\Http\Message\ResponseInterface             $res        The server response
     *
     * @return      \Psr\Http\Message\ResponseInterface
     */
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
            $user = $this->model->validateLogin($username, $password);
            $token = $this->model->generateIosToken($user->id_user);
        } catch (\Exception $e) {
            return $res->withJson(array(
                'success' => false,
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ), 500);
        }

        return $res->withJson(array(
            'success' => true,
            'message' => 'User is successfully authenticated',
            'id_user' => $user->id_user,
            'user_data' => $user,
            'token' => $token
        ));
    }

    /**
     * Register a new user
     *
     * @param       \Psr\Http\Message\ServerRequestInterface        $req        The client request
     * @param       \Psr\Http\Message\ResponseInterface             $res        The server response
     *
     * @return      \Psr\Http\Message\ResponseInterface
     */
    public function register($req, $res) {
        $body = $req->getParsedBody();

        $username = (isset($body['username']) ? $body['username'] : null);
        $email = (isset($body['email']) ? $body['email'] : null);
        $password1 = (isset($body['password1']) ? $body['password1'] : null);
        $password2 = (isset($body['password2']) ? $body['password2'] : null);
        $firstname = (isset($body['firstname']) ? $body['firstname'] : null);
        $middlename = (isset($body['middlename']) ? $body['middlename'] : null);
        $lastname = (isset($body['lastname']) ? $body['lastname'] : null);
        
        try {
            $user = $this->model->registerUser(
                $username,
                $email,
                $password1,
                $password2,
                $firstname,
                $middlename,
                $lastname
            );
        } catch (\Exception $e) {
            return $res->withJson(array(
                'success' => false,
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ), 500);
        }

        return $res->withJson(array(
            'success' => true,
            'message' => 'User registered',
            'user' => $user
        ));
    }

    /**
     * Activate a new user account
     *
     * @param       \Psr\Http\Message\ServerRequestInterface        $req        The client request
     * @param       \Psr\Http\Message\ResponseInterface             $res        The server response
     *
     * @return      \Psr\Http\Message\ResponseInterface
     */
    public function activate($req, $res) {
        $token = (isset($req->getQueryParams()['token']) ? $req->getQueryParams()['token'] : null);

        if (empty($token)) {
            return $res->withHeader('Location', $req->getUri()->getScheme().'://'.$req->getUri()->getHost());
        }

        try {
            $this->model->activateUser($token);
        } catch (\Exception $e) {
            http_response_code(500);
            echo '<p>Er is een fout opgetreden tijdens het activeren van uw account</p>';
            echo '<pre>'.$e->getMessage().'</pre>';
        }

        echo '<p>Uw account is succesvol geactiveerd. U kunt nu <a href="'.$req->getUri()->getScheme().'://'.$req->getUri()->getHost().'">inloggen</a>.</p>';
    }

    /**
     * Validate user token for iOS
     *
     * @param       \Psr\Http\Message\ServerRequestInterface        $req        The client request
     * @param       \Psr\Http\Message\ResponseInterface             $res        The server response
     *
     * @return      \Psr\Http\Message\ResponseInterface
     */
    public function swiftCheck($req, $res) {
        $body = $req->getParsedBody();

        $token = (isset($body['token']) ? $body['token'] : null);

        try {
            $valid = $this->model->validateToken($token);
        } catch(\Exception $e) {
            return $res->withJson(array(
                'success' => false,
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ), 500);
        }

        if ($valid !== true) {
            return $res->withJson(array(
                'success' => false,
                'message' => 'Token not valid'
            ), 500);
        }

        return $res->withJson(array(
            'success' => true,
            'message' => 'Token valid'
        ));
    }
}