<?php
namespace Backend\Middleware;

use Backend\Database;
use Backend\Models\UserModel;
use Zend\Config\Config;

class SwiftMiddleware {
    /**
     * @param       \Psr\Http\Message\ServerRequestInterface        $req        The client request
     * @param       \Psr\Http\Message\ResponseInterface             $res        The server response
     * @param       callable                                        $next       The next middleware
     *
     * @return      \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($req, $res, $next) {
        $body = $req->getParsedBody();

        $token = (isset($body['token']) ? $body['token'] : null);

        if (empty($token)) {
            return $res->withJson(array(
                'success' => false,
                'message' => 'Token not found'
            ), 400);
        }

        $config = new Config(require(__DIR__.'/../../config/config.php'));
        $db = new Database($config->database->host, $config->database->username, $config->database->password, $config->database->database);

        $stmt = $db->prepare("
            SELECT
                id_user
            FROM
                auth_users
            WHERE
                ios_token = :token
        ");

        $stmt->execute(array(":token" => $token));

        //var_dump($token);exit;

        if ($stmt->rowCount() < 1) {
            return $res->withJson(array(
                'success' => false,
                'message' => "User not found"
            ), 400);
        }

        $user = $stmt->fetch(\PDO::FETCH_OBJ);

        $req = $req->withAttribute('user', $user);

        $res = $next($req, $res);

        return $res;
    }
}