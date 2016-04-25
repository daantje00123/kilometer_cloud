<?php
namespace Backend\Models;

use Backend\Exceptions\JwtException;
use Backend\Exceptions\UserException;
use Firebase\JWT\JWT;
use Zend\Config\Config;

/**
 * Class JwtModel
 * @package Backend
 * @subpackage Models
 */
class JwtModel {
    /**
     * @var \Zend\Config\Config
     */
    private $config;

    /**
     * @var \Backend\Models\UserModel
     */
    private $userModel;

    /**
     * JwtModel constructor.
     * @param \Zend\Config\Config $config
     * @param \Backend\Models\UserModel $userModel
     */
    public function __construct(Config $config, UserModel $userModel) {
        $this->config = $config;
        $this->userModel = $userModel;
    }

    /**
     * Get a JWT
     *
     * @param   array       $data       Data to store in JWT
     * @return  string
     */
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

    /**
     * Regenerate a JWT
     *
     * @param   string      $old_jwt        The old JWT
     * @return  string
     * @throws  \Backend\Exceptions\JwtException
     * @throws  \Backend\Exceptions\UserException
     */
    public function regenerateJwt($old_jwt) {
        if (empty($old_jwt)) {
            throw new JwtException("Old JWT cannot be empty", JwtException::EMPTY_JWT);
        }

        try {
            $user = $this->userModel->getUserByUsername($old_jwt->data->username);
        } catch (UserException $e) {
            throw $e;
        }

        return $this->getJwt(array(
            "id_user" => $user->id_user,
            "username" => $user->username
        ));
    }
}