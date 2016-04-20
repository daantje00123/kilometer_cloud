<?php
namespace Backend\Exceptions;

class RouteException extends \Exception {
    const DATA_NOT_VALID = 301;
    const START_DATE_FORMAT_NOT_VALID = 302;
}