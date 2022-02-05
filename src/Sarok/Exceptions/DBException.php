<?php namespace Sarok\Exceptions;

use Exception;

/**
 * Thrown when a database operation can not be completed for any reason.
 */
class DBException extends Exception {
    
    public function __construct($message = '', $code = 0, $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
