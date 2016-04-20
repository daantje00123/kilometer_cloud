<?php
namespace Backend\Exceptions;

class UserException extends \Exception {
    const NOT_FOUND = 101;
    const USERNAME_EMPTY = 102;
    const PASSWORD_EMPTY = 103;
    const ID_EMPTY = 104;
    const WRONG_PASSWORD = 105;
    const NOT_ACTIVATED = 106;
}