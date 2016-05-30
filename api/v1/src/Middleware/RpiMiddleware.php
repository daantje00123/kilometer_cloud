<?php
namespace Backend\Middleware;

use Backend\Database;
use Zend\Config\Config;

class RpiMiddleware {
    /**
     * @param       \Psr\Http\Message\ServerRequestInterface        $req        The client request
     * @param       \Psr\Http\Message\ResponseInterface             $res        The server response
     * @param       callable                                        $next       The next middleware
     *
     * @return      \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($req, $res, $next) {
        $body = $req->getParsedBody();

        $key = (isset($body['key']) ? $body['key'] : null);

        if (empty($key)) {
            return $res->withJson(array(
                'success' => false,
                'message' => 'Key not found'
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
                rpi_key = :key
        ");

        $stmt->execute(array(":key" => $key));

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