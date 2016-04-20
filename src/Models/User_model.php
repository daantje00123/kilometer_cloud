<?php
namespace Backend\Models;

use Backend\Database;
use Backend\Exceptions\UserException;
use Zend\Config\Config;

class User_model {
    private $db;
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

    public function __construct(Config $config) {
        $this->db = new Database(
            $config->database->host,
            $config->database->username,
            $config->database->password,
            $config->database->database
        );
    }
    
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
}