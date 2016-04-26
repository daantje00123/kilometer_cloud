<?php
namespace Backend\Models;

use Backend\Database;
use Backend\Exceptions\UserException;
use Interop\Container\ContainerInterface;
use Zend\Config\Config;

/**
 * Class UserModel
 * @package Backend
 * @subpackage Models
 */
class UserModel {
    /**
     * @var \Backend\Database
     */
    private $db;

    /**
     * @var array         Database columns
     */
    private $userFields = array(
        "id_user",
        "username",
        "email",
        "password",
        "secret",
        "firstname",
        "middlename",
        "lastname",
        "confirm_token",
        "active",
        "role"
    );

    private $config;

    private $ci;

    /**
     * UserModel constructor.
     * @param \Zend\Config\Config       $config
     */
    public function __construct(Config $config, ContainerInterface $ci) {
        $this->config = $config;
        $this->ci = $ci;

        $this->db = new Database(
            $config->database->host,
            $config->database->username,
            $config->database->password,
            $config->database->database
        );
    }

    /**
     * Get user data
     *
     * @param   string      $username       Username
     * @return  array
     * @throws  \Backend\Exceptions\UserException
     */
    public function getUserByUsername($username) {
        if (empty($username)) {
            throw new UserException("Username cannot be empty", UserException::USERNAME_EMPTY);
        }

        $query = "SELECT ";

        foreach($this->userFields as $i => $field) {
            $query .= $field;

            if ($i+1 != count($this->userFields)) {
                $query .= ',';
            }
        }

        $query .= " FROM auth_users WHERE username = :username";

        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":username" => $username));

        if ($stmt->rowCount() < 1) {
            throw new UserException("User not found", UserException::NOT_FOUND);
        }

        return $stmt->fetch(\PDO::FETCH_OBJ);
    }

    /**
     * Get user data
     *
     * @param   int         $id         User id
     * @return  array
     * @throws  \Backend\Exceptions\UserException
     */
    public function getUserById($id) {
        $id = (int) $id;

        if (empty($id)) {
            throw new UserException("User id cannot be empty", UserException::ID_EMPTY);
        }

        $query = "SELECT ";

        foreach($this->userFields as $i => $field) {
            $query .= $field;

            if ($i+1 != count($this->userFields)) {
                $query .= ',';
            }
        }

        $query .= " FROM auth_users WHERE id_user = :id_user";

        $stmt = $this->db->prepare($query);
        $stmt->execute(array(':id_user' => $id));

        if ($stmt->rowCount() < 1) {
            throw new UserException("User not found", UserException::NOT_FOUND);
        }

        return $stmt->fetch(\PDO::FETCH_OBJ);
    }

    /**
     * Check if supplied credentials are valid
     *
     * @param   string      $username       Username
     * @param   string      $password       Password
     * @return  array
     * @throws  \Backend\Exceptions\UserException
     */
    public function validateLogin($username, $password) {
        if (empty($username)) {
            throw new UserException("Username cannot be empty", UserException::USERNAME_EMPTY);
        }

        if (empty($password)) {
            throw new UserException("Password cannot be empty", UserException::PASSWORD_EMPTY);
        }

        $user = $this->getUserByUsername($username);

        if ((bool) $user->active != true) {
            throw new UserException("User is not (yet) activated", UserException::NOT_ACTIVATED);
        }

        if (!password_verify($password.$user->secret, $user->password)) {
            throw new UserException("Wrong password", UserException::WRONG_PASSWORD);
        }

        return $user;
    }

    public function registerUser($username, $email, $password1, $password2, $firstname, $middlename, $lastname) {
        if (
            empty($username) ||
            empty($email) ||
            empty($password1) ||
            empty($password2) ||
            empty($firstname) ||
            empty($lastname)
        ) {
            throw new UserException("Data is not valid", UserException::DATA_NOT_VALID);
        }

        if ($password1 != $password2) {
            throw new UserException("Password mismatch", UserException::PASSWORD_MISMATCH);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new UserException("Email not valid", UserException::EMAIL_NOT_VALID);
        }

        try {
            $user = $this->getUserByUsername($username);
        } catch(UserException $e) {
            $user = null;
        }
        
        if (!empty($user)) {
            throw new UserException("Username already in use", UserException::USERNAME_ALREADY_IN_USE);
        }

        $confirm_token = sha1(microtime(true).mt_rand(10000,90000));
        $secret = sha1(microtime(true).mt_rand(10000,90000));
        $hash = password_hash($password1.$secret, PASSWORD_DEFAULT);

        $stmt = $this->db->prepare("
            INSERT INTO
                auth_users (username, email, password, secret, firstname, middlename, lastname, confirm_token, role)
            VALUES (
                :username,
                :email,
                :password,
                :secret,
                :firstname,
                :middlename,
                :lastname,
                :confirm_token,
                :role
            )
        ");

        $stmt->execute(array(
            ":username" => $username,
            ":email" => $email,
            ":password" => $hash,
            ":secret" => $secret,
            ":firstname" => $firstname,
            ":middlename" => $middlename,
            ":lastname" => $lastname,
            ":confirm_token" => $confirm_token,
            ":role" => "ROLE_USER"
        ));

        $fullname = ($firstname.(!empty($middlename) ? ' '.$middlename : '').' '.$lastname);

        $mail = new \PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = $this->config->email->host;
        $mail->SMTPAuth = true;
        $mail->Username = $this->config->email->username;
        $mail->Password = $this->config->email->password;
        if (!empty($this->config->email->secure)) {
            $mail->SMTPSecure = $this->config->email->secure;
        }
        $mail->Port = $this->config->email->port;

        $mail->setFrom($this->config->email->from_address, $this->config->email->from_name);
        $mail->addReplyTo($this->config->email->answer_address, $this->config->email->answer_name);
        $mail->addAddress($email, $fullname);

        $mail->isHTML($this->config->email->html);

        $mail->Body = '
<p>Geachte Heer/Mevrouw '.$fullname.',</p>
<p><a href="'.$this->ci->request->getUri()->getScheme().'://'.$this->ci->request->getUri()->getHost().'/api/v1/auth/activate?token='.$confirm_token.'">Activeer</a> uw account om in te kunnen loggen.</p>
<p>Met vriendelijke groet,</p>
<p>Kilometer cloud</p>
        ';

        $mail->send();

        return $this->getUserByUsername($username);
    }

    public function activateUser($confirm_token) {
        if (empty($confirm_token)) {
            throw new UserException("Data not valid", UserException::DATA_NOT_VALID);
        }

        $stmt = $this->db->prepare("
            UPDATE
                auth_users
            SET
                confirm_token = '',
                active = 1
            WHERE
                confirm_token = :token
        ");

        $stmt->execute(array(":token" => $confirm_token));
    }
}