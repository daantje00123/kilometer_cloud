<?php
namespace Backend\Controllers;

use Backend\Models\JwtModel;
use Backend\Models\UserModel;
use Firebase\JWT\JWT;
use Interop\Container\ContainerInterface;
use Zend\Config\Config;

/**
 * Class JwtController
 *
 * @package         Backend
 * @subpackage      Controllers
 */
class JwtController extends Controller {
    /**
     * @var     \Backend\Models\JwtModel
     */
    private $jwtModel;

    /**
     * @var     \Backend\Models\UserModel
     */
    private $userModel;

    /**
     * JwtController constructor.
     *
     * @param   \Interop\Container\ContainerInterface           $ci
     */
    public function __construct(ContainerInterface $ci) {
        parent::__construct($ci);

        $this->userModel = new UserModel($this->config);
        $this->jwtModel = new JwtModel($this->config, $this->userModel);
    }

    /**
     * Login an user.
     *
     * Required POST data:
     *  username
     *  password
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
            'id_user' => $user->id_user,
            'user_data' => $user
        ));
    }

    /**
     * Ping function sends a new JWT as response.
     *
     * Requires a valid JWT
     *
     * @param       \Psr\Http\Message\ServerRequestInterface        $req        The client request
     * @param       \Psr\Http\Message\ResponseInterface             $res        The server response
     *
     * @return      \Psr\Http\Message\ResponseInterface
     *
     * @throws      \Backend\Exceptions\JwtException
     * @throws      \Backend\Exceptions\UserException
     */
    public function ping($req, $res) {
        try {
            $new_jwt = $this->jwtModel->regenerateJwt($req->getAttribute('jwt'));
            $decoded = JWT::decode($new_jwt, $this->config->jwt->get('key'), array($this->config->jwt->get('algorithm')));
            $user = $this->userModel->getUserById($decoded->data->id_user);
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
            'jwt' => $new_jwt,
            'id_user' => $user->id_user,
            'user_data' => $user
        ));
    }
}