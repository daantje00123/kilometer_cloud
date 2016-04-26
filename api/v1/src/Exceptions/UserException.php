<?php
namespace Backend\Exceptions;

/**
 * Class UserException
 * @package Backend
 * @subpackage Exceptions
 */
class UserException extends \Exception {
    const NOT_FOUND = 101;
    const USERNAME_EMPTY = 102;
    const PASSWORD_EMPTY = 103;
    const ID_EMPTY = 104;
    const WRONG_PASSWORD = 105;
    const NOT_ACTIVATED = 106;
    const DATA_NOT_VALID = 107;
    const PASSWORD_MISMATCH = 108;
    const EMAIL_NOT_VALID = 109;
    const USERNAME_ALREADY_IN_USE = 110;
}