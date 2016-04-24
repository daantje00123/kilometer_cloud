<?php
namespace Backend\Exceptions;

/**
 * Class JwtException
 * @package Backend
 * @sucpackage Exceptions
 */
class JwtException extends \Exception {
    const EMPTY_JWT = 201;
}