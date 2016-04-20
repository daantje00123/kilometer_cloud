<?php
namespace Backend\Models;

use Backend\Exceptions\JwtException;
use Backend\Exceptions\UserException;
use Firebase\JWT\JWT;
use Zend\Config\Config;

class Jwt_model {
    private $config;

    public function __construct(Config $config) {
        $this->config = $config;
    }

    public function getJwt(array $data) {
        $tokenId = base64_encode(file_get_contents('/dev/urandom', false, null, 0, 32));
        $issuedAt = time();
        $notBefore = $issuedAt;
        $expire = $notBefore + 60;
        $serverName = $this->config->get('serverName');

        $data = array(
            'iat' => $issuedAt,
            'jti' => $tokenId,
            'iss' => $serverName,
            'nbf' => $notBefore,
            'exp' => $expire,
            'data' => $data
        );

        $secretKey = $this->config->jwt->get('key');
        $algorithm = $this->config->jwt->get('algorithm');

        return JWT::encode(
            $data,
            $secretKey,
            $algorithm
        );
    }

    public function regenerateJwt($old_jwt) {
        if (empty($old_jwt)) {
            throw new JwtException("Old JWT cannot be empty", JwtException::EMPTY_JWT);
        }

        $user_model = new User_model($this->config);

        try {
            $user = $user_model->getUserByUsername($old_jwt->data->username);
        } catch (UserException $e) {
            throw $e;
        }

        return $this->getJwt(array(
            "id_user" => $user->id_user,
            "username" => $user->username
        ));
    }
}