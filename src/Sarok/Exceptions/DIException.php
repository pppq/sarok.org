<?php namespace Sarok\Exceptions;

use Exception;

/**
 * Thrown when an issue is encountered during the dependency injection process.
 */
class DIException extends Exception {

    public function __construct($message = '', $code = 0, $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
